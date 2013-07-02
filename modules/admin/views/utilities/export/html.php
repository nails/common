<table border="1" cellspacing="1" cellpadding="1">
<thead>
<tr>
<?php

	echo '<th>' . implode( '</th>' . "\n" . '<th>', $fields ) . '</th>' . "\n";

?>
</tr>
</thead>
<tbody>
<?php

for ( $i=0; $i< count( $data ); $i++ ) :

	echo '<tr>' . "\n";

	$_data = array_values($data[$i]);
	for ( $x=0; $x < count( $_data ); $x++ ) :

		echo '<td>' . $_data[$x] . '</td>' . "\n";

	endfor;

	echo '</tr>' . "\n";

endfor;

?>
</tbody>
</table>