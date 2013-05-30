<div class="container cms-page slug-<?=str_replace( '/', '-', $page->slug )?>">
	<?php


		switch( $page->layout ) :

			case 'hero-sidebar-left' :

				$this->load->view( 'cms/page/_hero' );

				echo '<div class="row">';
				$this->load->view( 'cms/page/_sidebar', array( 'position' => 'left' ) );
				$this->load->view( 'cms/page/_body', array( 'position' => 'right' ) );
				echo '</div>';

			break;

			// --------------------------------------------------------------------------

			case 'hero-sidebar-right' :

				$this->load->view( 'cms/page/_hero' );

				echo '<div class="row">';
				$this->load->view( 'cms/page/_body', array( 'position' => 'left' ) );
				$this->load->view( 'cms/page/_sidebar', array( 'position' => 'right' ) );
				echo '</div>';

			break;

			// --------------------------------------------------------------------------

			case 'hero-full-width' :

				$this->load->view( 'cms/page/_hero' );

				echo '<div class="row">';
				$this->load->view( 'cms/page/_body', array( 'position' => 'full-width' ) );
				echo '</div>';

			break;

			// --------------------------------------------------------------------------

			case 'no-hero-sidebar-left' :

				echo '<div class="row">';
				$this->load->view( 'cms/page/_sidebar', array( 'position' => 'left' ) );
				$this->load->view( 'cms/page/_body', array( 'position' => 'right' ) );
				echo '</div>';

			break;

			// --------------------------------------------------------------------------

			case 'no-hero-sidebar-right' :

				echo '<div class="row">';
				$this->load->view( 'cms/page/_body', array( 'position' => 'left' ) );
				$this->load->view( 'cms/page/_sidebar', array( 'position' => 'right' ) );
				echo '</div>';

			break;

			// --------------------------------------------------------------------------

			case 'no-hero-full-width' :

				echo '<div class="row">';
				$this->load->view( 'cms/page/_body', array( 'position' => 'full-width' ) );
				echo '</div>';

			break;

		endswitch;

	?>
</div>