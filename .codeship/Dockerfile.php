FROM antistatique/php-dev:7.1

WORKDIR /var/www

RUN set -eux; \
  \
  sed -ri -e 's!\ErrorLog .+\.log!ErrorLog /dev/null!g' /etc/apache2/apache2.conf /etc/apache2/**/*.conf; \
  sed -ri -e 's!\CustomLog .+\.log!CustomLog /dev/null!g' /etc/apache2/apache2.conf /etc/apache2/**/*.conf

ADD ./composer.json ./composer.lock ./
RUN set -eux; \
  \
  composer install --prefer-dist --no-autoloader --no-scripts --no-progress --no-suggest --no-interaction; \
  composer clear-cache

COPY . ./

RUN set -eux; \
  \
  composer install --prefer-dist --no-progress --no-suggest --no-interaction; \
  composer clear-cache;
