# Local Development Setup (IDE)

This guide explains how to run the School Management System locally from your IDE while using Docker only for the database and Redis.

## Prerequisites

- PHP 8.3+ installed locally
- Composer installed
- Node.js 20+ and npm installed
- Docker Desktop (for database and Redis only)

## Setup Steps

### 1. Start Docker Services (Database & Redis)

```bash
# Start only database and Redis containers
docker-compose up -d

# Verify they're running
docker-compose ps
```

You should see:
- `school_management_db` (MySQL)
- `school_management_redis` (Redis)

### 2. Configure Environment

**Option 1: Using the setup script (Recommended)**
```bash
./setup-local.sh
```
This script will:
- Copy `.env.local` to `.env`
- Configure database settings for MySQL (127.0.0.1:3306)
- Configure Redis settings (127.0.0.1:6379)
- Install dependencies
- Run migrations and seeders

**Option 2: Manual setup**
```bash
# Copy the development environment file
cp .env.local .env

# Update database settings manually
# Edit .env and set:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_management
# DB_USERNAME=root
# DB_PASSWORD=root
# REDIS_HOST=127.0.0.1

# Generate application key
php artisan key:generate
```

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 4. Run Database Migrations

```bash
# Run migrations
php artisan migrate

# Seed the database (creates admin user)
php artisan db:seed
```

### 5. Build Frontend Assets

```bash
# For development with hot reload
npm run dev

# Or for production build
npm run build
```

### 6. Start Laravel Development Server

In a separate terminal:

```bash
# Start Laravel development server
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Development Workflow

### Terminal 1: Frontend Development (Hot Reload)
```bash
npm run dev
```

### Terminal 2: Laravel Server
```bash
php artisan serve
```

### Terminal 3: Queue Worker (if using queues)
```bash
php artisan queue:work
```

## Environment Configuration

Make sure your `.env` file has:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_management
DB_USERNAME=root
DB_PASSWORD=root

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## Common Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Run tests
php artisan test

# Access Tinker
php artisan tinker
```

## Troubleshooting

### Database Connection Issues
- Ensure Docker containers are running: `docker-compose ps`
- Check if port 3306 is available: `lsof -i :3306`
- Verify database credentials in `.env`

### Redis Connection Issues
- Ensure Redis container is running: `docker-compose ps redis`
- Check if port 6379 is available: `lsof -i :6379`

### Port Already in Use
If port 8000 is in use, use a different port:
```bash
php artisan serve --port=8001
```

## Stopping Services

```bash
# Stop Docker containers
docker-compose down

# Or stop and remove volumes (WARNING: deletes data)
docker-compose down -v
```

## System Features

The School Management System includes the following features:

### Core Management
- **User Management**: Admin, Teacher, Student, and Parent roles
- **Class Management**: Create and manage classes with multiple teachers and subjects
- **Subject Management**: Full CRUD operations for subjects
- **Enrollment Management**: Manage student enrollments in classes
- **Attendance Management**: Track student attendance (admin and teacher can manage)
- **Grade Management**: Record and manage student grades linked to assessments
- **Assessment Management**: Create and manage assessments (Quiz, Assignment, Mid-Term, Final Exam)

### Advanced Features
- **Timetable Management**: Create and manage class schedules with time conflict detection
- **Promotion Management**: Promote students between classes (individual and bulk promotions)
- **Report Management**: Generate various reports including:
  - Student Performance Reports
  - Class Performance Reports
  - Attendance Reports
  - Grade Distribution Reports
  - Teacher Workload Reports
  - Enrollment Reports
- **ID Card Management**: Generate and manage ID cards for students and teachers
- **Announcements**: School-wide and targeted announcements
- **Messaging**: Communication between users
- **Login Logs**: Security feature to track all user login attempts

### Portal Access
- **Admin Portal**: Full system access with comprehensive management tools
- **Teacher Portal**: Manage assigned classes, create assessments, record attendance and grades
- **Student Portal**: View classes, attendance, grades, and announcements
- **Parent Portal**: Read-only access to view children's information

### Additional Features
- **Multi-language Support**: English and Khmer language switching
- **Dark Mode**: Toggle between light and dark themes
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: Live data updates with Inertia.js

## Database Tables

The system includes the following main database tables:
- `users` - Base user accounts
- `teachers` - Teacher-specific information
- `students` - Student-specific information
- `parents` - Parent/guardian information
- `classes` - Class/course information
- `subjects` - Subject information
- `enrollments` - Student-class relationships
- `attendances` - Student attendance records
- `assessments` - Assessment information
- `grades` - Student grade records
- `timetables` - Class schedule/timetable entries
- `promotions` - Student promotion records
- `reports` - Generated report records
- `id_cards` - ID card information
- `announcements` - School announcements
- `messages` - User-to-user messages
- `login_logs` - Security logs

## Benefits of This Setup

- ✅ Faster development (no Docker overhead for PHP)
- ✅ Better IDE integration and debugging
- ✅ Direct access to PHP extensions and tools
- ✅ Easier to use Xdebug
- ✅ Still using Docker for database consistency

