	<!--  start page-heading -->
	<div id="page-heading">
		<h1><?=(count($_posts) > 1)? lang('blog_title_delete_plural') : lang('blog_title_delete')?></h1>
		<span style="float:right;margin-right:30px;position:relative;bottom:20px;font-size:0.9em;">
			<?=sprintf(lang('logged_in_as'), $this->auth->get_user()->email)?>
		</span>
	</div>
	<!-- end page-heading -->

	<table border="0" width="100%" cellpadding="0" cellspacing="0" id="content-table">
	<tr>
		<th rowspan="3" class="sized"><img src="<?=site_url( HC_ADMIN_SKIN_URL.'_assets/img/shared/side_shadowleft.jpg' )?>" width="20" height="300" alt="" /></th>
		<th class="topleft"></th>
		<td id="tbl-border-top">&nbsp;</td>
		<th class="topright"></th>
		<th rowspan="3" class="sized"><img src="<?=site_url( HC_ADMIN_SKIN_URL.'_assets/img/shared/side_shadowright.jpg' )?>" width="20" height="300" alt="" /></th>
	</tr>
	<tr>
		<td id="tbl-border-left"></td>
		<td>
		<!--  start content-table-inner ...................................................................... START -->
		<div id="content-table-inner">
		
			<!--  start table-content  -->
			<div id="table-content">
			
					<?php if (isset($del_error)) :?>
						<div id="message-red">
						<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="red-left"><?=$del_error?></td>
							<td class="red-right"><a class="close-red"><?=img(HC_ADMIN_SKIN_URL.'_assets/img/shared/icon_close_red.gif')?></a></td>
						</tr>
						</table>
						</div>
						<!--  end message-yellow -->

					<?php else : ?>
					<!--  start message-yellow -->
						<div id="message-yellow">
						<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="yellow-left">
							
							<?php if (count($_posts) == 1) : ?>
								<?=str_replace('\n', ' ', sprintf(lang('confirm_delete'),$_posts[0]->title))?>
							<?php else : ?>
							<?=lang('blog_confirm_delete_multiple')?>
							<?php endif; ?>
							</td>
							<td class="yellow-right"><a class="close-yellow"><?=img(HC_ADMIN_SKIN_URL.'_assets/img/shared/icon_close_yellow.gif')?></a></td>
						</tr>
						</table>
						</div>
						
						<?php if (count($_posts) > 1) : ?>
							<ul style="list-style:none;margin-left:20px;">
								<?=form_open(site_url("admin/blog/delete/multiple/confirm"))?>
								<?php foreach($_posts AS $p) : ?>
									<?=form_hidden('blog_list[]', $p->id)?>
									<li style="border-bottom:1px dashed #efefef;padding: 5px 0;">&rsaquo; <?=$p->title?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<p style="text-align:center;margin-top:30px;">
							<?php if (count($_posts) == 1) : ?>
								<a href="<?=site_url("admin/blog/delete/{$_posts[0]->id}/confirm")?>" style="margin-right:5px;" class="awesome red"><?=lang('blog_confirm_delete_button_yes')?></a>
								<a href="<?=site_url("admin/blog")?>" class="awesome"><?=lang('blog_confirm_delete_button_no')?></a>
							<?php else: ?>
								<input type="submit" name="submit" value="<?=lang('blog_confirm_delete_button_multiple_yes')?>" style="margin-right:5px;" class="awesome red" />
								<a href="<?=site_url("admin/blog")?>" class="awesome"><?=lang('blog_confirm_delete_button_multiple_no')?></a>
								</form>
							<?php endif; ?>
						</p>
						<!--  end message-yellow -->
					<?php endif; ?>


			</div>
			<!--  end table-content  -->
	
			<div class="clear"></div>
		 
		</div>
		<!--  end content-table-inner ............................................END  -->
		</td>
		<td id="tbl-border-right"></td>
	</tr>
	<tr>
		<th class="sized bottomleft"></th>
		<td id="tbl-border-bottom">&nbsp;</td>
		<th class="sized bottomright"></th>
	</tr>
	</table>
	<div class="clear">&nbsp;</div>