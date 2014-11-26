<?php namespace cahnrswp\pagebuilder;

class metabox_control {
	
	public $layout_model;
	
	public function __construct(){
		$this->layout_model = new layout_model();
	}
	
	public function init(){
		\add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		
		\add_action( 'save_post', array( $this, 'save' ) );
		
		\add_action( 'admin_enqueue_scripts', array( $this , 'add_scripts' ) );
	}
	
	public function add_meta_box( $post_type ){
		$post_types = array( 'post', 'page' , 'email' );     //limit meta box to certain post types
		if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'page_builder'
				,__( 'CAHNRS Custom Page Builder' )
				,array( $this, 'render_pagebuilder_meta_box_content' )
				,$post_type
				,'normal'
				,'high'
			);
		}
	}
	
	public function render_pagebuilder_meta_box_content( $post ){
		$this->layout_model = new layout_model();
		$layout_obj = $this->layout_model->get_layout_obj( $post );
		$layout_items = $this->layout_model->get_items( $layout_obj , 'content_block' );
		
		$pagebuilder_metabox = new metabox_view();
		
		$pagebuilder_metabox->render_editor_controls( $post );
		$pagebuilder_metabox->render_layout_editor( $post , $layout_obj );
		$pagebuilder_metabox->render_content_editors( $post, $layout_items );
		
		
		
		$pagebuilder_metabox->render_add_item_window( $post );
	}
	
	public function save( $post_id ){
		//if ( ! isset( $_POST['myplugin_meta_box_nonce'] ) ) { return; }

		// Verify that the nonce is valid.
		//if ( ! wp_verify_nonce( $_POST['myplugin_meta_box_nonce'], 'myplugin_meta_box' ) ) { return;}
	
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	
		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) { return; }
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) { return;}
		}
		
		// IS A MODIFIED LAYOUT
		
			
			$pb_settings_clean = array();
			$pb_settings = ( isset( $_POST['_pagebuilder_settings']) )? $_POST['_pagebuilder_settings'] : array();
			foreach( $pb_settings as $set_key => $set_val ){
				$pb_settings_clean[$set_key] = \sanitize_text_field( $set_val );
			}
			
			$pb = ( isset( $_POST['_pagebuilder']) )? $_POST['_pagebuilder'] : array();
			
			$pb_editors_clean = array();
			  $pb_editors = ( isset( $_POST['_pagebuilder_editor']) )? $_POST['_pagebuilder_editor'] : array();
			  foreach( $pb_editors as $set_key => $set_val ){
				  $pb_editors_clean[$set_key] = \wp_kses_post( $set_val );
			  }
			
			if( isset( $pb_settings_clean['reset_layout']) && 1 == $pb_settings_clean['reset_layout'] ){
				\delete_post_meta( $post_id, '_cahnrs_layout' );
			} else {
			
			  //var_dump( $pb_settings_clean );
			  if( $pb_settings_clean ){ \update_post_meta( $post_id, '_pagebuilder_settings' , $pb_settings_clean ); }
			  
			  if( $pb_editors_clean ){ \update_post_meta( $post_id, '_pagebuilder_editor' , $pb_editors_clean ); }
			  
			  if( isset( $pb_settings_clean['is_modified'] ) && 1 == $pb_settings_clean['is_modified'] ){ // MODIFIED LAYOUT  	
				  if( $pb ){ \update_post_meta( $post_id, '_cahnrs_layout' , $pb );}
			  } 
			  
			  if( isset( $pb_settings_clean['is_modified'] ) && 1 == $pb_settings_clean['is_modified'] && $pb ){ // MODIFIED LAYOUT
			  		$items = $this->layout_model->get_items( $pb );
			  } else {
				  $meta = \get_post_meta( $post_id, '_cahnrs_layout', true );
				  $items = ( $meta )? $this->layout_model->get_items( $meta ) : array();
			  }
			  
			  
			  $new_content = false;
			  
			  if( $items && isset( $pb_editors_clean['primary_content'] ) ){
				  foreach( $items as $item_key => $item ){
					  if( 'page_content' == $item['id'] ){ 	
					  	
						  $new_content .= '<!-- PRIMARY CONTENT -->'.$pb_editors_clean['primary_content'].'<!-- PRIMARY CONTENT -->';
					  } 
					  else if('content_block' == $item['id'] && isset( $item['settings']['is_content'] ) && $item['settings']['is_content'] ){
						  $new_content .= '<aside id="'.$item_key.'" class="'.$item['id'].'" >'.$pb_editors_clean[ $item_key ].'</aside>';
					  }
					  else if( $item['settings']['is_content'] ){
						  $new_content .= '<aside id="'.$item_key.'" class="'.$item['id'].'" ></aside>';
					  }
				  }
				  
			  } 
			  else if( isset( $pb_editors_clean['primary_content'] ) ) {
				  $new_content = $pb_editors_clean['primary_content'];
			  }
			  //var_dump( $new_content );
			  if( $new_content ) $this->update_new_content( $post_id , $new_content );
		}
		
	}
	
	public function update_new_content( $post_id , $content ){
		//global $post;
		//$layout_model = new layout_model();
		//$site_view = new site_view();
		//$layout_obj = $layout_model->get_layout_obj( $post );
		//ob_start();
		//$site_view->get_site_view( $post , $layout_obj , $layout_model );
		//$content = ob_get_clean();
		remove_action( 'save_post', array( $this , 'save' ) );
		wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );
		add_action( 'save_post', array( $this , 'save' ) );
	}
	
	public function add_scripts(){
		global $post;
		if( 'page' == $post->post_type || 'post' == $post->post_type ){
			
			wp_register_style( 'pagebuilder_metabox_css' , URL . '/css/metabox.css', false, '1.2.0' );
			
			wp_enqueue_style( 'pagebuilder_metabox_css' );
			
			wp_enqueue_script( 'pagebuilder_metabox_js' , URL . '/js/metabox.js' );
		}
	}
};?>