<!DOCTYPE html>
<!--
ERROR:   404
-->
<html lang="en">
    <head>
        <title>404 Page Not Found - <?=APP_NAME?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php

        include NAILS_COMMON_PATH . 'views/errors/components/styles.php';

        ?>
    </head>
    <body>
        <div id="container">
            <?php

            if (defined('NAILS_APP_PATH') && defined('BASE_URL')) {
                $aPaths = [
                    [
                        NAILS_APP_PATH . 'assets/img/errors/404.png',
                        BASE_URL . 'assets/img/errors/404.png',
                    ],
                ];
            }

            include NAILS_COMMON_PATH . 'views/errors/components/header.php';
            echo 'The page you are looking for was not found.';
            include NAILS_COMMON_PATH . 'views/errors/components/footer.php';

            ?>
        </div>
    </body>
</html>
