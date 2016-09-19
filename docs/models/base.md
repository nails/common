# Models
> Documentation is a WIP.


## Introduction models in Nails

    @todo explain models in more detail


## Expandable Fields

Often items returned by models reference other items (or many other items). The following Book object, for example, it references the author in the `author_id` field in a 1-to-1 relationship:

```json
{
    "id": 1,
    "title": "Treasure Island",
    "author_id": 23
}
```

In addition, there might be various items which reference back to the book via it's ID. For example, this book might have many reader reviews (in  a 1-to-many relationship)

    
It is possible to "expand" these items by passing in an "expand" element in `$aData` when calling `getAll()`, e.g.:

```php
$aBooks = $oBookModel->getAll(
    null,
    null,
    array(
        'expand' => array(
            'author',
            'reviews'
        )
    )
);
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

It is also possible to pass a configuration array to the expandable field's model. To do this, instead of passing a string to the `expand` array, pass instead an array where the first parameter is the trigger and the second parameter is the configuration array you want to pass to the associated item's model. for example:

```php
$aBooks = $oBookModel->getAll(
    null,
    null,
    array(
        'expand' => array(
            'author',
            array(
                'reviews',
                array(
                    'expand' => array('author'),
                    'where' => array(
                        array('rating >', 3)
                    ),
                    'sort' => array(
                        array('created', 'desc')
                    )
                )
            )
        )
    )
);
```

Note how you can nest requests to `expand` objects.


To learn more on how to configure your models to use Expandable Fields, [click here](docs/models/base-expandable-fields.md)
