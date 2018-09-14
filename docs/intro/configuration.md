# Configuration

There are two distinct configuration files: `config/app.php` and `config/deploy.php`; if they don't already exist then
both of these are created when running `nails install`.

## `config/app.php`

This is where global constants are defined which configure the app. This file is committed to version control and the
constants defined in here should apply regardless of the environment in which the app is being run.

Examples include:

- App name
- Timezone
- Database prefix
- Default language


## `config/deploy.php`
This is where global constants which _do_ vary between environments are stored, or if the values are particularly
sensitive/secret. This file is **not** committed to version control and it is expected that your deployment process will
create this file and populate it with the correct variables.

Examples include:

- Base URL
- Database credentials
- Email credentials
- API keys

When choosing which file a particular constant should go consider whether it needs to change between your local,
staging, or production environments. If it's consistent throughout (e.g the app's name) then it belongs in `app.php`; if
it might change between staging and production (e.g. the Base URL) then it belongs in `deploy.php`.

Another consideration is whether a value might change between *machines* in a multi-server environment. If this is the
case then `deploy.php` should be used as your deployment process can place the appropriate configs per machine (e.g a
machine identifier for an external logging system).
