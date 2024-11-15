# API Application

## Description

This API application provides a RESTful interface for managing products, categories, and user authentication. Built with Laravel, it follows best practices for RESTful design and includes input validation, error handling, and API documentation generated with Swagger/OpenAPI.

## Features

- User authentication (login, logout)
- Manage categories (CRUD)
- Manage products (CRUD)
- Input validation using Laravel's request validation
- API documentation generated with Swagger/OpenAPI

## Requirements

- PHP >= 8.2
- Composer
- Laravel >= 11.x
- MySQL or any other supported database

## Installation

1. **Clone the repository:**

2. Install dependencies

       composer install

3. Set up your environment

   cp .env.example .env

4. Generate the application key

       php artisan key:generate

5. Run migrations

       php artisan migrate

       php artisan db:seed


6. Start the server

   php artisan serve

7. Usage

    **Authentication Endpoints**
    
    POST /api/auth/login: Log in a user
        
     **Loggin credentiels**

   {
   "name": "Redouane",
   "email": "redouane@example.com",
   "password": "password",
   "password_confirmation": "password"
   }

   POST /api/auth/logout: Log out the authenticated user

   **Category Endpoints**
   GET /api/categories: Retrieve a list of categories

   POST /api/categories: Create a new category

   GET /api/categories/{id}: Retrieve a specific category

   PUT /api/categories/{id}: Update an existing category

   DELETE /api/categories/{id}: Delete a category

   **Product Endpoints**
   GET /api/products: Retrieve a list of products

   POST /api/products: Create a new product

       exemple data:

{
    "name": "Product",
    "description": "description of the product",
    "price": 10.99,
    "stock": 10,
    "categories": [1]
}

   GET /api/products/{id}: Retrieve a specific product

   PUT /api/products/{id}: Update an existing product

   DELETE /api/products/{id}: Delete a product

    **Testing**

    To run the tests for this API: 

        php artisan test
