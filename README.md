# Laravel Blog CRUD

This is a Laravel Blog CRUD API Application Built with Laravel, PHP, MySql

## Project Setup

The following are to be considered when running this Application:

### Running the project

<ul>
    <li>Open the terminal and navigate to <b>Project</b> folder</li>
    <li>Run <code>composer install</code> command to install required dependencies</li>
    <li>Copy .env.example to .env and update the DB_DATABASE, DB_PASSWORD, DB_USERNAME to credentials according your to your database credentials</li>
    <li>Run the <code>php artisan key:generate</code> command to generate an application key.</li>
    <li>Run the <code>php artisan migrate:fresh --seed</code> command to run migrations and seed.</li>
    <li>Run the <code>php artisan passport:install</code> command to install passport.</li>
    <li>Run the <code>php artisan serve</code> command and navigate to the url provided to start using the application.</li>
</ul>

### Default account 
<code>admin@mail.com</code> is the default admin email and <code>password</code>

### API Documentation
https://documenter.getpostman.com/view/14023069/TzskE3b4 Here is the API Documentation

### Tests 
Run the <code>php artisan test</code> to run the tests 
