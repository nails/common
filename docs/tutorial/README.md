# Tutorial

In this tutorial we'll create a XX application from scratch using Nails. It's purpose is to demonstrate the key building
blocks for Nails and get you familiar with how the various bits and pieces fit together.

The complete project can be viewed [here](#todo) should you want to refer to anything in more detail.

## Index

- [Prerequisites](#prerequisites)
- [The Nails CLI tool](#the-nails-cli-tool)
- [Setting up a new project](#setting-up-a-new-project)
- [Configuring the application](#configuring-the-application)
- [Hello World!](#hello-world)
- [Assets](#assets)
- [Understanding routes](#understanding-routes)
- [Creating a module, controller, and view](#creating-a-module-controller-and-view)
- [Understanding properties, services, models, and factories](#understanding-properties-services-models-and-factories)
- [Understanding migrations](#understanding-migrations)
- [Installing an external component](#installing-an-external-component)

--

## Prerequisites

Nails requires the following:

- PHP 5.6 compiled with Mcrypt Extension (Not yet PHP7 ready)
- A MySQL database
- A Webserver (e.g. Apache or Nginx)


## The Nails CLI tool

The easiest way to get started using Nails is with the Nails CLI tool. This command line utility provides some basic
bootstrapping functionality whilst also acting as a gateway to the bundled console application.

The latest installation instructions can be found in the tool's [README.md](https://github.com/nailsapp/command-line-tool).


## Setting up a new project

Using the CLI tool we can quickly set up a new project, using the Nails skeleton application; this skeleton has an
opinionated workflow, but it is easily changed.

```
cd ~/Sites
nails new nails-tutorial
```

This will create a new directory (named `nails-tutorial`), pull down the skeleton, download dependencies, and then
execute the interactive installer which will prompt you for some configuration details (see
[Configuring the application](#configuring-the-application)).

> Tip: Configure your Virtual Host and database now, you'll need them as the application installs


## Configuring the application

There are two distinct configuration files: `config/app.php` and `config/deploy.php`; if they don't already exist then
both of these are created when running `nails install`.

### `config/app.php`

This is where global constants are defined which configure the app. This file is committed to version control and the
constants defined in here should apply regardless of the environment in which the app is being run.

Examples include:

- App name
- Timezone
- Database prefix
- Default language


### `config/deploy.php`
This is where global constants which _do_ vary between environments are stored. This file is **not** committed to
version control and it is expected that your deployment process will create this file and populate it with the correct
variables.

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


## Hello World!

If you go to the application's URL in the browser you should see the default Nails landing page - congratulations,
everything is up and running and ready to be built upon! If you see an error, or something else, check that everything
has been configured correctly and any error messages which are shown in the terminal are addressed.

> Tip: in the skeleton there is a build file: `build.sh`. Runnig this will do a complete build and is a useful shortcut
> when pulling down changes. Take a look inside it to see exactly what it is doing.

## Assets

Nails, by default, uses the task runner Gulp to compile its CSS and JS. Sass is used for CSS. Read through the Gulp file
to see how the various tasks are configured; read on for a brief summary:

- `gulp css:app` Will compile `assets/sass/app.scss` to `assets/build/app.min.css` (will also auto-prefix and minify)
- `gulp js:app` will concatenate everything in `assets/js/app/` followed by `assets/js/app.js` into `assets/build/js/app.min.js` (will also minify)
- `gulp build` executes both of the above
- `gulp` will watch files for changes and execute the build on save


## Understanding routes

> @todo - complete this section
>
> - where routes are stored
> - how routes work
> - might be worth updating Nails so that all components and the app use the same route writing mechanic


Now that we have a working application we should take a moment to understand routing. Nails is based upon CodeIgniter,
so follows the same routing principles.

The URL segments are broken down into the following:

`/<module>/<controller>/<method>/<any>`

This maps to the following file structure:

`/application/modules/<module>/controllers/<controller>.php::method()`

If a valid controller is found then the method is corresponding to `<method>` is executed. If `<method>` is not
specified, it defaults to `index()`.

> Tip: `<controller>` will default to the value of `<module>` if not defined, so the URL `/module` would map to:
>  `/application/modules/<module>/controllers/<module>.php::index()`

### Customising Routes

In addition, it is possible to specify custom routes so that you have more control over the URL structure. This is done
in `application/config/routes.php`. Customise the `$route` array, e.g.:

```
$route['overridden-route/(.*)'] = 'module/controller/method'
```

This will force a URL pattern matching `overridden-route/(.*)` (note the use of regex) to the equivalent URL
`module/controller/method`


## Creating a module, controller, and view

> @todo - complete this section
>
> - Update the skeleton so that application/controllers and application/views aren't there


## Understanding properties, services, models, and factories

> @todo - complete this section


## Understanding migrations

> @todo - complete this section


## Installing an external component

> @todo - complete this section
