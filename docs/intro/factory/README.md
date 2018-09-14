# Factory

- [Overview](#overview)
- [Services](#services)
- [Models](#models)
- [Properties](#properties)


## Overview

The Nails Factory is a core component within Nails, its focus is to provide a single touch point for loading services,
models, properties, and factories. It utilises Symfony's Pimple library to build containers which return instances on
classes when requested.

The application, and components, inform the Factory which properties, services, models, and factories it provides
through the `services.php` file (located at `application/services/services.php`). This is a PHP file which returns
closures which in turn return instances; Pimple manages whether the closure is called just once (for services and models)
or multiple times (for factories).

**Sample services.php file**

```php
return [
    'properties' => [
        'foo' => 'bar',
    ],
    'services' => [
        'MyService' => function () {
            return new App\Service\MyService();
        },
    ],
    'models' => [
        'MyModel' => function () {
            return new App\Model\MyModel();
        },
    ],
    'factories' => [
        'MyFactory' => function () {
            return new App\Factory\MyFactory();
        },
    ],
];
```

Items are then loaded at runtime using the Factory's static methods:

```php
//  Returns a static value
\Nails\Factory::property($sKey, $sProvider);

//  Returns the same instance every time
\Nails\Factory::service($sKey, $sProvider);

//  Returns the same instance every time
\Nails\Factory::model($sKey, $sProvider);

//  Returns a new instance every time
\Nails\Factory::factory($sKey, $sProvider);
```

Where `$sKey` is the key defined in the `services.php` file and `$sProvider` is the name of the component which provides
the item; for example if the above example is in the app, then you'd load the model like so:

```
$oModel = \Nails\Factory::model('MyModel', 'app');
```

If the above was provided by the CMS module, it would be loaded like this:

```
$oModel = \Nails\Factory::model('MyModel', 'nails/module-cms');
```


## [Services](services.md)

Services are single instance classes which provide specific functionality, usually abstracting something (e.g. a third
party service).


## [Models](models.md)

Models are single instance classes which represent data from the database; usually this is a 1-to1 relationship with a
specific database table.


## [Properties](properties.md)

Properties are specific values which can be used for configuring Services or Models.


## [Factories](factories.md)

Factories are similar to services, but instead of the same instance being returned a new instance is constructed with
each request.
