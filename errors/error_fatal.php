<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <title>An Error Occurred - <?=APP_NAME?></title>
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
                FCPATH . 'assets/img/errors/fatal.png',
                BASE_URL . 'assets/img/errors/fatal.png'
            );
        }

        include NAILS_COMMON_PATH . 'errors/components/header.php';

        ?>
        <p>
            Sorry, an error occurred which we couldn't recover from. The technical team have
            been informed, we apologise for the inconvenience.
        </p>
        <?php

        include NAILS_COMMON_PATH . 'errors/components/footer.php';

        ?>
    </div>
</body>