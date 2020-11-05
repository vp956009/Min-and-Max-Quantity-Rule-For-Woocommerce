<?php
/**
* Plugin Name: Min and Max Quantity Rule For Woocommerce
* Description: Min and max woocommerce Purchase Rule can be give base on product category to all  user role wise
* Version: 1.0
* Tested up to: 4.9.8
* License: A "GNUGPLv3" license name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
  die('-1');
}

if (!defined('OCMAMQRW_PLUGIN_NAME')) {
  define('OCMAMQRW_PLUGIN_NAME', 'Min and max woocommerce Rule');
}
if (!defined('OCMAMQRW_PLUGIN_VERSION')) {
  define('OCMAMQRW_PLUGIN_VERSION', '1.0');
}
if (!defined('OCMAMQRW_PLUGIN_FILE')) {
  define('OCMAMQRW_PLUGIN_FILE', __FILE__);
}
if (!defined('OCMAMQRW_PLUGIN_DIR')) {
  define('OCMAMQRW_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('OCMAMQRW_BASE_NAME')) {
    define('OCMAMQRW_BASE_NAME', plugin_basename(OCMAMQRW_PLUGIN_FILE));
}
if (!defined('OCMAMQRW_DOMAIN')) {
  define('OCMAMQRW_DOMAIN', 'ocmamqrw');
}

//Main class
//Load required js,css and other files

if (!class_exists('OCMAMQRW')) {
	add_action('plugins_loaded', array('OCMAMQRW', 'OCMAMQRW_instance'));
  	class OCMAMQRW {

	    protected static $OCMAMQRW_instance;

	    public static function OCMAMQRW_instance() {
	      	if (!isset(self::$OCMAMQRW_instance)) {
	        	self::$OCMAMQRW_instance = new self();
	        	self::$OCMAMQRW_instance->init();
	        	self::$OCMAMQRW_instance->includes();
			}
	      	return self::$OCMAMQRW_instance;
	    }

	    function __construct() {
	        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	        add_action('admin_init', array($this, 'OCMAMQRW_check_plugin_state'));
	    }


	    function OCMAMQRW_check_plugin_state(){
	      	if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
	        	set_transient( get_current_user_id() . 'mamwrerror', 'message' );
	      	}
	    }


	    function init() {
	      	add_action( 'admin_notices', array($this, 'OCMAMQRW_show_notice'));
	       	add_action( 'admin_enqueue_scripts', array($this, 'OCMAMQRW_load_admin_script_style'), 1);
	       	add_action( 'wp_enqueue_scripts', array($this, 'OCMAMQRW_load_front_script_style'), 1);
	       	add_filter( 'plugin_row_meta', array( $this, 'OCMAMQRW_plugin_row_meta' ), 10, 2 );
	    }

	    function OCMAMQRW_plugin_row_meta( $links, $file ) {
         	if ( OCMAMQRW_BASE_NAME === $file ) {
             	$row_meta = array(
                 	'rating'    =>  '<a href="#" target="_blank"><img src="'.OCMAMQRW_PLUGIN_DIR.'/images/star.png" class="ocmamqrw_rating_div"></a>',
             	);
             	return array_merge( $links, $row_meta );
         	}
        	return (array) $links;
      	}

	    function OCMAMQRW_show_notice() {
	        if ( get_transient( get_current_user_id() . 'mamwrerror' ) ) {

	          	deactivate_plugins( plugin_basename( __FILE__ ) );

	          	delete_transient( get_current_user_id() . 'mamwrerror' );

	          	echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=woocommerce">WooCommerce</a> plugin installed and activated.</p></div>';
	        }
	    }
	   

	    function OCMAMQRW_load_admin_script_style() {
	      	wp_enqueue_style( 'mamqrw_admin_style', OCMAMQRW_PLUGIN_DIR . '/assets/css/mamwr-admin-style.css', false, '1.0.0' );
	      	wp_enqueue_script( 'mamqrw_admin_script', OCMAMQRW_PLUGIN_DIR . '/assets/js/mamwr-admin-script.js', array('jquery'), '1.0.0',false );
	    }

	    function OCMAMQRW_load_front_script_style() {
	      	wp_enqueue_style( 'mamqrw_admin_style', OCMAMQRW_PLUGIN_DIR . '/assets/css/mamwr-front-style.css', false, '1.0.0' );
	    }

	   
	    function includes() {

	      //admin settings
	      include_once('includes/mamqrw-adminsettings.php');

	      //Total Cart QTY Validation
	      include_once('includes/mamqrw-functionality.php');

	      //single,variations,category etc product setting
	      include_once('includes/mamqrw-product_cat_settings.php');
	    }     
	} 
}