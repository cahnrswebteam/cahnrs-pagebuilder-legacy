<?php namespace cahnrswp\pagebuilder;

class item_content_block_model {
	
	public $id = 'content_block';
	public $name = 'Additional Content';
	public $description = 'Adds additional text/content area to layout with an editor.';
	public $subtype = 'page_item';
	
	public function form(){
		return 'form';
	}
	
	public function render_site( $post, $item_obj = array() ){
		if( $post ){
			$meta = \get_post_meta( $post->ID , '_cahnrs_layout_items', true );
			if( $meta ){
				
				//apply_filters('the_content', $meta );
			} else {
				echo '<iframe class="pagebuilder-content-frame '.$item_obj['id'].'_i_'.$item_obj['instance'].'-frame" src="JavaScript:\'\'" frameborder="0"></iframe>';
				//$this->render_default();
			}
		} else {
			$this->render_default();
		}
	}
	
	public function render_default(){
		echo '<span class="default-content">Click "Edit" to add text/content.</span>';
	}
	
	public function item_render_site( $post , $instance ){
		$page_content = \get_post_meta( $post->ID , '_pagebuilder_editor', true ); // GET PAGEBUILDER EDITORS META
		$id = $instance['id'].'-'.$instance['instance']; // GET INSTANCE ID
		if( isset( $page_content[ $id ] ) ){ // IF ID IS IN PAGEBUILDER EDITORS META
			$content = $page_content[ $id ]; // CONTENT = META 
		} else { // IF INSTANCE ID IS NOT IN EDITORS META
			if( isset( $instance['content'] ) ){ // CHECK LEGACY CONTENT FIELD
				$content = $instance['content']; // IF EXISTS USE LEGACY CONTENT FIELD
			} else { // IF NO LEGACY FIELD
				if( strpos( $post->post_content, '<!--more-->' ) ){
					$more_content = explode( '<!--more-->' , $post->post_content );
					unset( $more_content[0] );
					$content = implode('', $more_content );
				} else {
					$content = ' '; // NO CONTENT
				}
			}
		}
		echo \apply_filters('the_content', $content ); // APPLY FILTERS AND ECHO CONTENT
	}
	
};?>