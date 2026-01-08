set :application, 'quartiers-solidaires'
set :repo_url, 'git@github.com:antistatique/quartiers-solidaires.git'

server 'ssh-quartiers-solidaires.alwaysdata.net', user: 'quartiers-solidaires', roles: %w{app db web}

set :app_path, "web"
set :config_path, "config/d8/sync"
set :theme_path, "themes/quartiers_solidaires"
set :build_path, "build"
set :deploy_libraries, [
  {
    src: "web/themes/quartiers_solidaires/libs/node-autocomplete",
    build: "web/themes/quartiers_solidaires/libs/node-autocomplete/build"
  }
]

# Link file settings.php & drushcr.php
set :linked_files, fetch(:linked_files, []).push("#{fetch(:app_path)}/sites/default/settings.php", "#{fetch(:app_path)}/sites/default/drushrc.php")

# Link dirs files and private-files
set :linked_dirs, fetch(:linked_dirs, []).push("#{fetch(:app_path)}/sites/default/files")

# Default value for :pty is false
# set :pty, true

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
set :log_level, :debug

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
set :keep_releases, 3

# Default value for keep_backups is 5
set :keep_backups, 3

# Loco Translate settings
set :loco_po_sync_lang, 'fr'
# Loco Translate path to template file from the root.
set :loco_po_file, './config/languages/loco-fr.po'

set :ssh_options, {
  forward_agent: true
}

# Loco Push Translate settings.
set :loco_push, {
  po_sync_lang: 'fr',
  po_file: './config/languages/loco-fr.po',
}

# Loco Pull Translate settings.
set :loco_pull, {
  languages: ['fr', 'de', 'en'],
  status: 'translated',
}

# Used only if composer.json isn't on root
# set :composer_working_dir, -> { fetch(:release_path).join(fetch(:app_path)) }

# Remove default composer install task on deploy:updated
# Rake::Task['deploy:updated'].prerequisites.delete('composer:install')
# Rake::Task['deploy:updated'];

namespace :deploy do
  # Ensure everything is ready to deploy.
  after "deploy:check:directories", "drupal:db:backup:check"

  # Backup the database before starting a deployment and rollback on fail.
  after :updated, "drupal:db:backup"
  # before :failed, "drupal:db:rollback"
  before :cleanup, "drupal:db:backup:cleanup"

  after :updated, "deploy:styleguide:libs:build" unless ENV['CI']
  after :updated, "styleguide:deploy_build"
  after :updated, "deploy:styleguide:libs:deploy"

  # Set the maintenance Mode on your Drupal online project when deploying.
  after :updated, "drupal:maintenance:on"

  # Must updatedb before import configurations, E.g. when composer install new
  # version of Drupal and need updatedb scheme before importing new config.
  # This is executed without raise on error, because sometimes we need to do drush config-import before updatedb.
  after :updated, "drupal:updatedb:silence"

  # Remove the cache after the database update
  after :updated, "drupal:cache:clear"
  after :updated, "drupal:config:import"

  # Update the database after configurations has been imported.
  after :updated, "drupal:updatedb"

  # Clear your Drupal cache.
  after :updated, "drupal:cache:clear"

  # Sync translations with Loco.
  after :updated, "drupal:loco:push"
  after :updated, "drupal:loco:pull"
  after :updated, "drupal:cache:clear"

  # Disable the maintenance on the Drupal project.
  after :updated, "drupal:maintenance:off"

  # Ensure permissions are properly set.
  after :updated, "drupal:permissions:recommended"
  after :updated, "drupal:permissions:writable_shared"

  # Fix the release permissions (due to Drupal restrictive permissions)
  # before deleting old release.
  before :cleanup, "drupal:permissions:cleanup"

  namespace :styleguide do
    namespace :libs do
      task :build do
        desc 'Build standalone libraries'
        run_locally do
          fetch(:deploy_libraries).each do |standalone_library|
            within standalone_library[:src] do
              info "Build locally: \e[35m#{standalone_library[:src]}\e[0m"
              execute 'yarn', '--check-files', '--no-progress', '--silent'
              execute 'yarn', 'build', '--production'
            end
          end
        end
      end

      task :deploy do
        desc 'Deploy standalone libraries'
        on roles(:web) do
          fetch(:deploy_libraries).each do |standalone_library|
            from = standalone_library[:build];
            to = release_path.join(from)
            info "Upload from local: \e[35m#{from}\e[0m to remote \e[35m#{to}\e[0m"
            upload! from, to, recursive: true
          end
        end
      end
    end
  end
end
