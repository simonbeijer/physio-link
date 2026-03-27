# PhysioLink - Backend (Laravel)

This is the Laravel-based REST API for PhysioLink. It handles authentication, exercise management, and workout schema generation.

## Tech Stack
- **Framework:** Laravel 11
- **Authentication:** Laravel Sanctum (Token-based)
- **Database:** MySQL
- **Tooling:** Laravel Breeze (API backend only)

## Key API Endpoints

### Authentication (Public)
- `POST /api/register` - Register a new professional
- `POST /api/login` - Login and receive a Bearer token

### Exercise Library (Auth Required)
- `GET /api/exercises` - List all global exercises
- `POST /api/exercises` - Create a new exercise (YouTube URL, start/end times, timer duration)
- `PATCH /api/exercises/{id}` - Update an exercise
- `DELETE /api/exercises/{id}` - Delete an exercise

### Schemas (Auth Required)
- `GET /api/schemas` - List user's schemas
- `POST /api/schemas` - Create a new schema (generates a `share_token`)
- `DELETE /api/schemas/{id}` - Delete a schema
- `GET /api/schemas/{id}/exercises` - List exercises in a specific schema
- `POST /api/schemas/{id}/exercises` - Add/Link an exercise to a schema

### Client View (Public)
- `GET /api/share/{share_token}` - Retrieve schema details and exercises for a client

## Development
This service runs within a Docker container.
- **URL:** `http://localhost:8080`
- **Commands:** Use `docker-compose exec app php artisan ...` for any Laravel commands.
