#!/bin/sh
#
# Bootstrap Drupal
# Author: Kevin Wenger
#
# Run as `./drupal.sh`
#

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

SITE_NAME="Quartiers Solidaires"
UUID="b38de9f3-fd4d-4779-ab7f-29e7b91556f1"
ACCOUNT_MAIL="behat@antistatique.net"
DEFAULT_CONTENT="qs_default_content"
CONFIG_DIR="../config/d8/sync/"

# Parameters
SKIP_DEPEDENCIES=0
SKIP_INTERACTION=0
SKIP_DEFAULT=0
SAVE_DATABASE=0
DATABASE_URL=""
MAILCATCHER=""
PRIVATE_FILES=""

while [ $# -gt 0 ]; do
  case "$1" in
    --skip-dependencies=*)
      SKIP_DEPEDENCIES=1
      ;;
    --skip-default=*)
      SKIP_DEFAULT=1
      ;;
    --save-clean-database=*)
      SAVE_DATABASE=1
      ;;
    --database=*)
      DATABASE_URL="${1#*=}"
      ;;
    --mailcatcher=*)
      MAILCATCHER="${1#*=}"
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
      echo "script usage: $(basename $0) [--skip-dependencies=1] [--skip-default=1] [--save-clean-database=1] [--database=DATABASE_URL] [--mailcatcher=MAILCATCHER]] [--private-files=\"./path/to/private\"] [--skip-interaction=1]" >&2
      exit 1
  esac
  shift
done

if [ $SKIP_DEPEDENCIES -eq 0 ]
then
  printf "\e[1;34m*************************\e[0m\n"
  printf "\e[1;34m* Install dependencies. *\e[0m\n"
  printf "\e[1;34m*************************\e[0m\n"

  yarn install
  composer install
fi

if [ ! -z "$MYSQL_USER" ]
then
  DATABASE_URL="mysql://$MYSQL_USER:$MYSQL_PASSWORD@localhost/development$TEST_ENV_NUMBER"
fi

printf "\e[1;35m********************************\e[0m\n"
printf "\e[1;35m* Install Drupal from scratch. *\e[0m\n"
printf "\e[1;35m********************************\e[0m\n"

printf "\e[1;93mthe database is: $DATABASE_URL\e[0m\n"

cd "web"

../vendor/bin/drush si standard --db-url=$DATABASE_URL --site-name="$SITE_NAME" --account-name=admin --account-pass=admin --account-mail=$ACCOUNT_MAIL $SKIP_INTERACTION
../vendor/bin/drush ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();'
../vendor/bin/drush config-set system.site uuid "$UUID" -y

# Sometimes Drupal 8.4.x import configs in wrong orders.
# So we repeat the config-import max. 4 times on successives fails.
attempt=0
until ../vendor/bin/drush config-import -y --source="$CONFIG_DIR"; do
  attempt=$(( attempt+1 ))
  if [ "$attempt" -ge 4 ]; then
    exit 1
  fi
done

../vendor/bin/drush updatedb -y

# Fix Sitename bugged keep display "| Drupal"
# @see https://www.drupal.org/node/2851877
../vendor/bin/drush ev '\Drupal::languageManager()->getLanguageConfigOverrideStorage("fr")->delete("system.site");'

# Set the Privates Files settings which need to be in the settings.php file
if [ -f ./sites/default/settings.php ]
then
  printf "\e[1;34m*************************\e[0m\n"
  printf "\e[1;34m* Override Settings.php *\e[0m\n"
  printf "\e[1;34m*************************\e[0m\n"
  chmod 775 ./sites/default
  chmod 664 ./sites/default/settings.php

  # override sync config directory settings using sed (replace setting line)
  printf "\e[1;93m* \$config_directories['sync'] = '../config/d8/sync'; *\e[0m\n"
  sed -ri -e "s@\\\$config_directories\['sync'\].+;@\\\$config_directories\['sync'\] = '../config/d8/sync';@g" ./sites/default/settings.php

  if [ ! -z "$PRIVATE_FILES" ]
  then
    mkdir -pv "$PRIVATE_FILES"
    chmod 775 "$PRIVATE_FILES"
    printf "\e[1;93m* \$settings['file_private_path'] = '$PRIVATE_FILES'; *\e[0m\n"
    sed -ri -e "s@.+\\\$settings\['file_private_path'\].+;@\\\$settings\['file_private_path'\] = '$PRIVATE_FILES';@g" ./sites/default/settings.php
  fi

  ../vendor/bin/drush cr
fi

if [ ! -z "$MAILCATCHER" ]
then
  printf "\e[1;35m**********************\e[0m\n"
  printf "\e[1;35m* Setup Mailcatcher. *\e[0m\n"
  printf "\e[1;35m**********************\e[0m\n"

  ../vendor/bin/drush cset swiftmailer.transport smtp_host "${MAILCATCHER%:*}" -y
  ../vendor/bin/drush cset swiftmailer.transport smtp_port "${MAILCATCHER#*:}" -y
  ../vendor/bin/drush cset swiftmailer.transport smtp_encryption '0' -y
fi

if [ $SAVE_DATABASE -eq 1 ]
then
  printf "\e[1;35m******************************************\e[0m\n"
  printf "\e[1;35m* Save database without default content. *\e[0m\n"
  printf "\e[1;35m******************************************\e[0m\n"

  ../vendor/bin/drush sql-dump --result-file=../database-clean.dump.sql -y
fi

if [ $SKIP_DEFAULT -eq 0 ]
then
  printf "\e[1;35m***********************************\e[0m\n"
  printf "\e[1;35m* Run fixtures & default content. *\e[0m\n"
  printf "\e[1;35m***********************************\e[0m\n"

  # Check if the Default Content module exists.
  if ../vendor/bin/drush pm-list | grep -q $DEFAULT_CONTENT
  then
    ../vendor/bin/drush en $DEFAULT_CONTENT -y
    ../vendor/bin/drush pmu default_content $DEFAULT_CONTENT -y
  fi
fi

# Build the styleguide using Toolbox.
cd ".."
yarn build --production
