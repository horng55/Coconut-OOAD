#Coconut Management System

A comprehensive school management system built with Laravel 11 and Vue.js (Inertia.js).

## Features

### Core Features
- **User Management**: Admin, Teacher, Student, and Parent roles with profile image upload
- **Role Management**: Create and manage custom roles with permissions for flexible access control
- **Enrollment**: Manage student enrollments in classes
- **Classes**: Create and manage classes with multiple teachers and subjects
- **Subjects**: Full CRUD functionality for subject management
- **Grading**: Record and manage student grades linked to assessments
- **Assessments**: Create and manage assessments (Quiz, Assignment, Mid-Term, Final Exam) for classes



- **Login Logs**: Security feature to track all user login attempts

### Portal Access
- **Admin Portal**: Full system access with comprehensive management tools and enhanced dashboard featuring:
  - Real-time statistics (teachers, students, parents, classes, enrollments, subjects, fees, attendance rate, average grades)
  - Recent enrollments and announcements tracking
  - Quick actions for common tasks
  - System status monitoring
- **Teacher Portal**: Enhanced portal with:
  - Dashboard overview of assigned classes, recent attendance, and grades
  - Class management with student lists
  - Assessment creation and management
  - Grade management with automatic validation
- **Student Portal**: Enhanced portal with:
  - Dashboard overview of enrolled classes and grades
  - Class viewing with teachers and subjects
  - Grade viewing linked to assessments
 


### Additional Features
- **Multi-language Support**: English and Khmer language switching
- **Dark Mode**: Toggle between light and dark themes
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: Live data updates with Inertia.js

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 with Inertia.js
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Containerization**: Docker & Docker Compose

## Requirements

### For Local Development (Recommended)
- PHP 8.3+
- Composer
- Node.js 20+ and npm
- Docker Desktop (for database and Redis only)

### For Full Docker Setup
- Docker Desktop (or Docker Engine + Docker Compose)
- Git

## Quick Start

### Local Development (IDE) - Recommended

This setup runs the application locally from your IDE while using Docker only for the database and Redis.

1. **Start Docker services (DB & Redis)**
   ```bash
   docker-compose up -d
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   
   **Quick setup (recommended):**
   ```bash
   ./setup-local.sh
   ```
   
   **Or manual setup:**
   ```bash
   cp .env.dev .env
   # Edit .env: Set DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306, etc.
   php artisan key:generate
   ```

4. **Run migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development servers**
   
   Terminal 1 (Frontend with hot reload):
   ```bash
   npm run dev
   ```
   
   Terminal 2 (Laravel server):
   ```bash
   php artisan serve
   ```

6. **Access the application**
   - Application: http://localhost:8000
   - Login: admin@schoolme.com / password123

See [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md) for detailed instructions.

### Full Docker Setup

### Option 1: Using the init script (Recommended)

```bash
# Clone the repository
git clone <repository-url>
cd school-management-system

# Run the initialization script
./docker/init.sh
```

### Option 2: Manual setup

1. **Create environment file**
   ```bash
   cp .env.example .env
   ```

2. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

3. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   ```

4. **Generate application key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

6. **Build frontend assets**
   ```bash
   docker-compose exec app npm run build
   # Or for development:
   docker-compose exec app npm run dev
   ```

7. **Access the application**
   - Application: http://localhost:8000
   - PHPMyAdmin: http://localhost:8080

## Using Makefile Commands

For easier Docker management, use the Makefile:

```bash
# Show all available commands
make help

# Initial setup
make setup

# Start containers
make up

# Stop containers
make down

# View logs
make logs

# Run migrations
make migrate

# Access shell
make shell

# Run artisan commands
make artisan cmd="route:list"
```

## Development

### Running in Development Mode

```bash
# Start containers
docker-compose up -d

# Run dev server with hot reload (in a separate terminal)
docker-compose exec app npm run dev

# Or use the development override
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

### Common Commands

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Run tests
docker-compose exec app php artisan test

# Clear cache
make clear
```

## Project Structure

```
school-management-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── Web/Admin/        # Admin controllers (Users, Classes, Subjects, Promotions, Reports, etc.)
│   │   ├── Web/Teacher/      # Teacher controllers (Dashboard, Grades, Attendance, Assessments)
│   │   ├── Web/Student/      # Student controllers (Dashboard, View-only)
│   │   ├── Web/Parent/       # Parent controllers (Dashboard, View-only)
│   │   └── Web/Auth/         # Authentication controllers
│   ├── Models/               # Eloquent models (User, Teacher, Student, Class, Promotion, Report, etc.)
│   ├── Plugin/               # Plugin system (AdminSidebar, etc.)
│   └── Support/              # Support classes (FlashMessage, etc.)
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   ├── js/
│   │   ├── Pages/            # Vue.js pages (Admin, Teacher, Student, Parent)
│   │   ├── Components/       # Vue.js components (Header, Sidebar, etc.)
│   │   ├── Layouts/          # Layout components (App, AdminLayout, etc.)
│   │   ├── Helper/           # JavaScript helper functions (ReportHelper, DateHelper, etc.)
│   │   └── Stores/           # Pinia stores for state management
│   └── lang/                 # Language files (en, km)
├── storage/
│   └── app/public/           # Public storage (profile images)
├── docker/                   # Docker configuration files
├── docker-compose.yml        # Docker Compose configuration
└── Dockerfile                # PHP application Dockerfile
```

