# Console Commands
> Documentation is a WIP.


The following console tools are provided by this module and can be executed using the [Nails Console Tool](https://github.com/nailsapp/module-console).


| Command             | Description                                                                   |
|---------------------|-------------------------------------------------------------------------------|
| `install`           | Generates the config files and assists with the database set up               |
| `make:controller`   | Creates a new app controller                                                  |
| `make:model`        | Creates a new app model along with its associated table and admin panel       |
| `make:db:migration` | Creates a new database migration, leading on from the previous, if any        |
| `db:migrate`        | Runs any outstanding database migrations on the app and all installed modules |
| `db:seed`           | Runs any seeders which are provided by the app                                |
| `test`              | Runs unit tests                                                               |
| `migrate`           | Alias to `db:migrate`                                                         |
| `routes:rewrite`    | Regenerates the routes file                                                   |


## Command Documentation



### `install [<componentName>]`

This command provides an interactive interface for building the app's config files as well as an interface for installing new modules. It will also run database migrations once complete to ensure the application is up to date.

#### Arguments & Options

| Argument      | Description                      | Required | Default |
|---------------|----------------------------------|----------|---------|
| componentName | The component to install, if any | no       | null    |



### `make:controller [<controllerName>] [<methods>]`

Interactively generates new app controllers and associated view files.

#### Arguments & Options

| Argument       | Description                                       | Required | Default |
|----------------|---------------------------------------------------|----------|---------|
| controllerName | The controller name. Specify multiples using CSV  | no       | null    |
| methods        | A comma separated list of method names to include | no       | index   |



### `make:model [options] [--] [<modelName>]`

Interactively generates new app models and associated table and admin controller.

#### Arguments & Options

| Argument  | Description                                  | Required | Default |
|-----------|----------------------------------------------|----------|---------|
| modelName | The model name. Specify multiples using CSV  | no       | null    |

| Option       | Description                         | Required | Default |
|--------------|-------------------------------------|----------|---------|
| --skip-db    | Skip database table check/creation  | no       | false   |
| --skip-admin | Skip admin controller creation      | no       | false   |



### `make:db:migration`

Creates a new database migration, leading off from the previous, if any.


### `db:migrate [options] [--]`

Runs database migrations on the app, Nails and any installed modules. Will use the database credentials provided by the app unless specifics are passed in via the options.

#### Arguments & Options

| Option   | Description                   | Required | Default |
|----------|-------------------------------|----------|---------|
| --dbHost | The database host to use      | no       | null    |
| --dbUser | The database username to use  | no       | null    |
| --dbPass | The database password to use  | no       | null    |
| --dbName | The database name to use      | no       | null    |



### `db:seed [<component>] [<class>]`

Seed the database with content using seeders provided by the app or installed modules.

#### Arguments & Options

| Argument  | Description                                             | Required | Default |
|-----------|---------------------------------------------------------|----------|---------|
| component | Which component to seed from                            | no       | null    |
| class     | The seed class to execute. Specify multiples using CSV  | no       | null    |



### `test`

Run unit tests.



### `test`

Alias of `db:migrate`.



### `routes:rewrite`

Regenerate the app's routes file.
