# www.resoli.ch
set :deploy_to, '/home/quartiers-solidaires/www/www.resoli.ch'

# set a branch for this release
set :branch, 'master'

before "styleguide:deploy_build", "styleguide:build_local" unless ENV['CI']

# Map composer and drush commands
# NOTE: If stage have different deploy_to
# you have to copy those line for each <stage_name>.rb
# See https://github.com/capistrano/composer/issues/22
SSHKit.config.command_map[:composer] = -> { shared_path.join('composer.phar') }
SSHKit.config.command_map[:drush] = -> { release_path.join('vendor/bin/drush') }
