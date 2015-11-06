# Database Migrations

Database migrations can be run on the CLI using the `nails migrate` command. Migrations are run automatically as part of an `install` or an `upgrade`.

The purpose of migrations is to programatically upgrade your database as the needs of the application changes over time.

Migrations will always be run in the following order:

1. `nailsapp/common`
2. Installed Nails modules
3. The application


## Writing migrations

> **Note -** Each module is responsible for migrating its own tables. It is also important to not rely on the existence of another module's tables as the order in which modules are migrated is not guaranteed (besides the running order mentioned above).

The migration system will automatically look for numerically (in ascending order, 0 being the first) indexed `.php` or `.sql` files in the module's `/migrations` directory and the app's `/application/migrations` directory, and apply them sequentially one after the other.

    vendor-name/module-name/migrations/0.php
    vendor-name/module-name/migrations/1.php
    vendor-name/module-name/migrations/2.php
    
You don't have to numerically index as shown above, but we recommend it for simplicity.

---

You can migrate using either [PHP](#php) or vanilla [SQL](#sql).


<a name="php"></a>
### PHP Migrations

Each migration takes the form of a class which extends the `Nails\Common\Console\Migrate\Base` class. The migration contains an `execute()` method which is called by the migration system when migrating. You should put your migrations in here. A basic migration will have a classname corresponding to the filename, for example a migration called `2.php` would contain a class called `Migration_2`.


#### › Namespace

Your migration should live within a namespace which corresponds to the module name, but camelCased. For example, in module `nailsapp/my-module` the migrations would be found at `nailsapp/my-module/migrations` and use namespace `Nails\Database\Migration\Nailsapp\MyModule`.

If the migration belongs to the App then it should use namespace `Nails\Database\Migration\App`.


#### › Database

Also provided are 4 helper functions for interacting with the database (which will be automatically connected to the app's database), these are essentially wrappers for a normal `PDO` connection.

- `$this->query()` - for running plaintext SQL
- `$this->prepare()` - for preparing a PDOStatement, execute the query using `$oStatement->execute()`
- `$this->lastInsertId()` - returns the ID of the last successful write operation
- `$this->db()` - returns the raw instance of the PDO class underlying the wrapper, should you need it

> **Note -** It is possible for Nails apps to specify an alternative table prefix for Nails tables, your migrations should respect this by replacing instances of `nails_` with `{{NAILS_DB_PREFIX}}`; `query()` and `prepare()` will automatically translate these for you.


#### › Example Migration

The following is a sample migration class for a module named `my-vendor/my-module`, it creates a table called `nails_my_table`:

```php
<?php
/**
 * Migration:   0
 * Started:     06/11/2015
 * Finalsied:   09/11/2015
 *
 * @package     Nails
 * @subpackage  my-module
 * @category    Database Migration
 * @author      Joe Bloggs <joe@bloggs.com>
 * @link
 */

namespace Nails\Database\Migration\MyVendor\MyModule;

use Nails\Common\Console\Migrate\Base;

class Migration_0 extends Base {

    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}my_table` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `key` varchar(50) DEFAULT NULL,
                `value` text,
                PRIMARY KEY (`id`),
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
```

<a name="sql"></a>
### SQL Migrations

> **Note -** SQL migrations are **deprecated** in favour of PHP migrations

If you wish to use SQL migrations then simply name the file with a `.sql` extension and write your queries one after the other, with the following considerations:

- One query per line. The parser isn't very smart and treats each line as an independent query, therefore queries which span multiple lines will generate syntax errors
- Comment out lines using `//`, `/* */` or `-- `