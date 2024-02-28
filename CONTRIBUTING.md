CONTRIBUTING
------------

## Check Drupal coding standards & Drupal best practices.

The Drupal and DrupalPractice Standard will automatically be applied following the rules on `phpcs.xml.dist` file.

```bash
./vendor/bin/phpcs
```

Automatically fix coding standards

```bash
./vendor/bin/phpcbf
```

## Analyzer of PHP code to search usages of deprecated functionality using PhpDeprecationDetector.

Analyzer of PHP code to search usages of deprecated functionality in newer interpreter versions

```bash
./tools/php-deprecation-detector/vendor/bin/phpdd --target 8.0 \
--file-extensions php,module,inc,install,test,profile,theme,info \
./web/modules/custom

./tools/php-deprecation-detector/vendor/bin/phpdd --target 8.0 --file-extensions php ./behat
```

## Ensure PHP Community Best Practices using PHP Coding Standards Fixer

It can modernize your code (like converting the pow function to the ** operator on PHP 5.6) and (micro) optimize it.

```bash
./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --format=checkstyle
```

### Catches whole classes of bugs even before you write tests using PHPStan

```bash
./vendor/bin/phpstan analyse ./web/modules/custom ./behat ./web/themes --error-format=checkstyle
```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

  ```bash
  $ cat ./scripts/hooks/post-commit >> ./.git/hooks/post-commit
  ```
