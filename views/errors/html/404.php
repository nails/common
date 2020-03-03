<!DOCTYPE html>
<!--
ERROR:   404
-->
<html lang="en">
    <head>
        <title>404 Page Not Found - <?=\Nails\Config::get('APP_NAME')?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php

        include \Nails\Config::get('NAILS_COMMON_PATH') . 'views/errors/components/styles.php';

        ?>
    </head>
    <body>
        <div id="container">
            <?php

            include \Nails\Config::get('NAILS_COMMON_PATH') . 'views/errors/components/header.php';
            echo 'The page you are looking for was not found.';
            include \Nails\Config::get('NAILS_COMMON_PATH') . 'views/errors/components/footer.php';

            ?>
        </div>
    </body>
</html>
