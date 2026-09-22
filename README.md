# Finity Fish Store

Online fish store built with **Laravel 10**. Browse products, manage cart & wishlist, checkout with coupons and shipping zones, and pay via **bKash** or **Nagad**. Includes an admin panel for catalog, orders, reviews, and customers. UI supports **English** and **Bangla**.

**Repository:** [github.com/mansuraananmita-wq/finity](https://github.com/mansuraananmita-wq/finity.git)

---

## Features

- Product catalog with categories, detail pages, and ratings
- Cart, wishlist, and coupon discounts
- Checkout with shipping zone calculation
- Payments: bKash & Nagad (sandbox / live via `.env`)
- Customer auth (Laravel Fortify): register, login, email verify, password reset
- Order history for customers
- Admin panel: products, categories, coupons, shipping zones, orders, reviews, customers
- Bilingual UI (`en` / `bn`)
- Vite + Tailwind CSS + Alpine.js frontend

---

## Tech stack

| Layer | Stack |
|--------|--------|
| Backend | PHP 8.1+, Laravel 10, Fortify, Sanctum |
| Frontend | Blade, Vite, Tailwind CSS 3, Alpine.js |
| Database | MySQL 8+ / MariaDB (XAMPP compatible) |
| Payments | bKash Tokenized, Nagad |

---

## Requirements

- PHP 8.1 or higher (extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`)
- Composer
- Node.js 18+ and npm
- MySQL / MariaDB (e.g. XAMPP)

---

## Local setup

### 1. Clone & install

```bash
git clone https://github.com/mansuraananmita-wq/finity.git
cd finity
composer install
npm install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` for your database (XAMPP default shown):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finity
DB_USERNAME=root
DB_PASSWORD=
```

Create the database in MySQL / phpMyAdmin:

```sql
CREATE DATABASE finity CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Migrate & seed

```bash
php artisan migrate --seed
php artisan storage:link
```

### 4. Run the app

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite assets
npm run dev
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## Admin account

Production-style admin credentials come from `.env` when using `ProductionSeeder`:

```env
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_NAME=Admin
ADMIN_PASSWORD=change-me-on-deploy
ADMIN_PHONE=01700000000
```

```bash
php artisan db:seed --class=ProductionSeeder
```

Then open `/admin` while logged in as that user.

---

## Payments (optional for local)

Configure sandbox keys in `.env`:

```env
BKASH_MODE=sandbox
BKASH_APP_KEY=...
BKASH_APP_SECRET=...
BKASH_USERNAME=...
BKASH_PASSWORD=...

NAGAD_MODE=sandbox
NAGAD_MERCHANT_ID=...
NAGAD_MERCHANT_PRIVATE_KEY=...
NAGAD_PG_PUBLIC_KEY=...
```

Without real keys, browse/catalog/cart still work; live payment calls need merchant credentials.

---

## Useful commands

```bash
php artisan migrate --seed   # reset schema + sample data
npm run build                # production frontend assets
php artisan test             # PHPUnit
```

---

## Project structure (high level)

```
app/Http/Controllers/   # Storefront + Admin controllers
app/Models/             # Eloquent models
database/migrations/    # Schema
database/seeders/       # Categories, products, shipping, etc.
resources/views/        # Blade templates
resources/lang/         # en + bn strings
routes/web.php          # Public, auth, and admin routes
```

---

## License

This project is based on the [Laravel](https://laravel.com) framework (MIT). Application code in this repository is provided as-is for the Finity Fish Store project.
