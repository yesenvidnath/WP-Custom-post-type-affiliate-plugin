<?php 
/*
*
*	***** Affiliate  *****
*
*	This file initializes all MC Core components
*	
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if
// Define Our Constants
define('MC_CORE_INC',dirname( __FILE__ ).'/assets/inc/');
define('MC_CORE_IMG',plugins_url( 'assets/img/', __FILE__ ));
define('MC_CORE_CSS',plugins_url( 'assets/css/', __FILE__ ));
define('MC_CORE_JS',plugins_url( 'assets/js/', __FILE__ ));
/*
*
*  Register CSS
*
*/
function mc_register_core_css() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css', array(), '4.5.2');

    // Enqueue your custom CSS
    wp_enqueue_style('mc-core', MC_CORE_CSS . 'mc-core.css', array('bootstrap'), time(), 'all');
}
add_action('wp_enqueue_scripts', 'mc_register_core_css');

/*
*
*  Register JS/Jquery Ready
*
*/
function mc_register_core_js(){
// Register Core Plugin JS	
wp_enqueue_script('mc-core', MC_CORE_JS . 'mc-core.js','jquery',time(),true);
};
add_action( 'wp_enqueue_scripts', 'mc_register_core_js' );    
/*
*
*  Includes
*
*/ 
// Load the Functions
if ( file_exists( MC_CORE_INC . 'mc-core-functions.php' ) ) {
	require_once MC_CORE_INC . 'mc-core-functions.php';
}     
// Load the ajax Request
if ( file_exists( MC_CORE_INC . 'mc-ajax-request.php' ) ) {
	require_once MC_CORE_INC . 'mc-ajax-request.php';
} 
// Load the Shortcodes
if ( file_exists( MC_CORE_INC . 'mc-shortcodes.php' ) ) {
	require_once MC_CORE_INC . 'mc-shortcodes.php';
}