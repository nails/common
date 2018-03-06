<?php

/**
 * This class allows the Nails Factory to load CodeIgniter helpers in the same way as it loads native helpers.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Helper
 * @author      Nails Dev Team
 * @link
 */

if (!function_exists('readFileChunked')) {

    /**
     * Outputs a file in bytesized chunks.
     * http://teddy.fr/2007/11/28/how-serve-big-files-through-php/
     * @param  string  $filename  The file to output
     * @param  integer $chunkSize The chunk size, in bytes
     * @return mixed              Ineger on success, false on failure
     */
    function readFileChunked($filename, $chunkSize = 1048576)
    {
        $bytesRead = 0;

        // $handle = fopen($filename, "rb");
        $handle = fopen($filename, 'rb');
        if ($handle === false) {

            return false;
        }

        while (!feof($handle)) {

            $buffer = fread($handle, $chunkSize);
            echo $buffer;

            $bytesRead += strlen($buffer);
        }

        $status = fclose($handle);

        if ($status) {

            return $bytesRead;

        } else {

            return false;
        }
    }
}

include FCPATH . 'vendor/codeigniter/framework/system/helpers/file_helper.php';
