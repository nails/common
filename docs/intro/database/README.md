# Database

- [Overview](#overview)
    - [Selecting Data](#selecting-data)
    - [Inserting Data](#inserting-data)
    - [Updating Data](#updating-data)
    - [Deleting Data](#deleting-data)
- [Transactions](#transactions)
- [Migrations](#migrations)
- [Seeders](#seeders)


## Overview

The database is primarily exposed via [Models](../factory/models.md), but can also be queried using the `Database`
service.

```php
$oDb = Factory::service('Database');
```

The `Database` service is simply a wrapper around CodeIgniter's QueryBuilder; it's main purpose is to aid building
queries and protecting user input.

Examples are usually easiest to follow, notice how the method names correspond to the part of the query:

### Selecting Data
```php
$oDb = Factory::service('Database');

//  Select a particular sub set of columns
$oDb->select(['id', 'column_1', 'column_2']);

//  Apply conditionals
$oDb->where('column_1', 'foo');
$oDb->where('column_2 !=', 'bar');
$oDb->where_in('id', [1, 2, 3]);
$oDb->like('column_3', 'baz'); // == LIKE '%baz%'

$oDb->limit(10, 10);    //  Number per page, offset
$oDb->order_by('column_2', 'desc'); // Column, order

//  Execute the query
$oQuery = $oDb->get('table_name');

//  Returns the result set as an associative array
$aResult = $oQuery->result();
```

### Inserting Data
```php
$oDb = Factory::service('Database');

//  Set the value of a particular column
$oDb->set('column_name', 'foo');

//  Can also be set as an associative array
$oDb->set([
    'column_name' => 'foo'
]);

$oDb->insert('table_name');
$iId = $oDb->insert_id();
````

### Updating Data
```php
$oDb = Factory::service('Database');

//  Set the value of a particular column
$oDb->set('column_name', 'foo');

//  Can also be set as an associative array
$oDb->set([
    'column_name' => 'foo'
]);

$oDb->where('id', 123);

$oDb->update('table_name');
````

### Deleting Data
```php
$oDb = Factory::service('Database');

$oDb->where('id', 123);

$oDb->delete('table_name');
````

For more information, see [CodeIgniter's documentation](https://codeigniter.com/userguide2/database/active_record.html).


## [Migrations](migrations.md)

    @todo


## [Transactions](transactions.md)

    @todo


## [Seeders](seeders.md)

    @todo
