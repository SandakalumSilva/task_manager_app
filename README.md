# Task Manager

A simple Laravel web application for managing tasks with drag-and-drop priority reordering and project filtering.

## Features

- Create, edit, and delete tasks
- Set task priority, name, and timestamps
- Drag-and-drop reordering — priority updates automatically (#1 = top)
- Filter tasks by project using a dropdown
- Tasks and projects stored in MySQL

## Requirements

- PHP >= 8.3
- Laravel >= 12
- MySQL >= 8.0
- Composer

---

## Installation

### 1. Install PHP Dependencies
```bash
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database

Open `.env` and update your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 4. Create the Database

Log into MySQL and run:

```sql
CREATE DATABASE task_manager;
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Run Seeders

```bash
php artisan db:seed
```

> Populates the database with sample projects and tasks.

To run a specific seeder:

```bash
php artisan db:seed 
```

### 7. Start the Development Server

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000) in your browser.

---


## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Blade templates, Bootstrap CSS, Jquery
- **Database:** MySQL 

