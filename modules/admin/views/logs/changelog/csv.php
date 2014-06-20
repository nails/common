id,user_id,first_name,last_name,email,verb,article,item,item_id,title,item_url,created,field,old_value,new_value
<?php

	foreach ( $items AS $item ) :

		foreach ( $item->changes AS $change ) :

			echo $item->id . ',';
			echo $item->user->id ? (int) $item->user->id . ',' : ',';
			echo $item->user->id ? '"' . str_replace( '"', '""', $item->user->first_name ) . '",' : ',';
			echo $item->user->id ? '"' . str_replace( '"', '""', $item->user->last_name ) . '",' : ',';
			echo $item->user->id ? '"' . str_replace( '"', '""', $item->user->email ) . '",' : ',';
			echo '"' . str_replace( '"', '""', $item->verb ) . '",';
			echo '"' . str_replace( '"', '""', $item->article ) . '",';
			echo '"' . str_replace( '"', '""', $item->item ) . '",';
			echo (int) $item->item_id . ',';
			echo '"' . str_replace( '"', '""', $item->title ) . '",';
			echo '"' . str_replace( '"', '""', site_url( $item->url ) ) . '",';
			echo '"' . str_replace( '"', '""', $item->created ) . '",';
			echo '"' . str_replace( '"', '""', $change->field ) . '",';
			echo '"' . str_replace( '"', '""', $change->old_value ) . '",';
			echo '"' . str_replace( '"', '""', $change->new_value ) . '"';
			echo "\n";

		endforeach;

	endforeach;

?>