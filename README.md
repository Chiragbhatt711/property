# qqtube

1. Create .env file
	add database name in your phpmyadmin below is phpmyadmin link
    http://localhost/phpmyadmin

2. Run below command to update composer and download dependencies in your local 
    composer update

3. run below command to generate key in .env

	php artisan key:generate

4. Run below command to create tables in your local database
    php artisan migrate

5. after run below commands


php artisan db:seed --class=CreateAdminUserSeeder

6. Run below command for run project
    php artisan serve --port=2023
    copy http://127.0.0.1:2023 this url to run project

    
7. Admin Login
    email = admin@admin.com
    password = admin#2024

