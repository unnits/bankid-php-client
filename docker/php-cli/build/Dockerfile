FROM php:8.1-cli-alpine3.17

WORKDIR /data

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=liteacz/whalesome:v0.0.1-alpha.1 /opt/whalesome /opt/whalesome

RUN mv /opt/whalesome/bin/* /usr/local/bin

RUN echo "Installing shared dependencies" \
    && install-php-extensions bcmath

RUN echo "Installing build specific dependencies" \
    # Git and openssh-client are needed for composer to function properly
    && apk add --no-cache git openssh-client \
    && lit install php/composer \
