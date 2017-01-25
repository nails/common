# Events
> Documentation is a WIP.


Your app can subscribe to events triggered by either installed modules or your app easily using the `Event` service.

Load the Event service using the Nails Factory: `Factory::service('Event')`


## Subscribing to Events

Easily subscribe to events using the `subscribe($sEvent, $sNamespace, $mCallback)` method. The event and namespace are case **insensitive**.

    $oEventService = Factory::service('Event');
    $oEventService->subscribe('ITEM.CREATED', 'myvendor/foo', array($this, 'myCallback'));

`$this->myCallback()` will be called each time the `ITEM.CREATED` event is triggered in the `myvendor/foo` namespace.

*Note: You can use whatever namespace you like when using the Event service for app specific events, but for consistency, we recommend you use `App`.*


### Auto-subscribing to events

In most cases it's useful to subscribe to an even very early on, so as to be sure to catch every time it is execute. In some cases, especially when executing routes which don't touch the app, you won't be able to register an event. Enter, autoloading.

When the Event service constructs it looks through all registered components (and finally, `App`) for a class which matches the format `\Namespace\Event`, where `Namespace` is the value provided by the component's `composer.json` (or `config.json` if provided by the app) `extra.nails.namespace` property. It then looks for a public method called `autoload()` and calls it.

`\Namespace\Event->autoload()` should return a multi-dimensional array with each index an array compatible with the `subscribe()` method (where index 0 is the event name, index 1 is the event namespace and index 2 is the callback)

Example `composer.json` for fictional module `module-foo`:

```json
{
    "name": "myvendor/foo",
    "autoload":
    {
        "psr-4": {"MyVendor\\Foo\\": "src/"}
    },
    "extra":
    {
        "nails" :
        {
            "name": "Foo",
            "namespace": "MyVendor\\Foo\\"
        }
    }
}
```

Example Event Handler (for the app)

```php
<?php

namespace App;

class Event {

    public function autoload()
    {
        return array(
            array(
                'ITEM.CREATED',
                'myvendor/foo',
                array($this, 'itemCreated')
            ),
            array(
                'ITEM.UPDATED',
                'myvendor/foo',
                array($this, 'itemUpdated')
            )
        );
    }

    public function itemCreated($aData)
    {
        //	Item has been created
        //	Do something with $aData
    }

    public function itemUpdated($aData)
    {
        //	Item has been updated
        //	Do something with $aData
    }
}
```

The Event service is instantiated early on in the app's execution, so events subscribed to here are guaranteed to be respected.


## Triggering Events

Events are triggered using the `trigger($sEvent, $sNamespace, $aData)` method. The event and namespace are case **insensitive**.

    $oEventService = Factory::service('Event');
    $oEventService->trigger('ITEM.CREATED', 'myvendor/foo', array('foo' => 'bar'));

Each listener subscribed to the `ITEM.CREATED` event in the `myvendor/foo` namespace will be called (in the order they were subscribed) and will be passed `array('foo' => 'bar')` as it's first, and only, parameter.

*Note: You can use whatever namespace you like when using the Event service for app specific events, but for consistency, we recommend you use `App`.*
