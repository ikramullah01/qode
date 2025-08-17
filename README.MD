# Blog Application

This is a full-featured blog application built with **Laravel** (backend) and **Vue.js** (frontend). It supports blog posting, scheduling, commenting, Redis caching, and email notifications using queues. The application is fully **Dockerized** for easy setup and deployment.

---

## Table of Contents


1. [Features](#features)  
2. [Prerequisites](#prerequisites)  
3. [Setup Instructions](#setup-instructions)  
4. [User Authentication](#4-user-authentication)  
7. [Frontend Features](#5-frontend)  
6. [Redis Caching](#6-caching-with-redis)  
5. [Queue & Email Notifications](#7-queues--email-notifications)  
8. [Docker Services](#8-dockerization)

---

## Features

### 1. Blog Post Creation and Management
- Create, read, update, and delete blog posts.
- Blog post fields:
  - Title
  - Excerpt
  - Description
  - Image upload
  - Keywords (tags)
  - SEO metadata (meta title, meta description)
- Schedule blog publishing at a specific date and time.
- Email notifications to authors when a blog is published (handled asynchronously via queues).

### 2. Search Functionality
- Full-text search by title, keywords, or description.
- Uses **Redis cache** for fast search results.
- Cache updates automatically when a post is created, updated, or deleted.

### 3. Commenting System
- Users can comment on blog posts (login required).
- Markdown editor for comments.
- Each comment is associated with a registered user.
- Unauthenticated users see a login prompt instead of the comment box.

### 4. User Authentication
- JWT / Bearer token based authentication.
- Passwordless login using OTP (one-time password) via email.
- Supports registration, login, and logout.

### 5. Frontend
- Built with **Vue.js**.
- Infinite scroll for loading blogs on the homepage.
- Smooth transitions when loading new posts.
- Blog list page shows title, excerpt, image, comment count, and publish date.
- Blog detail page displays all content and the comment section at the bottom.

### 6. Caching with Redis
- Blog list and individual blog data are cached for faster load times.
- Cache invalidation occurs automatically when data changes.
- Expiry policies set to keep frequently accessed data in memory.

### 7. Queues & Email Notifications
- Background queue using **Laravel Queue** with Redis driver.
- Email notifications for published blogs.
- Email queue ensures non-blocking blog publishing.

### 8. Dockerization
- The application is fully Dockerized:
  - Laravel backend
  - Vue.js frontend
  - Redis service
  - Queue worker
- Docker Compose makes it easy to run the full environment with a single command.

---

## Prerequisites

- Docker and Docker Compose installed on your system.  
- Optional: Node.js and npm (for local frontend build).  

**Port Availability:** The following ports must be available on your system, otherwise `docker compose up` will fail:

- `8000` → Laravel app  
- `5173` → Vite frontend  
- `3306` → MySQL database  
- `6379` → Redis server  

If any of these ports are already in use, either stop the conflicting service or update the port mappings in `docker-compose.yml` before starting the containers.


---

## Setup Instructions

Clone the repository:

```bash
git clone https://github.com/ikramullah01/qode
cd qode
```

Build and start Docker containers:
```bash
docker compose build
docker compose up -d
```

Verify running containers and enter the application container:
```bash
docker ps
docker exec -it app bash
```

Navigate to the Blogs project folder and run the following commands:
```bash
cd blog
cp .env.example .env
composer install
npm install
npm run build
```

Run database migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

Start the Laravel development server:
```bash
php artisan serve --port=8080 --host=0.0.0.0
```

Start the queue worker for email notifications:
```bash
php artisan queue:work --queue=emails
```

Now the application should be accessible at http://127.0.0.1:8000