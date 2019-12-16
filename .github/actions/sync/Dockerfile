FROM php:7.2-alpine

RUN apk --update add git less openssh && \
    rm -rf /var/lib/apt/lists/* && \
    rm /var/cache/apk/*

ENTRYPOINT ["bin/console", "app:github-event", "/github/workflow/event.json", "-vvv"]

