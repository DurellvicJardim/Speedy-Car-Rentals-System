# Car Rental System

## System Setup

1. Using XAMPP, ensure Apache and MySQL are started.
2. Ensure the `car_rental_system` folder is in your XAMPP `htdocs` folder.
   - Example: `C:\xampp\htdocs\car_rental_system\`
3. Access phpMyAdmin using [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
4. Create a database called `car_rental`.
5. Import `car_rental.sql`.
6. Open the site using [http://localhost/car_rental_system/](http://localhost/car_rental_system/)

## Login Credentials

There are currently two existing user accounts:
Username: admin | Password: admin123 || Username: employee1 | Password: employee123

If you would like to add your own custom login credentials:

1. Locate the `create_admin.php` file in `\xampp\htdocs\car_rental_system\`.
2. Open the file in your preferred IDE.
3. Manually edit the following variables: `$employee_text`, `$email_text`, and `$password_plain`.
4. Open the page in your browser: [http://localhost/car_rental_system/create_admin.php](http://localhost/car_rental_system/create_admin.php)
5. You should see a success or failure message depending on the result.

## Features Implemented

### Login

- Secure login using `password_hash` and `password_verify`.
- Session guard (`require_login.php`) only allows valid users to navigate the site.
- Logout button terminates the session.
- Forgot password section allows employees with valid emails to reset their password.

### Cars

- View a list of cars.
- Add new cars.
- Edit car details.
- Delete cars.
- Search cars by make, model, or plate.
- Car availability updates depending on rental status.
- Upload and display car images.

### Customers

- View a list of customers.
- Add new customers.
- Edit customer details.
- Delete customers.
- Search customers by name, email, or phone.

### Rentals

- Start a new rental of a car for a customer.
- Select both a start date and expected return date.
- Mark rentals as returned (cost calculated automatically) or overdue.
- Filter and search rentals.
- Rental price is automatically calculated based on the number of days rented × daily rate.
