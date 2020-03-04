<?php
/**
 * Class WC_Cielo_Debit_Gateway file.
 * Author Camilo da Silveira
 * Site silveiracamilo.com.br
 *
 * @package WooCommerce\Gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once realpath(__DIR__ . "/../../../").'/vendor/autoload.php';

use CieloWP\CieloWP;
use CieloWP\Gateway\Gateway;
use CieloWP\Order\Order;

/**
 * Cielo debit Payment Gateway.
 *
 * Provides a cielo debit to pay Payment Gateway.
 *
 * @class       WC_Cielo_Debit_Gateway
 * @extends     WC_Payment_Gateway
 */
class WC_Cielo_Debit_Gateway extends WC_Cielo_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'cielo_debit';
		$this->icon               = apply_filters( 'cielo_woocommerce_debit_icon', plugins_url( 'assets/images/cielo.png', plugin_dir_path( __FILE__) . "/../../../../" ) );
		$this->has_fields         = false;
		$this->method_title       = __( 'Cielo - Cartão de bébito', 'cielo-woocommerce' );
		$this->method_description = __( 'Pagamento seguro com cartão de debito via Cielo.', 'cielo-woocommerce' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option( 'title' );
		$this->sandbox = $this->get_option( 'sandbox' );
		$this->merchant_id = $this->get_option( 'merchant_id' );
		$this->merchant_key = $this->get_option( 'merchant_key' );
		$this->description  = $this->get_option( 'description' );
		$this->instructions = $this->get_option( 'instructions' );

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_api_wc_cielo_debit_gateway', array( $this, 'check_return' ) );
		add_action( 'woocommerce_' . $this->id . '_return', array( $this, 'return_handler' ) );
		add_action( 'woocommerce_thankyou_'. $this->id, array( $this, 'thankyou_page' ) );

		// Customer Emails.
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );

		parent::__construct();
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'      => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Habilitar Cielo Cartão de debito', 'cielo-woocommerce' ),
				'default' => 'no',
			),
			'title'        => array(
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Pagamento seguro com cartão de debito via Cielo.', 'cielo-woocommerce' ),
				'default'     => _x( 'Cartão de debito', 'Cielo Cartão de debito payment method', 'cielo-woocommerce' ),
				'desc_tip'    => true,
			),
			'sandbox'        => array(
				'title'       => __( 'Sandbox', 'woocommerce' ),
				'type'        => 'checkbox',
				'description' => __( 'Habilitar sandbox.', 'cielo-woocommerce' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'merchant_id'  => array(
				'title'       => __( 'Merchant ID', 'cielo-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Merchant ID Cielo.', 'cielo-woocommerce' ),
				'desc_tip'    => true,
			),
			'merchant_key'  => array(
				'title'       => __( 'Merchant KEY', 'cielo-woocommerce' ),
				'type'        => 'text',
				'description' => __( 'Merchant KEY Cielo.', 'cielo-woocommerce' ),
				'desc_tip'    => true,
			),
			'description'  => array(
				'title'       => __( 'Description', 'cielo-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Pagamento seguro com cartão de debito via Cielo.', 'cielo-woocommerce' ),
				'default'     => __( 'Pagamento seguro com cartão de debito via Cielo.', 'cielo-woocommerce' ),
				'desc_tip'    => true,
			),
			'instructions' => array(
				'title'       => __( 'Instructions', 'cielo-woocommerce' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page and emails.', 'cielo-woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Payment fields.
	 */
	public function payment_fields() {
		wc_get_template(
			'cielo_debit/payment-form.php',
			array(),
			'woocommerce/cielo/',
			__DIR__.'/../../checkout/'
		);
	}

	/**
	 * Output for the order received page.
	 */
	public function thankyou_page() {
		if ( $this->instructions ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @access public
	 * @param WC_Order $order Order object.
	 * @param bool     $sent_to_admin Sent to admin.
	 * @param bool     $plain_text Email format: plain text or HTML.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
			echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		$order_cielo = new Order($this->get_app_env(), $this->merchant_id, $this->merchant_key,
								 $order->get_total(), $order_id, Gateway::TYPE_DEBIT_CARD, $this->get_api_return_url($order));
        $returnCielo = CieloWP::process_payment($order_cielo);

        if($returnCielo['success']!=null) {
			update_post_meta( $order->id, '_payment_id', $returnCielo['success']['payment_id'] );
			update_post_meta( $order->id, '_transaction_id', $returnCielo['success']['tid'] );

			return array(
				'result'   => 'success',
				'redirect' => $returnCielo['success']['returnAuthenticationUrl'],
			);
        } else {
			$this->add_error($returnCielo["error"]);
			
			return array(
				'result'   => 'fail',
				'redirect' => '',
			);
		}
	}
}
