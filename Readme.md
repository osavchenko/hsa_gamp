# Monitoring systems for user metrics

## How to init the project

```shell
docker compose up build
docker compose run php composer install
```

## How to start the worker

To check that command works correctly in a single mode:

```shell
docker compose run php bin/console app:exhange-rate-exporter
```

To run it in a loop:

```shell
 while true; do docker compose run php bin/console app:exhange-rate-exporter; sleep 3600; done
```

## How the result looks from the Google Analytics Console

![Engagement dashboard](screenshots/Firefox_Screenshot_2024-03-18T15-39-09.750Z.png)

![Explorations free form table](screenshots%2FFirefox_Screenshot_2024-03-18T15-40-37.707Z.png)

[Credentials to GA Console](https://share.1password.com/s#1MRAkkvnGXtqtmXhhklB7bfNOIkzl4fJgga_gPqr5EI)