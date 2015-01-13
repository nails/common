<?php

    /**
     * Renders a data section
     * @param  string $title The title to give the section
     * @param  mixed  $data  The data to display
     * @return string
     */
    function keyValueSection($title, $data) {

        ob_start();
        echo '<section class="data-section">';
            echo '<h3>' . $title . '</h3>';
            echo '<div class="table-responsive">';
                echo '<table>';
                    echo '<tbody>';

                    if (!empty($data)) {

                        foreach ($data as $k => $v) {

                            echo '<tr>';
                                echo '<td class="key">';
                                    echo $k;
                                echo '</td>';
                                echo '<td class="value">';

                                    if (is_string($v) || is_numeric($v)) {

                                        echo $v;

                                    } else {

                                        echo json_encode($v, JSON_PRETTY_PRINT);
                                    }

                                echo '</td>';
                            echo '</tr>';
                        }

                    } else {

                        echo '<tr>';
                            echo '<td class="no-data">';
                                echo 'No Data';
                            echo '</td>';
                        echo '</tr>';
                    }

                    echo '</tbody>';
                echo '</table>';
            echo '</div>';
        echo '</section>';

        $out = ob_get_contents();
        ob_end_clean();

        return $out;
    }

?><!DOCTYPE html>
<html>
<head>
    <title>[Dev] An Error Occurred: <?=$subject?></title>
    <style type="text/css">

        html,
        body
        {
            padding: 0;
            margin: 0;
            background: #FAFAFA;
            font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            font-weight: 300;
            font-size: 13px;
            line-height: 1.5em;
        }

        .dev-only,
        header,
        section.data-section
        {
            padding: 1em;
        }

        .dev-only
        {
            background: #D55600;
            color: #FFFFFF;
            height: 30px;
            line-height: 30px;
        }

        #nailsLogo
        {
            float: right;
            max-height: 100%;
        }

        header
        {
            background:#D5D5D5;
        }

        section.data-section h3
        {
            border-bottom: 1px solid #CCCCCC;
            padding-bottom: 1em;
            margin-bottom: 1em;
        }

        section.data-section .table-responsive
        {
            min-height: .01%;
            overflow-x: auto;
        }

        section.data-section table
        {
            background: #FFFFFF;
            border: 1px solid #EFEFEF;
            width: 100%;
            box-sizing: border-box;
        }

        section.data-section table td
        {
            border-bottom: 1px solid #EEEEEE;
            border-right: 1px dotted #EEEEEE;
            padding: 0.75em;
            font-family: monospace;
        }

        section.data-section table tr:last-of-type td
        {
            border-bottom: 0;
        }

        section.data-section table td.value
        {
            border-right: 0;
        }

        section.data-section table td.no-data
        {
            border: 0;
        }

        section.data-section table td.value
        {
            color: #555555;
        }

        section.data-section code
        {
            color: green;
        }

    </style>
</head>
<body>
    <div class="dev-only">
        This page is viewable in development environments only.
        <a href="http://docs.nailsapp.co.uk">
            <img src="<?=NAILS_ASSETS_URL?>img/nails/icon/icon@2x.png" id="nailsLogo"/>
        </a>
    </div>
    <header>
        <h1><?=$subject?></h1>
        <h2><?=$message?></h2>
    </header>

    <?php

    //  Error Variables
    $displayDetails              = array();
    $displayDetails['Code/Type'] = $details->code;
    $displayDetails['Message']   = $details->msg;
    $displayDetails['File']      = $details->file;
    $displayDetails['Line']      = $details->line;

    echo keyValueSection('Error Details', $displayDetails);

    // --------------------------------------------------------------------------

    //  Backtrace
    $backtrace = array();
    foreach ($details->backtrace as $bt) {

        $file  = !empty($bt['file']) ? $bt['file'] : '&lt;unknown&gt;';
        $line  = !empty($bt['line']) ? $bt['line'] : '&lt;unknown&gt;';
        $class = !empty($bt['class']) ? $bt['class'] . '-&gt;' : '';
        $func  = !empty($bt['function']) ? $bt['function'] : '&lt;unknown&gt;';

        $backtrace[] = 'File <code>"' . $file . '"</code> line <code>' . $line . '</code> in <code>' . $class . $func . '</code>';
    }
    echo keyValueSection('Backtrace', $backtrace);

    // --------------------------------------------------------------------------

    $queries = get_instance()->db->queries;
    $queries = array_reverse($queries);

    echo keyValueSection('Database Queries (most recent first)', $queries);

    // --------------------------------------------------------------------------

    //  Environment and state variables
    if (isset($_SERVER)) {

        echo keyValueSection('Server / Request Data', $_SERVER);
    }

    if (isset($_GET)) {

        echo keyValueSection('GET Data', $_GET);
    }

    if (isset($_POST)) {

        echo keyValueSection('POST Data', $_POST);
    }

    if (isset($_COOKIE)) {

        echo keyValueSection('COOKIE Data', $_COOKIE);
    }

    if (isset($_GLOBALS)) {

        echo keyValueSection('GLOBAL Data', $_GLOBALS);
    }

    echo keyValueSection('SESSION Data', get_instance()->session->userdata);

    ?>
</body>
</html>