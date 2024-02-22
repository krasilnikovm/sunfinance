# Sunfinance application 
Application to handle payment import CSV files and incoming API calls.

## Setup

1. Run docker container:
````bash
docker-compose up --build --force-recreate
````

## API Documentation

The API documentation is available via Swagger UI. You can access it using the following link:

[Swagger UI](http://localhost:8080/api/doc)

## CLI
* bin/console import --file=./payments.csv
* bin/console report --date=2023-01-10

## Makefile Commands

- `cs-check`: Checks the PHP code for coding standards violations.
- `cs-fix`: Fixes the PHP code coding standards violations automatically.
- `phpstan`: Runs PHPStan for static analysis.
- `unit-test`: Runs PHPUnit tests for the unit test suite.
- `application-test`: Runs WebTestCase tests.
- `fixtures`: Apply fixtures
- `composer`: Install vendors

To run the commands, use:
````bash
docker exec -it sunfinance_php bash 
make <command>
````

## Info
.env.local used for executing CI pipelines, inorder to run tests on local machine need to create `.env.test.local` and add the env variable 
````bash
DATABASE_URL="postgresql://me:me@database:5432/sunfinance_test?serverVersion=16&charset=utf8"
````
