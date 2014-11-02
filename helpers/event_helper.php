<?php

if (!function_exists('create_event')) {

    function create_event($type, $vars = null, $created_by = null, $ref = null, $_recorded = null)
    {
        $_ci =& get_instance();
        $_ci->load->library('event');
        return $_ci->event->create($type, $vars, $created_by, $ref);
    }
}

/* End of file event_helper.php */
/* Location: ./helpers/event_helper.php */
