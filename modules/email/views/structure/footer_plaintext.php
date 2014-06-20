
<?php if ( isset( $email_ref ) ) : ?>
<?php $_time = time(); ?>

---------------

Trouble viewing this email? View it online here:

<?=site_url( 'email/view_online/' . $email_ref . '/' . $_time . '/' . md5( $_time . $secret . $email_ref ) )?>


Email Ref: <?=$email_ref?>
<?php endif; ?>