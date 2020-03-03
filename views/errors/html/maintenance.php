<!DOCTYPE html>
<!--
ERROR:   Maintenance
-->
<html lang="en">
    <head>
        <title>Down For Maintenance - <?=\Nails\Config::get('APP_NAME')?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="10">
        <?php

        include \Nails\Config::get('NAILS_COMMON_PATH') . 'views/errors/components/styles.php';

        ?>
    </head>
    <body>
        <div id="container">
            <?php

            include \Nails\Config::get('NAILS_COMMON_PATH') . 'views/errors/components/header.php';

            if (empty($sMaintenanceTitle)) {
                echo '<p>We are currently updating our website</p>';
            } else {
                echo $sMaintenanceTitle;
            }

            if (empty($sMaintenanceBody)) {
                echo '<p>Please try again shortly.</p>';
            } else {
                echo $sMaintenanceBody;
            }

            include \Nails\Config::get('NAILS_COMMON_PATH') . 'views/errors/components/footer.php';

            ?>
        </div>
    </body>
</html>
