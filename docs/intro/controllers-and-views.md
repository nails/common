# Controllers & Views

- [The Base Controller](#the-base-controller)
- [Views](#views)
- [Sample Controller](#sample-controller)

Controllers and views are the basic building blocks of Nails applications. Controllers are responsible for handling
business logic, while the views are responsible for handling presentation.

In all but exceptional cases, controllers and views are stored within modules, i.e under `application/modules`.

## The Base Controller

Controllers should always extend the application's Base controller (located at `src/Controller/Base`). This Base
controller is responsible for setting up the majority of the Environment; without it, expect odd and undefined
behaviours.

In addition to bootstrapping much of Nails' functionality it also provides an opportunity for the developer to perform
global actions on every page request (e.g. loading a menu, loading assets, or checking if the active user has new
messages).

The following diagram shows how a requests filters through the system:

    index.php
    ↳ Nails\Common\Controller\Base->__construct()   //  Nails Bootstrapping
     ↳ App\Controller\Base->__construct()           //  App bootstrapping
      ↳ Controller->__construct()                   //  Routed controller bootstrapping
       ↳ Controller->method()                       //  Routed method

In some cases controllers will extend another Base controller (e.g. in the Admin, API, or Cron packages) - but they
themselves are extensions of the app's base controller, but do additional set up.


## Views

Views are loaded using the `View` service. this service provides a `load()` method which accepts three arguments:

1. The view to load
2. An array of variables to make available to the view (default `[]`)
3. Whether to echo the rendered view to the browser, or return as a string (default: `true`)

View variables are simply a key > value array, where the array keys are transposed into variable names; e.g. the
following array:

```
[
    'foo'   => 'bar'
    'micky' => 'mouse'
]
```

Would be accessible in the view as `$foo` and `$micky`. Controllers have a protected property named `$data` which is
intended to be used to store variables which will be passed to the view as it can be accessed across controller methods.

## Sample controller

The following example is a very basic controller with a method which loads a view, top and tailed with the global site
header and footer.

### Folder structure
```
application/
    ↳ modules/
        ↳ foo/
            ↳ controllers/
                ↳ foo.php
            ↳ views/
                ↳ index.php
```
### Controller
```php
<?php

use Nails\Factory;
use App\Controller\Base;

class Foo extends Base
{
    public function index()
    {
        $this->data['name'] = 'Jane';

        $oView = Factory::service('View');
        $oView->load('structure/header', $this->data);
        $oView->load('foo/index', $this->data);
        $oView->load('structure/footer', $this->data);
    }
}
```

### View

```html
<h1>Hello, <?=$name?>!</h1>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
```
