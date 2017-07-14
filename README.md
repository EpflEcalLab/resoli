# Quartiers Solidaires
Drupal 8 powered.

[ ![Codeship Status for antistatique/quartiers-solidaires](https://app.codeship.com/projects/fa0ca830-4aa8-0135-46f5-7acfff03633b/status?branch=master)](https://app.codeship.com/projects/232628)

[ ![Codeship Status for antistatique/quartiers-solidaires](https://app.codeship.com/projects/fa0ca830-4aa8-0135-46f5-7acfff03633b/status?branch=dev)](https://app.codeship.com/projects/232628)

## 🔧 Prerequisites

First of all, you need to have the following tools installed globally on your environment:

  * composer
  * drush
  * npm
  * yarn

don't forget to add bins to your path such:

  * php
  * mysql

### Tips

To run any drush command, you need to be on a hight bootstrapped drupal directory, such `/web`.

  ```bash
  $ cd /web
  ```

On common errors, see the Troubleshootings section.

## 🚔 Check Drupal coding standards & Drupal best practices

You need to run composer before using PHPCS. Then register the Drupal and DrupalPractice Standard with PHPCS: `./vendor/bin/phpcs --config-set installed_paths "`pwd`/vendor/drupal/coder/coder_sniffer"`

### Command Line Usage

Check Drupal coding standards:

  ```bash
  $ ./vendor/bin/phpcs --standard=Drupal --colors --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md ./web/modules/custom
  ```

Check Drupal best practices:

  ```bash
  $ ./vendor/bin/phpcs --standard=DrupalPractice --colors --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md ./web/modules/custom
  ```

Automatically fix coding standards

  ```bash
  $ ./vendor/bin/phpcbf --standard=Drupal --colors --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md ./web/modules/custom
  ```

### Improve global code quality using PHPCPD (Code duplication) &  PHPMD (PHP Mess Detector).

Detect overcomplicated expressions & Unused parameters, methods, properties

  ```bash
  $ ./vendor/bin/phpmd ./web/modules/custom text ./phpmd.xml
  ```

Copy/Paste Detector

  ```bash
  $ ./vendor/bin/phpcpd ./web/modules/custom
  ```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

  ```bash
  $ cat ./scripts/hooks/post-commit >> ./.git/hooks/post-commit
  ```

## 🚛 Install

1. Setup your virtualhost (like `http://qs.dev`) to serve `/web`.

2. Install Drupal and dependencies using composer

  ```bash
  composer install
  ```

3. Install and configure PHPCS for coding standards, see the previous section.

4. Go to http://qs.dev and follow install instruction
   Or run the following command:

  ```bash
  $ drush si standard --db-url=mysql://root:root@127.0.0.1/qs_staging --site-name="Quartiers Solidaires" --account-name=admin --account-pass=admin --account-mail=dev@antistatique.net
  ```

5. Use the same site UUID than your collegue:

  ```bash
    $ drush config-set system.site uuid "b38de9f3-fd4d-4779-ab7f-29e7b91556f1"
  ```

  (This is certainly a bad idea, [follow this drupal issue](https://www.drupal.org/node/1613424)).

6. Update your `web/sites/default/settings.php`:

  ```bash
  $ vim web/sites/default/settings.php
  ```

  ```php
    $config_directories['sync'] = '../config/d8/sync';
  ```

  If you want to use the LDAP authentication, you must fill the LDAP settings.

7. *(optional)* Update your `web/sites/default/drushrc.php`:

  ```bash
  $ cp web/sites/default/default.drushrc.php web/sites/default/drushrc.php
  $ vim web/sites/default/drushrc.php
  ```

  ```php
  $options['uri'] = "http://qs.dev";
  ```

8. Import the configuration

  ```bash
  $ drush cim
  ```

9. Rebuild the cache

  ```bash
  $ drush cr
  ```

## After a git pull/merge

  ```bash
  $ drush cr
  $ drush cim
  $ drush updatedb
  $ drush entity-updates
  $ drush cr
  ```

## 🎨 Build the theme

The main styleguide of **Quartiers Solidaires** is inside this project under `themes/quartiers_solidaires/assets/`.
The styleguide is then processed using [Toolbox](https://frontend.github.io/toolbox/).

You first need to setup the work environment by running `$ yarn install`.

You can generate the styleguide and watch it:

  ```bash
    $ yarn start
  ```

You can generate only the built assets for production by running:

  ```bash
    $ yarn build
  ```

For more help about Toolbox, the [official documentation](http://frontend.github.io/toolbox/toolbox/#build-the-styleguide) is your best friend.

## 🚀 Deploy

### First time

  ```bash
    # You need to have ruby & bundler installed
    $ bundle install
    $ npm login
    # enter the dev@antistatique.net npm credentials, ask Antistatique if you don't have these. (they should normally be in 1Password)
    $ npm install -g gulp
  ```

### Each times

We use Capistrano to deploy:

  ```bash
    $ bundle exec cap -T
    $ bundle exec cap staging deploy
  ```

## 🏆 Tests

  ```bash
  $ ./vendor/bin/phpunit
  ```

  For kernel tests you need a working database connection and for browser tests your Drupal installation needs to be reachable via a web server. Copy the phpunit config file:

  ```bash
  $ cp phpunit.xml.dist phpunit.xml
  ```

## 📋 Documentations

Customs modules:

 - [Antistatique - Easy Breadcrumb](./web/modules/custom/antistatique/antistatique_easy_breadcrumb/README.md)

## 🚑 Troubleshootings

### Error while importing config ?

  ```
  The import failed due for the following reasons:                                                                                                   [error]
  Entities exist of type <em class="placeholder">Shortcut link</em> and <em class="placeholder"></em> <em class="placeholder">Default</em>. These
  entities need to be deleted before importing.
  ```

Solution 1: Delete all your shortcuts from the Drupal Admin on [admin/config/user-interface/shortcut/manage/default/customize](admin/config/user-interface/shortcut/manage/default/customize).

Solution 2: Delete all your shortcuts with drush

  ```bash
  drush ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();'
  ```

### How to disable the Drupal Cache for dev ?

The tricks is to add this two lines in your `settings.php`:

  ```php
    // do this only after you have installed the drupal
    $settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
    $settings['cache']['bins']['render'] = 'cache.backend.null';
  ```

A better way is to use the `example.settings.local.php` that do more for your dev environement (think about it like the `app_dev.php` front controller):

 1. Copy the example local file:

  ```bash
  $ cp sites/example.settings.local.php sites/default/settings.local.php
  ```

 2. Uncomment the following line in your `settings.php`

  ```php
  if (file_exists(__DIR__ . '/settings.local.php')) {
    include __DIR__ . '/settings.local.php';
  }
  ```

 3. Clear the cache

  ```bash
  $ drush cr
  ```

### How to enable the Twig Debug for dev ?

 1. Copy the example local file:

  ```bash
  $ cp sites/default/default.services.yml sites/default/services.yml
  ```

 2. set the debug value of twig to `true`

  ```php
  twig.config:
    debug: true
  ```

 3. Clear the cache

  ```bash
  $ drush cr
  ```

[Read More about it](https://www.drupal.org/node/1903374)

### Trouble when runing coding standard validations

  ```bash
  ERROR: the "Drupal" coding standard is not installed. The installed coding standards are MySource, PEAR, PHPCS, PSR1, PSR2, Squiz and Zend
  ```

You have to register the Drupal and DrupalPractice Standard with PHPCS:

  ```bash
    $ ./vendor/bin/phpcs --config-set installed_paths [absolute-path-to-vendor]/drupal/coder/coder_sniffer
  ```

## 💻 Drush Commands

## 🕙 Crons

### Crontab

## 🔐 Security


