<?php namespace cahnrswp\pagebuilder;

class item_featured_image_model {
	
	public $id = 'featured_image';
	public $name = 'Featured Image';
	public $description = 'If a featured image is set, displays the featured image.';
	public $subtype = 'page_item';
	
	public function get_form( $instance, $ipt_name ){
	}
	
	public function render_site( $post ){
		if( has_post_thumbnail( $post->ID ) ){
			the_post_thumbnail('large');
		} else {
			echo 'No featured image set';
		}
		
	}
	
	public function item_render_site( $post , $instance ){
		if( has_post_thumbnail( $post->ID ) ){
			$size = ( 'email' == $post->post_type )? 'email-700' : 'large';
			echo '<div class="featured_image column-item">';
				echo get_the_post_thumbnail( $post->ID, $size );
			echo '</div>';
		}
	}
	
};?>