# Email Parser API

This Laravel project provides a CLI command and a RESTful API to manage and parse raw email content. The system extracts clean plain text from email payloads and stores the results in a MySQL database.

## ✨ Features

- Parse raw email payloads to extract plain text.
- Scheduled email parsing command (runs every hour).
- RESTful API for creating, retrieving, updating, and soft-deleting email records.
- Secure API access via Laravel Sanctum token-based authentication.

---

## 🛠️ Tech Stack

- Laravel 10+
- MySQL
- Laravel Sanctum (API Authentication)
- [`zbateson/mail-mime-parser`](https://github.com/zbateson/mail-mime-parser) for MIME parsing

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- MySQL
- Laravel CLI
- SSH access to Inflektion’s server

### 1. Clone the Repository

```bash
git clone https://github.com/coder-artisan/email-parser-backend.git
cd email-parser-backend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Create Environment File

```bash
cp .env.example .env
```
Update your .env file with the correct database credentials:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=email
DB_USERNAME=root
DB_PASSWORD=12345678
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Seed Default User
```bash
php artisan db:seed
```
A default user will be created.
```bash
Email: test@example.com
Password: password
```

### 6 Run the API
```bash
php artisan serve
```

## 🧠 Email Parsing Command

The following Artisan command parses unprocessed emails (where raw_text is empty):
```bash
php artisan emails:parse
```
It is automatically scheduled to run hourly in app/Console/Kernel.php:
```bash
$schedule->command('emails:parse')->hourly();
```

## 📡 API Endpoints

All endpoints (except login) require authentication using a Bearer token via Laravel Sanctum.

| Method | Endpoint            | Description              |
|--------|---------------------|--------------------------|
| POST   | `/api/login`        | Login and get token      |
| POST   | `/api/logout`       | Logout current session   |
| GET    | `/api/emails`       | List all emails          |
| GET    | `/api/emails/{id}`  | Get single email by ID   |
| POST   | `/api/emails`       | Create & parse email     |
| PUT    | `/api/emails/{id}`  | Update an email          |
| DELETE | `/api/emails/{id}`  | Soft delete an email     |


## 🧪 Testing Locally
To test the CLI command:
```bash
php artisan emails:parse
```
To test API, you can use Postman or other options.

## 📂 Project Structure
- `app/Console/Commands/ParseEmailContent.php` – CLI parsing command
- `app/Http/Controllers/API` – Auth & Email controllers
- `app/Models/Email.php` – Eloquent model for email table
- `app/Services/EmailParserService.php` – Handles parsing/cleaning logic
- `routes/api.php` – API routes

## 🔐 Security Notes
- All endpoints are protected with API tokens via Laravel Sanctum.
- The `raw_text` field only stores clean, printable content (no HTML, scripts, or special chars).
- Soft deletes ensure no data loss.