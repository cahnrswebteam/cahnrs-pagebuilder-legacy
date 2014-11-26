<?php namespace cahnrswp\pagebuilder;

class item_sidebar_top_model {
	
	public $id = 'sidebar_top';
	public $name = 'Layout Sidebar - Top';
	public $sub_type = 'sidebar';
	public $description = 'Adds Dynamic Widget Area ( Sidebar ).';
	
	public function get_form( $instance, $ipt_name ){
	}
	
	public function render_site( $post ){
		echo 'test';
		
	}
	
	public function item_render_site( $post , $instance ){
	  if ( is_active_sidebar( 'wp_sidebar_top' ) ) : ?>
			 <?php dynamic_sidebar( 'wp_sidebar_top' ); ?>
	  <?php endif;
	}
	
};?>