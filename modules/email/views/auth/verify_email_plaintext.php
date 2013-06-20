<?php if ( isset( $admin ) && $admin ) : ?>
<?=$admin?>, an administrator for <?=APP_NAME?>'s website, has just created a new <?=$group?> account for you.
<?php else : ?>
Thank you for registering at <?=APP_NAME?>'s website.
<?php endif; ?>

<?php if ( isset( $password ) && $password ) : ?>
Your password is <?=$password?><?=isset( $temp_pw ) && $temp_pw ? ', you will be asked to set this to something more memorable when you first log in.' : ''?>.
<?php endif; ?>

We would appreciate it if you could take a second to verify your email address using the link below, we do this to ensure the integrity of our database.

{unwrap}<?=site_url( 'auth/activate/' . $user->id . '/' . $user->activation_code )?>{/unwrap}