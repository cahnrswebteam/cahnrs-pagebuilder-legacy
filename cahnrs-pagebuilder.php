<?php namespace cahnrswp\pagebuilder;

/**
* Plugin Name: CAHNRS Page Builder
* Plugin URI: http://cahnrs.wsu.edu/communications
* Description: Builds Custom Layouts For Pages/Posts
* Version: 1.5
* Author: CAHNRS Communication, Danial Bleile, Phil Cabel
* Author URI: http://URI_Of_The_Plugin_Author
* License: GPL2
*/

class cahnrs_pagebuilder{
	
	public function __construct(){
		$this->init_autoload(); // ACTIVATE CUSTOM AUTOLOADER FOR CLASSES
		$this->define_constants(); // YEP, THAT'S WHAT IT DOES
		
		//$init_layout_tab = new layout_control();
		//$init_layout_tab->init();
		
		//$init_save = new save_control();
	}
	
	public function init(){
		if ( is_admin() ) { 
			add_action( 'load-post.php', array( $this , 'init_admin_post' ) );
			add_action( 'load-post-new.php', array( $this , 'init_admin_post' ) );
		}
		if( isset( $_GET['cahnrs-pagebuilder'] ) ){
			$ajax_control = new ajax_page_control();
			$ajax_control->init();
		}
		
		$manage_items = new item_control();
		$manage_items->init();
		
		$render_site = new render_site_control();
		$render_site->init();
	}
	
	public function init_admin_post(){
		$metabox = new metabox_control();
		$metabox->init();
	}
	
	private function init_autoload(){
		require_once 'controls/autoload_control.php'; //REQUIRE AUTOLOADER CONTROL - MAKES IT MORE PORTABLE
		$autoload = new autoload_contol(); // INIT AUTOLOADER SO WE DON'T HAVE TO USE REQUIRE ANY MORE
	}
	
	private function define_constants(){
		define( __NAMESPACE__.'\URL' , plugins_url( 'cahnrs-pagebuilder' ) ); // PLUGIN BASE URL
		define( __NAMESPACE__.'\DIR' , plugin_dir_path( __FILE__ ) ); // DIRECTORY PATH
	}
	
}

$cahnrs_pabebuilder = new cahnrs_pagebuilder();
$cahnrs_pabebuilder->init();
?>