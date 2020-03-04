<?php
/**
 * Plugin's main class
 * Author Camilo da Silveira
 * Site silveiracamilo.com.br
 *
 * @package WooCommerce_Cielo
 */

/**
 * WooCommerce bootstrap class.
 */
class WC_Cielo {

	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			self::includes();

			add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'add_gateway' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( WC_CIELO_PLUGIN_FILE ), array( __CLASS__, 'plugin_action_links' ) );

		} 
	}

	/**
	 * Action links.
	 *
	 * @param array $links Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$plugin_links   = array();
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=cielo_credit' ) ) . '">' . __( 'Config. Crédito', 'woocommerce-cielo' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=cielo_debit' ) ) . '">' . __( 'Config. Débito', 'woocommerce-cielo' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Includes.
	 */
	private static function includes() {
		include_once dirname( __FILE__ ) . '/gateway/class-wc-cielo-payment-gateway.php';
		include_once dirname( __FILE__ ) . '/gateway/cielo_credit/class-wc-cielo-credit-gateway.php';
		include_once dirname( __FILE__ ) . '/gateway/cielo_debit/class-wc-cielo-debit-gateway.php';
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 *
	 * @return array          Payment methods with PagSeguro.
	 */
	public static function add_gateway( $methods ) {
		$methods[] = 'WC_Cielo_Credit_Gateway';
		$methods[] = 'WC_Cielo_Debit_Gateway';

		return $methods;
	}
}
