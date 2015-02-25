<?php

if (!function_exists('getUserObject')) {

    /**
     * Returns a reference to the Nails user object
     * @return mixed
     */
    function &getUserObject()
    {
        //  So we can return a reference
        $fail = FALSE;

        $ci =& get_instance();

        if (!isset($ci->user_model)) {

            return $fail;
        }

        return $ci->user_model;
    }
}

// --------------------------------------------------------------------------

if (!function_exists('activeUser')) {

    /**
     * Alias to user_model->activeUser(); method
     * @param  string  $keys      The key to look up in activeUser
     * @param  string  $delimiter If multiple fields are requested they'll be joined by this string
     * @return mixed
     */
    function activeUser($keys = FALSE, $delimiter = ' ')
    {
        $userObject =& getUserObject();

        if ($userObject) {

            return $userObject->activeUser($keys, $delimiter);

        } else {

            return false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('userHasPermission')) {

    /**
     * Alias to user_model->hasPermission(); method
     * @param   string  $permission The permission to check for
     * @param   mixed   $user       The user to check for; if null uses activeUser, if numeric, fetches user, if object uses that object
     * @return  boolean
     */
    function userHasPermission($permission, $user = null)
    {
        $userObject = getUserObject();

        if ($userObject) {

            return $userObject->hasPermission($permission, $user);

        } else {

            return false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isLoggedIn')) {

    /**
     * Alias to user_model->isLoggedIn()
     * @return boolean
     */
    function isLoggedIn()
    {
        $userObject = getUserObject();

        if ($userObject) {

            return $userObject->isLoggedIn();

        } else {

            return false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isAdmin')) {

    /**
     * Alias to user_model->isAdmin()
     * @param  mixed   $user The user to check, uses activeUser if null
     * @return boolean
     */
    function isAdmin($user = null)
    {
        $userObject = getUserObject();

        if ($userObject) {

            return $userObject->isAdmin($user);

        } else {

            return false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('wasAdmin')) {

    /**
     * Alias to user_model->wasAdmin()
     * @return boolean
     */
    function wasAdmin()
    {
        $userObject = getUserObject();

        if ($userObject) {

            return $userObject->wasAdmin();

        } else {

            return false;
        }
    }
}

// --------------------------------------------------------------------------

if (!function_exists('isSuperuser')) {

    /**
     * Alias to user_model->isSuperuser()
     * @param  mixed   $user The user to check, uses activeUser if null
     * @return boolean
     */
    function isSuperuser($user = null)
    {
        $userObject = getUserObject();

        if ($userObject) {

            return $userObject->isSuperuser($user);

        } else {

            return false;
        }
    }
}
