# Quartiers Solidaires
Drupal 8 powered.

| Staging CodeShip-CI | Prod CodeShip-CI |
|:-------------------:|:----------------:|
| [ ![Staging CodeShip-CI](https://app.codeship.com/projects/fa0ca830-4aa8-0135-46f5-7acfff03633b/status?branch=dev)](https://app.codeship.com/projects/232628) | [ ![Prod CodeShip-CI](https://app.codeship.com/projects/fa0ca830-4aa8-0135-46f5-7acfff03633b/status?branch=master)](https://app.codeship.com/projects/232628) |


## 🔧 Prerequisites

First of all, you need to have the following tools installed globally on your environment:

  * docker
  * composer
  * drush
  * npm
  * yarn

you can only install docker, but yarn is recommended if you work actively on styleguide.
If you don't use docker as environment, don't forget to add bins to your path such:

  * php
  * mysql

### Tips

To run any drush command, you need to be on a hight bootstrapped drupal directory, such `/web`.

  ```bash
  $ cd /web
  ```

On common errors, see the Troubleshootings section.

## 🐳 Docker

### Project setup

````bash
cp docker-compose.override-example.yml docker-compose.override.yml
```

Update any values as needed, example when you already use the 8080 port:

```yaml
  # Drupal development server
  dev:
    ports:
      - "8081:80"
```

Another example when you already have a local MySQL server using port 3306:

```yaml
  # Database
  db:
    ports:
      - "13306:3306"
```

### Project boostrap

Build project imgaes (and pull recent development image), start docker services, then run
drupal bootsrap script (get a coffee, this will take some time...).

```bash
docker-compose build --pull
docker-compose up --build -d
docker-compose exec dev docker-as-wait --mysql -- docker-as-drupal bootstrap
```

### (optional) Get the productions files and database

Local Drupal site files directory is mounted in Docker dev container, you can sync them with
production by using capistrano tasks.

```bash
bundle exec cap production files:download
bundle exec cap production files:dump
docker-compose exec dev docker-as-drupal db-restore --file=/var/www/web/sites/default/files/production_dump.sql
```

### Docker Tips

```bash
docker-compose exec dev docker-as-drupal --help
```

Only directories like custom modules, styleguide, and config related are mounted in Docker
container (dev container have files too). If you need to rebuild the image and container, you can
use `up` command again with the service to update.

```bash
docker-compose up -d --build --no-deps dev
```

You can run tests in docker.

```bash
docker-compose exec test docker-as-drupal behat
docker-compose exec test docker-as-drupal phpunit
```

You can reset database, and optionnaly load default content.

```bash
docker-compose exec dev docker-as-drupal db-reset --with-default-content
```


## 🏋️ Export/Import all translations to a PO file

This project don't use the traditionnal translates strings in English.
We move to a flexible solution which is not language based but keys strings Eg. `form.submit`

To allow the client to update the translation him/herself, you need to export all the custom translations from our modules & templates by running our custom extractor.

### Prerequisites

  * gettext & xgettext
  * xargs & gxargs

1. Install `gettext`

  ```bash
  $ brew install gettext
  $ brew link gettext
  ```

1. Install `xargs`

  ```bash
  $ brew install findutils --with-default-names
  ```

### Export

  ```bash
  $ ./scripts/trans-extractor/run
  ```

The PO file is created in the `./config/d8/lang/` directory.

Once done, don't forget to add the new assets on the Loco project.

### Import

When importing in the [Drupal admin interface](admin/config/regional/translate/import) don't forget to check the 2 following checkboxes:

- Overwrite non-customized translations
- Overwrite existing customized translations

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
  $ ./vendor/bin/phpmd ./web/modules/custom text ./phpmd.xml --suffixes php,module,inc,install,test,profile,theme,css,info,txt
  ```

Copy/Paste Detector

  ```bash
  $ ./vendor/bin/phpcpd ./web/modules/custom --names=*.php,*.module,*.inc,*.install,*.test,*.profile,*.theme,*.css,*.info,*.txt --names-exclude=*.md,*.info.yml --ansi
  ```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

  ```bash
  $ cat ./scripts/hooks/post-commit >> ./.git/hooks/post-commit
  ```

## 🔥 Behavior Driven Development using Behat

For isolation test databases, you should run Behat using our custom script `scripts/tests/behat.sh`.

### Quick & dirty

1. Launch a stand-alone server with `drush runserver`.

1. Keep this command line open and run `./vendor/bin/behat`
in the root of your project.

### Re-install default values

You can use the Driven Development script to install re-install default values by running:

  ```bash
  ./scripts/tests/behat.sh --skip-dependencies=1 --skip-tests=1 --skip-interaction=1
  ```

## 🚛 Install

1. Setup your virtualhost (like `http://qs.dev`) to serve `/web`.

1. Install Drupal and dependencies using composer

  ```bash
  composer install
  ```

1. Install and configure PHPCS for coding standards, see the previous section.

1. Go to http://qs.dev and follow install instruction
   Or run the following command:

  ```bash
  $ drush si standard --db-url=mysql://root:root@127.0.0.1/qs_staging --site-name="Quartiers Solidaires" --account-name=admin --account-pass=admin --account-mail=dev@antistatique.net
  ```

1. Use the same site UUID than your collegue:

  ```bash
    $ drush config-set system.site uuid "b38de9f3-fd4d-4779-ab7f-29e7b91556f1"
  ```

  (This is certainly a bad idea, [follow this drupal issue](https://www.drupal.org/node/1613424)).

1. Update your `web/sites/default/settings.php`:

  ```bash
  $ vim web/sites/default/settings.php
  ```

  Set the custom configuration directory location:

  ```php
   $config_directories['sync'] = '../config/d8/sync';
  ```

  Set the custom private directory location:

  ```php
   $settings['file_private_path'] = '/privates/qs';
  ```

  Be aware that this new private directory (`'/privates/qs'`) mustn't be served
  by your Apache & the Apache's user need write access on this dir.
  Finaly, this dir should exists on your file system.

  ```shell
  mkdir -p /privates/qs && \
  mkdir -p /privates/qs/photos && \
  chmod -R 700 /privates/qs
  ```

  Sometimes, you will need to `chmod -R 777` according your server conf.

  Enable the Mail rerouting to prevent outgoing mails:

  ```shell
  $config['backerymails.settings']['reroute']['status'] = TRUE;
  $config['backerymails.settings']['reroute']['recipients'] = 'kevin@antistatique.net';
  ```

1. *(optional)* Update your `web/sites/default/drushrc.php`:

  ```bash
  $ cp web/sites/default/default.drushrc.php web/sites/default/drushrc.php
  $ vim web/sites/default/drushrc.php
  ```

  ```php
  $options['uri'] = "http://qs.dev";
  ```

1. Import the configuration

  ```bash
  $ drush cim
  ```

1. Rebuild the cache

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
./scripts/tests/phpunit.sh [-g group] [-x exclude-group]
./scripts/tests/behat.sh
```

### Kernel tests

```bash
./vendor/bin/phpunit -x qs_functional
```

### Browser tests

1. *(optional)* Bootstrap your Drupal if you don't already have a working env.

```bash
./scripts/bootstrap/drupal.sh --private-files="PATH/TO/PRIVATES" [--skip-dependencies=1] [--skip-default=1] [--database=DATABASE_URL] [--skip-interaction=1]
```

1. Then you can run functional tests

```bash
./vendor/bin/phpunit -g qs_functional
```

### Behat

1. *(optional)* Bootstrap your Drupal if you don't already have a working env.

```bash
./scripts/bootstrap/drupal.sh --private-files="PATH/TO/PRIVATES" [--skip-dependencies=1] [--skip-default=1] [--database=DATABASE_URL] [--skip-interaction=1]
```

1. Then you can run functional tests

```bash
./vendor/bin/behat
```

## 📋 Documentations

Customs modules:

 - [Antistatique - Easy Breadcrumb](./web/modules/custom/antistatique/antistatique_easy_breadcrumb/README.md)
 - [Quartiers-Solidaires - Badges](./web/modules/custom/qs_badge/README.md)

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
