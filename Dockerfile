FROM antistatique/php-dev:7.1-node9

WORKDIR /var/www

ADD ./composer.json ./composer.lock ./
RUN set -eux; \
  \
  composer install --prefer-dist --no-autoloader --no-scripts --no-progress --no-suggest --no-interaction; \
  composer clear-cache

ADD ./package.json ./yarn.lock ./
RUN set -eux; \
  \
  yarn install --no-progress --non-interactive; \
  yarn cache clean

COPY . ./

RUN set -eux; \
  \
  mkdir -p .codeship/build; \
  \
  composer install --prefer-dist --no-progress --no-suggest --no-interaction; \
  composer clear-cache;
