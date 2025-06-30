# task-cafe

A simple Fullstack PHP project to split a €100 bill equally among participants.  
Users can enter their name and email, and the app calculates the share for each participant. Data is stored in MySQL.

---

## Features

- Add users with validation (required, email format, unique email)
- Calculate and update equal shares on each addition
- Reset all users
- Responsive frontend with vanilla JavaScript and Bootstrap
- Clean backend with custom routing, validation, and service layers
- Uses `.env` for configuration
- Uses Composer for autoloading and dependency management

---

## Requirements

- PHP 8.1+
- MySQL
- Apache or other web server with URL rewriting
- Composer
- Optional: Node.js/npm if you want to manage frontend assets

---

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/task-cafe.git
   cd task-cafe
   ```

2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Create `.env` file in the project root based on `.env.example`:

   ```dotenv
   DB_HOST=127.0.0.1
   DB_DATABASE=task_cafe
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Setup MySQL database and create `users` table:

   ```sql
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL UNIQUE,
       share DECIMAL(10,2) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

5. Configure your Apache virtual host to point to `public/` directory. Example:

   ```apache
   <VirtualHost *:80>
       ServerName task-cafe.local
       DocumentRoot "E:/xampp/htdocs/task-cafe/public"
       <Directory "E:/xampp/htdocs/task-cafe/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

6. Add host entry:

   ```
   127.0.0.1 task-cafe.local
   ```

7. Restart Apache.

---

## Usage

- Open your browser at [http://task-cafe.local](http://task-cafe.local)
- Add participants by entering name and email
- The share for each participant will update automatically
- Reset the list anytime with the Reset button

---

## Project Structure

```
/app
    /Controllers
    /Models
    /Services
/core
    Router.php
    Validator.php
    Response.php
/database
    /migrations
    Database.php
/public
    index.html
    script.js
    style.css
    .htaccess
    index.php
/routes
    api.php
/vendor
.env
composer.json
README.md
```

---

## Technologies Used

- PHP 8.x with strict typing
- Custom Router and Validator
- MySQL with PDO
- Vanilla JavaScript and Bootstrap
- Composer Autoloading
- vlucas/phpdotenv for environment config

