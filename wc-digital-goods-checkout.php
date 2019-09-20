<?php
/**
 * Plugin Name: Digital Goods Checkout on WooCommerce
 * Plugin URI: http://github.com/claudiosmweb/wc-digital-goods-checkout
 * Description: Hide billing fields when have only digital products in the cart.
 * Author: Claudio Sanches
 * Author URI: http://claudiosmweb.com/
 * Version: 1.1.0
 * License: GPLv2 or later
 * Text Domain: wc-digital-goods-checkout
 * Domain Path: languages/
 *
 * @package WC_Digital_Goods_Checkout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Digital_Goods_Checkout' ) ) :

	/**
	 * Plugin main class.
	 *
	 * @package WC_Digital_Goods_Checkout
	 */
	class WC_Digital_Goods_Checkout {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '1.1.0';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin public actions.
		 */
		private function __construct() {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ) );
			add_filter( 'wcbcf_disable_checkout_validation', array( $this, 'disable_checkout_validation_for_ecfb' ) );
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wc-digital-goods-checkout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Check if cart contains only digital goods.
		 *
		 * @return bool
		 */
		protected function has_digital_goods_only() {
			return ! ( WC()->cart && WC()->cart->needs_shipping() );
		}

		/**
		 * Manipule the checkout fields.
		 * Remove all billing fields when cart contains only digital goods.
		 *
		 * @param array $fields Checkout fields.
		 * @return array
		 */
		public function checkout_fields( $fields ) {
			if ( $this->has_digital_goods_only() ) {
				unset( $fields['billing']['billing_company'] );
				unset( $fields['billing']['billing_address_1'] );
				unset( $fields['billing']['billing_address_2'] );
				unset( $fields['billing']['billing_city'] );
				unset( $fields['billing']['billing_postcode'] );
				unset( $fields['billing']['billing_state'] );
				unset( $fields['billing']['billing_country'] );
				unset( $fields['billing']['billing_phone'] );

				// Extra Checkout Fields for Brazil fields.
				unset( $fields['billing']['billing_persontype'] );
				unset( $fields['billing']['billing_cpf'] );
				unset( $fields['billing']['billing_rg'] );
				unset( $fields['billing']['billing_cnpj'] );
				unset( $fields['billing']['billing_ie'] );
				unset( $fields['billing']['billing_number'] );
				unset( $fields['billing']['billing_neighborhood'] );
				unset( $fields['billing']['billing_cellphone'] );
				unset( $fields['billing']['billing_birthdate'] );
				unset( $fields['billing']['billing_sex'] );

				// Fix email field size.
				$fields['billing']['billing_email']['class'] = array( 'form-row-wide' );
			}

			return $fields;
		}

		/**
		 * Disable checkout validation for WooCommerce Extra Checkout Fields for Brazil
		 * when cart have only digital goods.
		 *
		 * @param bool $valid Cart validation.
		 * @return bool
		 */
		public function disable_checkout_validation_for_ecfb( $valid ) {
			return $this->has_digital_goods_only();
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Digital_Goods_Checkout', 'get_instance' ) );

endif;
