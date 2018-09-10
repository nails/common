# Factory › Models

## Index

- [Model overview](#overview)
- [Where do models live?](#where-do-models-live)
- [How are models loaded?](#how-are-models-loaded)
- [The Base model](#the-base-model)
    - [Querying models](#querying-models)
    - [Creating records](#creating-records)
    - [Updating records](#updating-records)
    - [Deleting records](#deleting-records)
- [Expandable Fields](#expandable-fields)
    - [Defining Expandable Fields](#defining-expandable-fields)


## Overview

In Nails, models (generally speaking) represent data from the database; usually there is a one-to-one relationship
between a database table and a model, all interactions with that table should be done by querying the model.


## Where do models live?

Models live at `src/Model` and in the `App/Model` namespace.


## How are models loaded?

Models should be loaded using the `Factory` and are defined within the `models` element of `services.php`. Each time you
load a model you are given the same instance.

## The Base model

Mose models will extend the Nails Base model (`Nails\Common\Model\Base`); the base model provides a consistent and
predictable CRUDy API for all models, as well as enabling Expandable Fields (a very powerful feature of Nails).


### Querying models

The `getAll` method is the primary way of querying the model; it accepts pagination parameters as well as a
configuration array which allows you to reduce the result set. By default, a query to `getAll()` with no parameters will
return the entire contents of the database as an array.

_Note: Also available are `getById()`, `getBySlug()`, `getByToken()` for retrieving a record by its ID, slug, or token,
respectively._

```
public function getAll($iPage = null, $iPerPage = null, $aData = [])

//  You may also call getAll like this, which disables pagination 
public function getAll($aData = [])
```

Typically, unless there is a need for the _entire_ table to be returned, this will be called with various `where`
conditionals defined to restrict the result set; also typical is to sort results. You can pass in various statements
using the `$aData` array:

```
[
    'where' => [
        //  If the option is a string then the statement is absolute
        'label = "Foo"'
        
        //  If the option is an array then:
        //  - the first parameter is the column
        //  - the second parameter is the value
        //  - the third parameter is whether to escape the value or not (boolean; default true)
        ['label', 'Foo']
    ],

    //  Also available
    //  'or_where'        => []
    //  'where_in'        => []
    //  'or_where_in'     => []
    //  'where_not_in'    => []
    //  'or_where_not_in' => []
    //  'like'            => [],
    //  'or_like'         => [],
    //  'not_like'        => [],
    //  'or_not_like'     => [],
    
    'sort' => [
        ['label', 'ASC'],
        ['created', 'DESC'],
    ],
]
```

_Note: all the blocks will be contained within parenthesis and joined with `AND`. i.e
`(where conditions) AND (or_where conditions)`._

### Creating records

Records can be created by passing in a key/value array to the model's `create()` method.

```
$oModel  = Factory::model('MyModel', 'app');
$iItemId = $oModel->create([
    'label' => 'The label of my thing',
    'foo'   => 'bar'
]);
```

### Updating records

Records can be updated by passing in the item's ID and a key/value array to the model's `update()` method.

```
$oModel  = Factory::model('MyModel', 'app');
$bResult = $oModel->update(
    123,
    [
        'label' => 'The label of my thing',
        'foo'   => 'bar'
    ]
);
```

### Deleting records

Records can be deleted by passing in the item's ID model's `delete()` method.

```
$oModel  = Factory::model('MyModel', 'app');
$bResult = $oModel->delete(123);
```

_Note: models can be configured to be non-destructive, if this is enabled then to permanently delete an item the
`destroy()` method must be used_


## Expandable Fields

Often items returned by models reference other items (or many other items). The following Book object, for example, it
references the author in the `author_id` field in a 1-to-1 relationship:

```json
{
    "id": 1,
    "title": "Treasure Island",
    "author_id": 23
}
```

In addition, there might be various items which reference back to the book via its ID. For example, this book might have
many reader reviews (in a 1-to-many relationship)

It is possible to "expand" these items by passing in an "expand" element to`$aData` when calling `get*l()`, e.g.:

```php
$oBookModel = Factory::model('Book');
$aBooks     = $oBookModel->getAll([
    'expand' => [
        'author',
        'reviews'
    ]
]);
```

This might return the following array:

```json
[
    {
        "id": 1,
        "title": "Treasure Island",
        "author": {
            "id": 23,
            "name": "Charles Dickens"
        },
        "reviews": {
            "count": 2,
            "data": [
                {
                    "id": 123,
                    "comment": "Great read!",
                    "number_stars": 4
                },
                {
                    "id": 356,
                    "comment": "Not bad, not bad at all.",
                    "number_stars": 3
                }
            ]
        }
    }
]
```

It is also possible to pass a configuration array to the expanded field's model. To do this, instead of passing a string
to the `expand` array, pass instead an array where the first parameter is the trigger and the second parameter is the
configuration array you want to pass to the associated item's model. for example:

```php
$oBookModel = Factory::model('Book');
$aBooks     = $oBookModel->getAll([
    'expand' => [
        //  This is a basic expansion, i.e the trigger word as a string
        'author',
        
        //  This is equivalent to the above
        [
            'author',
            []
        ],
        
        //  This is a nested expansion, where we're passing in an array to the Book\Review Model
        [
            //  The trigger word
            'reviews',
            //  To pass to the expanded model's getAll() method
            [
                //  Expansions we want the Book\Review model to perform
                'expand' => [
                    'author'
                    // [...] further expansions, if needed
                ],
                'where' => [
                    ['rating >', 3]
                ],
                'sort' => [
                    [('created', 'desc']
                ]
            ]
        ]
    ]
]);
```

So, in the above example, the expansions are happening like this:

```
Book->getAll()
    ↳ Book\Author->getAll()
    ↳ Book\Review->getAll()
        ↳ Book\Review\Author->getAll()
```

Note how you can nest requests to expanded objects.

#### Defining Expandable Fields

The base model provides a protected method called `addExpandableField($aOptions)` which can be used in your model's constructor to define fields which can be expanded.

The `$aOptions` parameter accepts an array with the following indexes:

```php
$aOptions = array(

    //  The text which triggers this expansion, passed in via $aData['expand']
    'trigger' => 'myTriggerText',

    //  The type of expansion: single or many
    //  This must be one of static::EXPAND_TYPE_SINGLE or static::EXPAND_TYPE_MANY
    'type' => static::EXPAND_TYPE_SINGLE,

    //  What property to assign the results of the expansion to
    'property' => 'my_property',

    //  Which model to use for the expansion
    'model' => 'MyModel',

    //  The provider of the model
    'provider' => 'app',

    /**
     * The ID column to use; for EXPANDABLE_TYPE_SINGLE this is property of the
     * parent object which contains the ID, for EXPANDABLE_TYPE_MANY, this is the
     * property of the child object which contains the parent's ID.
     */
    'id_column' => 'item_id',

    //  Whether the field is expanded by default
    'auto_expand' => true,
    
    //  Whether to automatically save expanded objects when the trigger is
    //  passed as a key to the create or update methods
    'auto_save' => true
)

$this->addExpandableField($aOptions);
```
