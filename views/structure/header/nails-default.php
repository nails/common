<?php

    $this->load->view('structure/header/blank');

?>
<div class="container">
    <div class="row text-center" style="margin-top:1em;">
        <h1>
            <?=anchor('', APP_NAME, 'style="text-decoration:none;color:inherit;"')?>
        </h1>
    </div><!-- /.row -->
    <hr />
    <?php

        if ($success || $error || $message || $notice) {

            echo '<div class="container row">';
                echo $success ? '<p class="alert alert-success">' . $success . '</p>' : '';
                echo $error   ? '<p class="alert alert-danger">' . $error . '</p>' : '';
                echo $message ? '<p class="alert alert-warning">' . $message . '</p>' : '';
                echo $notice  ? '<p class="alert alert-info">' . $notice . '</p>' : '';
            echo '</div>';

        }
