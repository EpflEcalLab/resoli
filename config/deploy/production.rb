# www.resoli.ch
set :deploy_to, '/home/quartiers-solidaires/www/www.resoli.ch'

# set a branch for this release
set :branch, 'master'

before "styleguide:deploy_build", "styleguide:build_local" unless ENV['CI']

# Map php command in order to change PHP version from "default" setup-one
# See https://help.alwaysdata.com/fr/langages/php/probl%C3%A8mes-fr%C3%A9quents/#utiliser-diff%C3%A9rentes-versions-en-ssh
set :php, '/usr/bin/php'
SSHKit.config.command_map[:php] = -> { fetch(:php, 'php') }

# Map composer and drush commands
# NOTE: If stage have different deploy_to
# you have to copy those line for each <stage_name>.rb
# See https://github.com/capistrano/composer/issues/22
SSHKit.config.command_map[:composer] = -> { fetch(:php, 'php') + ' ' + shared_path.join('composer.phar').to_s }
SSHKit.config.command_map[:drush] = -> { fetch(:php, 'php') + ' ' + release_path.join('vendor/bin/drush').to_s }
