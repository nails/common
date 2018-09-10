# Overview

## Index

- [What is Nails](#what-is-nails)
- [What are components](#what-are-components)
- [Where do you get components](#where-do-you-get-components)
- [Anatomy of an application](#anatomy-of-an-application)
- [Anatomy of a component](#anatomy-of-a-component)


## What is Nails

Nails is an open-source PHP framework. Super quick and easy to install and work with, it contains numerous handy modules
to suit the most common use cases, and leverages the [Composer](http://getcomposer.org) ecosystem in order to bring
about advanced additional functionality.

Nails is **not** an out-of-the-box, plug-and-play CMS, blogging platform or shopping platform (like WordPress, Drupal or
ExpressionEngine); it is a tool for developers, allowing them to build any type of site efficiently and effectively
without coercing a tool that doesn't quite fit their needs.

Nails sits on top of the also open-source framework CodeIgniter, as well as utilising components from Symfony; its goal
is to make developing powerful websites simple by focusing on innovation over repetition (i.e not re-inventing the
wheel), all whilst giving the developer a huge amount of flexibility so as to meet the needs of the business. In
addition, all user-facing interfaces are built with user experience in mind to make managing the content a pleasure, not
a chore.

Out of the box, Nails does very little besides managing the essentials (like session management, admin, logging, error
catching, routing and a whole bunch of handy helpers and tools) - a deliberate decision in order to keep the operating
footprint as low as possible. Its real power comes through the use of the powerful component system (which leverages
Composer), allowing complete chunks of functionality to be brought in so time and money can be spent on building the
“important stuff”.

Nails is a young framework, but one which is actively developed with features, fixes and updates being pushed almost
every day.


## What are components

Summarised, components are simply packages which are pulled in via Composer. What makes them a _Nails_ component is the
presence of the `extra.nails` property in the package's `composer.json` file. The existence of this property allows the
Nails loader to auto-discover the component, and can also used to configure various other aspects of the component.

Generally speaking, components bring about additional, generic functionality to Nails apps; this can be full blown
front-end GUIs, admin controllers, or simply a service which exposes functionality. For example:

- The CMS component brings about admin interfaces which allow the user to generate static pages, manage menus, create
  snippets as well as a front end mechanic for rendering these.
- The PDF component provides a service for creating PDFs, saving them to the CDN, or streaming them to the browser.


## Where do you get components

Nails is distributed via Composer so it is natural for components to be delivered this way, too.

Official components, as well as any which are intended to be used by the wider community, can be found on
[Packagist](http://packagist.org). While this is the preferred method of distribution, it is also perfectly acceptable
to distribute using your own VCS - if Composer can see it, it can be used.


## Non-distributed components

Sometimes it is necessary to define some code as a component but not necessarily distribute it publicly. For example, an
application might use non-distributed components in order to provide drivers for compatibility with various providers
(e.g. a payment provider); in order to allow the module to use a proprietary driver (which shouldn't be public) the app
can define a component at `application/components`. Components defined here function in exactly the same way as Composer
installed components (and have no special preference). The only difference is instead of a `composer.json` file the
loader will look for a `config.json` file; this file should contain a JSON object which is the equivalent of what would
be found at `extra.nails` in `composer.json`.

Examples are often easier to understand; the following are equivalent:

**Composer distributed package**

```
Location: /vendor/my-vendor/my-module/json

{
    [...]
    "extra":
    {
        "nails" :
        {
            "name": "My Module",
            "type": "module"
        }
    },
    [...]
}

```

**Application bundled package**

```
Location: /application/components/my-module/config.json

{
    "name": "My Module",
    "type": "module"
}

```


## Anatomy of an application

The basic directory structure of an application looks like this:

```
application/                //  Contains most of the app's business logic.
    ↳ cache/                //  Should be used for temp storage, the `DEPLOY_CACHE_DIR` constant points here.
    ↳ config/               //  Contains various configuration files; it is mainly used by CodeIgniter
        ↳ routes.php        //  The routing file
    ↳ helpers/              //  Helpers directory
    ↳ logs                  //  The Log directory
    ↳ migrations/           //  Where database migrations are stored
    ↳ modules/              //  Where modules are stored
        *moduleName*/
            ↳ controllers/
            ↳ views/
    ↳ services/
        ↳ services.php      //  The application's services definition
assets/                     //  Where front end assets are stored
config/                     //  Where the app configurations are stored
    ↳ app.php
    ↳ deploy.php
src/                        //  Where services, models and other PSR-4 auto-loadable classes are stored
    ↳ Controller/
    ↳ Model/
composer.json               //  The application's composer dependencies
```

## Anatomy of a component

A typical component looks like this:

```
composer.json       //  The component's composer file
src/                //  Services, Models, etc which can be loaded
services/
    ↳ services.php  //  The application's services definition
*moduleName*/       //  If the module provides controllers and/or views, they are in here
    ↳ controllers/
    ↳ views/
```
