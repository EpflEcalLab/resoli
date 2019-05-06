#!/bin/sh
#
# Upload logs folder(s) from Codeship to AWS S3.
#
# This is essentially used to push debug file of the current build on Amazon S3
# to help developers debugging applications crash or fails.
#
# Author: Kevin Wenger
# Depends on https://github.com/travis-ci/artifacts
#
# Run as `./artifacts.sh`

ARTIFACTS_INSTALL="${HOME}/cache/artifacts-install"
export ARTIFACTS_DEST="${HOME}/cache/artifacts"

# To be configured in the CI.
export ARTIFACTS_KEY=${ARTIFACTS_KEY:=""}
export ARTIFACTS_SECRET=${ARTIFACTS_SECRET:=""}
export ARTIFACTS_BUCKET=${ARTIFACTS_BUCKET:="codeship-artifact"}
export ARTIFACTS_REGION=${ARTIFACTS_REGION:="eu-west-1"}

# Artifact Configurations - Change it to fit your needs.
export ARTIFACTS_LOGS=${ARTIFACTS_LOGS:="./log/behat"}

# Download Artifacts.
wget --continue --output-document "${ARTIFACTS_INSTALL}" "https://raw.githubusercontent.com/travis-ci/artifacts/master/install"
# Make it executable & install Artifacts.
chmod +x "${ARTIFACTS_INSTALL}"
"${ARTIFACTS_INSTALL}"

# Upload to Amazon S3.
"${ARTIFACTS_DEST}" upload \
  --target-paths "${CI_REPO_NAME}/${CI_BUILD_NUMBER}/log" \
  "${ARTIFACTS_LOGS}"

