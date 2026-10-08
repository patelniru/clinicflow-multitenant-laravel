# ClinicFlow SaaS

A multi-tenant patient, consultant, appointment, document, and reporting platform built with Laravel.

> ClinicFlow is a personal portfolio project created with fictional data only. It is not affiliated with a healthcare provider, employer, client, or real patient records. Do not use this project with real personal health information.

## Features

### Multi-Company SaaS

- Shared-database multi-tenancy using `company_id`
- Company-level data isolation
- Super Admin platform access
- Company-specific Manager, Consultant, and Patient portals
- Paginated company, patient, appointment, and audit-log data

### Roles and Permissions

- **Super Admin**: Platform-level administration and cross-company access
- **Manager**: Full operational access within their own company
- **Consultant**: Access based on Manager-assigned permissions
- **Patient**: Access only to their own appointments and patient-visible documents
- Database-driven permissions by module and action
- Module-level permission assignment with Select All, Clear, accordion sections, Expand All, and Collapse All controls

### Patient Management

- Create, view, update, search, and manage fictional patient records
- Tenant-aware patient access
- Role-based patient authorization
- Patient portal account support

### Consultant Management

- Create and manage company consultants
- Activate or deactivate consultant profiles
- Assign consultant permissions by module
- Consultant-specific appointment access

### Appointment Management

- Create, view, and manage appointments
- Appointment statuses:
  - Scheduled
  - Confirmed
  - Checked In
  - Completed
  - Cancelled
  - No Show
- Consultant availability validation
- Double-booking prevention
- Company-level appointment isolation
- Patient appointment portal

### Documents

- Private patient document uploads
- Supported file types: PDF, JPG, JPEG, PNG, DOCX
- Maximum upload size: 5 MB
- Private Laravel storage
- Document visibility levels:
  - Manager only
  - Consultant and Manager
  - Patient visible
- Authorized document download and deletion
- Patient document portal

### Reports and Audit Logs

- Appointment reporting with date, patient, consultant, and status filters
- CSV export using streamed/chunked queries
- Company audit logs
- Platform audit logs for Super Admin
- Audit events for document upload, download, and deletion
- Paginated audit logs for better performance

## Technology Stack

- PHP 8.2+
- Laravel 12
- MySQL
- Laravel Breeze
- Blade
- Tailwind CSS
- Alpine.js
- PHPUnit
- Larastan / PHPStan
- Docker Compose
- Nginx
- GitHub Actions

## Architecture

ClinicFlow uses a practical Laravel architecture:

```text
Controllers
    → Form validation
    → Actions / Services
    → Repositories
    → Eloquent models
    → MySQL
```

### Key patterns

- MVC for web requests and Blade views
- Repository pattern for appointment data access
- Service layer for appointment availability and audit logging
- Action classes for appointment creation
- Policies for record-level authorization
- Middleware for roles, permissions, and company access
- Shared-database multi-tenancy using `company_id`
- Paginated queries and chunked CSV export for performance

## Roles

| Role | Access |
|---|---|
| Super Admin | All companies, platform dashboard, companies, platform audit logs |
| Manager | Full access within own company, consultant permission assignment |
| Consultant | Only permissions assigned by Manager plus assigned record access |
| Patient | Own portal, own appointments, patient-visible documents only |

## Project Structure

```text
app/
├── Actions/
├── Contracts/
├── Enums/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── App/
│   │   └── Patient/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Policies/
├── Repositories/
└── Services/

database/
├── factories/
├── migrations/
└── seeders/

resources/views/
├── admin/
├── app/
├── patient/
└── layouts/

tests/
├── Feature/
└── Unit/
```

## Local Setup

### Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL
- XAMPP, Laravel Herd, Docker Desktop, or another local PHP/MySQL environment

### Installation

```bash
git clone [https://github.com/patelniru/clinicflow-multitenant-laravel.git](https://github.com/patelniru/clinicflow-multitenant-laravel.git)
cd clinicflow-multitenant-laravel
composer install
cp .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinicflow
DB_USERNAME=root
DB_PASSWORD=
```

Run database migrations and seed fictional demo data:

```bash
php artisan migrate:fresh --seed
```

Install and build frontend assets:

```bash
npm install
npm run build
```

Start the application:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Demo Accounts

> These credentials are only for local development after running the database seeder.

| Role | Email | Password |
|---|---|---|
| Super Admin | `admin@clinicflow.test` | `password` |
| NorthStar Manager | `manager@northstar.test` | `password` |
| NorthStar Consultant | `consultant@northstar.test` | `password` |
| NorthStar Patient | `patient@northstar.test` | `password` |
| Maple Manager | `manager@maple.test` | `password` |

## Testing

Run the complete test suite:

```bash
php artisan test
```

Run static analysis:

```bash
./vendor/bin/phpstan analyse
```

On Windows PowerShell:

```powershell
.\vendor\bin\phpstan analyse
```

## Docker

Docker configuration is included.

Start containers:

```bash
docker compose up -d --build
```

Run migrations:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Run tests:

```bash
docker compose exec app php artisan test
```

If Docker Desktop is not available, the project can also run with XAMPP and MySQL using the local setup instructions above.

## Security Notes

- Use fictional data only.
- Keep `.env` private and never commit it.
- Documents use a private storage disk.
- Document downloads require authorization through Laravel routes.
- Company data is isolated using `company_id`, middleware, policies, and permission checks.
- Do not use demo passwords in a production deployment.
- Add HTTPS, email verification, rate limiting, queue workers, monitoring, backups, antivirus scanning, and privacy/compliance review before any real-world use.

## Portfolio Highlights

This project demonstrates:

- Laravel 12 application development
- PHP and MySQL
- Multi-tenant SaaS architecture
- Role-based and permission-based authorization
- REST-ready application design
- Appointment conflict validation
- Secure private file storage
- CSV export with streamed processing
- Automated testing
- Docker and local-development setup
- Git and GitHub workflow

## Author

**Niru Patel**

- LinkedIn: https://www.linkedin.com/in/niru-patel-5701738a
- GitHub: https://github.com/patelniru
- Location: Victoria, British Columbia, Canada