# Todo List REST API


## Project Url: https://roadmap.sh/projects/todo-list-api

A RESTful API built with **Laravel 13** and **PostgreSQL** for managing a personal to-do list, featuring full user authentication via **Laravel Sanctum**. Built as a backend practice project covering authentication, schema design, CRUD operations, authorization, and API security.

## Features

- User registration with hashed passwords
- Token-based authentication (Laravel Sanctum)
- Full CRUD for to-do items
- Ownership-based authorization (users can only modify their own todos)
- Pagination and filtering on the todo list endpoint
- Centralized validation and error handling
## Tech Stack

| Component | Technology |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8+ |
| Database | PostgreSQL |
| Auth | Laravel Sanctum (token-based) |

## Requirements

- PHP >= 8.2
- Composer
- PostgreSQL
- Laravel CLI / Artisan
## Installation

1. Clone the repository
```bash
   git clone <your-repo-url>
   cd todo-api
```

2. Install dependencies
```bash
   composer install
```
Update `.env` with your PostgreSQL credentials:
```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=todo_api
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
```

4. Generate the application key
```bash
   php artisan key:generate
```

5. Run migrations
```bash
   php artisan migrate
```

6. Start the development server
```bash
   php artisan serve
```

The API will be available at `http://localhost:8000/api`.

## Authentication

All todo endpoints are protected and require a Bearer token, obtained from either the register or login endpoint.

Include the token in every protected request:
```
Authorization: Bearer <your_token>
Accept: application/json
```

## API Endpoints

### Register

```
POST /api/register
```

**Body**
```json
{
  "name": "Kelvin Justine",
  "email": "kelvinjus@example.com",
  "password": "password123"
}
```

**Response — 201**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"
}
```

### Login

```
POST /api/login
```

**Body**
```json
{
  "email": "kelvinjus@example.com",
  "password": "password123"
}
```

**Response — 200**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"
}
```

**Response — 401 (invalid credentials)**
```json
{
  "message": "Invalid credentials"
}
```

### Create Todo

```
POST /api/todos
```
*Requires authentication*

**Body**
```json
{
  "title": "Buy groceries",
  "description": "Buy milk, eggs, and bread"
}
```

**Response — 201**
```json
{
  "id": 1,
  "title": "Buy groceries",
  "description": "Buy milk, eggs, and bread"
}
```

### Get Todos (paginated + filterable)

```
GET /api/todos?page=1&limit=10&title=groceries
```
*Requires authentication*

| Query param | Required | Description |
|---|---|---|
| `page` | No (default: 1) | Page number |
| `limit` | No (default: 10) | Items per page |
| `title` | No | Partial match filter on todo title |

**Response — 200**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Buy groceries",
      "description": "Buy milk, eggs, and bread",
      "user_id": 2,
      "created_at": "2026-07-23T03:41:11.000000Z",
      "updated_at": "2026-07-23T03:41:11.000000Z"
    }
  ],
  "page": 1,
  "limit": 10,
  "total": 1
}
```

### Update Todo

```
PUT /api/todos/{id}
```
*Requires authentication and ownership*

**Body**
```json
{
  "title": "Buy groceries",
  "description": "Buy milk, eggs, bread, and cheese"
}
```

**Response — 200**
```json
{
  "id": 1,
  "title": "Buy groceries",
  "description": "Buy milk, eggs, bread, and cheese"
}
```

**Response — 403 (not the owner)**
```json
{
  "message": "Forbidden"
}
```

**Response — 404 (todo not found)**
```json
{
  "message": "Todo not found"
}
```

### Delete Todo

```
DELETE /api/todos/{id}
```
*Requires authentication and ownership*

**Response — 204** — No content

**Response — 403 / 404** — same as update

## Authorization Rules

- Every todo endpoint (except register/login) requires a valid Sanctum token in the `Authorization` header.
- Users can only view, update, or delete todos they created — enforced via a `user_id` ownership check on every mutating request.
- Unauthenticated requests to protected routes return `401 Unauthenticated`.
## Database Schema

**users**
| Column | Type |
|---|---|
| id | bigint, primary key |
| name | string |
| email | string, unique |
| password | string (hashed) |
| timestamps | created_at, updated_at |

**todos**
| Column | Type |
|---|---|
| id | bigint, primary key |
| user_id | bigint, foreign key → users.id |
| title | string |
| description | text |
| timestamps | created_at, updated_at |

## Author

Kelvin Justine


