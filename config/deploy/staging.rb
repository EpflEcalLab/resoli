# staging.quartiers-solidaires.ch
set :deploy_to, '/home/quartiers-solidaires/www/staging.quartiers-solidaires.ch'

# set a branch for this release
set :branch, 'dev'

# Protect the staging with a password
set :http_auth_users, [
   [ "quartiers-solidaires", "$apr1$vHMguZuD$ZD0IeqhM0Ioypda9rIdf./" ]
]

# Override the pull configuration to get all translations from Loco.
set :loco_pull, {
  languages: ['fr', 'de', 'en'],
  status: 'all',
}

before "styleguide:build_local", "httpauth:protect"
before "styleguide:deploy_build", "styleguide:build_local" unless ENV['CI']

# Map php command in order to change PHP version from "default" setup-one
# See https://help.alwaysdata.com/fr/langages/php/probl%C3%A8mes-fr%C3%A9quents/#utiliser-diff%C3%A9rentes-versions-en-ssh
set :php, '/usr/bin/php'
# set :php, '/usr/bin/php8.0 -c /home/quartiers-solidaires/admin/config/php/php-497349.ini'
SSHKit.config.command_map[:php] = -> { fetch(:php, 'php') }

# Map composer and drush commands
# NOTE: If stage have different deploy_to
# you have to copy those line for each <stage_name>.rb
# See https://github.com/capistrano/composer/issues/22
SSHKit.config.command_map[:composer] = -> { fetch(:php, 'php') + ' ' + shared_path.join('composer.phar').to_s }
SSHKit.config.command_map[:drush] = -> { fetch(:php, 'php') + ' ' + release_path.join('vendor/bin/drush').to_s }
