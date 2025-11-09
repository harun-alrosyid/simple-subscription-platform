# Simple Subscription Platform

This is a simple REST API built with **Laravel 9** and **MySQL**.  
Users can subscribe to a website, and when a new post is published, all subscribers receive an email containing the post title and description.

---

## Main Features

-   Simple REST API (no authentication)
-   Subscribe users to websites
-   Create new posts for a website
-   Command to send posts to subscribers
-   Queue-based background email sending
-   Prevent duplicate email deliveries

---

## Installation

### 1. Clone and install dependencies

```bash
git clone https://github.com/<username>/simple-subscription-platform.git
cd simple-subscription-platform
composer install
```

### 2. Environment setup

Copy the example file and generate a key:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your local database and queue settings:

```
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simple_subscription_platform
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="Subscription API"
```

> For local testing, use `MAIL_MAILER=log` — all sent emails will appear inside `storage/logs/laravel.log`.

### 3. Run migrations

```bash
php artisan migrate
```

### 4. (Optional) Seed demo websites

```bash
php artisan db:seed --class=WebsiteSeeder
```

---

## How to Run

### Start the server

```bash
php artisan serve
```

API runs at: `http://127.0.0.1:8000`

### Run the queue worker

```bash
php artisan queue:work --queue=emails --tries=3
```

---

## API Endpoints

### 1. Subscribe to a website

**POST** `/api/websites/{website}/subscribe`

**Body:**

```json
{
    "email": "john.due@example.com",
    "name": "John Due"
}
```

**Response:**

```json
{ "message": "Subscribed" }
```

---

### 2. Create a post

**POST** `/api/websites/{website}/posts`

**Body:**

```json
{
    "title": "The First Post in 2025",
    "description": "Decription The First Post in 2025"
}
```

**Response:**

```json
{
    "message": "Post created",
    "data": {
        "id": 1,
        "website_id": 1,
        "title": "The First Post in 2025",
        "description": "Decription The First Post in 2025"
    }
}
```

---

## Sending Emails to Subscribers

Run the command below to find all new posts and send them to each website’s subscribers:

```bash
php artisan posts:send-new
```

This command will queue jobs to send emails in the background.  
Each job will be processed by the queue worker.

---

## Quick Testing

1. Start the server:
    ```bash
    php artisan serve
    ```
2. Run the queue worker:
    ```bash
    php artisan queue:work
    ```
3. Subscribe a user:
    ```bash
    curl -X POST http://127.0.0.1:8000/api/websites/1/subscribe \
    -H "Content-Type: application/json" \
    -d '{"email":"john.due@example.com","name":"John Due"}'
    ```
4. Create a post:
    ```bash
    curl -X POST http://127.0.0.1:8000/api/websites/1/posts \
    -H "Content-Type: application/json" \
    -d '{"title":"The First Post in 2025","description":"Decription The First Post in 2025"}'
    ```
5. Dispatch email jobs:
    ```bash
    php artisan posts:send-new
    ```

---

## Project Structure

```
app/
 ├── Console/Commands/SendNewPostsCommand.php
 ├── Jobs/SendPostEmailJob.php
 ├── Mail/NewPostMail.php
 ├── Models/
 │   ├── User.php
 │   ├── Website.php
 │   ├── Subscription.php
 │   ├── Post.php
 │   └── PostDelivery.php
 ├── Http/
 │   ├── Controllers/
 │   │   ├── PostController.php
 │   │   └── SubscriptionController.php
 │   └── Requests/
 │       ├── CreatePostRequest.php
 │       └── SubscribeRequest.php
resources/
 └── views/emails/posts/new.blade.php
```

---

## Notes

-   Emails are sent through the queue system in the background.
-   Duplicate deliveries are prevented using a unique key on the `post_deliveries` table.
-   The command `posts:send-new` can be run manually or scheduled using cron.

---

## License

This project is created for demonstration and technical evaluation purposes only.

---

### Author

**Harun Al Rosyid**  
[harunalrosyid.com](https://harunalrosyid.com)  
hello@harunalrosyid.com
