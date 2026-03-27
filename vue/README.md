# PhysioLink - Frontend (Vue 3)

The PhysioLink frontend is built with Vue 3, providing a dashboard for professionals to manage exercises and a streamlined, mobile-friendly view for clients.

## Tech Stack
- **Framework:** Vue 3 (Composition API)
- **Build Tool:** Vite
- **Styling:** Tailwind CSS + Shadcn/Vue
- **Routing:** Vue Router
- **Icons:** Lucide Vue Next

## Core Views
- **Dashboard:** Management of exercises and schemas.
- **Login/Register:** Authentication for professionals.
- **Client View (`/s/:share_token`):** Public view for clients to follow a workout with a YouTube video player and an integrated timer.

## Key Components
- **VideoPlayer.vue:** Custom YouTube player with sub-second precision looping based on exercise start/end times.
- **WorkoutTimer.vue:** A visual progress ring and timer for exercise duration.
- **SchemaExerciseSelector.vue:** Interface for therapists to build workouts from the exercise library.

## Development
This service runs within a Docker container.
- **URL:** `http://localhost:3000`
- **Commands:** Use `docker-compose exec vue npm ...` for package management or scripts.
