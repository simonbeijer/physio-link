# PhysioLink

**PhysioLink** is a lightweight tool for professionals to create and share workout programs with clients via a simple link. Clients can follow exercises with a built-in timer and looped YouTube videos—no account required.

## 🚀 Quick Start (Docker)

```bash
docker-compose up -d
```

- **Frontend:** [http://localhost:3000](http://localhost:3000)
- **Backend:** [http://localhost:8080](http://localhost:8080)

## 🛠 Tech Stack

- **Backend:** Laravel 12 (REST API)
- **Frontend:** Vue 3 + Tailwind CSS + Shadcn/Vue
- **Player:** Custom YouTube loop integration
- **Infra:** Docker (Nginx, PHP-FPM, MySQL)

## 📂 Structure

- `/src` - Laravel Backend
- `/vue` - Vue 3 Frontend
- `/docker` - Infrastructure config
