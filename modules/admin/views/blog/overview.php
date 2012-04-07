<?php

	$asc_active		= APPPATH . 'modules/admin/views/_assets/img/sort/asc-active.png';
	$asc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/asc-inactive.png';
	$desc_active	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-active.png';
	$desc_inactive	= APPPATH . 'modules/admin/views/_assets/img/sort/desc-inactive.png';
	$search			= ( isset( $page->search ) && $page->search !== FALSE) ? '?search=' . urlencode( $page->search ) : NULL;
				
?>


<script type="text/javascript">
<!--// Hide from old browsers
	
	$( function() {
		
		$( 'a.delete' ).click( function() {
		
			return confirm( 'Delete this blog post?\n\nThis action os not undoable.\n\nContinue?' );
		
		});
		
		$( 'a.fancybox' ).fancybox();
		
	})
	
//-->
</script>

<h1>
	Blog &rsaquo; Post Overview
	<?=(isset($page->search) && $page->search !== FALSE) ? " (".sprintf(lang('search_title_results'), $page->search, $pagination->total).")" : NULL?>
</h1>

<p>
	Manage your blog posts from this page.
		<?=anchor( 'admin/blog/add', 'Write New Post', 'class="right a-button a-button-small"' )?>
</p>

<hr />

<section>
	<table>
		<thead>
			<tr>
				
				<!--	TITLE	-->
				<?php $col = "title"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th>
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>
					<?=anchor( 'admin/blog/index/' . $col . '/' . $sortmode . $search, 'Title' . $img )?>
				</th>
				
				
				<!--	AUTHOR	-->
				<?php $col = "author_first"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th>
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>

					<?=anchor( 'admin/blog/index/' . $col . '/' . $sortmode . $search, 'Author' . $img )?>
				</th>
				
				
				<!--	MODIFIED	-->
				<?php $col = "modified"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th>
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>

					<?=anchor( 'admin/blog/index/' . $col . '/' . $sortmode . $search,  'Last Modified' . $img )?>
				</th>
				
				
				<!--	STATUS	-->
				<?php $col = "status"; ?>
				<?php $sortmode = ( $order_col == $col && $order_dir == 'asc' ) ? 'desc' : 'asc' ?>
				<th>
					<?php
						
						if ( $order_dir == 'asc' )
							$img = ( $order_col == $col )	? img( $asc_active )	: img( $desc_inactive );
						
						if ( $order_dir == 'desc' )
							$img = ( $order_col == $col )	? img( $desc_active )	: img( $desc_inactive );
					
					?>

					<?=anchor( 'admin/blog/index/' . $col . '/' . $sortmode . $search, 'Status' . $img )?>
				</th>
				
				
				<!--	OPTIONS	-->
				<th>Options</th>
				
			</tr>
		</thead>
		
		<tbody>
		<?php if ( count( $posts ) == 0 ) : ?>
		
			<tr>
				<td colspan="9" style="padding:40px;text-align:center;color:#ccc;font-size:1.5em;font-style:italic">
					<?=lang('search_no_records')?>
				</td>
			</tr>
		
		<?php else: ?>

			<?php $i=0; foreach($posts AS $post) : ?>
			<tr>
				<td>
					<?php
					
						$_img_style	= 'width:75px;height:45px;float:left;margin-right:10px;';
						
						if ( ! empty( $post->featured_img ) ) :
							
							$_url	= CDN_SERVER . 'blog/featured/'.$post->featured_img;
							$_img	= img( array( 'src' => cdn_thumb( 'blog/featured', $post->featured_img, 75, 45 ), 'style' => $_img_style ) );
							echo anchor( $_url, $_img, 'class="fancybox"' );
							
						else :
						
							echo img( array( 'src' => cdn_placeholder( 75, 45, 1 ), 'style' => $_img_style ) );
							
						endif;
					?>
					<?=($post->status == 0) ? '<strong style="text-transform:uppercase;">' . lang('blog_status_draft') . ':</strong>' : NULL ?>
					<?=title_case( $post->title )?>
					<?=( ! empty( $post->body ) ) ? '<div style="padding-top:5px;color:#ccc;font-style:italic;max-width:450px;text-overflow: ellipsis;-o-text-overflow: ellipsis;overflow:hidden;white-space:nowrap"> &rsaquo; ' . character_limiter( strip_tags( $post->body ), 300 ) . '</div>' : NULL?></td>
				<td><?=title_case( $post->author_first . ' ' . $post->author_last )?></td>
				<td><?=nice_time( strtotime( $post->modified ) )?></td>
				<td>
					<?php
						switch($post->status) :
							case 0:	echo lang('blog_status_draft');		break;
							case 1:	echo lang('blog_status_published');	break;
						endswitch;
					?>
				</td>
				<td>
				
					<?=anchor( 'admin/blog/edit/' . $post->id . '?return_to=' . urlencode( uri_string() . '?'. $_SERVER['QUERY_STRING'] ),		'Edit',		'class="a-button a-button-small"' )?>
					<?=anchor( 'admin/blog/delete/' . $post->id . '/confirm?return_to=' . urlencode( uri_string() . '?'. $_SERVER['QUERY_STRING'] ),	'Delete',	'class="a-button a-button-small a-button-red delete"' )?>
					
				</td>
			</tr>
			<?php $i++; endforeach; ?>
			
		<?php endif; ?>
		</tbody>
		
	</table>
</section>

<aside>
	<!--	PAGINATION	-->
	<?php if ( isset( $pagination ) ) : ?>
	<ul class="pagination">
	
		<?php
		
			if ( $pagination->page != 0 ) :
			
				$prev = ( $pagination->page - 1 >= 0 ) ? $pagination->page-1 : 0;
				echo '<li class="previous start">'	. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/0' . $search . $filter, '&laquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="previous">'		. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $prev . $search . $filter, '&lsaquo;', 'class="a-button"' ) . '</li>';
			
			else :
			
				echo '<li class="previous start disabled"><a href="#" class="a-button" onclick="return false;">&laquo;</a></li>';
				echo '<li class="previous disabled"><a href="#" class="a-button" onclick="return false;">&lsaquo;</a></li>';
				
			endif;
			
		?>
		
		<li class="info">Page <strong><?php $pagination->page++; echo $pagination->page;?></strong> / <?=( $pagination->num_pages == 0 ) ? 1 : $pagination->num_pages?></li>
		
		
		<?php
		
			if  ($pagination->page != $pagination->num_pages && $pagination->num_pages != 0 ) :
				
				$next = ( $pagination->page < $pagination->num_pages ) ? $pagination->page : $pagination->num_pages - 1;
				$pagination->num_pages--;
				
				echo '<li class="next">'		. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $next . $search . $filter, '&rsaquo;', 'class="a-button"' ) . '</li>';
				echo '<li class="next end">'	. anchor( 'admin/accounts/' . $method . '/' . $order_col . '/' . $order_dir . '/' . $pagination->num_pages . $search . $filter, '&raquo;', 'class="a-button"' ) . '</li>';
			
			else :
			
				echo '<li class="next end disabled"><a href="#" class="a-button" onclick="return false;">&rsaquo;</a></li>';
				echo '<li class="next disabled"><a href="#"  class="a-button" onclick="return false;">&raquo;</a></li>';
	
			endif;
			
		?>
	</ul>
	<?php endif; ?>
</aside>