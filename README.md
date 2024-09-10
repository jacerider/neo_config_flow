## INTRODUCTION

Provides a unified config workflow that prevents accidental or unexpected
config changes.

## REQUIREMENTS

This module requires drupal/config_ignore, drupal/config_readonly,
drupal/config_split and Drupal core.

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION

Add to settings.local.php.

```php
/**
 * Config Split.
 *
 * Enable dev config split if available.
 */
$config['config_split.config_split.dev']['status'] = TRUE;
```
