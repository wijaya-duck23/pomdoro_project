# Pomodoro Timer Application

A fully functional Pomodoro Timer web application built with HTML5, Tailwind CSS, PHP (MVC), and MySQL.

## Features

- Start, pause, resume, and reset Pomodoro sessions
- Automatic transitions between work and break periods
- Session history tracking in MySQL database
- Real-time statistics dashboard
- Browser notifications and sound alerts
- Clean, minimalist Tailwind CSS design

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled

## Installation

1. Clone this repository to your web server's document root directory

2. Create the MySQL database by importing `database.sql`:
   ```
   mysql -u root -p < database.sql
   ```
   
   Alternatively, you can use phpMyAdmin to import the database.sql file.

3. Update database configuration in `config/database.php` with your MySQL credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_mysql_username');
   define('DB_PASS', 'your_mysql_password');
   define('DB_NAME', 'pomodoro_db');
   ```

4. Ensure Apache's mod_rewrite is enabled and that .htaccess files are allowed:
   ```
   a2enmod rewrite
   ```

5. Configure your virtual host to point to the `public` directory:
   ```apache
   <VirtualHost *:80>
       ServerName pomodoro.local
       DocumentRoot /path/to/pomodoro-timer/public
       
       <Directory /path/to/pomodoro-timer/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

6. Restart Apache:
   ```
   service apache2 restart
   ```

7. Access the application at http://localhost or your configured domain name.

## Project Structure

- `/app`
  - `/controllers` - Contains PomodoroController.php
  - `/models` - Contains SessionModel.php for database interaction
  - `/views` - Contains dashboard.php for displaying the UI
- `/config` - Configuration files (database.php)
- `/public` - Publicly accessible files
  - `/js` - JavaScript files (timer.js)
  - `index.php` - Entry point
- `/routes` - Route definitions and request handling

## Future Enhancements

- User authentication and registration
- User preferences (custom timer durations)
- Task management integration
- Dark mode toggle
- Mobile application

## License

MIT 