#!/bin/sh
#
# Run PHPUnit tests for local or development purpose.
# Author: Kevin Wenger
#
# Run as `./phpunit.sh`
#
# @see http://wiki.bash-hackers.org/howto/getopts_tutorial
#

# Kill runserver (drush 8 or drush 9) if one is alive.
ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
PHPUNIT_GROUP="qs"
PHPUNIT_EXCLUDE_GROUP=""

while getopts ':g:x:' OPTION; do
  case "$OPTION" in
    g)
      PHPUNIT_GROUP="$OPTARG"
      ;;
    x)
      PHPUNIT_EXCLUDE_GROUP="$OPTARG"
      ;;
    ?)
      echo "script usage: $(basename $0) [-g group] [-x exclude-group]" >&2
      exit 1
      ;;
  esac
done
shift "$(($OPTIND -1))"

printf "\e[1;33m**************************\e[0m\n"
printf "\e[1;33m* Running PHPUnit Tests. *\e[0m\n"
printf "\e[1;33m**************************\e[0m\n"

export SYMFONY_DEPRECATIONS_HELPER=weak
export SIMPLETEST_DB="mysql://root:root@localhost/qs_test"
export SIMPLETEST_BASE_URL='http://127.0.0.1:8888'
export BROWSERTEST_OUTPUT_DIRECTORY='./web/sites/default/files/tests'

if [ ! -z "$MYSQL_USER" ]
then
  SIMPLETEST_DB="mysql://$MYSQL_USER:$MYSQL_PASSWORD@localhost/development$TEST_ENV_NUMBER"
fi

# Create output directory when not exists.
mkdir -p "$BROWSERTEST_OUTPUT_DIRECTORY"
chmod -R 777 "$BROWSERTEST_OUTPUT_DIRECTORY"

cd "web"

nohup bash -c "php ../vendor/bin/drush runserver &" && sleep 3;

../vendor/bin/phpunit -c core --group $PHPUNIT_GROUP --exclude-group $PHPUNIT_EXCLUDE_GROUP --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" --stop-on-error --stop-on-failure
phpunit_exit=$?

if [ $phpunit_exit -ne 0 ]
then
  exit $phpunit_exit
fi

# Kill runserver (drush 8 or drush 9) if one is alive.
ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9

exit $phpunit_exit
