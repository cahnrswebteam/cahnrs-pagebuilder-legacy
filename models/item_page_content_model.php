<?php namespace cahnrswp\pagebuilder;

class item_page_content_model {
	
	public $id = 'page_content';
	public $name = 'Primary Content';
	public $description = 'Content from the standard Wordpress editor.';
	public $subtype = 'page_item';
	
	public function form($instance, $ipt_name){
	}
	
	public function render_site( $post, $item_obj = array() ){
		
		echo '<iframe class="pagebuilder-content-frame page_content-frame" src="JavaScript:\'\'" frameborder="0"></iframe>';
	}
	
	public function item_render_site( $post , $instance ){
		$page_content = \get_post_meta( $post->ID , '_pagebuilder_editor', true );  // GET PAGEBUILDER EDITORS META
		if( isset( $page_content['primary_content'] )){ // CONTENT EXISTS IN PAGEBUILDER EDITORS
			$content = $page_content['primary_content']; // USER PAGEBUILDER EDITOR CONTENT
		} else { // CHECK LEGACY CONTENT FIELD
			$legacy_content = \get_post_meta( $post->ID , '_cahnrs_page_content', true );  // GET PAGEBUILDER EDITORS META
			$content = ( $legacy_content )? $legacy_content : $post->post_content; // USE LEGACY OR POST CONTENT
			if( strpos( $post->post_content, '<!--more-->' ) ){
					$more_content = explode( '<!--more-->' , $post->post_content );
					$content = $more_content[0];
			}
		}
		//echo \apply_filters('pagebuilder_the_content', $content ); // APPLY FILTERS AND ECHO 
		echo \apply_filters('the_content', $content );
	}
	
};?>