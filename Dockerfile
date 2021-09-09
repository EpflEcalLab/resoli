FROM antistatique/php-dev:7.4-node12

WORKDIR /var/www

# Keep composer version 1 until drupal-console-extend-plugin has not been updated.
# @see https://github.com/hechoendrupal/drupal-console-extend-plugin/pull/25
RUN composer self-update --1

ADD ./composer.json ./composer.lock ./
RUN set -eux; \
  \
  composer install --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction; \
  composer clear-cache

ADD ./package.json ./yarn.lock ./
RUN set -eux; \
  \
  yarn install --no-progress --non-interactive; \
  yarn cache clean

COPY . ./

RUN set -eux; \
  \
  \
  jq 'del(.. |."patches_applied"? | select(. != null))' ./vendor/composer/installed.json > ./vendor/composer/installed.json.new; \
  mv ./vendor/composer/installed.json.new ./vendor/composer/installed.json; \
  \
  composer install --prefer-dist --no-progress --no-suggest --no-interaction; \
  composer clear-cache;
