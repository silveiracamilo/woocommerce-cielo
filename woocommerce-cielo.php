<?php
/**
 * Plugin Name:          Camilo da Silveira - Cielo for WooCommerce
 * Plugin URI:           https://github.com/silveiracamilo/woocommerce-cielo
 * Description:          Includes Cielo as a payment gateway to WooCommerce.
 * Author:               Camilo da Silveira
 * Author URI:           http://silveiracamilo.com.br
 * Version:              1.0.0
 * License:              GPLv3 or later
 * Text Domain:          woocommerce-cielo
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      3.4.0
 *
 * Camilo da Silveira - Cielo for WooCommerce is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or any later version.
 *
 * Camilo da Silveira - Cielo for WooCommerce is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Camilo da Silveira - Cielo for WooCommerce. If not, see
 * <https://www.gnu.org/licenses/gpl-3.0.txt>.
 *
 * @package WooCommerce_Cielo
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants.
define( 'WC_CIELO_VERSION', '1.0.0' );
define( 'WC_CIELO_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'WC_Cielo' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-cielo.php';
	add_action( 'plugins_loaded', array( 'WC_Cielo', 'init' ) );
}