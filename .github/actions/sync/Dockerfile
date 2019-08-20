FROM php:7.2-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk --update add git less openssh && \
    rm -rf /var/lib/apt/lists/* && \
    rm /var/cache/apk/*

RUN composer install

CMD ["bin/console"]
