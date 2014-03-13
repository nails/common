NAILS MIGRATIONS
================

Nails handles it's own migrations, seperately from the app.

To run migrations hit `php index.php deploy post` on the command line
and the Nails database will be brought up to the latest available.

App migrations will happen after Nails migrations; app migration files
should be defined at application/migrations (within the app).

More info can be found in the docs

[TODO: Add link]