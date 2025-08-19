# Mini PMS API

A mini Project Management System API built with Laravel 11 and Laravel Sanctum for authentication.

## Features Implemented

- **User Authentication System**
  - User registration with role assignment
  - Secure login/logout with Laravel Sanctum tokens
  - User profile retrieval
  - Single session token management (previous tokens revoked on login)

- **API Architecture**
  - RESTful API design with versioning (v1)
  - Service layer pattern for business logic separation
  - Consistent JSON response format across all endpoints
  - Custom exception handling for API errors
  - Request validation using Form Request classes

- **Security Features**
  - Laravel Sanctum token-based authentication
  - Password hashing with Laravel's Hash facade
  - Input validation and sanitization
  - CORS support for cross-origin requests
  - Rate limiting protection

- **Documentation & Development**
  - Swagger/OpenAPI 3.0 documentation
  - Interactive API documentation UI
  - Comprehensive README with setup instructions
  - Clean code structure following Laravel best practices

## Tech Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum
- **Database**: MySQL/SQLite
- **API Documentation**: L5-Swagger (Swagger/OpenAPI 3.0)
- **Architecture**: Service Layer Pattern

## Project Setup Instructions

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL/SQLite database
- Git

### Installation Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd mini-pms-app
```

2. **Install PHP dependencies**
```bash
composer install
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
- Authentication examples
- Real-time API exploration

### Quick Reference
- **Base URL**: `http://127.0.0.1:8000/api/v1`
- **Authentication**: Bearer Token (Laravel Sanctum)
- **Available Endpoints**:
  - `POST /register` - User registration
  - `POST /login` - User authentication
  - `GET /me` - Get current user profile (Protected)
  - `POST /logout` - User logout (Protected)

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

### Directory Structure
```
app/Http/Controllers/Api/
└── V1/
    ├── BaseController.php
    └── AuthController.php
```

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

## Testing

Run the test suite:
```bash
php artisan test
```

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

### Testing Endpoints

```bash
# Register a new user
curl -X POST http://127.0.0.1:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123","role":"Developer"}'

# Login user
curl -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'

# Get user profile (Auth required)
curl -H "Authorization: Bearer {your-token}" http://127.0.0.1:8000/api/v1/me

# Logout user (Auth required)
curl -X POST http://127.0.0.1:8000/api/v1/logout \
  -H "Authorization: Bearer {your-token}"
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
