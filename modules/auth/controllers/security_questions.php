<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		Auth [security questions]
 *
 * Description:	This controller handles two factor authentication
 *
 **/

/**
 * OVERLOADING NAILS' AUTH MODULE
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

require_once '_auth.php';

class NAILS_Security_questions extends NAILS_Auth_Controller
{
	public function _remap()
	{
		if ( APP_AUTH_TWO_FACTOR ) :

			$_return_to	= $this->input->get( 'return_to' );
			$_remember	= $this->input->get( 'remember' );
			$_user_id	= $this->uri->segment( 3 );
			$_user		= $this->user->get_by_id( $_user_id );

			if ( ! $_user ) :

				$this->session->set_flashdata( 'error', lang( 'auth_twofactor_token_unverified' ) );

				if ( $_return_to ):

					redirect( 'auth/login?return_to=' . $_return_to );
					return;

				else :

					redirect( 'auth/login' );
					return;

				endif;

			endif;

			$_salt			= $this->uri->segment( 4 );
			$_token			= $this->uri->segment( 5 );
			$_ip			= $this->input->ip_address();
			$_login_method	= $this->uri->segment( 6 ) ? $this->uri->segment( 6 ) : 'native';

			//	Safety first
			switch( $_login_method ) :

				case 'facebook' :
				case 'twitter' :
				case 'linkedin' :
				case 'native' :

				//	All good, homies.

				break;

				default :

					$_login_method = 'native';

				break;

			endswitch;

			if ( $this->auth_model->verify_two_factor_token( $_user->id, $_salt, $_token, $_ip ) ) :

				//	Token is valid, generate a new one for the next request
				$this->data['token'] = $this->auth_model->generate_two_factor_token( $_user->id );

				//	Set data for the views
				$this->data['user_id']		= $_user->id;
				$this->data['login_method']	= $_login_method;
				$this->data['return_to']	= $_return_to;
				$this->data['remember']		= $_remember;

				if ( $this->input->post( 'answer' ) ) :

					//	Validate the answer, if correct then log user in and forward, if not
					//	then generate a new token and show errors

					$this->data['question'] = $this->user->get_security_question( $_user->id );
					$_valid					= $this->user->validate_security_answer( $this->data['question']->id, $_user->id, $this->input->post( 'answer' ) );

					if ( $_valid ) :

						//	Set login data for this user
						$this->user->set_login_data( $_user->id );

						//	If we're remembering this user set a cookie
						if ( $_remember ) :

							$this->user->set_remember_cookie( $_user->id, $_user->password, $_user->email );

						endif;

						//	Update their last login and increment their login count
						$this->user->update_last_login( $_user->id );

						// --------------------------------------------------------------------------

						//	Generate an event for this log in
						create_event( 'did_log_in', $_user->id, 0, NULL, array( 'method' => $_login_method ) );

						// --------------------------------------------------------------------------

						//	Say hello
						if ( $_user->last_login ) :

							$this->load->helper( 'date' );

							$_last_login = $this->config->item( 'auth_show_nicetime_on_login' ) ? nice_time( strtotime( $_user->last_login ) ) : user_datetime( $_user->last_login );

							if ( $this->config->item( 'auth_show_last_ip_on_login' ) ) :

								$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_with_ip', array( $_user->first_name, $_last_login, $_user->last_ip ) ) );

							else :

								$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome', array( $_first_name, $_last_login ) ) );

							endif;

						else :

							$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_notime', array( $_user->first_name ) ) );

						endif;



						// --------------------------------------------------------------------------

						//	Delete the token we generated, its no needed, eh!
						$this->auth_model->delete_two_factor_token( $this->data['token']['id'] );

						// --------------------------------------------------------------------------

						$_redirect = $_return_to != site_url() ? $_return_to : $_user->group_homepage;

						redirect( $_redirect );
						return;

					else :

						$this->data['error'] = lang( 'auth_twofactor_answer_incorrect' );

						//	Ask away cap'n!
						$this->data['page']->title = lang( 'auth_twofactor_answer_title' );

						$this->load->view( 'structure/header',				$this->data );
						$this->load->view( 'auth/security_question/ask',	$this->data );
						$this->load->view( 'structure/footer',				$this->data );

					endif;


				else :

					//	Determine whether the user has any security questions set
					$this->data['question'] = $this->user->get_security_question( $_user->id );

					if ( $this->data['question'] ) :

						//	Ask away cap'n!
						$this->data['page']->title = 'Security Question';

						$this->load->view( 'structure/header',				$this->data );
						$this->load->view( 'auth/security_question/ask',	$this->data );
						$this->load->view( 'structure/footer',				$this->data );

					else :

						//	Auth config stuffz
						$this->data['questions']			= $this->config->item( 'auth_two_factor_questions' );
						$this->data['num_questions']		= count( $this->data['questions'] ) < $this->config->item( 'auth_two_factor_num_questions' ) ? count( $this->data['questions'] ) : $this->config->item( 'auth_two_factor_num_questions' );
						$this->data['num_custom_questions']	= $this->config->item( 'auth_two_factor_num_custom_question' );

						if ( $this->data['num_questions'] + $this->data['num_custom_questions'] <= 0 ) :

							show_fatal_error( 'Two-factor auth is enabled, but no questions available', 'A user tried to set security questions but there are no questions available for them to choose. Please ensure auth.php is configured correctly.' );

						endif;

						if ( $this->input->post() ) :

							$this->load->library( 'form_validation' );

							for( $i = 0; $i < $this->data['num_questions']; $i++ ) :

								$this->form_validation->set_rules( 'question[' . $i . '][question]',	'',	'xss_clean|required|is_natural_no_zero' );
								$this->form_validation->set_rules( 'question[' . $i . '][answer]',		'',	'xss_clean|trim|required' );

							endfor;

							for( $i = 0; $i < $this->data['num_custom_questions']; $i++ ) :

								$this->form_validation->set_rules( 'custom_question[' . $i . '][question]',	'',	'xss_clean|trim|required' );
								$this->form_validation->set_rules( 'custom_question[' . $i . '][answer]',		'',	'xss_clean|trim|required' );

							endfor;

							$this->form_validation->set_message( 'required', lang( 'fv_required' ) );
							$this->form_validation->set_message( 'is_natural_no_zero', lang( 'fv_required' ) );

							if ( $this->form_validation->run() ) :

								//	Make sure that we have different questions
								$_question_index	= array();
								$_question			= (array) $this->input->post( 'question' );
								$_error				= FALSE;

								foreach ( $_question AS $q ) :

									if ( array_search( $q['question'], $_question_index ) === FALSE ) :

										$_question_index[] = $q['question'];

									else :

										$_error = TRUE;
										break;

									endif;

								endforeach;

								$_question_index	= array();
								$_question			= (array) $this->input->post( 'custom_question' );

								foreach ( $_question AS $q ) :

									if ( array_search( $q['question'], $_question_index ) === FALSE ) :

										$_question_index[] = $q['question'];

									else :

										$_error = TRUE;
										break;

									endif;

								endforeach;

								if ( ! $_error ) :

									//	Good arrows. Save questions
									$_data = array();

									if ( $this->input->post( 'question' ) ) :

										foreach ( $this->input->post( 'question' ) AS $q ) :

											$_temp				= new stdClass();
											$_temp->question	= isset( $this->data['questions'][$q['question']-1] ) ? $this->data['questions'][$q['question']-1] : NULL;
											$_temp->answer		= $q['answer'];

											$_data[] = $_temp;

										endforeach;

									endif;

									if ( $this->input->post( 'custom_question' ) ) :

										foreach ( (array) $this->input->post( 'custom_question' ) AS $q ) :

											$_temp				= new stdClass();
											$_temp->question	= trim( $q['question'] );
											$_temp->answer		= $q['answer'];

											$_data[] = $_temp;

										endforeach;

									endif;

									if ( $this->user->set_security_questions( $_user->id, $_data ) ) :

										//	Set login data for this user
										$this->user->set_login_data( $_user->id );

										//	If we're remembering this user set a cookie
										if ( $_remember ) :

											$this->user->set_remember_cookie( $_user->id, $_user->password, $_user->email );

										endif;

										//	Update their last login and increment their login count
										$this->user->update_last_login( $_user->id );

										// --------------------------------------------------------------------------

										//	Generate an event for this log in
										create_event( 'did_log_in', $_user->id, 0, NULL, array( 'method' => $_login_method ) );

										// --------------------------------------------------------------------------

										//	Say hello
										if ( $_user->last_login ) :

											$this->load->helper( 'date' );

											$_last_login = $this->config->item( 'auth_show_nicetime_on_login' ) ? nice_time( strtotime( $_user->last_login ) ) : user_datetime( $_user->last_login );

											if ( $this->config->item( 'auth_show_last_ip_on_login' ) ) :

												$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_with_ip', array( $_user->first_name, $_last_login, $_user->last_ip ) ) );

											else :

												$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome', array( $_first_name, $_last_login ) ) );

											endif;

										else :

											$this->session->set_flashdata( 'message', lang( 'auth_login_ok_welcome_notime', array( $_user->first_name ) ) );

										endif;

										// --------------------------------------------------------------------------

										//	Delete the token we generated, its no needed, eh!
										$this->auth_model->delete_two_factor_token( $this->data['token']['id'] );

										// --------------------------------------------------------------------------

										$_redirect = $_return_to != site_url() ? $_return_to : $_user->group_homepage;
										redirect( $_redirect );
										return;

									else :

										$this->data['error'] = lang( 'auth_twofactor_question_set_fail' ) . ' ' . $this->user->last_error();

									endif;

								else :

									$this->data['error'] = lang( 'auth_twofactor_question_unique' );

								endif;

							else :

								$this->data['error'] = lang( 'fv_there_were_errors' );

							endif;

						endif;

						//	No questions, request they set them
						$this->data['page']->title = lang( 'auth_twofactor_question_set_title' );

						$this->load->view( 'structure/header',				$this->data );
						$this->load->view( 'auth/security_question/set',	$this->data );
						$this->load->view( 'structure/footer',				$this->data );

					endif;

				endif;

			else :

				$this->session->set_flashdata( 'error', lang( 'auth_twofactor_token_unverified' ) );

				if ( $this->input->get( 'return_to' ) ):

					redirect( 'auth/login?return_to=' . $this->input->get( 'return_to' ) );
					return;

				else :

					redirect( 'auth/login' );
					return;

				endif;

			endif;

		else :

			show_404();

		endif;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' AUTH MODULE
 *
 * The following block of code makes it simple to extend one of the core auth
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_SECURITY_QUESTIONS' ) ) :

	class Security_questions extends NAILS_Security_questions
	{
	}

endif;

/* End of file security_questions.php */
/* Location: ./application/modules/auth/controllers/security_questions.php */