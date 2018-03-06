<?php

/**
 * Renders a data section
 *
 * @param  string $sTitle The title to give the section
 * @param  mixed  $mData  The data to display
 *
 * @return string
 */
function keyValueSection($sTitle, $mData)
{
    ob_start();

    ?>
    <section class="data-section">
        <h3><?=$sTitle?></h3>
        <div class="table-responsive">
            <table>
                <tbody>
                    <?php

                    if (!empty($mData)) {

                        foreach ($mData as $sKey => $mValue) {

                            ?>
                            <tr>
                                <td class="key">
                                    <?=$sKey?>
                                </td>
                                <td class="value" width="100%">
                                    <?php

                                    if (is_string($mValue) || is_numeric($mValue)) {
                                        echo $mValue;
                                    } else {
                                        echo json_encode($mValue, JSON_PRETTY_PRINT);
                                    }

                                    ?>
                                </td>
                            </tr>
                            <?php
                        }

                    } else {

                        ?>
                        <tr>
                            <td class="no-data">
                                No Data
                            </td>
                        </tr>
                        <?php
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php

    $sOut = ob_get_contents();
    ob_end_clean();

    return $sOut;
}

?>
<!DOCTYPE html>
<!--

    ERROR:   FATAL
    SUBJECT: <?=strip_tags($sSubject)?>
    MESSAGE: <?=strip_tags($sMessage)?>

-->
<html>
    <head>
        <title>[Dev] An Error Occurred: <?=$sSubject?></title>
        <style type="text/css">

            html,
            body {
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
            section.data-section {
                padding: 1em;
            }

            .dev-only {
                background: #D55600;
                color: #FFFFFF;
                height: 30px;
                line-height: 30px;
            }

            #nailsLogo {
                float: right;
                max-height: 100%;
            }

            header {
                background: #D5D5D5;
            }

            header h1 {
                line-height: 3rem;
            }

            section.data-section h3 {
                border-bottom: 1px solid #CCCCCC;
                padding-bottom: 1em;
                margin-bottom: 1em;
            }

            section.data-section .table-responsive {
                min-height: .01%;
                overflow-x: auto;
            }

            section.data-section table {
                background: #FFFFFF;
                border: 1px solid #EFEFEF;
                width: 100%;
                box-sizing: border-box;
            }

            section.data-section table td {
                border-bottom: 1px solid #EEEEEE;
                border-right: 1px dotted #EEEEEE;
                padding: 0.75em;
                font-family: monospace;
            }

            section.data-section table tr:last-of-type td {
                border-bottom: 0;
            }

            section.data-section table td.value {
                border-right: 0;
            }

            section.data-section table td.no-data {
                border: 0;
            }

            section.data-section table td.value {
                color: #555555;
            }

            section.data-section code {
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
            <h1><?=$sSubject?></h1>
            <h2><?=$sMessage?></h2>
        </header>

        <?php

        //  Error Variables
        $aDisplayDetails = [
            'Type'    => $oDetails->type,
            'Code'    => $oDetails->code,
            'Message' => $oDetails->msg,
            'File'    => $oDetails->file,
            'Line'    => $oDetails->line,
        ];

        echo keyValueSection('Error Details', $aDisplayDetails);

        // --------------------------------------------------------------------------

        //  Backtrace
        $aBacktrace = [];
        foreach ($oDetails->backtrace as $aItem) {
            $aBacktrace[] = sprintf(
                'File <code>"%s"</code> line <code>%s</code> in <code>%s-&gt;%s</code>',
                getFromArray('file', $aItem, '&lt;unknown&gt;'),
                getFromArray('line', $aItem, '&lt;unknown&gt;'),
                getFromArray('class', $aItem, ''),
                getFromArray('function', $aItem, '&lt;unknown&gt;')
            );
        }
        echo keyValueSection('Backtrace', $aBacktrace);

        // --------------------------------------------------------------------------

        $oDb      = \Nails\Factory::service('Database');
        $aQueries = $oDb->queries;
        $aQueries = array_reverse($aQueries);

        echo keyValueSection('Database Queries (most recent first)', $aQueries);

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

        $oSession = \Nails\Factory::service('Session', 'nailsapp/module-auth');
        echo keyValueSection('SESSION Data', $oSession->userdata);

        ?>
    </body>
</html>
