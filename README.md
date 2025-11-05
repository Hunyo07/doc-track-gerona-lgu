# Document Tracking System (DTS)

A comprehensive document tracking system for the Bids and Awards Committee (BAC) at Gerona Tarlac Municipal Office. Built with Laravel 12, Vue 3, and Tailwind CSS.

## Features

-   **Document Management**: Create, edit, delete, and track documents
-   **Role-Based Access Control**: Admin, BAC Member, and User roles with specific permissions
-   **Document Tracking**: Complete audit trail of document status changes
-   **File Attachments**: Upload and manage document attachments
-   **Comments System**: Internal and external comments on documents
-   **Dashboard Analytics**: Overview of document statistics and recent activity
-   **User Management**: Manage users, roles, and permissions
-   **API-First Architecture**: RESTful API with Laravel Sanctum authentication

## Tech Stack

### Backend

-   **Laravel 12**: PHP framework
-   **Laravel Sanctum**: API authentication
-   **Spatie Permission**: Role and permission management
-   **MySQL**: Database
-   **Laravel Migrations**: Database schema management

### Frontend

-   **Vue 3**: Progressive JavaScript framework
-   **Pinia**: State management
-   **Vue Router**: Client-side routing
-   **Tailwind CSS**: Utility-first CSS framework
-   **Vite**: Build tool

## Installation

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js 18+ and npm
-   MySQL 8.0 or higher

### Backend Setup

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd document-tracking-system
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database configuration**
   Update your `.env` file with database credentials:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=document_trackingphp _system
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

5. **Run migrations and seeders**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

6. **Create storage link**
    ```bash
    php artisan storage:link
    ```

### Frontend Setup

1. **Install Node.js dependencies**

    ```bash
    npm install
    ```

2. **Build assets**

    ```bash
    npm run build
    ```

3. **Development mode**
    ```bash
    npm run dev
    ```

## Running the Application

### Development Mode

1. **Start Laravel server**

    ```bash
    php artisan serve
    ```

2. **Start Vite dev server** (in another terminal)

    ```bash
    npm run dev
    ```

3. **Access the application**
    - Frontend: http://localhost:5173
    - API: http://localhost:8000/api

### Production Mode

1. **Build assets**

    ```bash
    npm run build
    ```

2. **Optimize Laravel**
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## Default Users

After running the seeders, you'll have these default users:

-   **Administrator**

    -   Email: admin@gerona.gov.ph
    -   Password: password
    -   Role: Admin (full access)

-   **BAC Member**
    -   Email: bac@gerona.gov.ph
    -   Password: password
    -   Role: BAC Member (document management and approval)

## API Documentation

### Authentication Endpoints

-   `POST /api/auth/register` - Register new user
-   `POST /api/auth/login` - Login user
-   `POST /api/auth/logout` - Logout user
-   `GET /api/auth/me` - Get current user profile

### Document Endpoints

-   `GET /api/documents` - List documents (with filtering)
-   `POST /api/documents` - Create document
-   `GET /api/documents/{id}` - Get document details
-   `PUT /api/documents/{id}` - Update document
-   `DELETE /api/documents/{id}` - Delete document
-   `POST /api/documents/{id}/attachments` - Upload attachment
-   `POST /api/documents/{id}/comments` - Add comment

### User Management Endpoints

-   `GET /api/users` - List users
-   `GET /api/users/{id}` - Get user details
-   `PUT /api/users/{id}` - Update user
-   `POST /api/users/{id}/assign-role` - Assign role to user
-   `DELETE /api/users/{id}/remove-role` - Remove role from user

### Tracking Endpoints

-   `GET /api/tracking` - List tracking records
-   `POST /api/tracking` - Create tracking record
-   `GET /api/documents/{id}/history` - Get document history
-   `GET /api/dashboard/stats` - Get dashboard statistics

## Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter AuthTest

# Run with coverage
php artisan test --coverage
```

## Database Schema

### Core Tables

-   `users` - User accounts
-   `roles` - User roles
-   `permissions` - System permissions
-   `model_has_roles` - User-role relationships
-   `model_has_permissions` - User-permission relationships

### Document Tables

-   `documents` - Main document records
-   `document_tracking` - Document status changes
-   `document_attachments` - File attachments
-   `document_comments` - Comments and notes

## Permissions

### Admin Role

-   Full system access
-   User management
-   Role and permission management
-   All document operations

### BAC Member Role

-   Document creation and editing
-   Document approval/rejection
-   View tracking history
-   User viewing (limited)

### User Role

-   Document creation and editing
-   View assigned documents
-   Basic tracking access

## File Structure

```
app/
├── Http/Controllers/Api/     # API controllers
├── Models/                   # Eloquent models
├── Providers/               # Service providers

database/
├── migrations/              # Database migrations
├── seeders/                 # Database seeders
└── factories/              # Model factories

resources/
├── js/                     # Vue.js application
│   ├── components/         # Vue components
│   ├── pages/             # Vue pages
│   ├── stores/            # Pinia stores
│   └── app.js             # Main application file
└── views/                 # Blade templates

routes/
├── api.php                 # API routes
└── web.php                # Web routes

tests/
├── Feature/               # Feature tests
└── Unit/                  # Unit tests
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team or create an issue in the repository.
