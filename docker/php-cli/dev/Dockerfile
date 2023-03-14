FROM php:8.1-cli-alpine3.17

WORKDIR /data

COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin

ENTRYPOINT ["/bin/sh"]
CMD ["/entrypoint.sh"]
