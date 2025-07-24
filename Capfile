# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'

# Install Capistrano Git plugin that replace scm settings
require "capistrano/scm/git"
install_plugin Capistrano::SCM::Git

# Composer is needed to install drush on the server
require 'capistrano/composer'

require 'capistrano/antistatique'
require 'capistrano/antistatique/drupal/loco'
require 'capdrupal'

# Load custom tasks from `lib/capistrano/tasks` if you have any defined
Dir.glob('config/capistrano/tasks/*.rake').each { |r| import r }
