# Environments

- [Overview](#overview)
- [Password Protecting Environments](#password-protecting-environments)


## Overview

> @todo - complete this section


## Password Protecting Environments

Sometimes it can be desireable to restrict access to a particular environment, e.g. a stopping the public from accessing a staging instance. Nails offers a simple mechanism for implementing basic authentication across the entire application when it detects a specific environment.

*Note: This is not designed to be a cryptographically secure method of protection.*


### Defining users and passwords

Users, and their corresponding passwords, are defined in the application's `config/app.php` file using a simple JSON string.

A constant should be defined for each environment being protected, it taks the form:

    APP_USER_PASS_{{ENVIRONMENT}}

So, for example, to protect the `STAGING` environment you'd define the constant:

    APP_USER_PASS_STAGING

The JSON string is a series of key/value pairs, where the key is the username and the value is the password sha256 encoded (with no salt). For example, for 2 users (`john` and `amy`) with passwords `password` and `something` respectively, the JSON String would look like this:

    {
        'john': '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8',
        'amy': '3fc9b689459d738f8c88a3a48aa9e33542016b7a4052e001aaa536fca74813cb'
    }


#### Example

It is often easier to define the user/password pairs in PHP and then use `json_encode()` to convert to a string; here's a sample of what might exist in `config/app.php`

    $aUserPass = array(
        'john' => '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8',
        'amy'  => '3fc9b689459d738f8c88a3a48aa9e33542016b7a4052e001aaa536fca74813cb'
    );
    define('APP_USER_PASS_STAGING', json_encode($aUserPass));


### Generating the hash

There are many online tools to do this, but it is recommended to use a local system when encrypting secrets. You can use PHP to encode the string on the command line as follows:

    $ php -r 'echo hash("sha256", "password-to-encode");'


### Whitelisting IPs

whitelisting an IP (i.e not requiring a password) is straightforward. Similar to the above, create a constant in `config/app.php` which is a JSON encoded array of IP and IP Ranges. the name of the constant should match the environment.

    APP_USER_PASS_WHITELIST_{{ENVIRONMENT}}

So, for example, to define a whitelist for the `STAGING` environment you'd define the constant:

    APP_USER_PASS_WHITELIST_ STAGING

#### Example

    $aIpWhitelist = array(
        '123.456.78.0/15',
        '123.456.79.1'
    );
    define('APP_USER_PASS_WHITELIST_STAGING', json_encode($aIpWhitelist));
