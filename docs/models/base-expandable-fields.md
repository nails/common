# Expandable Fields
> Documentation is a WIP.


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
    'auto_expand' => true

)

$this->addExpandableField($aOptions);
```
