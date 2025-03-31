# Card Game Application

This application is built with PHP and uses Symfony with Docker setup. It also uses Composer for dependency management.

## Requirements

- Docker and Docker Compose
- PHP (local or via Docker)
- Composer

## Setup Application

1. **Clone the repository:**

   ```bash
   git clone https://github.com/MazBazDev/your-repository.git
   cd your-repository
   ```

2. **Install PHP dependencies using Composer:**

   ```bash
   composer install
   ```

3. **Setup Docker:**

   The project uses Symfony's basic Docker setup. Use the provided `docker-compose.yml` file to bring the containers up. Run:

   ```bash
   docker-compose up -d
   ```

   This will start the web service, database and any other needed services.

4. **Configure environment variables:**

   Duplicate the `.env` file into `.env.local` and adjust your configurations if needed (especially the database connection parameters).

## Swagger API Documentation

- Once the application is running, you can access the API documentation via Swagger at:

  ```
  http://localhost/api/doc
  ```

## JWT Token Creation and Use

1. **Generating JWT Keys:**

   If using the LexikJWTAuthenticationBundle, generate the keys by running:

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

2. **Obtain a Token:**

   Use the login endpoint (usually `POST /login_check`) with valid credentials:
   Username: **John**
   Password: **Doe**
   ```bash
   curl -X POST "http://localhost/login_check" -H "Content-Type: application/json" -d '{"username":"John", "password":"Doe"}'
   ```

   This will return a JSON with your JWT token.

3. **Using JWT in Swagger:**

   In the Swagger UI, click on the "Authorize" button and enter:

   ```
   Bearer <your_jwt_token>
   ```

   This allows you to access protected endpoints directly via Swagger.

## Data Fixtures

To load example data into your database, run the following command:

```bash
php bin/console doctrine:fixtures:load
```

This will load the configured fixtures (if any) into your database.

## Entities Overview

The application includes several entities. Some key ones are:

- **GlobalStats:**  
  Used to store and update global statistics such as route counts. The entity has a value field (as a string) and methods to increment this value.

- *Other entities:*  
  Additional entities (related to card-game functionality) may be located in the `/src/Entity` folder.

## Global Statistics & Middleware

- **Global Statistics:**  
  The application tracks usage statistics through the `GlobalStats` entity. Routes are registered with keys (e.g., `route.test`, `route.api_doc`, etc.) and corresponding counts.

- **Middleware:**  
  A custom middleware is used to update these statistics on every request. The middleware calls the `GlobalStatsService` to update or create records. This ensures every route access is tracked and aggregated.

## Running Unit Tests

Unit tests are provided in the `/tests` directory. To run them, use the following command:

```bash
php bin/phpunit
```

This will execute tests for both the entities (like GlobalStats) and services (such as GlobalStatsService).

## Additional Information

- **Environment:**  
  The Docker setup provides a fast way to spin up the application. Make sure you have Docker and Docker Compose installed.

Enjoy developing and testing your application!
