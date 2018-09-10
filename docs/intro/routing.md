# Routing

Routing in Nails is relatively simple. There are three tiers to routing:

- [Explicit Routes](#explicit-routes)
- [Generated Routes](#generated-routes)
- [Automatic Routes](#automatic-routes)

Specificity is important, a match near the top of the chain will always take precedence over something further down.


## Explicit Routes

Explicit application routes are set in the `application/config/routes.php` file and take the following format:

```php
$route['some/url/structure']   = 'module/controller/methodOne';
$route['some/other/structure'] = 'module/controller/methodTwo';
```

When Nails detects a matching URL structure, it will direct the request to the specified controller and execute the
specified method. There's no reason multiple routes shouldn't point to the same controller/method.

Routes also support wildcards, allowing you to make dynamic URLs which are all handled by a single controller. You may
use `:any` and `:num` in your routes to allow for any combination of letters or numbers. Note that these are actually
aliases for regular expressions with `:any` being translated to `[^/]+` and `:num` to `[0-9]+`, respectively.


## Generated Routes

Nails also provides the ability for routes to be generated dynamically; this is useful when components (or indeed the
application) need to write specific routes dependent on the contents of the database. These are generated on demand when
a module determines that the routes need to be updated. These are written to a file in
`application/cache/routes_app.php` and take the exact same format as the manually specified routes in the
`application/config/routes.php` file.

A good example of these being used is by the CMS module, the URI for each page can be defined by the user, so for each
page a custom route is written which maps it to its ID, for example: a page with the route `my-cms-page` and the ID
`123` would have the following route written:

```php
$route['my-cms-page'] = 'cms/render/123';
```

This will cause Nails to load the CMS module's `render` controller and pass in the distinct Page ID which it can then
use to render the page.

## Automatic Routes
If no matching explicit or generated route is found then the router switches into automatic mode and attempts to infer
the desired controller. It assumes that the URL segments map to controllers in the following way:

```
example.com/<module>/<controller>/<method>
```

Modules are defined in the `application/modules/` directory and have the following structure:

```
application/
    ↳ modules/
        ↳ mymodule/
            ↳ controllers/
                ↳ mymodule.php
                ↳ foo.php
            ↳ views/
                ↳ index.php
```

In the above example we have a module called `mymodule` which contains two controllers, `mymodule` and `foo`. If no
method is provided, the router will assume `index`, if no controller is provided then the router will assume a
controller named the same as the module. For example, all the following URLs would resolve to the same place:

    mymodule/mymodule/index
    mymodule/mymodule
    mymodule

Assuming each controller contains the following methods `index()` and `bar()` then the following routes would resolve as
follows:

| URL                     | Maps to                    |
|:------------------------|:---------------------------|
| mymodule/mymodule/index | mymodule/mymodule->index() |
| mymodule/mymodule       | mymodule/mymodule->index() |
| mymodule                | mymodule/mymodule->index() |
| mymodule/foo/index      | mymodule/foo->index()      |
| mymodule/foo            | mymodule/foo->index()      |

If the `module` segment is not found in `application/modules` then the router will look through the installed components
(which can themselves register a particular module namespace).

See the [CodeIgniter docs](https://www.codeigniter.com/user_guide/general/routing.html) for further information on
routing.


## Reserved Routes

There are three reserved routes:

| Route                            | Description                                                                                                           |
|:---------------------------------|:----------------------------------------------------------------------------------------------------------------------|
| `$route['default_controller']`   | This is the default route which is used when no URL segments are specified.                                           |
| `$route['404_override']`         | When a 404 occurs this route is used.                                                                                 |
| `$route['translate_uri_dashes']` | Automatically translates dashes into underscores ; useful as dashes aren't valid characters to use in PHP class names |
