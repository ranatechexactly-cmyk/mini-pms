# Mini PMS (Project Management System)

A full-featured Project Management System built with Laravel 11, featuring both web interface and RESTful API with Laravel Sanctum authentication.

## 🌟 Features

### 👥 User Authentication & Authorization
- Multi-role system (Admin, Manager, Developer)
- Secure authentication with session-based web interface
- RESTful API authentication with Laravel Sanctum
- Role-based access control (RBAC) with policies
- User management interface for administrators

### 📊 Project Management
- Intuitive dashboard with project statistics
- Create, view, update, and delete projects
- Assign/remove developers to/from projects
- Track project status and progress
- View project tasks and team members
- Responsive design for all devices

### ✅ Task Management
- Interactive task board interface
- Create, view, update, and delete tasks
- Assign tasks to developers
- Update task status (pending, in_progress, completed)
- Set task priority and due dates
- Task filtering and search functionality

### 🔄 API Features (v1)
- RESTful API design with versioning
- Service layer pattern for clean architecture
- Consistent JSON:API response format
- Comprehensive request validation
- Detailed error handling with proper HTTP status codes
- Rate limiting and security headers
- OpenAPI (Swagger) documentation

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum (API) + Session (Web)
- **Database**: MySQL/SQLite
- **Frontend**: Blade Templates + Tailwind CSS
- **JavaScript**: Alpine.js for interactivity
- **Testing**: PHPUnit, Laravel Dusk
- **API Documentation**: L5-Swagger (OpenAPI 3.0)
- **PHP Version**: 8.2+
- **Package Manager**: Composer

### Development Tools
- Laravel Sail for Docker development
- Laravel Mix for asset compilation
- PHP_CodeSniffer for code style
- PHPStan for static analysis

## 📚 Documentation

### Web Interface
Access the web interface at:
```
http://localhost:8000
```

### API Documentation
Interactive API documentation is available at:
```
http://localhost:8000/api/documentation
```

#### API Authentication
1. Register a new user or use existing credentials
2. Log in via `/api/login` to get an authentication token
3. Include the token in the `Authorization` header: `Bearer YOUR_AUTH_TOKEN`

### Role-Based Access
- **Admin**: Full access to all features and user management
- **Manager**: Can manage projects and tasks, view reports
- **Developer**: Can view assigned projects and update task status

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer 2.0+
- Node.js 16+ and NPM
- MySQL 5.7+ or SQLite
- Git
- (Optional) Docker and Docker Compose

### 🛠️ Installation

#### Using Docker (Recommended)
```bash
git clone https://github.com/yourusername/mini-pms-app.git
cd mini-pms-app
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

#### Manual Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/mini-pms-app.git
   cd mini-pms-app
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install NPM dependencies:
   ```bash
   npm install
   npm run dev
   ```

4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mini_pms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
   This will create the necessary tables and seed the database with sample users:
   - Admin: admin@example.com / password
   - Manager: manager@example.com / password
   - Developer: developer@example.com / password

8. Start the development server:
   ```bash
   php artisan serve
   ```
   Access the application at: http://localhost:8000

7. Generate Swagger documentation:
   ```bash
   php artisan l5-swagger:generate
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. Access the application at `http://localhost:8000`

## API Usage

### Authentication
All endpoints except `/api/v1/register` and `/api/v1/login` require authentication.

#### Register a new user:
```http
POST /api/v1/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "developer"
}
```

#### Login:
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

### Available Roles
- `admin`: Full access to all features
- `manager`: Can manage projects and tasks
- `developer`: Can view and update assigned tasks

## Development

### Testing
Run the test suite:
```bash
php artisan test
```

### Generating API Documentation
After making changes to API endpoints, regenerate the documentation:
```bash
php artisan l5-swagger:generate
```

## Contributing
1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License
Distributed under the MIT License. See `LICENSE` for more information.

## Contact
Your Name - [@your_twitter](https://twitter.com/your_twitter) - email@example.com

Project Link: [https://github.com/yourusername/mini-pms-app](https://github.com/yourusername/mini-pms-app)
```

3. **Environment configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

4. **Database configuration**
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_pms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run database migrations**
```bash
php artisan migrate
```

6. **Generate Swagger documentation**
```bash
php artisan l5-swagger:generate
```

7. **Start the development server**
```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`

### Verification
- API Base URL: `http://127.0.0.1:8000/api/v1`
- Swagger Documentation: `http://127.0.0.1:8000/api/documentation`

## API Documentation

### Swagger UI
Access the interactive API documentation at:
```
http://127.0.0.1:8000/api/documentation
```

### Base URL
```
http://127.0.0.1:8000/api/v1
```

### Authentication
All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## API Endpoints

For complete API endpoint documentation with request/response examples, interactive testing, and detailed specifications, visit our Swagger documentation:

**📋 [Interactive API Documentation](http://127.0.0.1:8000/api/documentation)**

The Swagger UI provides:
- Complete endpoint documentation
- Request/response schemas
- Interactive API testing

## Response Format

All API responses follow a consistent format:

**Success Response:**
```json
{
  "status": "success",
  "message": "Operation successful",
  "data": { /* response data */ }
}
```

**Error Response:**
```json
{
  "status": "error",
  "message": "Error description",
  "data": { /* error details or null */ }
}
```

## User Roles

Available user roles:
- `Developer` (default)
- `Admin`
- `Manager`
- `User`

## Security Features

- **Password Hashing**: Secure password storage using Laravel's Hash facade
- **Token Management**: Single session tokens (previous tokens revoked on new login)
- **Request Validation**: Comprehensive input validation using Form Requests
- **CORS Support**: Configurable cross-origin resource sharing
- **Rate Limiting**: Built-in API rate limiting

## API Versioning

This application supports API versioning with a clean, scalable structure.

### Current Version (v1)
All endpoints are currently under version 1:
- **Base URL**: `/api/v1/`
- All authentication endpoints use the v1 prefix

### Adding New Versions
When adding future API versions:

1. Create new version directory: `app/Http/Controllers/Api/V2/`
2. Create `BaseController.php` extending main Controller
3. Add version-specific routes in `routes/api.php`
4. Update default routes to point to latest version

## Architecture

### Service Layer Pattern
- **Controllers**: Handle HTTP requests/responses only
- **Services**: Contain all business logic
- **Models**: Data access layer
- **Requests**: Input validation

### Key Services
- `AuthService`: Authentication business logic
- `UserService`: User management operations

## Development

### Generate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
