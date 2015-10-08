<?php

if (!function_exists('create_event')) {

    /**
     * Creates an event object
     * @param  string  $type      The type of event to create
     * @param  mixed   $data      Any data to store alongside the event object
     * @param  integer $createdBy The event creator (null == system)
     * @param  integer $ref       A numeric reference to store alongside the event (e.g the id of the object the event relates to)
     * @param  string  $recorded  A strtotime() friendly string of the date to use instead of NOW() for the created date
     * @return mixed              Int on success false on failure
     */
    function create_event($type, $data = null, $createdBy = null, $ref = null, $recorded = null)
    {
        $ci =& get_instance();
        return $ci->event->create($type, $data, $createdBy, $ref, $recorded);
    }
}
