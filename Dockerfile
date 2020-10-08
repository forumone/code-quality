FROM golang:alpine as REVIEWDOG

# Build and install Reviewdog
# See https://github.com/reviewdog/reviewdog#installation
RUN wget -O - -q https://raw.githubusercontent.com/reviewdog/reviewdog/master/install.sh | sh -s

FROM wodby/php:latest

COPY --from=REVIEWDOG /go/bin/reviewdog /usr/local/bin
