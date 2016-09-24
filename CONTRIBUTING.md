
# Contributing

Made some local modifications that might benefit upstream and have a
motivation to contribute back to this project? Great! Before opening a
PR (pull request), though, be sure the code style requirements standards
described in this file have been met. This will expedite your PR and
ensure a seamless experience in pushing your code back upstream.

## A. Code Style

All projects must adhere to strict code style requirements. Ensuring
your PR meets these requirements is now easier than ever thanks to 
the excellent code styling auto-correcting CLI tool
[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).

### Get Development Deps

You no longer have to download PHP CS Fixer yourself or have it pre-
installed globally on your system. Instead, it's a composer development
dependency. If you don't already have the development dependencies
installed, simply run (assuming you have composer installed globally on
your system):

```bash
composer install --dev
```

### Run PHP CS Fixer

Thanks to the recently added `.styleci.yml` (a style rule-set config
files) and `.php_cs.dist` (a php-cs-fixer bridge to read `.styleci.yml`
for its config) you do not have to provide any configuration parameters
to PHP CS Fixer. Simply enter the repository root and run:

```bash
bin/php-cs-fixer fix ./
```

This ensures all code style rules are implemented and auto corrects any
inconsistencies. It also forcibly sets the PHP file-level doc-blocks,
which may remove any attributes you set if they were in the file-level
php doc-block. Don't fret: the next section describes acceptable 
attribution methods (if you require such).

## B. Attribution

Attribution for yourself is 100% optional, but in some cases people
prefer an explicit doc-block `@author` attribution for all classes or
methods they wrote entirely themselves. If this is the case, the only
acceptable places to add attribution tags is in a *class doc-block* or 
a *method doc-block*. The below example show how to implement atribution
for both these cases.

```php
/**
 * ...
 * File-level doc-block goes here automatically...
 * ...
 */

/**
 * A description of the purpose of this class.
 *
 * @author CONTRIBUTOR_NAME <CONTRIBUTOR_EMAIL>
 */
class ReallyCoolContributedClass
{
    // ...

    /**
     * A description of the purpose of this method.
     *
     * @author CONTRIBUTOR_NAME <CONTRIBUTOR_EMAIL>
     */
    public function myWonderfulMethod()
    {
        // ...
    }
    
    // ...
}
```

## C. Submit PR

After completing the above requirements (items **A.** and **B.**) your
code is primed and ready to be accepted upstream. Go ahead and open a
PR: we appreciate your time and contribution to this project, however
small or large!
