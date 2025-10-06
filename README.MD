# Mini Wallet Project

## Description

This is a Laravel application built with Laravel 12.x. The frontend uses Vue 3 and Inertia.js.

## Tech Stack

* **Backend:** Laravel 12.x
* **Frontend:** Vue 3 + Inertia.js
* **Database:** MySQL
* **CSS Framework:** TailwindCSS
* **Authentication:** Laravel Breeze
* **Dev Tools:** Vite, Composer, NPM

## Installation

1. Clone the repository:

```bash
git https://github.com/permalinkserbia/mini-wallet.git
cd mini-wallet
```

2. Install PHP dependencies:

```bash
composer install
```

3. Install JavaScript dependencies:

```bash
npm install
```

4. Copy `.env` file:

```bash
cp .env.example .env
```

5. Generate application key:

```bash
php artisan key:generate
```

6. Configure `.env` (database, email, API keys, etc.)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Run migrations and seed database (if any):

```bash
php artisan migrate --seed
```

8. Start the local server:

```bash
composer run dev
```

## Commands

* **Run tests:**

```bash
php artisan test
```

* **Build frontend for production:**

```bash
npm run build
```

## Dummy user accounts
* **DB seed creates two dummy users with following credentials:**

```bash
user_a@miniwallet.com / 123123123
user_b@miniwallet.com / 123123123
```

## Postman Collection
* **In root folder of project exists postman collection:**

```bash
Mini Wallet.postman_collection.json
```

## Project Structure

```
app/                # Laravel application (Models, Controllers, Services)
database/           # Migrations and seed files
resources/          # Vue 3 components and TailwindCSS files
routes/             # Web, API, and other routes
tests/              # PHPUnit tests
```

## Deployment

1. Install PHP, Composer, NPM, and MySQL on server.
2. Pull code from repository.
3. Run `composer install` and `npm install`.
4. Configure `.env` file.
5. Run migrations: `php artisan migrate --force`
6. Build frontend for production: `npm run build`
7. Restart server or PHP-FPM.

## Author

[Nenad Živanović](https://www.linkedin.com/in/nenad-zivanovic-permalink/)
