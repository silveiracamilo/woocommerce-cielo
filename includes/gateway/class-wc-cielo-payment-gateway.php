<?php
/**
 * Class WC_Cielo_Payment_Gateway file.
 * Author Camilo da Silveira
 * Site silveiracamilo.com.br
 * @package WooCommerce\Gateways
 */

use CieloWP\CieloWP;
use CieloWP\Order\Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Cielo Payment Gateway.
 *
 * @class       WC_Cielo_Payment_Gateway
 * @extends     WC_Payment_Gateway
 */
class WC_Cielo_Payment_Gateway extends WC_Payment_Gateway {
	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'checkout_scripts' ) );
	}

	public function get_app_env(){
		return $this->sandbox=="yes" ? "sandbox" : "production";
	}

	/**
	 * Checkout scripts.
	 */
	public function checkout_scripts() {
		if ( is_checkout() && $this->is_available() ) {
			wp_enqueue_script( 'cielo-checkout-jquery-mask', plugins_url( '../assets/js/jquery.mask.min.js', plugin_dir_path( __FILE__ ) ), array('jquery'), WC_CIELO_VERSION, true );
			wp_enqueue_script( 'cielo-checkout', plugins_url( '../assets/js/woocommerce-checkout.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'cielo-checkout-jquery-mask' ), WC_CIELO_VERSION, true );
		}
	}	

	/**
	 * Check return.
	 */
	public function check_return() {
		@ob_clean();

		if ( isset( $_GET['key'] ) && isset( $_GET['order'] ) ) {
			header( 'HTTP/1.1 200 OK' );

			$order_id = absint( $_GET['order'] );
			$order    = new WC_Order( $order_id );

			if ( $order->order_key == $_GET['key'] ) {
				do_action( 'woocommerce_' . $this->id . '_return', $order );
			}
		}

		wp_die( __( 'Invalid request', 'cielo-woocommerce' ) );
	}

	/**
	 * Return handler.
	 * Return bank debit card
	 * @param WC_Order $order Order data.
	 */
	public function return_handler( $order ) {
		global $woocommerce;

		$payment_id = get_post_meta( $order->id, '_payment_id', true );
		$tid = get_post_meta( $order->id, '_transaction_id', true );
		

		if ( '' != $payment_id || $payment_id!=$_POST['PaymentId']) {
			$order_cielo = new Order($this->get_app_env(), $this->merchant_id, $this->merchant_key);
			$check_payment_done = CieloWP::check_payment_done($order_cielo, $payment_id);

			//autorizado
			if(($check_payment_done["status"]==1 || $check_payment_done["status"]==2)) {
				
				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
					$return_url = $this->get_return_url( $order );
				} else {
					$return_url = add_query_arg( 'order', $order->id, add_query_arg( 'key', $order->order_key, get_permalink( woocommerce_get_page_id( 'thanks' ) ) ) );
				}

				$order->payment_complete();
				
				// Remove cart.
				WC()->cart->empty_cart();

				wp_redirect( esc_url_raw( $return_url ) );
				exit;
			} else {
				$this->add_error($check_payment_done["returnMessage"]);

				if ( function_exists( 'wc_get_page_id' ) ) {
					$cart_url = get_permalink( wc_get_page_id( 'checkout' ) );
				} else {
					$cart_url = get_permalink( woocommerce_get_page_id( 'checkout' ) );
				}
	
				wp_redirect( esc_url_raw( $cart_url ) );
				exit;
			}
		} else {
			if ( function_exists( 'wc_get_page_id' ) ) {
				$cart_url = get_permalink( wc_get_page_id( 'cart' ) );
			} else {
				$cart_url = get_permalink( woocommerce_get_page_id( 'cart' ) );
			}

			wp_redirect( esc_url_raw( $cart_url ) );
			exit;
		}
	}

	/**
	 * Get the order API return URL.
	 *
	 * @param  WC_Order $order Order data.
	 *
	 * @return string
	 */
	public function get_api_return_url( $order ) {
		global $woocommerce;

		// Backwards compatibility with WooCommerce version prior to 2.1.
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			$url = WC()->api_request_url( get_class( $this ) );
		} else {
			$url = $woocommerce->api_request_url( get_class( $this ) );
		}

		// return urlencode( add_query_arg( array( 'key' => $order->order_key, 'order' => $order->id ), $url ) );
		return add_query_arg( array( 'key' => $order->order_key, 'order' => $order->id ), $url );
	}

	/**
	 * Add error messages in checkout.
	 *
	 * @param string $message Error message.
	 */
	public function add_error( $message ) {
		global $woocommerce;

		$title = '<strong>' . esc_attr( $this->title ) . ':</strong> ';

		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( $title . $message, 'error' );
		} else {
			$woocommerce->add_error( $title . $message );
		}
	}
}