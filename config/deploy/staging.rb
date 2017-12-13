# staging.quartiers-solidaires.ch
set :deploy_to, '/home/quartiers-solidaires/www/staging.quartiers-solidaires.ch'

# set a branch for this release
set :branch, 'dev'

# Protect the staging with a password
set :http_auth_users, [
   [ "quartiers-solidaires", "$apr1$vHMguZuD$ZD0IeqhM0Ioypda9rIdf./" ]
]

# Disable Notification on slack
set :slackistrano, false

before "styleguide:build_local", "httpauth:protect"
before "styleguide:deploy_build", "styleguide:build_local"

# Map composer and drush commands
# NOTE: If stage have different deploy_to
# you have to copy those line for each <stage_name>.rb
# See https://github.com/capistrano/composer/issues/22
SSHKit.config.command_map[:composer] = shared_path.join("composer.phar")
SSHKit.config.command_map[:drush] = shared_path.join("vendor/bin/drush")
