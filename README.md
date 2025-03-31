# Card Game Application

This application is built with PHP using the Symfony framework and a Docker setup. It also utilizes Composer for dependency management.

## Requirements

- Docker and Docker Compose
- PHP (local installation or via Docker)
- Composer

## Setup Application

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/KAymeric/card-game.git
   cd card-game
   ```

2. **Setup Docker:**
    1. Ensure you have [Docker Compose installed](https://docs.docker.com/compose/install/) (v2.10+).
    2. Run `docker compose build --no-cache` to build fresh images.
    3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project.
    4. Open `https://localhost` in your browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334).
    5. Run `docker compose down --remove-orphans` to stop the Docker containers when needed.

   This setup starts the web service, database, and any other necessary services.

3. **Configure Environment Variables:**

   Duplicate the `.env` file into `.env.local` and adjust your configurations if needed (especially the database connection parameters).

## Swagger API Documentation

Once the application is running, you can access the API documentation via Swagger at:

```
http://localhost/api/doc
```

## JWT Token Creation and Use

1. **Generating JWT Keys:**

   If using the LexikJWTAuthenticationBundle, generate the keys by running:

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

   > **Note:** To disable authentication temporarily, see the `access_control` section in the `security.yaml` file.

2. **Obtain a Token:**

   Use the login endpoint (usually `POST /login_check`) with valid credentials:
    - Username: **John**
    - Password: **Doe**

   ```bash
   curl -X POST "http://localhost/login_check" -H "Content-Type: application/json" -d '{"username":"John", "password":"Doe"}'
   ```

   This returns a JSON response with your JWT token.

3. **Using JWT in Swagger:**

   In the Swagger UI, click the "Authorize" button and enter:

   ```
   Bearer <your_jwt_token>
   ```

   This authorizes you to access protected endpoints directly via Swagger.

4. **Disable Authentication**

   If you want to disable authentication for easier tests, just comment/uncomment the following lines in security.yaml
   ```
   - { path: ^/, roles: IS_AUTHENTICATED_FULLY  } # comment this to desactivate the authentication
   # - { path: ^/, roles: PUBLIC_ACCESS  } # uncomment this to desactivate the authentication
   ```

## Data Fixtures

To load example data into your database, run:

```bash
php bin/console doctrine:fixtures:load
```

This command loads the configured fixtures (if any) into your database.

## Entities Overview

The application includes several entities. Key ones include:

- **GlobalStats:**  
  This entity stores and updates global statistics such as route counts. It has a `key` field (a string identifying the statistic) and a `value` field (also stored as a string but used as a counter).
    - **Increment Functionality:**  
      The entity includes an `incrementValue()` method which converts the stored string to an integer, increments it, and saves it back as a string.

- **Other Entities:**  
  Additional entities (related to the card-game functionality) are located in the `/src/Entity` folder.

## Global Statistics & Middleware

### Global Statistics

- **Purpose:**  
  The application tracks usage statistics via the `GlobalStats` entity.
- **Implementation:**
    - **Keys:**  
      Keys follow a naming convention such as `route.api_doc`, `user_agent.Chrome`, or `total_request`. This allows you to group and aggregate statistics by prefix.
    - **Service:**  
      The `GlobalStatsService` provides methods to increment counts for different types of statistics (routes, user agents, total requests) and to aggregate these statistics with a `getStats()` method.

### Middleware

- **Custom Middleware:**  
  A custom event listener (`RequestListener`) intercepts every HTTP request:
    - **Route Counting:**  
      It extracts the route from the request and calls `incrementRouteCount()`.
    - **User Agent Tracking:**  
      It extracts the User-Agent header, processes it (using a helper method to return a generic browser name like "Chrome" or "Firefox"), and calls `incrementUserAgent()`.
    - **Total Request Count:**  
      It also updates a global counter for total requests.

This middleware ensures that every request is tracked, and the statistics are aggregated in a meaningful way.

## Running Unit Tests

Unit tests are provided in the `/tests` directory and cover both the entities (like `GlobalStats`) and services (such as `GlobalStatsService`). To run the tests, use:

```bash
./vendor/bin/phpunit
```

The tests validate:
- The correct behavior of the `incrementValue()` method in `GlobalStats`.
- The correct aggregation and creation logic in `GlobalStatsService` (using mocks for the Doctrine EntityManager and Repository).

## Additional Information

- **Environment:**  
  The Docker setup offers a quick way to spin up the application. Make sure you have both Docker and Docker Compose installed.
- **Extensibility:**  
  You can further extend the statistics system to include additional metrics such as HTTP methods, status codes, request duration, client IP addresses, or even referer information. This can help you analyze application performance and user behavior in more detail.

---

Enjoy developing and testing our application!
