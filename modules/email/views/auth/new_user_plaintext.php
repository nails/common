<?php if ( ! empty( $admin->id ) && $admin->id != $sent_to->id ) : ?>
<?=$admin->first_name . ' ' . $admin->last_name?>, an administrator for the <?=APP_NAME?> website, has just created a new <?=$admin->group->name?> account for you.
<?php else : ?>
Thank you for registering at the <?=APP_NAME?> website.
<?php endif; ?>

<?php if ( ! empty( $password ) ) : ?>
Your password is <?=$password?><?=! empty( $temp_pw ) ? ', you will be asked to set this to something more memorable when you log in' : ''?>.

<?php endif; ?>
You can log in using the link below:

{unwrap}<?=site_url( 'auth/login' )?>{/unwrap}

<?php if ( ! empty( $verification_code ) ) : ?>
Additionally, we would appreciate it if you could verify your email address using the link below, we do this to maintain the integrity of our database.

{unwrap}<?=site_url( 'email/verify/' . $sent_to->id . '/' . $verification_code )?>{/unwrap}
<?php endif; ?>