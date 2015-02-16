<?php

if (!function_exists('possessionise'))
{
    /**
     * Correctly adds possession to a word
     * @param  string $str The word to possesionise
     * @return string
     */
    function possessionise($str)
    {
        return (substr($str, -1) == 's') ? $str . '\'' : $str . '\'s';
    }
}

// --------------------------------------------------------------------------

if (!function_exists('genderise'))
{
    /**
     * Performs a basic genderisation of a string, so that pronouns, etc are correct
     * @param  string $gender The gender to transform to
     * @param  string $str    The string to apply the changes to
     * @return string
     */
    function genderise($gender, $str)
    {
        $pattern = NULL;
        $replace = NULL;

        //  Rules
        switch (strtolower($gender)) {

            //  Male
            case 'm':
            case 'male':

                $pattern[] = '/([^a-z])?(her)([^a-z])|([^a-z])?(their)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'his\')';

                $pattern[] = '/([^a-z])?(her)([^a-z])|([^a-z])?(them)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'him\')';

                $pattern[] = '/([^a-z])?(she)([^a-z])|([^a-z])?(they)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'he\')';
                break;

            //  Female
            case 'f':
            case 'female':

                $pattern[] = '/([^a-z])?(his)([^a-z])|([^a-z])?(their)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'her\')';

                $pattern[] = '/([^a-z])?(him)([^a-z])|([^a-z])?(them)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'her\')';

                $pattern[] = '/([^a-z])?(he)([^a-z])|([^a-z])?(they)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'she\')';
                break;

            //  Unisex
            default:

                $pattern[] = '/([^a-z])?(his)([^a-z])|([^a-z])?(her)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'their\')';

                $pattern[] = '/([^a-z])?(him)([^a-z])|([^a-z])?(her)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'them\')';

                $pattern[] = '/([^a-z])?(he)([^a-z])|([^a-z])?(she)([^a-z])/ei';
                $replace[] = '_genderise(\'\\1\\4\', \'\\2\\5\', \'\\3\\6\', \'their\')';
                break;
        }

        //  Modify string
        return preg_replace($pattern, $replace, $str);
    }

    //  Helper func to maintain case
    function _genderise($oldPre, $old, $oldPost, $new)
    {
        //  Determine case
        $case = NULL;

        // work it out here...
        if (ctype_upper($old)) {
            $case = 'upper';
        }

        if (ctype_lower($old)) {
            $case = 'lower';
        }

        if (preg_match('/[A-Z][a-z]+/', $old)) {
            $case = 'title';
        }

        //  Transform string
        switch ($case) {

            case 'lower':

                $return = $oldPre . strtolower($new) . $oldPost;
                break;

            case 'upper':

                $return = $oldPre . strtoupper($new) . $oldPost;
                break;

            case 'title':

                $return = $oldPre . title_case($new) . $oldPost;
                break;

            default:

                $return = $oldPre . $new . $oldPost;
                break;
        }

        return $return;
    }
}
