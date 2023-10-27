# Developer guide

Thank you for your intention of contributing to this project.
Following paragraphs will walk you through all the necessary steps you need
to take in order to start developing this library.

## TL;DR

```bash
# Setup
git clone git@gitlab.litea.dev:unnits/unniparts/bankid-client.git
cd bankid-client
make configure
make install

# Day to day
make up
make shell
make phpstan
make phpunit
make phpcs
make down
make seucrity-check

# Starting server for examples
make serve
```

## Requirements

Bank iD client is a standard PHP library which means you can develop
it using standard environment setup.

On the other hand, in order to make the developer experience more straightforward,
we use [Docker](https://www.docker.com/).

To be able to start developing right away, you will need:

1. make
    
    Make is not mandatory, but it makes (non pun intended) it easier to execute all the docker commands
    required to start developing.

    ```bash
    make --version
    ```
   
    ```
    GNU Make 4.3
    Built for x86_64-pc-linux-gnu
    ...
    ```
   
2. docker

    ```bash
    docker --version
    ```
   
    ```
    Docker version 24.0.5, build ced0996
    ```
 3. `docker compose` or `docker-compose` available


## Initial setup

1. clone the repository

    ```bash
    git clone git@gitlab.litea.dev:unnits/unniparts/bankid-client.git
    cd bankid-client
    ```

2. Initialize library configuration

    ```bash
    make configure
    ```
   
    This command copies the `docker/docker-compose.yml.example` into `docker-compose.yml`
    which is git-ignored.

3. Install the dependencies

    ```bash
    make install
    ```
   
## Day to day work

1. Star the project

    ```bash
    make up
    ```
   
2. Enter the container

    ```bash
    make shell
    ```
   
    This command attaches your terminal's shell into the container.
    Within there, you can execute any `php` or `composer` commands you are used to.

    Or you can use the handful `make` recipes listed below (or see `cat Makefile`)

3. Run automated tests with phpunit

    ```bash
    make phpunit
    ```
   
4. Run static analysis using PHPStan

    ```bash
    make phpstan
    ```
   
5. Verify your code style via php code sniffer

    ```bash
    make phpcs
    ```
   
    or automatically fix fixable errors with beautifier

    ```bash
    make phpcbf
    ```

6. Analyse composer packages for known vulnerabilities

    ```bash
    make security-check
    ```

7. Shutdown the project

    ```bash
    make down
    ```

## Docker

Bank iD client is a dockerized project. That means we use [docker](https://www.docker.com/)
to simplify the project setup. That allows you to start developing way faster. You don't have
to install any other dependencies yourself.

**That does NOT mean**, you need docker to install and use the library in your own projects.
That stays the same as with any other php library installable via composer.

You can opt out to using docker even for development if you prefer to.

## Static analysis

PHP is interpreted programming language. That means any errors that occur in your application
happen at runtime. That is usually too late. Static analysis helps us with preventing those mistakes
via scanning the code and evaluating possible problems before the code is even run.

We use [PHPStan](https://phpstan.org/) to make sure the library does not contain any surprises.

## Automated tests

The library is also covered with automated tests that also make sure the business logic is correct.
It includes both Unit and Feature/API tests.

## Code style

To take the quality even further, code style of the library itself is being tested against
set of rules. This help us keep the code **maintainable**, **readable** and suitable for **future extensions**.

## Certificates

In order to use Bank iD [to digitally sign](https://developer.bankid.cz/docs/apis_sep#api-for-sign) documents
you need [X.509 certificate](https://datatracker.ietf.org/doc/html/rfc5280) to digitally sign the request to the Bank iD
**NOT the document itself**.

You can generate one with `openssl` CLI library. Then you also need to convert it to the [JWKs](https://www.rfc-editor.org/rfc/rfc7517.html)
representation and expose it's public part to the internet (so Bank iD can reach it).

If you have `openssl` installed on your system, you can use our helper script to generate certificate and
convert it to `JWKs`

```bash
./bin/jwksgen.sh
```
