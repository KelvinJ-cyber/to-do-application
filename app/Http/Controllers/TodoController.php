<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    //
    public function store(Request $request){

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $todos = $request->user()->todos()->create($validated);

        return response()->json([
            'id' => $todos["id"],
            'title' => $todos["title"],
            'description' => $todos["description"]
        ] , 201);

    }

    public function index(Request $request){

        $query = $request->user()->todos();
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        $todos = $query->paginate($request->input('limit', 10));
        return response()->json([
            'page' => $todos->currentPage(),
            'limit'       => $todos->perPage(),
            'total'       => $todos->total(),
            'data'        => $todos->items()
            ]);

    }

    public function update(Request $request, $id)
    {

        $todo = Todo::find($id);

        if(!$todo){
            return response()->json(['message' => 'Todo not found'], 404);
        }
        if ($todo->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $todo->update($validated);

        return response()->json([
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $todo = Todo::find($id);

        if(!$todo){
            return response()->json(['message' => 'Todo not found'], 404);
        }
        if ($todo->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $todo->delete();
        return response()->noContent(204);

    }
}
