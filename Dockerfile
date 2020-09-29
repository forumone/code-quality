FROM golang:alpine as REVIEWDOG

RUN wget -O - -q https://raw.githubusercontent.com/reviewdog/reviewdog/master/install.sh | sh -s

FROM forumone/composer:latest

RUN apk add --no-cache git

COPY --from=REVIEWDOG /go/bin/reviewdog /usr/local/bin

COPY composer.json /tmp/composer.json

RUN rm /tmp/composer.lock

RUN composer global install

ENV PATH "/tmp/vendor/bin/:$PATH"

VOLUME "/code"

WORKDIR /code
