<?php

return array(
    'services' => array(
        'Asset' => function () {
            return new \Nails\Common\Library\Asset();
        },
        'Meta' => function () {
            return new \Nails\Common\Library\Meta();
        }
    ),
    'factories' => array(
        'DateTime' => function () {
            return new \DateTime();
        }
    )
);
