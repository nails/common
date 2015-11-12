<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <title><?=APP_NAME?> is down for maintenance</title>
    <?php

    include NAILS_COMMON_PATH . 'errors/components/styles.php';

    ?>
</head>
<body>
    <div id="container">
        <?php

        if (defined('FCPATH') && defined('BASE_URL')) {

            $paths   = array();
            $paths[] = array(
                FCPATH . 'assets/img/errors/maintenance.png',
                BASE_URL . 'assets/img/errors/maintenance.png'
            );
        }

        include NAILS_COMMON_PATH . 'errors/components/header.php';

        if (empty($sMaintenanceTitle)) {

            echo '<p>We are down for maintenance.</p>';

        } else {

            echo $sMaintenanceTitle;
        }

        if (empty($sMaintenanceBody)) {

            echo '<p>Please bear with us while we bring improvements to the site.</p>';

        } else {

            echo $sMaintenanceBody;
        }

        include NAILS_COMMON_PATH . 'errors/components/footer.php';

        ?>
    </div>
</body>