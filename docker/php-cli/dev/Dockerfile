FROM php:8.1-cli-alpine3.17

WORKDIR /data

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=liteacz/whalesome:v0.0.1-alpha.1 /opt/whalesome /opt/whalesome
COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin

RUN mv /opt/whalesome/bin/* /usr/local/bin

RUN echo "Installing dev specific dependencies" \
  # Git and openssh-client are needed for composer to function properly
  && apk add --no-cache git openssh-client \
  && lit install php/composer

RUN echo "Installing xdebug" \
    && install-php-extensions xdebug-3.2.0 bcmath

ENTRYPOINT ["docker-entrypoint"]
CMD ["loop"]
