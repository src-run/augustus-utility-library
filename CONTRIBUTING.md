# Contributing

When contributing, ensure the following list of requirements have been completed
prior to submission of pull requests.

## A. PHP CS Fixer

Before submitting your code, it is important that its style matches that which
is already used within this project. To achieve this,
[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) is used.

### Download and Setup

The most current release of *PHP CS Fixer* can be included in your project using
composer or downloaded directly.

#### As Composer Dependency

If using composer, add `fabpot/php-cs-fixer` to your project's `composer.json`
file and update the project's vendor dependencies using the `composer` CLI.

*Note: For the purposes of this document we will assume installation using
the __below detailed system-wide method__ and not this composer method.*

#### As System-Wide Executable

Installing `php-cs-fixer` as a system-wide executable saves time and removes
redundant `require-dev` entries with each project's `composer.json`. To use
this method, following the below steps.

__Step 1__: Ensure the entry for `phar.readonly` is set to `Off` within the
PHP CLI SAPI config. The following will ensure `phar.readonly` is set correctly.

```bash
sudo sed -i '/.*phar.readonly.*/c\phar.readonly = Off' $(php -i | grep "Loaded Configuration File" | grep -oP '[^\s]+ini')
```

__Step 2__: Build PHP-CS-Fixer and move executable to global location.

```bash
bash <(curl -s https://raw.githubusercontent.com/src-run/usr-src-runner/master/scripts/get-php-cs-fixer)
sudo chmox +x php-cs-fixer
sudo mv php-cs-fixer /usr/loca/bin/php-cs-fixer
```

### Configure and Run

When running `php-cs-fixer`, there are a collection of "levels" and "fixers" one
can enable or disable to achieve the expected formatting. For the purpose of
all `SR\` namespaced code, the `level` must be set to `symfony` and the
`filters` must disable `empty_return`. To `php-cs-fixer` against your code, use
the following.

```bash
php-cs-fixer fix path/to/[src|lib] --level=symfony --fixers=-empty_return
```

## B. File-Level Doc-Blocks

All files must contain a file-level doc-block using the following template.

```php
/*
 * This file is part of the `<package-name>` project.
 *
 * (c) Rob Frawley 2nd <rmf@src.run>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
```

## B. Contributor Attributions

Any desired contributor attribution must be added *at the class or method
level* using the PHPDocumentor `@author` tag. For example, a class-level
attribution would use the following template.

```php
/**
 * Class <class-name>.
 *
 * @author [Contributor Name] <[contributor@email]>
 */
class ClassName
{
    // ...
}
```

# Pull-Requests Welcome

With the above requirements met, submit your pull requests to the respective
project repository for inclusion.

