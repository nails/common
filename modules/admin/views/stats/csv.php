id,url,type_id,type_label,user_id,first_name,last_name,email,gender
<?php


	foreach ( $events AS $event ) :


		echo $event->id . ',';
		echo '"' . str_replace( '"', '\"', $event->url ) . '",';
		echo $event->type->id . ',';
		echo $event->type->label ? '"' . str_replace( '"', '\"', $event->type->label ) . '",' : '"' . title_case( str_replace( '"', '\"', str_replace( '_', ' ',  $event->type->slug ) ) ) . '",' ;
		echo $event->user->id . ',';
		echo '"' . str_replace( '"', '\"', $event->user->first_name ) . '",';
		echo '"' . str_replace( '"', '\"', $event->user->last_name ) . '",';
		echo '"' . str_replace( '"', '\"', $event->user->email ) . '",';
		echo '"' . str_replace( '"', '\"', $event->user->gender ) . '"';
		echo "\n";

	endforeach;

?>