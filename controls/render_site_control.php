<?php namespace cahnrswp\pagebuilder;

class render_site_control {
	
	public $layout_model;
	public $site_view;
	
	public function __construct(){
		$this->layout_model = new layout_model();
		$this->site_view = new site_view();
	}
	
	public function init(){
		
		 \add_filter( 'the_content', array( $this , 'render_site' ), 15 ); 
		 
		 \add_action( 'wp_enqueue_scripts', array( $this , 'add_scripts' ) );
	}
	
	public function render_site( $content ) {
		global $post; 
		global $in_loop;
		global $force_builder;
		if( $in_loop ) return $content;
		if ( ( is_singular('post') || is_singular('page') ) /*&& is_main_query()*/ ) {
			$in_loop = true;
			$layout_obj = $this->layout_model->get_layout_obj( $post );
			ob_start();
			$this->site_view->get_site_view( $post , $layout_obj , $this->layout_model );
			$this->add_tertiary_nav( $post , $layout_obj , $this->layout_model );
			$in_loop = false;
			return ob_get_clean();
		} 
		else if ( is_singular('email') ){
			$in_loop = true;
			$layout_obj = $this->layout_model->get_layout_obj( $post );
			ob_start();
			$this->site_view->get_email_view( $post , $layout_obj , $this->layout_model );
			$in_loop = false;
			return ob_get_clean();
		};
		
	
		return '<div class="pagebuilder-item">'.$content.'</div>';
	}
	
	private function add_tertiary_nav( $post , $layout_obj , $layout_model ){
		/************************************************
		** Add third level nav to layout **
		*************************************************/
		if ( $layout_obj['tertiary_nav'] ) {
			echo '<nav id="pagebuilder-tertiary-nav" role="navigation">';
			echo '<ul>';
			$is_active = false;
			$i = 0;
			foreach ( $layout_obj['tertiary_nav'] as $menu_item ){
				if( $menu_item->object_id == $post->ID ) $is_active = $post->ID;
			}
			foreach ( $layout_obj['tertiary_nav'] as $menu_item ) {
				if( $is_active ){
					$active = (  $is_active == $menu_item->object_id  )? 'selected' : '';
				} 
				else {
					$active = (  0 == $i )? 'selected' : '';
				}
				$dynamic = ( $menu_item->type == 'post_type' )? 'is-dynamic' : '';
				echo '<li class="' . $active . '"><a class="'.$dynamic.'" href="' . $menu_item->url . '" data-index="'.$i.'">' . $menu_item->title . '</a></li>';
				$i++;
			}
			echo '</ul>';
			echo '</nav>';
			$i = 0;
			foreach ( $layout_obj['tertiary_nav'] as $menu_item ) {
				if( $is_active ){
					$active = (  $is_active == $menu_item->object_id  )? 'selected' : 'inactive';
				} 
				else {
					$active = (  0 == $i )? 'selected' : '';
				}
				if( $menu_item->type == 'post_type' ){
					echo '<div class="pagebuilder-tertiary-page tertiary-'.$i.' '.$active.'" >';
					$post = get_post( $menu_item->object_id );
					$lay_obj = $this->layout_model->get_layout_obj( $post );
					$this->site_view->get_site_view( $post , $lay_obj , $layout_model );
					echo '</div>';
				}
				$i++;
			}
		}
		//$this->get_third_level_nav( $post );
	}
	
	public function add_scripts(){
		\wp_register_style( 'pagebuilder_css', URL . '/css/pagebuilder.css', false, '1.6.0' );
		\wp_enqueue_style( 'pagebuilder_css' );
		// Preferably enqueue this conditionally
		\wp_enqueue_script( 'tertiary-nav', URL . '/js/tertiary-nav.js', array( 'jquery' ), '1.8.0' );
	}
	
}
?>