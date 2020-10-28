FROM golang:alpine as REVIEWDOG

# Build and install Reviewdog
# See https://github.com/reviewdog/reviewdog#installation
RUN wget -O - -q https://raw.githubusercontent.com/reviewdog/reviewdog/master/install.sh | sh -s

FROM wodby/php:latest

COPY --from=REVIEWDOG /go/bin/reviewdog /usr/local/bin

COPY --chown=wodby:wodby ./ /code-quality

COPY --chown=wodby:wodby ./vendor-bin /home/wodby/.composer/vendor-bin

RUN composer global require bamarni/composer-bin-plugin
RUN PATH="$(composer config -g home)/vendor/bin:$PATH"

RUN composer global bin all install

CMD ['robo']
