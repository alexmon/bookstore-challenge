# Bookstore API

A simple REST API for managing a bookstore — authors, books, and loans.

## Setup

### Requirements

- Docker and Docker Compose

### Getting Started

1. Clone the repository
2. Copy the environment file: `cp .env.example .env`
3. Start the containers: `docker compose up -d --build`
4. Install dependencies: `docker exec -it bookstore_api composer install`
5. Generate app key: `docker exec -it bookstore_api php artisan key:generate`
6. Run migrations: `docker exec -it bookstore_api php artisan migrate`
7. Seed the database: `docker exec -it bookstore_api php artisan db:seed`
8. The API is available at `http://localhost:8080`

### Running Tests

```bash
docker exec -it bookstore_test ./vendor/bin/phpunit --testdox
```

---

## API Documentation

### Authors

#### Create Author

```
POST /api/authors
```

Request body:

```json
{
    "name": "George Orwell"
}
```

Response `201 Created`:

```json
{
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "George Orwell"
}
```

#### List Authors

```
GET /api/authors
```

Response `200 OK`:

```json
{
    "data": [
        {
            "uuid": "550e8400-e29b-41d4-a716-446655440000",
            "name": "George Orwell"
        }
    ]
}
```

---

### Books

#### Create Book

```
POST /api/books
```

Request body:

```json
{
    "title": "1984",
    "isbn": "978-0451524935",
    "author_uuid": "550e8400-e29b-41d4-a716-446655440000"
}
```

Response `201 Created`:

```json
{
    "uuid": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
    "title": "1984",
    "isbn": "978-0451524935",
    "author": {
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "name": "George Orwell"
    }
}
```

#### List Books

```
GET /api/books
```

Query parameters:

| Parameter | Type   | Description                      |
|-----------|--------|----------------------------------|
| `search`  | string | Filter by title (partial match)  |
| `author`  | string | Filter by author UUID            |

Response `200 OK`:

```json
{
    "data": [
        {
            "uuid": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
            "title": "1984",
            "isbn": "978-0451524935",
            "author": {
                "uuid": "550e8400-e29b-41d4-a716-446655440000",
                "name": "George Orwell"
            }
        }
    ]
}
```

#### Get Book

```
GET /api/books/{uuid}
```

Response `200 OK`:

```json
{
    "uuid": "7c9e6679-7425-40de-944b-e07fc1f90ae7",
    "title": "1984",
    "isbn": "978-0451524935",
    "author": {
        "uuid": "550e8400-e29b-41d4-a716-446655440000",
        "name": "George Orwell"
    }
}
```

---

## Your Task

### 1. Refactor

The current code works but could be better structured. Improve the codebase — reorganize, extract, clean up — whatever you think makes it more maintainable.

The API endpoints should remain the same. We're interested in the choices you make and why.

### 2. Add Feature: Book Loans

Add the ability to borrow and return books:

- `POST /api/books/{uuid}/borrow` — Borrow a book (requires a `borrower` name)
- `POST /api/books/{uuid}/return` — Return a book

Rules:

- A book that is already borrowed cannot be borrowed again
- A book that is not borrowed cannot be returned
- The `GET /api/books/{uuid}` endpoint should show whether a book is available or borrowed, and who has it
- The `GET /api/books` endpoint should support an `available` filter (e.g., `?available=true`)
- Invalid operations should return appropriate HTTP status codes and clear error messages

How you model and structure this is up to you.

### 3. Tests

Write tests you think are meaningful for your changes. There is no minimum number — we want to see what you choose to test.

### Submission

Fork this repository, make your changes, and send us the link.

We care more about clean, thoughtful code than completeness. If you run out of time, a short note explaining what you'd do next is perfectly fine. If you have any questions, feel free to reach out.
