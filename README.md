# Requirements
Build a System to create Customer Installment. This will be a one-page solution without reloading and it’s must.
You must create API based Customer Installment System with PHP &amp; Javascript. Do it use
Laravel and for the Frontend you can use Nuxtjs (Vuejs) framework.

- Create a Login system based on OAuth 2.0 or JWT authorization.
- Create a customer CRUD
- Now, make a Customer Installment form with multiple installments periods. On
Create/Edit page just one Field (Customer) will be there. After clicking on “Add New”
then, another row will be added to add installment period. If you want to create an
installment, you must add two installments. Please ensure this validation.

- Show the list of All customer installment. PAY, delete and edit that installment.
- When click green “PAY” button will be open a modal to show all customer installments
to payment each installment amount. So, if any installment already paid in this case
“PAY to Stripe” button will be show “PAID” text and disable.

- After payment successfully completed it’s need to back on site and show a success
message.
## Requirements
- Laravel latest verstion .
- JWT or OAuth2.
- Front-end NuxtJs.

### Bonus (Optional):
● Must use SOLID Principles.
● Separated Form Request in the application.
● Follow Service Pattern in the application
● If you can do Unit and Feature Testing, it will be a plus point for you.

## I used the following
- Laravel 9.43 (Framework)
- JWT Authentication (Authentication)
# How to Setup
1. Pull the repository form 
https://github.com/ahsan-ullah/ict-euro.git
- or Run the following command 
> git clone https://github.com/ahsan-ullah/ict-euro.git

## Run the following (quote) commands
> cp .env.example .env
- Create a database by ict_euro
- Update .env file database name to ict_euro
- set Stripe API key and secret in .env file as follows

* STRIPE_KEY=pk_test_TYooMQauvdEDq54NiTphI7jx
* STRIPE_SECRET=sk_test_4eC39HqLyjWDarjtT1zdp7dc

> composer install
- or
> composer update

> php artisan key:generate

> php artisan jwt:secret

> php artisan migrate

> php artisan db:seed

> php artisan serve

* Use the following authentication
* Email: ahsanullah716@gmail.com
* Password: 123456789

## Postman Collection Documentaiont
Postman Collection Documentaiont Link:
[Postmean Collection Documentaiont](https://documenter.getpostman.com/view/1952071/2s8YzXtzLp)
https://documenter.getpostman.com/view/1952071/2s8YzXtzLp
## Dependencies
- PHP 8.1 or hire
## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
