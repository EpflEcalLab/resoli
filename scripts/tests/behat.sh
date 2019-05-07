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

# Kill mailcatcher if one is alive
ps -ef | grep 'mailcatcher' | grep -v grep | awk '{print $2}' | xargs kill -9

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
DEFAULT_CONTENT="qs_default_content"
MAILCATCHER="127.0.0.1:10025"
LOG="./log/behat"
DB_DUMP="./dump/behat"

while [ $# -gt 0 ]; do
  case "$1" in
    *)
      printf "\e[1;91m***************************\e[0m\n"
      printf "\e[1;91m* Error: Invalid argument.*\e[0m\n"
      printf "\e[1;91m***************************\e[0m\n"
      echo "script usage: $(basename $0)" >&2
      exit 1
  esac
  shift
done

# Check if the Swiftmailer module exists then add Mailcatcher.
if ./vendor/bin/drush pm-list | grep -q 'swiftmailer'
then
  printf "\e[1;33m************************\e[0m\n"
  printf "\e[1;33m* Running Mailcatcher. *\e[0m\n"
  printf "\e[1;33m************************\e[0m\n"

  if command -v mailcatcher >/dev/null 2>&1
  then
    mailcatcher --smtp-port "${MAILCATCHER#*:}"
  fi

  ./vendor/bin/drush cset swiftmailer.transport smtp_host "${MAILCATCHER%:*}" -y
  ./vendor/bin/drush cset swiftmailer.transport smtp_port "${MAILCATCHER#*:}" -y
  ./vendor/bin/drush cset swiftmailer.transport smtp_encryption '0' -y
fi

# Check if the Default Content module exists & enable it.
if ./vendor/bin/drush pm-list | grep -q $DEFAULT_CONTENT
then
  printf "\e[1;33m***************************\e[0m\n"
  printf "\e[1;33m* Enable Default Content. *\e[0m\n"
  printf "\e[1;33m***************************\e[0m\n"

  ./vendor/bin/drush en $DEFAULT_CONTENT -y
fi

# Big Pipe cause some block to be rendered with delay and break Behat tests.
# Honeypot prevent Behat to use some form.
./vendor/bin/drush pmu honeypot big_pipe -y

# Create log directory when not exists.
mkdir -p "$LOG"
chmod -R 777 "$LOG"

# Create database dump directory when not exists.
mkdir -p "$DB_DUMP"
chmod -R 777 "$DB_DUMP"

nohup bash -c "./vendor/bin/drush runserver &" && sleep 3;

printf "\e[1;33m************************\e[0m\n"
printf "\e[1;33m* Running Behat Tests. *\e[0m\n"
printf "\e[1;33m************************\e[0m\n"

BEHAT_PARAMS='{"gherkin" : {"cache" : null}}' ./vendor/bin/behat --colors --strict
behat_exit=$?

# Kill runserver if one is alive
ps -ef | grep 'runserver' | grep -v grep | awk '{print $2}' | xargs kill -9
ps -ef | grep 'd8-rs-router.php' | grep -v grep | awk '{print $2}' | xargs kill -9

# Kill mailcatcher if one is alive
ps -ef | grep 'mailcatcher' | grep -v grep | awk '{print $2}' | xargs kill -9

# Check if the Default Content module exists & disable it.
if ./vendor/bin/drush pm-list | grep -q $DEFAULT_CONTENT
then
  ./vendor/bin/drush pmu default_content $DEFAULT_CONTENT -y
fi

# Re-enable Big Pipe & Honeypot.
./vendor/bin/drush en honeypot big_pipe -y

exit $behat_exit