## Database Schema

The system includes the following main entities:

- **Users**: Base user accounts (admin, teacher, student, parent) with first_name, last_name, email, phone_number, profile_image
- **Roles**: Custom roles with name, slug, description, and status for flexible access control
- **Role_User**: Pivot table for many-to-many relationship between roles and users
- **Teachers**: Teacher-specific information (employee_id, specialization)
- **Students**: Student-specific information (student_id, date_of_birth, address)
- **Parents**: Parent/guardian information (phone_number, address)
- **Classes**: Class/course information with multiple teachers and subjects support
- **Class_Teacher**: Pivot table for many-to-many relationship between classes and teachers
- **Class_Subject**: Pivot table for many-to-many relationship between classes and subjects
- **Subjects**: Subject information (name, code, description, status)
- **Enrollments**: Student-class relationships
- **Attendances**: Student attendance records
- **Assessments**: Assessment information (type, name, score, max_score, date) linked to classes
- **Grades**: Student grade records linked to assessments
- **Promotions**: Student promotion records tracking class transitions and academic year changes
- **Reports**: Generated report records with parameters and status tracking
- **ID Cards**: ID card information for students and teachers
- **Fees**: Fee information (name, type, amount, class, due date, recurring settings, status)
- **Fee_Payments**: Payment records with amount, payment method, transaction details, receipt numbers, and status tracking
- **Announcements**: School announcements with JSON-based target audience (all, teachers, students, parents)
- **Messages**: User-to-user messages
- **Login_Logs**: Security logs for tracking user login attempts

## User Roles

### Admin
- **Full system access** with comprehensive management tools
- **Enhanced Dashboard**: 
  - Real-time statistics cards (teachers, students, classes, subjects, average grades)
  - Recent enrollments and announcements tracking
  - Quick actions for common tasks
  - System status monitoring
- **User Management**: Create, edit, and manage all user types (admin, teacher, student, parent)
- **Role Management**: Create and manage custom roles, assign roles to users
- **Class Management**: Create classes with multiple teachers and subjects
- **Subject Management**: Full CRUD operations for subjects
- **Grade Management**: View and manage all grades, create/edit grades
- **Assessment Management**: View all assessments created by teachers
- **Security**: View login logs for all users

### Teacher
- **Enhanced Dashboard**: Overview of assigned classes, recent attendance, and grades
- **Class Management**: View assigned classes with student lists and multiple subjects
- **Student Management**: View and manage students in assigned classes
- **Assessment Management**: Create, edit, and delete assessments (Quiz, Assignment, Mid-Term, Final Exam) for assigned classes
- **Grade Management**: Create and edit grades linked to assessments, with automatic validation against assessment maximum scores


### Student
- **Enhanced Dashboard**: Overview of enrolled classes, recent attendance, and grades
- **Classes**: View enrolled classes with teachers and subjects
- **Grades**: View own grades linked to assessments with performance metrics




## Environment Variables

Key environment variables (see `.env.example` for full list):

- `APP_ENV`: Application environment (local, production)
- `APP_DEBUG`: Debug mode (true/false)
- `DB_HOST`: Database host (use `db` for Docker)
- `DB_DATABASE`: Database name
- `DB_USERNAME`: Database username
- `DB_PASSWORD`: Database password
- `REDIS_HOST`: Redis host (use `redis` for Docker)

## Key Features Details

### Assessment System
- Teachers can create assessments (Quiz, Assignment, Mid-Term, Final Exam) for their assigned classes
- Assessments include: type, name, maximum score, and assessment date
- Grades are linked to assessments, ensuring consistency and validation
- Student scores are automatically validated against assessment maximum scores
- Admin can view all assessments across the system


### Role Management
- Create custom roles with descriptive names and slugs
- Assign roles to users for flexible access control
- Role status management (active/inactive)
- Soft delete support for role management

### Multi-Teacher & Multi-Subject Classes
- Classes can have multiple teachers assigned
- Classes can have multiple subjects
- Flexible class management for team teaching scenarios


### Security Features
- Login logging for all user types
- Deduplication logic to prevent duplicate log entries
- Track successful and failed login attempts with IP addresses and timestamps


## Documentation

- [Docker Setup Guide](DOCKER.md) - Detailed Docker setup and usage
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)

## Troubleshooting

### Permission Issues
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Issues
- Ensure database container is running: `docker-compose ps`
- Wait a few seconds after starting for MySQL to initialize
- Check `.env` file for correct database credentials

### Port Conflicts
- Change ports in `docker-compose.yml` or `.env`
- Stop other services using the same ports

### Clear Everything
```bash
docker-compose down -v
docker-compose up -d --build
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

[Specify your license here]

## Support

For issues and questions, please open an issue on GitHub.

