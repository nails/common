<?php

namespace Nails\Common\Traits\Database;

use Nails\Config;

trait HasDatabaseCredentials
{
    /**
     * Returns the connection's host
     *
     * @return string
     */
    public function getHost(): string
    {
        return (string) Config::get('DB_HOST', '127.0.0.1');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) Config::get('DB_USERNAME');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) Config::get('DB_PASSWORD');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's port
     *
     * @return int
     */
    public function getPort(): int
    {
        return (int) Config::get('DB_PORT', 3306);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the connection's database
     *
     * @return string
     */
    public function getDatabase(): string
    {
        return Testing::enabled()
            ? Testing::DB_NAME
            : (string) Config::get('DB_DATABASE');
    }
}