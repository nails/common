<?php if ( isset( $admin ) && $admin ) : ?>
<?=$admin?>, an administrator for the <?=APP_NAME?> website, has just created a new <?=$group->display_name?> account for you.
<?php else : ?>
Thank you for registering at the <?=APP_NAME?> website.
<?php endif; ?>

<?php if ( isset( $password ) && $password ) : ?>
Your password is <?=$password?><?=isset( $temp_pw ) && $temp_pw ? ', you will be asked to set this to something more memorable when you log in.' : ''?>.
<?php endif; ?>