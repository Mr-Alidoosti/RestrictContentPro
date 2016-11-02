<?php
/*
Plugin Name: درگاه پرداخت زرین پال برای Restrict Content Pro
Version: 1.1.0
Requires at least: 3.5
Description: درگاه پرداخت <a href="http://www.zarinpal.com/" target="_blank"> زرین پال </a> برای افزونه Restrict Content Pro | از سری محصولات وب سایت <a href="http://webforest.ir">وب فارست</a>
Plugin URI: http://webforest.ir/
Author: حنّان ابراهیمی ستوده
Author URI: http://hannanstd.ir/
License: GPL 2
*/
if (!defined('ABSPATH')) exit;
require_once('HANNANStd_Session.php');
if (!class_exists('RCP_ZarinPal') ) {
	class RCP_ZarinPal {
	
		public function __construct() {
			add_action('init', array($this, 'ZarinPal_Verify_By_HANNANStd'));
			add_action('rcp_payments_settings', array($this, 'ZarinPal_Setting_By_HANNANStd'));
			add_action('rcp_gateway_ZarinPal', array($this, 'ZarinPal_Request_By_HANNANStd'));
			add_filter('rcp_payment_gateways', array($this, 'ZarinPal_Register_By_HANNANStd'));
			if (!function_exists('RCP_IRAN_Currencies_By_HANNANStd') && !function_exists('RCP_IRAN_Currencies'))
				add_filter('rcp_currencies', array($this, 'RCP_IRAN_Currencies_By_HANNANStd'));
		}

		public function RCP_IRAN_Currencies_By_HANNANStd( $currencies ) {
			unset($currencies['RIAL']);
			$currencies['تومان'] = __('تومان', 'rcp_zarinpal');
			$currencies['ریال'] = __('ریال', 'rcp_zarinpal');
			return $currencies;
		}
				
		public function ZarinPal_Register_By_HANNANStd($gateways) {
			global $rcp_options;
			$gateways['ZarinPal'] = isset($rcp_options['zarinpal_name']) ? $rcp_options['zarinpal_name'] : __( 'زرین پال', 'rcp_zarinpal');
			return $gateways;
		}

		public function ZarinPal_Setting_By_HANNANStd($rcp_options) {
		?>	
			<hr/>
			<table class="form-table">
				<?php do_action( 'RCP_ZarinPal_before_settings', $rcp_options ); ?>
				<tr valign="top">
					<th colspan=2><h3><?php _e( 'تنظیمات زرین پال', 'rcp_zarinpal' ); ?></h3></th>
				</tr>				
				<tr valign="top">
					<th>
						<label for="rcp_settings[zarinpal_server]"><?php _e( 'سرور زرین پال', 'rcp_zarinpal' ); ?></label>
					</th>
					<td>
						<select id="rcp_settings[zarinpal_server]" name="rcp_settings[zarinpal_server]">
							<option value="German" <?php selected('German', isset($rcp_options['zarinpal_server']) ? $rcp_options['zarinpal_server'] : '' ); ?>><?php _e( 'آلمان', 'rcp_zarinpal' ); ?></option>
							<option value="Iran" <?php selected('Iran', isset($rcp_options['zarinpal_server']) ? $rcp_options['zarinpal_server'] : '' ); ?>><?php _e( 'ایران', 'rcp_zarinpal' ); ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label for="rcp_settings[zarinpal_merchant]"><?php _e( 'مرچنت زرین پال', 'rcp_zarinpal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[zarinpal_merchant]" style="width: 300px;" name="rcp_settings[zarinpal_merchant]" value="<?php if( isset( $rcp_options['zarinpal_merchant'] ) ) { echo $rcp_options['zarinpal_merchant']; } ?>"/>
					</td>
				</tr>				
				<tr valign="top">
					<th>
						<label for="rcp_settings[zarinpal_query_name]"><?php _e( 'نام لاتین درگاه', 'rcp_zarinpal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[zarinpal_query_name]" style="width: 300px;" name="rcp_settings[zarinpal_query_name]" value="<?php echo isset($rcp_options['zarinpal_query_name']) ? $rcp_options['zarinpal_query_name'] : 'ZarinPal'; ?>"/>
						<div class="description"><?php _e( 'این نام در هنگام بازگشت از بانک در آدرس بازگشت از بانک نمایان خواهد شد . از به کاربردن حروف زائد و فاصله جدا خودداری نمایید . این نام باید با نام سایر درگاه ها متفاوت باشد .', 'rcp_zarinpal' ); ?></div>
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label for="rcp_settings[zarinpal_name]"><?php _e( 'نام نمایشی درگاه', 'rcp_zarinpal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[zarinpal_name]" style="width: 300px;" name="rcp_settings[zarinpal_name]" value="<?php echo isset($rcp_options['zarinpal_name']) ? $rcp_options['zarinpal_name'] : __( 'زرین پال', 'rcp_zarinpal'); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label><?php _e( 'تذکر ', 'rcp_zarinpal' ); ?></label>
					</th>
					<td>
						<div class="description"><?php _e( 'از سربرگ مربوط به ثبت نام در تنظیمات افزونه حتما یک برگه برای بازگشت از بانک انتخاب نمایید . ترجیحا نامک برگه را لاتین قرار دهید .<br/> نیازی به قرار دادن شورت کد خاصی در برگه نیست و میتواند برگه ی خالی باشد .', 'rcp_zarinpal' ); ?></div>
					</td>
				</tr>
				<?php do_action( 'RCP_ZarinPal_after_settings', $rcp_options ); ?>
			</table>
			<?php
		}
		
		public function ZarinPal_Request_By_HANNANStd($subscription_data) {
			
			global $rcp_options;
			ob_start();
			$query = isset($rcp_options['zarinpal_query_name']) ? $rcp_options['zarinpal_query_name'] : 'ZarinPal';
			$amount = $subscription_data['price'];
			//fee is just for paypal recurring or ipn gateway ....
			//$amount = $subscription_data['price'] + $subscription_data['fee']; 

			$zarinpal_payment_data = array(
				'user_id'             => $subscription_data['user_id'],
				'subscription_name'     => $subscription_data['subscription_name'],
				'subscription_key'	 => $subscription_data['key'],
				'amount'           => $amount
			);			
			
			$HANNANStd_session = HANNAN_Session::get_instance();
			@session_start();
			$HANNANStd_session['zarinpal_payment_data'] = $zarinpal_payment_data;
			$_SESSION["zarinpal_payment_data"] = $zarinpal_payment_data;	
			
			//Action For ZarinPal or RCP Developers...
			do_action( 'RCP_Before_Sending_to_ZarinPal', $subscription_data );	
		
			if ($rcp_options['currency'] == 'ریال' || $rcp_options['currency'] == 'RIAL' || $rcp_options['currency'] == 'ریال ایران' || $rcp_options['currency'] == 'Iranian Rial (&#65020;)')
				$amount = $amount/10;
			
			//Start of ZarinPal
			$MerchantID = isset($rcp_options['zarinpal_merchant']) ? $rcp_options['zarinpal_merchant'] : '';
			$Amount = intval($amount);
			$Email = isset($subscription_data['user_email']) ? $subscription_data['user_email'] : '-'; 
			$CallbackURL =  add_query_arg('gateway', $query, $subscription_data['return_url']);
			$Description = sprintf(__('خرید اشتراک %s برای کاربر %s', 'rcp_zarinpal'), $subscription_data['subscription_name'],$subscription_data['user_name']);
			$Mobile ='-'; 
			
			
			//Filter For ZarinPal or RCP Developers...
			$Description = apply_filters( 'RCP_ZarinPal_Description', $Description, $subscription_data );
			$Mobile = apply_filters( 'RCP_Mobile', $Mobile, $subscription_data );
			
			
			if( isset( $rcp_options['zarinpal_server'] ) and ($rcp_options['zarinpal_server'] == 'Iran') )
			{	
				$WebServiceUrl = 'https://ir.zarinpal.com/pg/services/WebGate/wsdl';
			}
			else 
			{
				$WebServiceUrl = 'https://de.zarinpal.com/pg/services/WebGate/wsdl';
			}	

			$client = new SoapClient( $WebServiceUrl , array('encoding' => 'UTF-8')); 
			$result = $client->PaymentRequest(
				array(
						'MerchantID' 	=> $MerchantID,
						'Amount' 	=> $Amount,
						'Description' 	=> $Description,
						'Email' 	=> $Email,
						'Mobile' 	=> $Mobile,
						'CallbackURL' 	=> $CallbackURL
					)
			);
			
			/*$result->Status =100;
			$result->Authority =100;*/
	
			if($result->Status == 100)
			{			
				global $rcp_options, $post; ?>

				<?php if( ! is_user_logged_in() ) { ?>
					<h3 class="rcp_header">
						<?php echo apply_filters( 'rcp_registration_header_logged_out', __( 'Register New Account', 'rcp' ) ); ?>
					</h3>
				<?php } else { ?>
					<h3 class="rcp_header">
						<?php echo apply_filters( 'rcp_registration_header_logged_in', __( 'Upgrade Your Subscription', 'rcp' ) ); ?>
					</h3>
				<?php }

				// show any error messages after form submission
				rcp_show_error_messages( 'register' ); ?>

				<form id="rcp_registration_form" class="rcp_form" method="POST" action="<?php echo esc_url( rcp_get_current_url() ); ?>">

					<?php if( ! is_user_logged_in() ) { ?>

					<div class="rcp_login_link">
						<p><?php printf( __( '<a href="%s">Log in</a> if you wish to renew an existing subscription.', 'rcp' ), rcp_get_login_url( rcp_get_current_url() ) ); ?></p>
					</div>

					<?php do_action( 'rcp_before_register_form_fields' ); ?>

					<fieldset class="rcp_user_fieldset">
						<p id="rcp_user_login_wrap">
							<label for="rcp_user_login"><?php echo apply_filters ( 'rcp_registration_username_label', __( 'Username', 'rcp' ) ); ?></label>
							<input name="rcp_user_login" id="rcp_user_login" class="required" type="text" <?php if( isset( $_POST['rcp_user_login'] ) ) { echo 'value="' . esc_attr( $_POST['rcp_user_login'] ) . '"'; } ?>/>
						</p>
						<p id="rcp_user_email_wrap">
							<label for="rcp_user_email"><?php echo apply_filters ( 'rcp_registration_email_label', __( 'Email', 'rcp' ) ); ?></label>
							<input name="rcp_user_email" id="rcp_user_email" class="required" type="text" <?php if( isset( $_POST['rcp_user_email'] ) ) { echo 'value="' . esc_attr( $_POST['rcp_user_email'] ) . '"'; } ?>/>
						</p>
						<p id="rcp_user_first_wrap">
							<label for="rcp_user_first"><?php echo apply_filters ( 'rcp_registration_firstname_label', __( 'First Name', 'rcp' ) ); ?></label>
							<input name="rcp_user_first" id="rcp_user_first" type="text" <?php if( isset( $_POST['rcp_user_first'] ) ) { echo 'value="' . esc_attr( $_POST['rcp_user_first'] ) . '"'; } ?>/>
						</p>
						<p id="rcp_user_last_wrap">
							<label for="rcp_user_last"><?php echo apply_filters ( 'rcp_registration_lastname_label', __( 'Last Name', 'rcp' ) ); ?></label>
							<input name="rcp_user_last" id="rcp_user_last" type="text" <?php if( isset( $_POST['rcp_user_last'] ) ) { echo 'value="' . esc_attr( $_POST['rcp_user_last'] ) . '"'; } ?>/>
						</p>
						<p id="rcp_password_wrap">
							<label for="rcp_password"><?php echo apply_filters ( 'rcp_registration_password_label', __( 'Password', 'rcp' ) ); ?></label>
							<input name="rcp_user_pass" id="rcp_password" class="required" type="password"/>
						</p>
						<p id="rcp_password_again_wrap">
							<label for="rcp_password_again"><?php echo apply_filters ( 'rcp_registration_password_again_label', __( 'Password Again', 'rcp' ) ); ?></label>
							<input name="rcp_user_pass_confirm" id="rcp_password_again" class="required" type="password"/>
						</p>

						<?php do_action( 'rcp_after_password_registration_field' ); ?>

					</fieldset>
					<?php } ?>

					<?php do_action( 'rcp_before_subscription_form_fields' ); ?>

					<fieldset class="rcp_subscription_fieldset">
					<?php $levels = rcp_get_subscription_levels( 'active' );
					if( $levels ) : ?>
						<p class="rcp_subscription_message"><?php echo apply_filters ( 'rcp_registration_choose_subscription', __( 'Choose your subscription level', 'rcp' ) ); ?></p>
						<ul id="rcp_subscription_levels">
							<?php foreach( $levels as $key => $level ) : ?>
								<?php if( rcp_show_subscription_level( $level->id ) ) : ?>
								<li class="rcp_subscription_level rcp_subscription_level_<?php echo $level->id; ?>">
									<input type="radio" id="rcp_subscription_level_<?php echo $level->id; ?>" class="required rcp_level" <?php if ( isset( $_GET['level'] ) ) { checked( $level->id, $_GET['level'] ); } ?> name="rcp_level" rel="<?php echo esc_attr( $level->price ); ?>" value="<?php echo esc_attr( absint( $level->id ) ); ?>" <?php if( $level->duration == 0 ) { echo 'data-duration="forever"'; } ?>/>
									<label for="rcp_subscription_level_<?php echo $level->id; ?>">
										<span class="rcp_subscription_level_name"><?php echo rcp_get_subscription_name( $level->id ); ?></span><span class="rcp_separator">&nbsp;-&nbsp;</span><span class="rcp_price" rel="<?php echo esc_attr( $level->price ); ?>"><?php echo $level->price > 0 ? rcp_currency_filter( $level->price ) : __( 'free', 'rcp' ); ?><span class="rcp_separator">&nbsp;-&nbsp;</span></span>
										<span class="rcp_level_duration"><?php echo $level->duration > 0 ? $level->duration . '&nbsp;' . rcp_filter_duration_unit( $level->duration_unit, $level->duration ) : __( 'unlimited', 'rcp' ); ?></span>
										<div class="rcp_level_description"> <?php echo rcp_get_subscription_description( $level->id ); ?></div>
									</label>

								</li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p><strong><?php _e( 'You have not created any subscription levels yet', 'rcp' ); ?></strong></p>
					<?php endif; ?>
					</fieldset>

					<?php if( rcp_has_discounts() ) : ?>
					<fieldset class="rcp_discounts_fieldset">
						<p id="rcp_discount_code_wrap">
							<label for="rcp_discount_code">
								<?php _e( 'Discount Code', 'rcp' ); ?>
								<span class="rcp_discount_valid" style="display: none;"> - <?php _e( 'Valid', 'rcp' ); ?></span>
								<span class="rcp_discount_invalid" style="display: none;"> - <?php _e( 'Invalid', 'rcp' ); ?></span>
							</label>
							<input type="text" id="rcp_discount_code" name="rcp_discount" class="rcp_discount_code" value=""/>
							<button class="rcp_button" id="rcp_apply_discount"><?php _e( 'Apply', 'rcp' ); ?></button>
						</p>
					</fieldset>
					<?php endif; ?>

					<?php do_action( 'rcp_after_register_form_fields', $levels ); ?>

					<div class="rcp_gateway_fields">
						<?php
						$gateways = rcp_get_enabled_payment_gateways();
						if( count( $gateways ) > 1 ) : $display = rcp_has_paid_levels() ? '' : ' style="display: none;"'; ?>
							<fieldset class="rcp_gateways_fieldset">
								<p id="rcp_payment_gateways"<?php echo $display; ?>>
									<select name="rcp_gateway" id="rcp_gateway">
										<?php foreach( $gateways as $key => $gateway ) : $recurring = rcp_gateway_supports( $key, 'recurring' ) ? 'yes' : 'no'; ?>
											<option value="<?php echo esc_attr( $key ); ?>" data-supports-recurring="<?php echo esc_attr( $recurring ); ?>"><?php echo esc_html( $gateway ); ?></option>
										<?php endforeach; ?>
									</select>
									<label for="rcp_gateway"><?php _e( 'Choose Your Payment Method', 'rcp' ); ?></label>
								</p>
							</fieldset>
						<?php else: ?>
							<?php foreach( $gateways as $key => $gateway ) : $recurring = rcp_gateway_supports( $key, 'recurring' ) ? 'yes' : 'no'; ?>
								<input type="hidden" name="rcp_gateway" value="<?php echo esc_attr( $key ); ?>" data-supports-recurring="<?php echo esc_attr( $recurring ); ?>"/>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>

					<?php do_action( 'rcp_before_registration_submit_field', $levels ); ?>

					<p id="rcp_submit_wrap">
						<input type="hidden" name="rcp_register_nonce" value="<?php echo wp_create_nonce('rcp-register-nonce' ); ?>"/>
						<input type="submit" name="rcp_submit_registration" id="rcp_submit" value="<?php esc_attr_e( apply_filters ( 'rcp_registration_register_button', __( 'Register', 'rcp' ) ) ); ?>"/>
					</p>
				</form>
			<?php
				echo '<script type="text/javascript" src="https://cdn.zarinpal.com/zarinak/v1/checkout.js"></script>
						<script>
						Zarinak.setAuthority( ' . $result->Authority . ');
						Zarinak.open();
						</script>';
				wp_die( sprintf(__('<b> %s </b>', 'rcp_zarinpal'), $this->Fault($result->Status)),'پرداخت','پرداخت' );
			} 
			else
			{	
				wp_die( sprintf(__('متاسفانه پرداخت به دلیل خطای زیر امکان پذیر نمی باشد . <br/><b> %s </b>', 'rcp_zarinpal'), $this->Fault($result->Status)) );
			}
			//End of ZarinPal
				
			exit;
		}
		
		public function ZarinPal_Verify_By_HANNANStd() {
			
			if ( !class_exists('RCP_Payments') )
				return;
			
			if (!isset($_GET['gateway']))
				return;
			
			global $rcp_options, $wpdb, $rcp_payments_db_name;
			@session_start();
			$HANNANStd_session = HANNAN_Session::get_instance();
			if (isset($HANNANStd_session['zarinpal_payment_data']))
				$zarinpal_payment_data = $HANNANStd_session['zarinpal_payment_data'];
			else 
				$zarinpal_payment_data = isset($_SESSION["zarinpal_payment_data"]) ? $_SESSION["zarinpal_payment_data"] : '';
			
			$query = isset($rcp_options['zarinpal_query_name']) ? $rcp_options['zarinpal_query_name'] : 'ZarinPal';
						
			if 	( ($_GET['gateway'] == $query) && $zarinpal_payment_data )
			{
				
				$user_id 			= $zarinpal_payment_data['user_id'];
				$subscription_name 	= $zarinpal_payment_data['subscription_name'];
				$subscription_key 	= $zarinpal_payment_data['subscription_key'];
				$amount 			= $zarinpal_payment_data['amount'];
				
				/*
				$subscription_price = intval(number_format( (float) rcp_get_subscription_price( rcp_get_subscription_id( $user_id ) ), 2)) ;
				*/
				
				$subscription_id    = rcp_get_subscription_id( $user_id );
				$user_data          = get_userdata( $user_id );
				$payment_method =  isset($rcp_options['zarinpal_name']) ? $rcp_options['zarinpal_name'] : __( 'زرین پال', 'rcp_zarinpal');
				
				if( ! $user_data || ! $subscription_id || ! rcp_get_subscription_details( $subscription_id ) )
					return;
				
				$new_payment = 1;
				if( $wpdb->get_results( $wpdb->prepare("SELECT id FROM " . $rcp_payments_db_name . " WHERE `subscription_key`='%s' AND `payment_type`='%s';", $subscription_key, $payment_method ) ) )
					$new_payment = 0;

				unset($GLOBALS['zarinpal_new']);
				$GLOBALS['zarinpal_new'] = $new_payment;
				global $new;
				$new = $new_payment;
				
				if ($new_payment == 1) {
				
					//Start of ZarinPal
					$MerchantID = isset($rcp_options['zarinpal_merchant']) ? $rcp_options['zarinpal_merchant'] : '';
					$Amount = intval($amount);
					if ($rcp_options['currency'] == 'ریال' || $rcp_options['currency'] == 'RIAL' || $rcp_options['currency'] == 'ریال ایران' || $rcp_options['currency'] == 'Iranian Rial (&#65020;)')
						$Amount = $Amount/10;
					
					$Authority = $_GET['Authority'];
					
					if( isset( $rcp_options['zarinpal_server'] ) and ($rcp_options['zarinpal_server'] == 'Iran') )
					{
						$WebServiceUrl = 'https://ir.zarinpal.com/pg/services/WebGate/wsdl';
					}
					else 
					{
						$WebServiceUrl = 'https://de.zarinpal.com/pg/services/WebGate/wsdl';
					}	
					
					if($_GET['Status'] == 'OK'){
						
						$client = new SoapClient( $WebServiceUrl , array('encoding' => 'UTF-8')); 
						$result = $client->PaymentVerification(
							array(
								'MerchantID'	 => $MerchantID,
								'Authority' 	 => $Authority,
								'Amount'	 => $Amount
							)
						);
						
						if($result->Status == 100){
							$payment_status = 'completed';
							$fault = 0;
							$transaction_id = $result->RefID;
						}
						else {
							$payment_status = 'failed';
							$fault = $result->Status;
							$transaction_id = 0;
						}
					} 
					else {
						$payment_status = 'cancelled';
						$fault = 0;
						$transaction_id = 0;
					}
					//End of ZarinPal
				
				
				
					unset($GLOBALS['zarinpal_payment_status']);
					unset($GLOBALS['zarinpal_transaction_id']);
					unset($GLOBALS['zarinpal_fault']);
					unset($GLOBALS['zarinpal_subscription_key']);
					$GLOBALS['zarinpal_payment_status'] = $payment_status;
					$GLOBALS['zarinpal_transaction_id'] = $transaction_id;
					$GLOBALS['zarinpal_subscription_key'] = $subscription_key;
					$GLOBALS['zarinpal_fault'] = $fault;
					global $zarinpal_transaction;
					$zarinpal_transaction = array();
					$zarinpal_transaction['zarinpal_payment_status'] = $payment_status;
					$zarinpal_transaction['zarinpal_transaction_id'] = $transaction_id;
					$zarinpal_transaction['zarinpal_subscription_key'] = $subscription_key;
					$zarinpal_transaction['zarinpal_fault'] = $fault;
				
		
					if ($payment_status == 'completed') 
					{
				
						$payment_data = array(
							'date'             => date('Y-m-d g:i:s'),
							'subscription'     => $subscription_name,
							'payment_type'     => $payment_method,
							'subscription_key' => $subscription_key,
							'amount'           => $amount,
							'user_id'          => $user_id,
							'transaction_id'   => $transaction_id
						);
					
						//Action For ZarinPal or RCP Developers...
						do_action( 'RCP_ZarinPal_Insert_Payment', $payment_data, $user_id );
					
						$rcp_payments = new RCP_Payments();
						$rcp_payments->insert( $payment_data );
					
					
						rcp_set_status( $user_id, 'active' );
					
						
						if( version_compare( RCP_PLUGIN_VERSION, '2.1.0', '<' ) ) {
							rcp_email_subscription_status( $user_id, 'active' );
							if( ! isset( $rcp_options['disable_new_user_notices'] ) )
								wp_new_user_notification( $user_id );
						}
					
						update_user_meta( $user_id, 'rcp_payment_profile_id', $user_id );
					
						update_user_meta( $user_id, 'rcp_signup_method', 'live' );
						//rcp_recurring is just for paypal or ipn gateway
						update_user_meta( $user_id, 'rcp_recurring', 'no' ); 
					
						$subscription = rcp_get_subscription_details( rcp_get_subscription_id( $user_id ) );
						$member_new_expiration = date( 'Y-m-d H:i:s', strtotime( '+' . $subscription->duration . ' ' . $subscription->duration_unit . ' 23:59:59' ) );
						rcp_set_expiration_date( $user_id, $member_new_expiration );	
						delete_user_meta( $user_id, '_rcp_expired_email_sent' );
									
						$log_data = array(
							'post_title'    => __( 'تایید پرداخت', 'rcp_zarinpal' ),
							'post_content'  =>  __( 'پرداخت با موفقیت انجام شد . کد تراکنش : ', 'rcp_zarinpal' ).$transaction_id.__( ' .  روش پرداخت : ', 'rcp_zarinpal' ).$payment_method,
							'post_parent'   => 0,
							'log_type'      => 'gateway_error'
						);

						$log_meta = array(
							'user_subscription' => $subscription_name,
							'user_id'           => $user_id
						);
						
						$log_entry = WP_Logging::insert_log( $log_data, $log_meta );
				

						//Action For ZarinPal or RCP Developers...
						do_action( 'RCP_ZarinPal_Completed', $user_id );				
					}	
					
					
					if ($payment_status == 'cancelled')
					{
					
						$log_data = array(
							'post_title'    => __( 'انصراف از پرداخت', 'rcp_zarinpal' ),
							'post_content'  =>  __( 'تراکنش به دلیل انصراف کاربر از پرداخت ، ناتمام باقی ماند .', 'rcp_zarinpal' ).__( ' روش پرداخت : ', 'rcp_zarinpal' ).$payment_method,
							'post_parent'   => 0,
							'log_type'      => 'gateway_error'
						);

						$log_meta = array(
							'user_subscription' => $subscription_name,
							'user_id'           => $user_id
						);
						
						$log_entry = WP_Logging::insert_log( $log_data, $log_meta );
					
						//Action For ZarinPal or RCP Developers...
						do_action( 'RCP_ZarinPal_Cancelled', $user_id );	

					}	
					
					if ($payment_status == 'failed') 
					{
									
						$log_data = array(
							'post_title'    => __( 'خطا در پرداخت', 'rcp_zarinpal' ),
							'post_content'  =>  __( 'تراکنش به دلیل خطای رو به رو ناموفق باقی باند :', 'rcp_zarinpal' ).$this->Fault($fault).__( ' روش پرداخت : ', 'rcp_zarinpal' ).$payment_method,
							'post_parent'   => 0,
							'log_type'      => 'gateway_error'
						);

						$log_meta = array(
							'user_subscription' => $subscription_name,
							'user_id'           => $user_id
						);
						
						$log_entry = WP_Logging::insert_log( $log_data, $log_meta );
					
						//Action For ZarinPal or RCP Developers...
						do_action( 'RCP_ZarinPal_Failed', $user_id );	
					
					}
			
				}
				add_filter( 'the_content', array($this,  'ZarinPal_Content_After_Return_By_HANNANStd') );
				//session_destroy();	
			}
		}
		 
		
		public function ZarinPal_Content_After_Return_By_HANNANStd( $content ) { 
			
			global $zarinpal_transaction, $new;
			
			$HANNANStd_session = HANNAN_Session::get_instance();
			@session_start();
			
			$new_payment = isset($GLOBALS['zarinpal_new']) ? $GLOBALS['zarinpal_new'] : $new;
			
			$payment_status = isset($GLOBALS['zarinpal_payment_status']) ? $GLOBALS['zarinpal_payment_status'] : $zarinpal_transaction['zarinpal_payment_status'];
			$transaction_id = isset($GLOBALS['zarinpal_transaction_id']) ? $GLOBALS['zarinpal_transaction_id'] : $zarinpal_transaction['zarinpal_transaction_id'];
			$fault = isset($GLOBALS['zarinpal_fault']) ? $this->Fault($GLOBALS['zarinpal_fault']) : $this->Fault($zarinpal_transaction['zarinpal_fault']);
			
			if ($new_payment == 1) 
			{
			
				$zarinpal_data = array(
					'payment_status'             => $payment_status,
					'transaction_id'     => $transaction_id,
					'fault'     => $fault
				);
				
				$HANNANStd_session['zarinpal_data'] = $zarinpal_data;
				$_SESSION["zarinpal_data"] = $zarinpal_data;	
			
			}
			else
			{
				if (isset($HANNANStd_session['zarinpal_data']))
					$zarinpal_payment_data = $HANNANStd_session['zarinpal_data'];
				else 
					$zarinpal_payment_data = isset($_SESSION["zarinpal_data"]) ? $_SESSION["zarinpal_data"] : '';
			
				$payment_status = isset($zarinpal_payment_data['payment_status']) ? $zarinpal_payment_data['payment_status'] : '';
				$transaction_id = isset($zarinpal_payment_data['transaction_id']) ? $zarinpal_payment_data['transaction_id'] : '';
				$fault = isset($zarinpal_payment_data['fault']) ? $this->Fault($zarinpal_payment_data['fault']) : '';
			}
			
			$message = '';
			
			if ($payment_status == 'completed') {
				$message = '<br/>'.__( 'پرداخت با موفقیت انجام شد . کد تراکنش : ', 'rcp_zarinpal' ).$transaction_id.'<br/>';
			}
			
			if ($payment_status == 'cancelled') {
				$message = '<br/>'.__( 'تراکنش به دلیل انصراف شما نا تمام باقی ماند .', 'rcp_zarinpal' );
			}
			
			if ($payment_status == 'failed') {
				$message = '<br/>'.__( 'تراکنش به دلیل خطای زیر ناموفق باقی باند :', 'rcp_zarinpal' ).'<br/>'.$fault.'<br/>';
			}
			
			return $content.$message;
		}
		
		private static function Fault($error) {
			$response	= '';
			switch($error){
			
                case '-1' :
					$response	=  __( 'اطلاعات ارسال شده ناقص است .', 'rcp_zarinpal' );
				break;

				case '-2' :
					$response	=  __( 'آی پی یا مرچنت زرین پال اشتباه است .', 'rcp_zarinpal' );
				break;

				case '-3' :
					$response	=  __( 'با توجه به محدودیت های شاپرک امکان پرداخت با رقم درخواست شده میسر نمیباشد .', 'rcp_zarinpal' );
				break;
                                                
				case '-4' :
					$response	=  __( 'سطح تایید پذیرنده پایین تر از سطح نقره ای میباشد .', 'rcp_zarinpal' );
				break;
										
				case '-11' :
					$response	=  __( 'درخواست مورد نظر یافت نشد .', 'rcp_zarinpal' );
				break;
												
				case '-21' :
					$response	=  __( 'هیچ نوع عملیات مالی برای این تراکنش یافت نشد .', 'rcp_zarinpal' );
				break;
												
				case '-22' :
					$response	=  __( 'تراکنش نا موفق میباشد .', 'rcp_zarinpal' );
                break;
												
				case '-33' :
					$response	=  __( 'رقم تراکنش با رقم وارد شده مطابقت ندارد .', 'rcp_zarinpal' );
				break;
												
				case '-40' :
					$response	=  __( 'اجازه دسترسی به متد مورد نظر وجود ندارد .', 'rcp_zarinpal' );
				break;
												
				case '-54' :
					$response	=  __( 'درخواست مورد نظر آرشیو شده است .', 'rcp_zarinpal' );
				break;
												
				case '100' :
					$response	=  __( 'اتصال با زرین پال به خوبی برقرار شد و همه چیز صحیح است .', 'rcp_zarinpal' );
				break;		
												
				case '101' :
					$response	=  __( 'تراکنش با موفقیت به پایان رسیده بود و تاییدیه آن نیز انجام شده بود .', 'rcp_zarinpal' );
				break;		
			
			}
			
			return $response;
		}
		
	}
}
new RCP_ZarinPal();
if ( !function_exists('change_cancelled_to_pending_By_HANNANStd')) {	
	add_action( 'rcp_set_status', 'change_cancelled_to_pending_By_HANNANStd', 10, 2 );
	function change_cancelled_to_pending_By_HANNANStd( $status, $user_id ) {
		if( 'cancelled' == $status )
			rcp_set_status( $user_id, 'expired' );
			return true;
	}
}
?>