#!/bin/sh
#
# Run PHPUnit tests for local or development purpose.
# Author: Kevin Wenger
#
# Run as `./behat.sh`
#
# Kill runserver if one is alive
ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

SKIP_DEPEDENCIES=0
SKIP_TESTS=0
SKIP_INTERACTION=0
PRIVATE_FILES=''

while [ $# -gt 0 ]; do
  case "$1" in
    --skip-dependencies=*)
      SKIP_DEPEDENCIES=1
      ;;
    --skip-tests=*)
      SKIP_TESTS=1
      ;;
    --database=*)
      BEHAT_DATABASE="${1#*=}"
      ;;
    --skip-interaction=*)
      SKIP_INTERACTION="-y"
      ;;
    --private-files=*)
      PRIVATE_FILES="${1#*=}"
      ;;
    *)
      printf "\e[1;91m***************************\e[0m\n"
      printf "\e[1;91m* Error: Invalid argument.*\e[0m\n"
      printf "\e[1;91m***************************\e[0m\n"
      exit 1
  esac
  shift
done

if [ ! -z "$MYSQL_USER" ]
then
# Codeship env.
  BEHAT_DATABASE="mysql://$MYSQL_USER:$MYSQL_PASSWORD@localhost/development$TEST_ENV_NUMBER"
elif [ ! -z "$BEHAT_DATABASE" ]
then
  # Jenkins env.
  BEHAT_DATABASE="$BEHAT_DATABASE"
elif [ ! -z "$BEHAT_DATABASE" ]
then
  BEHAT_DATABASE="sqlite://localhost//tmp/development-qs.sqlite"
fi

if [ $SKIP_DEPEDENCIES -eq 0 ]
then
  printf "\e[1;34m*************************\e[0m\n"
  printf "\e[1;34m* Install dependencies. *\e[0m\n"
  printf "\e[1;34m*************************\e[0m\n"

  composer install
  bundle install
  bundle exec cap staging styleguide:build_local
fi

cd "web"

printf "\e[1;35m******************************************\e[0m\n"
printf "\e[1;35m* Install Drupal - Quartiers Solidaires. *\e[0m\n"
printf "\e[1;35m******************************************\e[0m\n"

if [ ! -z "$BEHAT_DATABASE" ]
then
  printf "\e[1;93mthe database is: $BEHAT_DATABASE\e[0m\n"
fi

../vendor/bin/drush si standard --db-url=$BEHAT_DATABASE --site-name='Behat' --account-name=admin --account-pass=admin --account-mail=behat@antistatique.net $SKIP_INTERACTION
../vendor/bin/drush ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();'
../vendor/bin/drush config-set system.site uuid "b38de9f3-fd4d-4779-ab7f-29e7b91556f1" -y

# Sometimes Drupal 8.4.x import configs in wrong orders.
# So we repeat the config-import max. 3 times on successives fails.
n=0
until [ $n -ge 3 ]; do
  ../vendor/bin/drush config-import -y --source='../config/d8/sync/'
  n=$((n + 1))
  # delay 3s.
  sleep 3
done

../vendor/bin/drush en qs_default_content -y
../vendor/bin/drush updatedb -y
../vendor/bin/drush pmu hal serialization default_content qs_default_content -y

# Fix Sitename bugged keep display "| Drupal"
# https://www.drupal.org/node/2851877
../vendor/bin/drush eval "\Drupal::languageManager()->getLanguageConfigOverrideStorage('fr')->delete('system.site');"

if [ $SKIP_TESTS -eq 0 ]
then
  nohup bash -c "php ../vendor/bin/drush runserver &" && sleep 3;
fi

# Set the Privates Files settings which need to be in the settings.php file
if [ -d "$PRIVATE_FILES" ] && [ -f ./sites/default/settings.php ]
then
  printf "\e[1;34m*************************\e[0m\n"
  printf "\e[1;34m* Override Settings.php *\e[0m\n"
  printf "\e[1;34m*************************\e[0m\n"
  chmod -R 777 ./sites/default/settings.php

  printf "\e[1;93m* $settings['file_private_path'] = '$PRIVATE_FILES'; *\e[0m\n"
  echo "\$settings['file_private_path'] = '$PRIVATE_FILES';" >> ./sites/default/settings.php

  ../vendor/bin/drush cr
fi

if [ $SKIP_TESTS -eq 0 ]
then
  # Big Pipe cause some block to be rendered with delay and break Behat tests.
  ../vendor/bin/drush pmu big_pipe -y

  printf "\e[1;33m************************\e[0m\n"
  printf "\e[1;33m* Running Behat Tests. *\e[0m\n"
  printf "\e[1;33m************************\e[0m\n"

  cd ".."

  set -e
  BEHAT_PARAMS='{"gherkin" : {"cache" : null}}' ./vendor/bin/behat --colors --strict
  behat_exit=$?

  # Kill runserver if one is alive
  ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
  ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9

  cd "web"
  # Enable Big Pipe.
  ../vendor/bin/drush en big_pipe -y

  exit $behat_exit
fi

