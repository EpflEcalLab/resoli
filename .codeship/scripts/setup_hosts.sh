#!/usr/bin/env bash

set -eux

CMD=""

while [ $# -gt 0 ]; do
  case "$1" in
    --)
      shift
      CMD=("$@")
      break
      ;;
  esac
  shift
done

host test | awk '/has address/ { print  $4 "  resoli.test" }' >> /etc/hosts
cat /etc/hosts

# Run command
if [ ! -z "$CMD" ]; then
  exec "${CMD[@]}"
fi
