# Mini PMS

A lightweight Project Management System with role-based access control, built with Laravel 11.

## ✨ Features

- **Role-based Access Control** (Admin, Manager, Developer)
- **Projects & Tasks Management** with real-time updates
- **Responsive Dashboard** with statistics and insights
- **RESTful API** with OpenAPI documentation
- **Task Assignment & Tracking** with status updates
- **User Management** for administrators
- **Responsive Design** works on all devices

## 🛠 Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Blade, Tailwind CSS, Alpine.js
- **Database**: MySQL/SQLite
- **Auth**: Laravel Sanctum (API) + Session (Web)
- **Tools**: Laravel Sail, PHPUnit, PHPStan

## 📚 Quick Start

### Web Access
- **URL**: `http://localhost:8000`
- **Demo Users**:
  - Admin: `admin@example.com` / `password`
  - Manager: `manager@example.com` / `password`
  - Developer: `dev@example.com` / `password`

### API Access
- **Docs**: `http://localhost:8000/api/documentation`
- **Auth**: Use `/api/login` to get a token

## 🚀 Installation

### Prerequisites
- PHP 8.2+, Composer, Node.js 16+
- MySQL 5.7+ or SQLite
- (Optional) Docker

### Quick Start with Docker
```bash
git clone https://github.com/yourusername/mini-pms-app.git
cd mini-pms-app
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install && npm run dev
```

### Manual Installation
```bash
git clone https://github.com/yourusername/mini-pms-app.git
cd mini-pms-app
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Visit `http://localhost:8000` to get started.

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

**📋 [Interactive API Documentation](http://127.0.0.1:8000/api/documentation)**

Explore the interactive documentation for complete API details, request/response schemas, and testing.
