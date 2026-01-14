# Quartiers Solidaires
Drupal 10 powered.

[![CI](https://github.com/antistatique/quartiers-solidaires/actions/workflows/ci.yml/badge.svg)](https://github.com/antistatique/quartiers-solidaires/actions/workflows/ci.yml)

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

```bash
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
docker compose build --pull
docker compose up --build -d
docker compose exec dev docker-as-drupal bootstrap
```

### When it's not the first time

```bash
docker compose build --pull
docker compose up --build -d
docker compose exec dev drush cr (or any other drush command you need)
```

#### reCaptcha configurations

To protect the subscription form, we decided to use reCaptcha from Google.
To be used properly, you need to setup both `` and `` keys in your `settings.php`.

As the recipient for the contact form in a property details page is not the agent, you should set the value(s) here.

```php
/**
 * reCaptcha by Google credentials.
 */
$config['recaptcha.settings.yml']['site_key'] = 'RECAPTCHA-SITE-KEY';
$config['recaptcha.settings.yml']['secret_key'] = 'RECAPTCHA-SECRET-KEY';
```

#### Symfony Mailer Liter

We use Symfony Mailer Liter to manager the Mail Transport.
For this project, we use Mailjet *SMTP* on both production & staging servers and we use a local *SMTP* server for development.

```php
/**
 * The Symfony Mailer transporter.
 *
 * @var string
 */
$config['symfony_mailer_lite.settings']['default_transport'] = 'smtp';
$config['symfony_mailer_lite.symfony_mailer_lite_transport.smtp']['configuration']['host'] = 'localhost';
$config['symfony_mailer_lite.symfony_mailer_lite_transport.smtp']['configuration']['port'] = '25';
```

### (optional) Get the productions files and database

Local Drupal site files directory is mounted in Docker dev container, you can sync them with
production by using capistrano tasks.

```bash
bundle exec cap production files:download
bundle exec cap production files:dump
docker compose exec dev docker-as-drupal db-restore --file=/var/www/web/sites/default/files/production_dump.sql
```

### Docker help & tips

```bash
docker compose exec dev docker-as-drupal --help
```

## 🚔 Check Drupal coding standards & Drupal best practices

You can read more about it in our [CONTRIBUTING section](./CONTRIBUTING.md).

## 🚛 Install localy

Refer to Docker project bootstrap section for more information how install with docker, but in resume
you must run `docker compose exec dev docker-as-drupal bootsrap` command.

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

Or on Docker environement:

  ```bash
  docker compose up -d --build dev test
  docker compose exec dev docker-as-drupal db-update
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

###Build on Docker

>ou can build locally without any issue, but use following command to do it in Docker environment.

  ```bash
   docker compose exec dev yarn build
  ```

## 🧩 Frontend Libraries

- [node-autocomplete](web/themes/quartiers_solidaires/libs/node-autocomplete/README.md)

## 🚀 Deploy
The deployment is managed By Capistrano. See [the recipes](./config/deploy.rb).

### First time

  ```bash
    # You need to have ruby & bundler installed
   $ bundle install
  ```

### Each times

We use Capistrano to deploy:

  ```bash
  $ bundle exec cap -T
  $ bundle exec cap staging deploy
  ```

## 🏆 Tests

Every tests should be run into the Docker environment.

1. Run a shell on your Docker test env.

```bash
docker compose exec test bash
```

1. Once connected via ssh on you Docker test, you may run any `docker-as-drupal` commands

```bash
docker-as-drupal [behat|phpunit|nightwatch]
```

You also may use the direct access - whitout opening a bash on the Docket test env. using:

```bash
docker compose exec test docker-as-drupal [behat|phpunit|nightwatch]
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
./scripts/bootstrap/drupal.sh [--skip-dependencies=1] [--skip-default=1] [--database=DATABASE_URL] [--skip-interaction=1]
```

1. Then you can run functional tests

```bash
./vendor/bin/behat
```

## 📋 Documentations

Customs modules:

 - [Quartiers-Solidaires - Badges](./web/modules/custom/qs_badge/README.md)

### 🌍 i18n

We use [Localise.biz](https://localise.biz) (Loco) to translate the interface. The translation PO file is automatically pushed to Loco when deploying to staging. This means that **the PO file must contain ALL custom translation string of the projet**.

- **Developers** should never forget to add a translation string to the PO file as soon as they create the new key.
- Translations should be marked as **fuzzy** in the PO file.
- **Project managers** should be aware of all the new translation strings and that these will be the ones in the staging *AND* production environments.
- **Clients** should take it upon themselves to translate the keys in the required languages.

## How to translate keys?

Log into [localise.biz/antistatique](https://localise.biz/antistatique) and make sure you have the correct role to work on the Vevey project.

In the project page [localise.biz/antistatique/resoli](https://localise.biz/antistatique/resoli), you have access to a "Manage" and a "Translate" tabs.

Use the Translate tab and show the "Asset ID" and "Context" columns to work with the translation keys. Select the language you want to translate to and you're done!

- Don't forget to set the translation status to a correct state (Fuzzy, translated, untranslated, etc)
- Don't forget to save once you're done

## What to do if a translation string is missing?

1. **Notify the nearest developer about this.**
1. Add the asset in the "Manage" section of the Loco project: use the "Asset ID" field to write the translation key (e.g. `resoli.title`) and save.
1. You can now translate the asset in any language.
1. The next cron job will update the string in the website.
1. Once the developers have updated the file, it will get pushed to Loco.

*You can also use the User Interface Translation page from the Drupal admin (`/admin/config/regional/translate`) to set a value temporarily, but be aware that it will get overwritten on the next deployment.*

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

### Bootstrapping via Docker crash

```
ERROR: for kudelski_dev_1  Cannot start service dev: driver failed programming external connectivity Creating kudelski_test_1    ... done
starting userland proxy: Bind for 0.0.0.0:8080 failed: port is already allocated
```

```
ERROR: for dev  Cannot start service dev: driver failed programming external connectivity on endpoint kudelski_dev_1 (62326d0a5590025826f90f9b22f43b809853f40df9dd3955b973868a44328ec4): Error starting userland proxy: Bind for 0.0.0.0:8080 failed: port is already allocated
ERROR: Encountered errors while bringing up the project.
```

You have to update the docker-compose.override.yml to change the port binding. It seems you already use the port 8080
on your computer. (use docker-compose.override-example.yml as defailt)

```yaml
services:
  # Drupal development server
  dev:
    image: antistatique/php-dev:7.2-node11
    ports:
      - "8081:80"
```

### Drush give me an error "Missing scheme in URL ''"
It seems you don't use an old-school local environment but Docker.

Read section about setup Docker, then, you have to prepend every command with `docker compose exec dev` to
run them on the Docker environment OR open a shell in docker using `docker compose exec dev bash`.

## 💻 Drush Commands

## 🕙 Crons

### Crontab
