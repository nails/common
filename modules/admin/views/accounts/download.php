<pre><?php if ( count( $users ) == 0 ) : ?>
No records found
<?php else : ?>
first_name,last_name,email
<?php foreach ( $users AS $u ) : ?>
"<?=( empty( $u->first_name ) )	? NULL : title_case( $u->first_name )?>","<?=( empty( $u->last_name ) )	? NULL : title_case( $u->last_name )?>","<?=$u->email?>"
<?php endforeach; ?>
<?php endif; ?></pre>