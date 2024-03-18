# User guide

This guide will walk you through the installation and the setup process of the 
Bank iD Client library.

In this context, **user is a developer** who uses this library on their own PHP project
in order to use Bank iD services. It is not a person who participates in development of the library.
Even though some people may be both users and developers with respect to the library. 

## Installation

The simpliest way to install this library is via composer:

```php
composer require unnits/bankid-client
```

## Examples

Best way to understand the principles behind using this library is actually to try
it out on real-life scenarios.

1. Follow the [instructions](developer_guide.md#initial-setup) in developer's guide to set up the project locally
2. `cp ./examples/.env.example ./examples/.env`
3. Specify the `DEMO_CLIENT_ID` and `DEMO_CLIENT_SECRET` variables.
4. Start the php's built in server

    ```bash
    make serve
    ```

5. Now go to http://localhost:8000

    ℹ if you'd like to start the application on different port change the
    [port mapping](https://docs.docker.com/network/#published-ports) in `docker-compose.yml`.

## Value objects and DTOs

The library ships with all the Bank iD responses wrapped up in [data transfer objects](https://en.wikipedia.org/wiki/Data_transfer_object).
Instead of using generic strings and/or integers, we wrap all the known enumerable values into PHP's native [Enums](https://www.php.net/manual/en/language.types.enumerations.php)
That makes it easier for the user to consume the resulting data as they get autocompletion and type-hints from their IDEs.

## PSR

### PSR-7

The library uses standard [PSR-7](https://www.php-fig.org/psr/psr-7/) interfaces for HTTP communication.
This makes it easier for the user to swap HTTP Client implementation if they wish to.
Our examples use GuzzleHttp library to issue HTTP requests, but you can choose your own favorite client.

## Extensible

The library is written with all the best practices and OOP principles in mind.
We promote `composition over inheritance` principle, which gives you the ability to
extend the library with confidence.
