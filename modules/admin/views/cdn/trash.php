<?php

	parse_str( $this->input->server( 'QUERY_STRING' ), $_query );
	$_query = array_filter( $_query );
	$_query = $_query ? '?' . http_build_query( $_query ) : '';
	$_return = $_query ? '?return=' . urlencode( uri_string() . $_query ) : '';

?>
<div class="group-cdn browse trash">
	<p>
		The following items are currently in the CDN trash.
		<?=anchor( 'admin/cdnadmin/purge' . $_return, 'Empty Trash', 'style="float:right" data-title="Are you sure?" data-body="Purging the trash will <strong>permanently</strong> delete items." class="confirm awesome small red"' )?>
	</p>
	<hr />
	<p class="system-alert no-close">
		<strong>TODO:</strong> facility for browsing trashed CDN objects, plus empty trash facility.
	</p>
</div>