<?php namespace cahnrswp\pagebuilder;

class item_control {
	
	public function init(){
		\add_action( 'widgets_init', array( $this , 'add_sidebars' ) );
	}
	
	public function add_sidebars(){
		\register_sidebar( array(
			'name'         => __( 'Layout Sidebar Top' ),
			'id'           => 'wp_sidebar_top',
			'description'  => __( 'Widgets in this area will be shown in the "WP Sideber Top" item .' ),
			'before_title' => '<h2>',
			'after_title'  => '</h2>',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
		) );
		\register_sidebar( array(
			'name'         => __( 'Layout Sidebar Bottom' ),
			'id'           => 'wp_sidebar_bottom',
			'description'  => __( 'Widgets in this area will be shown in the "WP Sideber Bottom" item .' ),
			'before_title' => '<h2>',
			'after_title'  => '</h2>',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
		) );
	}
};?>