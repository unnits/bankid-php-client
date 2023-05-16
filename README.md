# BankID Client

## Maintainers
- Dan Charousek - dan.charousek@unnits.com
- Lenka Kolářová - lenka.kolarova@unnits.com

## Obsah
- [Instalace projektu](#instalace-projektu)
- [Každodenní používání](#každodenní-používání)
- [Pomocné skripty](#pomocné-skripty)
- [Testy](#testy)
- [Upgrade projektu](#plošná-aktualizace-projektu)
- [Examples](#examples)

## Instalace projektu

1. Naklonovat repozitář:

        $ git clone git@gitlab.litea.dev:unnits/unniparts/bankid-client.git
        $ cd bankid-client

2. Překopírování souborů nezávislých na prostředí:

        $ make configure

3. Inicializace aplikace:

        $ make install

## Každodenní používání

1. Spuštění projektu

        $ make up

2. Ukončení projektu

        $ make down

### Pomocné skripty

Přepne uživatele do PHP kontejneru, pracovat s Composer nebo Laravel artisanem

    $ make shell

## Testy

1. PHPUnit

        $ make phpunit

2. PHPStan

        $ make phpstan

3. Code Sniffer

        $ make phpcs    // kontrola
        $ make phpcbf   // automatická oprava

4. Security check

        $ make security-check 

## Plošná aktualizace projektu

    $ make upgrade

Aktualizuje docker image a composer závislosti.
Vhodné pokud jste na projektu dlouho nepracovali nebo pro hromadnou aktualizaci knihoven.

## Examples

1. Odkomentovat mapování portu `8000:80` v souboru `docker-compose.yml`

2. Nakonfigurovat BankId údaje v `.env`

2. Spustit PHP built-in server

   ```bash
   make serve
   ```
   
3. Na adrese http://localhost je k dispozici příklad získávání dat o uživateli a podpisu dokumentu přes BankID sign
