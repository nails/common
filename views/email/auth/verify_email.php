<?php if ( isset( $admin ) && $admin ) : ?>
<p>
	<?=$admin?>, an administrator for <?=APP_NAME?>'s website, has just created a new <em><?=$group?></em> account for you.
</p>
<?php else : ?>
<p>
	Thank you for registering at <?=APP_NAME?>'s website.
</p>
<?php endif; ?>
<?php if ( isset( $password ) && $password ) : ?>
<p>
	Your password is <strong><?=$password?></strong><?=isset( $temp_pw ) && $temp_pw ? ', you will be asked to set this to something more memorable when you first log in.' : ''?>.
</p>
<?php endif; ?>
<p>
	We would appreciate it if you could take a second to verify your email address using the link below, we do this to ensure the integrity of our database.
</p>
<p>
	<?=anchor( 'auth/activate/' . $user->id . '/' . $user->activation_code, 'Verify Email', 'class="large"' )?>
</p>