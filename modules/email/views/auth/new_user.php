<?php if ( isset( $admin ) && $admin ) : ?>
<p>
	<?=$admin?>, an administrator for <?=APP_NAME?>, has just created a new <em><?=$group_name?></em> account for you.
</p>
<?php else : ?>
<p>
	Thank you for registering at the <?=APP_NAME?> website.
</p>
<?php endif; ?>
<?php if ( isset( $password ) && $password ) : ?>
<p>
	Your password is <strong><?=$password?></strong><?=isset( $temp_pw ) && $temp_pw ? ', you will be asked to set this to something more memorable when you log in.' : ''?>.
</p>
<?php endif; ?>