# Command Line Tool

The Nails Command Line Tool is the easiest way to manage basic, repetitive aspects of a Nails Application. It is
distributed via [Homebrew](http://brew.sh). This command line utility provides some basic bootstrapping functionality
whilst also acting as a gateway to the bundled console application.

The latest installation instructions can be found in the tool's
[README.md](https://github.com/nails/command-line-tool).

## Common Commands

- [Setting up a new Nails project](#setting-up-a-new-nails-project)
- [Configuring a Nails project](#configuring-a-nails-project)
- [Running database migrations](#running-database-migrations)
- ["Making"](#making)
- [Rewrite Routes](#rewrite-routes)

### Setting up a new Nails project

    nails new <projectName>

This command creates a new Nails project; it essentially automates the following actions:

- Download a copy of the [Nails Skeleton repository](https://github.com/nails/skeleton-app)
- Installs all the dependencies (`composer`, `bower`, `npm`)
- Executes the Nails installer (i.e. `nails install`)
- Runs database migrations (i.e. `nails db:migrate`)
- Rewrites routes (i.e. `nails rewrite:routes`)

### Configuring a Nails project

    nails install

This command builds requests/confirms the values for the two config files (`config/app.php` and `config/deploy.php`).

### Running database migrations

    nails db:migrate

This command will run database migrations, bringing the database up tod ate across the application and all installed components.

### "Making"

    nails make:*

Many modules provide utilities for creating placeholder files (and in some cases database tables or data). Using these
builders means you are adding to the application in the correct way, and you can save time by using default values.

### Rewrite Routes

    nails rewrite:routes

This command will rebuild the generated routes (stored in `application/cache/routes_app.php`)
