<!DOCTYPE html>
<!--

    ERROR:   404
    MESSAGE: <?=strip_tags($sMessage)?>

-->
<html lang="en">
    <head>
        <title><?=$heading . ' - ' . APP_NAME?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php

        include NAILS_COMMON_PATH . 'errors/components/styles.php';

        ?>
    </head>
    <body>
        <div id="container">
            <?php

            if (defined('FCPATH') && defined('BASE_URL')) {

                $aPaths = [
                    [
                        FCPATH . 'assets/img/errors/404.png',
                        BASE_URL . 'assets/img/errors/404.png',
                    ],
                ];
            }

            include NAILS_COMMON_PATH . 'errors/components/header.php';

            echo 'The page you are looking for was not found.';

            include NAILS_COMMON_PATH . 'errors/components/footer.php';

            ?>
        </div>
    </body>
