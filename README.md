# News Aggregator Application
This repository contains Laravel 12 application, which provides news aggregator api. The application consumes these news articles from several sources and the same is available thorough the API.

## Application architecture
![Architecture](./assets/images/news_aggregator_arch.png "architecture")

The above image describes the overall application architecture

## Tech Stack

* Laravel 12
* LaravelShift Blueprint (For initial scaffolding)
* Laravel Sanctum
* Laravel Octane (Frankenphp application runner)
* SQLite
* Redis

## Build and run application
### Build application
To build and run the application you need to have Podman/Docker installed in your system. From the terminal type below command if you have podman installed
```
podman compose \
    -f compose.local.yml \
    up \
    --build
```

For Docker use the command below
```
docker compose \
    -f compose.local.yml \
    up \
    --build
```

Please copy the .env file shared to the root of the application folder, before running the application

Once the application is running, you can access swagger from the url http://localhost:8000/swagger

Please create a user and generate token from the /register & /token URLs respectively. The token should be used to authenticate for other API requests.

**** Although it was mentioned that authentication need not be implemented in the doc, it wouldn't be complete without authentication. That's why authentication is built into the application ****

Articles are fetched with the help of background jobs, which are part of docker compose file (queue & scheduler). Articles are fetched regularly at 15 mins intervals
