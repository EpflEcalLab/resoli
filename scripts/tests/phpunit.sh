#!/bin/sh
#
# Run PHPUnit tests for local or development purpose.
# Author: Kevin Wenger
#
# Run as `./phpunit.sh`
#

# Kill runserver (drush 8 or drush 9) if one is alive.
ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

while [ $# -gt 0 ]; do
  case "$1" in
    --group=*)
      PHPUNIT_GROUP="${1#*=}"
      ;;
    *)
      printf "\e[1;91m***************************\e[0m\n"
      printf "\e[1;91m* Error: Invalid argument.*\e[0m\n"
      printf "\e[1;91m***************************\e[0m\n"
      exit 1
  esac
  shift
done

if [ ! -z "PHPUNIT_GROUP" ]
then
  PHPUNIT_GROUP="qs"
fi

printf "\e[1;33m**************************\e[0m\n"
printf "\e[1;33m* Running PHPUnit Tests. *\e[0m\n"
printf "\e[1;33m**************************\e[0m\n"

cd "web"

nohup bash -c "php ../vendor/bin/drush runserver &" && sleep 3;

export SYMFONY_DEPRECATIONS_HELPER=weak
export SIMPLETEST_DB="sqlite://tmp//tmp/qs.sqlite"
export SIMPLETEST_BASE_URL='http://127.0.0.1:8888'

../vendor/bin/phpunit -c core --group $PHPUNIT_GROUP --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" --stop-on-error --stop-on-failure
phpunit_exit=$?

if [ $phpunit_exit -ne 0 ]
then
  exit $phpunit_exit
fi

# Kill runserver (drush 8 or drush 9) if one is alive.
ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9
