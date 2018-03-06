# Events
> Documentation is a WIP.



Events in Nails are a way of broadcasting to other parts of the system that something happened, and provides an interface for code to react. For example; you may wish to update a mailing list database every time a user is created, or send a notification to an administrator whenever somebody makes a purchase.

Your code can subscribe to events triggered by either installed modules or other parts of your app easily using the `Event` service.

Load the service using the Nails Factory: `Factory::service('Event')`

> See what events can be subscribed to in your app using `nails events` on the command line.



## Announcing Events

In order for an event to be useable, it first must be announced. This is simply a case of creating a class in the root namespace of the component called `Events`. This class must extend `Nails\Common\Events\Base` and is simply a collection of constants and DocBlocs.


```php
<?php
namespace App;

use Nails\Common\Events\Base;

class Events extends Base
{
    /**
     * Fired when a user yells
     *
     * @param integer $iUserId  The User's ID
     * @param string  $sYelling What they're yelling
     */
    const USER_YELL = 'USER_YELL';
    
    /**
     * Fired when a user whispers
     *
     * @param integer $iUserId     The User's ID
     * @param string  $swhispering What they're whispering
     */
    const USER_WHISPER = 'USER_WHISPER';
}
```

In the above example, we're declaring two events (`\App\Events::USER_YELL` and `\App\Events::USER_WHISPER`), as well as providing some documentation about when the event is fired and what arguments are passed to subscribers. The DocBloc is parsed and is what is used by the `nails events` command.

*Note: The values of the constants do not matter so long as they're unique for the class*



## Triggering Events

Events are triggered using the `trigger($sEvent, $sNamespace, $aData)` method on the `Event` service. Using the above declaration as an example:

    $oEventService = Factory::service('Event');
    $oEventService->trigger(\App\Events::USER_YELL, 'app', [$iUserId, $sYelling]);

Each listener subscribed to the `\App\Events::USER_YELL` event in the `app` namespace will be called (in the order they were subscribed) and will be passed `$iUserId` and `$sYelling` as two separate arguments (under the hood, the service is calling `call_user_func_array`).

*Note: You can use whatever namespace you like when using the Event service, but for consistency, we recommend you use `app` for the app and the composer name for components, e.g. `vendor/package`*





## Subscribing to Events


When the `Event` service constructs it looks through all registered components and the app for a class which matches the format `\Namespace\Events`, where `Namespace` is the value provided by the component's `extra.nails.namespace` property in the `composer.json` file. It then looks for a public method called `autoload()` and calls it. **Note, this is the _same_ class where events are announced.**

`\Namespace\Events->autoload()` should return an array of `EventSubscription` objects. Each `EventSubscription` object should be given an event, a namespace, and a callback. Additionally, you can also configure the subscription to only fire once, even if the event is triggered many times.


Example Event Handler (for the app):

```php
<?php

namespace App;

use Nails\Factory;

class Events
{
    
    //  Declared events omitted for brevity

    public function autoload()
    {
        return [
            Factory::factory('EventSubscription')
                   ->setEvent(static::USER_YELL)
                   ->setNamespace('app')
                   ->setCallback([$this, 'userDidYell']),

            Factory::factory('EventSubscription')
                   ->setEvent(static::USER_WHISPER)
                   ->setNamespace('app')
                   ->setCallback([$this, 'userDidWhisper']),

            Factory::factory('EventSubscription')
                   ->setEvent(\Nails\Common\Events::SYSTEM_READY)
                   ->setNamespace('nailsapp/common')
                   ->setCallback([$this, 'systemIsReady'])
                   ->setOnce(),
        ];
    }

    public function userDidYell($iUserId, $sYelling)
    {
        //  The user with ID $iUserId yelled "$sYelling"
    }

    public function userDidWhisper($iUserId, $sWhispering)
    {
        //  The user with ID $iUserId whispered "$sWhispering"
    }

    public function systemIsReady()
    {
        //  The system is ready
    }
}
```


> Tip: You can subscribe the same callback to multiple events in the same namespace by passing an array of events to the `setEvent()` method.


### Dynamic subscriptions

If you only need a subscription to be attached in certain circumstances, then you can add a new subscription on-the-fly using the `Event` service's `subscribe($sEvent, $sNamespace, $mCallback, $bOnce = false)` method.

    $oEventService = Factory::service('Event');
    $oEventService->subscribe(\App\Events::USER_YELL, 'app', [$this, 'myCallback']);

`$this->myCallback()` will be called each time the `\App\Events::USER_YELL` event is triggered in the `app` namespace.

