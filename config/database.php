<?php

/* Load the base config file from the CodeIgniter package */
require FCPATH . 'vendor/codeigniter/framework/application/config/database.php';

$db['default']['hostname'] = DEPLOY_DB_HOST;
$db['default']['username'] = DEPLOY_DB_USERNAME;
$db['default']['password'] = DEPLOY_DB_PASSWORD;
$db['default']['database'] = DEPLOY_DB_DATABASE;
$db['default']['dbdriver'] = 'mysqli';
$db['default']['db_debug'] = DEPLOY_DB_DEBUG;
$db['default']['cachedir'] = DEPLOY_CACHE_DIR;
$db['default']['char_set'] = 'utf8mb4';
$db['default']['dbcollat'] = 'utf8mb4_unicode_ci';
