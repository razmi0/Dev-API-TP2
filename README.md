# Description

This project is a PHP-based web application built using the Slim framework. It follows a structured approach with a focus on dependency injection, middleware, and routing to create a robust and maintainable codebase.

## Dependencies

The project relies on several key dependencies, as specified in the `composer.json` file:

- `slim/slim`: A PHP micro-framework that helps you quickly write simple yet powerful web applications and APIs.
- `slim/psr7`: PSR-7 implementation for Slim framework.
- `php-di/php-di`: Dependency Injection Container for PHP.
- `vlucas/valitron`: A simple, elegant, stand-alone validation library with no dependencies.
- `slim/php-view`: PHP View Renderer for Slim framework.
- `vlucas/phpdotenv`: Loads environment variables from `.env` files into `$_ENV` and `$_SERVER`.

## Folder Structure

- **config**
  - `definitions.php`: Contains dependency injection container configuration.
  - `routes.php`: Defines the application's routes and associates them with controllers and middleware.
- **controllers**
  - `Products.php`: Contains the controller logic for handling product-related requests.
- **middlewares**
  - `BodyValidationMiddleware.php`: Middleware for validating the request body.
  - `HeaderMiddleware.php`: Middleware for adding headers to the response.
  - `IdMiddleware.php`: Middleware for handling and validating the id parameter in requests.
- **model**
  - **dao/**: Contains Data Access Objects (DAOs) for interacting with the database.
    - `Connection.php`: Manages the database connection.
    - `ProductDao.php`: Contains methods for interacting with the products table in the database.
  - **entity/**: Contains entity classes representing database tables.
    - `Product.php`: Represents the products table in the database.
- **public**
  - `index.php`: The entry point of the application. It sets up the Slim app, loads environment variables, and configures the dependency injection container and routes.
- **vendor**
  - Contains all the third-party libraries and dependencies installed via Composer.
- `.env`
  - Environment variables configuration file.
- `.gitignore`
  - Specifies files and directories to be ignored by Git.
- `composer.json`
  - Contains the project's dependencies and autoloading configuration.
- `composer.lock`
  - Locks the versions of the dependencies to ensure consistency across installations.

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- Composer

### Installation

1. Clone the repository:
2. Navigate to the project directory:
3. Install the dependencies:
4. Set up the environment variables by copying `.env.example` to `.env` and filling in the required values.

### Running the Application

Start the PHP built-in server:

```sh
php -S localhost:8080 -t public
```

The application will be accessible at [http://localhost:8080](http://localhost:8080).

## License

This project is licensed under the MIT License. See the `LICENSE` file for details.

## Acknowledgements

- Slim Framework
- PHP-DI
- Valitron
- PHP-Dotenv

Feel free to contribute to this project by submitting issues or pull requests.
