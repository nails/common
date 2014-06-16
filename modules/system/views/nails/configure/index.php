<div class="container">
	<div id="nails-configure">
		<h1>Nails. Configuration Manager</h1>
		<p class="system-alert notice testing">
			<?=img( NAILS_ASSETS_URL . 'img/loader/20px-TRANS.gif' )?>
			<strong>Hey!</strong> Please exscuse me while I run some tests...
		</p>

		<ul class="tabs">
			<?php $_active = $this->input->post( 'update' ) == 'modules' || ! $this->input->post() ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-modules">Modules</a>
			</li>
			<?php $_active = $this->input->post( 'update' ) == 'deploy' ? 'active' : ''?>
			<li class="tab <?=$_active?>">
				<a href="#" data-tab="tab-deploy">Deploy Settings</a>
			</li>

		</ul>

		<section class="tabs pages">

			<?php $_display = $this->input->post( 'update' ) == 'app' || ! $this->input->post() ? 'active' : ''?>
			<div id="tab-modules" class="tab page <?=$_display?> modules">
				<table class="modules">
					<thead>
						<tr>
							<th>Module</th>
							<th>Description</th>
							<th>Dependencies</th>
							<th>Enabled</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Auth</td>
							<td>The auth module allows the site to cater for authenticated users.</td>
							<td>Admin</td>
							<td>No</td>
							<td>
								<?=anchor( '', 'Install', 'class="awesome small green"' )?>
							</td>
						</tr>
						<tr>
							<td>Admin</td>
							<td>The Admin module allows certain authenticated users to manage the site.</td>
							<td>Auth</td>
							<td>Yes</td>
							<td>
								<?=anchor( $_SERVER['REQUEST_URI'], 'Reinstall', 'class="awesome small confirm" data-title="Are you sure?" data-body="Reinstalling will destroy any existing content"' )?>
								<?=anchor( $_SERVER['REQUEST_URI'], 'Uninstall', 'class="awesome small confirm red" data-title="Are you sure?" data-body="Database data will be left (so the module can be re-enabled if nessecary)."' )?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<?php $_display = $this->input->post( 'update' ) == 'deploy' ? 'active' : ''?>
			<div id="tab-deploy" class="tab page <?=$_display?> deploy">
				<p>
					These settings are specific to this deployment only.
				</p>
			</div>

		</section>
	</div>
	<div id="nails-configure-footer">
		Powered by <a href="http://nailsapp.co.uk">Nails.</a> form <a href="http://shedcollective.org">Shed Collective</a>
	</div>
</div>
<script type="text/javascript">

	var _config;

	$(function(){

		_config = new NAILS_Configure();
		_config.init( '<?=$this->input->get( 'token' )?>', '<?=$this->input->get( 'guid' )?>', '<?=$this->input->get( 'time' )?>' );

	});

</script>