<!DOCTYPE html>
<!--
    
    ERROR:   STARTUP
    MESSAGE: <?=$strip_tags($_ERROR)?>

-->
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <title>Startup Error<?=defined('APP_NAME') ? ' - ' . APP_NAME : ''?></title>
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
                FCPATH . 'assets/img/errors/startup.png',
                BASE_URL . 'assets/img/errors/startup.png'
            );
        }

        include NAILS_COMMON_PATH . 'errors/components/header.php';

        echo '<p>';
        echo '<strong style="color:red;">STARTUP ERROR: </strong>';
        echo $_ERROR;
        echo '</p>';

        include NAILS_COMMON_PATH . 'errors/components/footer.php';

        ?>
    </div>
</body>
<?php

exit();