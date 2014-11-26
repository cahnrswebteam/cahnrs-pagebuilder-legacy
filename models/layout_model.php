<?php namespace cahnrswp\pagebuilder;

class layout_model {
	
	public $row_index = 0;
	
	public $registered_items = array( 
		'page_content',
		'content_block',
		'featured_image',
		'page_title',
		'subtitle' 
		 );
		 
	public $registered_sidebars = array(  
		'sidebar',
		'sidebar_top',
		'sidebar_bottom', 
		);
		
	public $registered_layouts = array(
		'pagbuilder-layout-full' => 1,
		'pagbuilder-layout-aside-empty' => 1,
		'pagbuilder-layout-aside' => 2,
		'pagbuilder-layout-half' => 2,
		'pagbuilder-layout-third-left' => 2,
		'pagbuilder-layout-third-right' => 2,
		'pagbuilder-layout-thirds' => 3,
		'pagbuilder-layout-fourths' => 4,
	);
	
	public $legacy_layouts = array(
		'column-layout-1' => 'pagbuilder-layout-full',
		'column-layout-1-2' => 'pagbuilder-layout-aside',
		'column-layout-1-2-3' => 'pagbuilder-layout-thirds',
		'column-layout-1-2-3-4' => 'pagbuilder-layout-fourths',
	);
	
	public $layout_styles = array(
		'pagbuilder-layout-full' => array( 
			'column-1' => 'display:block;' 
			),
		'pagbuilder-layout-aside' => array( 
			'column-1' => 'display:inline-block; width: 75%; vertical-align: top;', 
			'column-2' => 'display:inline-block; width: 25%; vertical-align: top;', 
			),
		'pagbuilder-layout-aside-empty' => array( 
			'column-1' => 'display:block;', 
			),
		'pagbuilder-layout-half' => array( 
			'column-1' => 'display:inline-block; width: 50%; vertical-align: top;', 
			'column-2' => 'display:inline-block; width: 50%; vertical-align: top;', 
			),
		'pagbuilder-layout-third-right' => array( 
			'column-1' => 'display:inline-block; width: 66.66%; vertical-align: top;', 
			'column-2' => 'display:inline-block; width: 33.33%; vertical-align: top;', 
			),
		'pagbuilder-layout-third-left' => array( 
			'column-1' => 'display:inline-block; width: 33.33%; vertical-align: top;', 
			'column-2' => 'display:inline-block; width: 66.66%; vertical-align: top;', 
			),
		'pagbuilder-layout-thirds' => array( 
			'column-1' => 'display:inline-block; width: 33.33%; vertical-align: top;', 
			'column-2' => 'display:inline-block; width: 33.33%; vertical-align: top;', 
			'column-3' => 'display:inline-block; width: 33.33%; vertical-align: top;',
			 ),
		'pagbuilder-layout-fourths' => array( 
			'column-1' => 'display:inline-block; width: 25%; vertical-align: top;', 
			'column-2' => 'display:inline-block; width: 25%; vertical-align: top;', 
			'column-3' => 'display:inline-block; width: 25%; vertical-align: top;', 
			'column-4' => 'display:inline-block; width: 25%; vertical-align: top;', 
			),
	);
	
	public $email_column_widths = array(
		'pagbuilder-layout-full' => array( 'column-1' => 1 ),
		'pagbuilder-layout-aside' => array( 'column-1' => 0.70, 'column-2' => 0.30, ),
		'pagbuilder-layout-thirds' => array( 'column-1' => 0.3333, 'column-2' => 0.3333, 'column-3' => 0.3333, ),
		'pagbuilder-layout-fourths' => array( 'column-1' => 0.25, 'column-2' => 0.25, 'column-3' => 0.25, 'column-4' => 0.25 ),
	);
		
	public function get_popular_widgets(){
		$widgets = array(
			'cahnrs_insert_existing',
			'CAHNRS_Slideshow_widget',
			'CAHNRS_feed_widget',
			'cahnrs_action_item',
			'cahnrs_custom_gallery_widget',
			/*'cahnrs_insert_item',*/
			'cahnrs_az_index',
			'cahnrs_insert_video',
			'cahnrs_iframe',
			'cahnrs_surveygizmo_embed',
			
		);
		return $widgets;
	}
	
	public function get_layout_obj( $post ){
		$meta = \get_post_meta( $post->ID , '_cahnrs_layout', true );
		$layout_obj = ( $meta )? $meta : $this->get_default_layout_obj( $post );
		/****************************************************************
		** LEGACY MAP **
		*******************************************************/
		foreach ( $layout_obj as $row_k => $row_v ){
			if( array_key_exists( $row_v['layout'] , $this->legacy_layouts ) ){
				$layout_obj[$row_k]['layout'] = $this->legacy_layouts[ $row_v['layout'] ];
			}
		}
		$layout_obj['tertiary_nav'] = $this->get_tertiary_nav( $post );
		//usort( $layout_obj , array( $this , 'service_sort_rows') );
		return $layout_obj;
	}
	
	public function get_item_object( $item_array ){
		switch ( $item_array['type'] ){
        case 'native':
			  $item_class = __NAMESPACE__.'\\item_'.$item_array['id'].'_model';
			  if( class_exists( $item_class, true ) ){
			  	$item = new $item_class();
			  } else {
				  $item = false;
			  }
			  break;
		  case 'widget':
			  $item_class = $item_array['id'];
			  $item_class = str_replace('\\\\', '\\' , $item_class );
			  if( class_exists( $item_class, true ) ){
				  $item = new $item_class();
				  $item->name .= ' - Widget';
				  $item->description = $item->widget_options['description'];
				  $item->number = 1;
			  } else { 
			  	$item = false;
			  }
			  break;
	  };
	  return $item;
	}
	
	public function get_items( $layout_array, $type = false ){
		$items = array();
		foreach( $layout_array as $row ){
			if( isset( $row['columns'] ) ){
				foreach( $row['columns'] as $column_key => $column ){
					foreach( $column['items'] as $item_key => $item_value ){
						if( $type ){
							if( $type == $item_value['id'] ){
								$item_value['input-name'] = '_pagebuilder['.$row['id'].'][columns]['.$column_key.'][items]['.$item_key.']';
								$items[$item_key] = $item_value;
							}
						} else {
							$item_value['input-name'] = '_pagebuilder['.$row['id'].'][columns]['.$column_key.'][items]['.$item_key.']';
							$items[$item_key] = $item_value;
						}
					}
				}
			}
		}
		return $items;
	}
	
	public function get_default_layout_obj( $post ){
		$header = array(
						'id' => 'row-100',
						'name' => 'Header',
					);
		$footer = array(
						'id' => 'row-200',
						'name' => 'Footer',
						);
		$content = array(
			'items' => array(
				'page_content-1' => array(
					'id' => 'page_content',
					'instance' => 1,
					'type' => 'native',
				),
			),
		);
		$sidebar = array(
			'items' => array(
				'sidebar_top-1' => array(
					'id' => 'sidebar_top',
					'instance' => 1,
					'type' => 'native',
				),
				'sidebar_bottom-1' => array(
					'id' => 'sidebar_bottom',
					'instance' => 1,
					'type' => 'native',
				),
			),
		);
		$legacy = array(
			'items' => array(
				'content_block-1000' => array(
					'id' => 'content_block',
					'instance' => 1000,
					'type' => 'native',
					'settings' => array('title' =>'Legacy More Tag')
				),
			),
		);
		$layout_type = ( \get_option( 'cahnrs_pagebuilder_layout_default' ))? 'cahnrs_pagebuilder_layout_default' : 'default';
		switch( $layout_type ){
			case 'sidebar-left':
				$layout = array(
					'row-100' => $header,
					'row-1' => array(
						'id' => 'row-1',
						'name' => 'Content Row',
						'layout' => 'pagbuilder-layout-aside',
						'columns' => array(
							'column-1' => $content,
							'column-2' => $sidebar,
						),
					),
					'row-200' => $footer,
				);
				break;
			default:
				$layout = array(
					'row-100' => $header,
					'row-1' => array(
						'id' => 'row-1',
						'name' => 'Content Row',
						'layout' => 'pagbuilder-layout-aside',
						'columns' => array(
							'column-1' => $content,
						),
					),
					'row-200' => $footer,
				);
				break;
		}
		/**************************************************
		** DO LEGACY MORE CHECK **
		***************************************************/
		if( strpos( $post->post_content, '<!--more-->' ) ) $layout['row-1']['columns']['column-2'] = $legacy;
		return $layout;
	}
	
	public function get_columns_by_layout( $layout ){
		if( array_key_exists( $layout , $this->registered_layouts ) ) return $this->registered_layouts[ $layout ];
		return 1;
	}
	
	public function get_tertiary_nav( $post ){
		$tertiary = array(); // WE'LL POPULATE THIS LATER
		$id_menu = array(); // NEW ARRAY BECUASE THE DEFAULT MENU ARRAY SUCKS
		$parent_id = false; // RESET LATER
		$menu_loc = 'site'; // TO DO: MAKE THIS A DROPDOWN IN THEME OPTIONS
		$menu_name = $menu_loc; // LOCATION TO LOOK FOR
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) { // CHECK IF LOCATION IS SET
		
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] ); // GET THE MENU OBJECT FROM LOCATION
			$menu_items = wp_get_nav_menu_items( $menu->term_id ); // GET MENU ITEMS FROM OBJECT ID
			
			
			foreach( $menu_items as $key => $menu ){ // LOOP THROUGH MENU ITEMS - DB
				$id_menu[$menu->ID] = $menu; // USE MENU ID AS KEY => MENU - DB
			} // END FOREACH
			
			
			foreach( $id_menu as $key => $menu ){ // LOOP THROUGH MENU ITEMS - DB
				if( $menu->object_id == $post->ID ){
					if( !$menu->menu_item_parent ) return array(); // TOP LEVEL RETURN  - EMPTY ARRAY - DB
					$c_parent = $menu->menu_item_parent; // GET CURRENT PARENT
					if( !$id_menu[$c_parent]->menu_item_parent ){ // IS SECOND LEVEL - DB
						$parent_id = $menu->ID; // THIS IS THE PARENT OF NAV
						break; // BREAK FOREACH LOOP
					} else { // THIRD LEVEL
						$parent_id = $c_parent; // PARENT IS CURRENT PARENT
						break; // BREAK FOREACH LOOP
					}
					break; // BREAK FOREACH
				} // END IF
			} // END FOREACH
			
			if( $parent_id ){ // LET'S CHECK IF WE HAVE PARENTS
				foreach( $id_menu as $key => $menu ){ // ONCE MORE LOOP THROUGH MENU
					if( $menu->menu_item_parent == $parent_id ){ // IF IS CHILD OF PARENT
						$tertiary[] = $menu; // TOLD YOU WE'D SET IT LATER
					} // END IF
				} // END FOREACH
			} // END IF
			
		} // END IF
		return $tertiary; // AND WE ARE DONE
	} // END FUNCTION
	
	public function get_current_menu_array( $post , $menu ){
		$full_menu = array(); // WE'LL POPULATE THIS LATER
		$id_menu = array(); // NEW ARRAY BECUASE THE DEFAULT MENU ARRAY SUCKS
		$menu_name = $menu; // LOCATION TO LOOK FOR
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) { // CHECK IF LOCATION IS SET
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] ); // GET THE MENU OBJECT FROM LOCATION
			$menu_items = wp_get_nav_menu_items( $menu->term_id ); // GET MENU ITEMS FROM OBJECT ID
			foreach( $menu_items as $key => $menu ){ // LOOP THROUGH MENU ITEMS - DB
				$id_menu[$menu->ID] = $menu; // USE MENU ID AS KEY => MENU - DB
			}
			foreach( $id_menu as $id => $menu ){ // LOOP THROUGH NEW MENUS BY ID
					if( !$menu->menu_item_parent ) { // IF NO PARENT - TOP LEVEL - DB
						$full_menu[$id]['obj'] = $menu; // ADD TO TERTIARY
					} else { // HAS PARENT - 2ND OR 3RD LEVEL
						$parent_id = $menu->menu_item_parent; // GET PARENT ID
						if( !$id_menu[ $parent_id ]->menu_item_parent && array_key_exists( $parent_id , $full_menu ) ){ // IS SECOND LEVEL
							$full_menu[$parent_id]['subnav'][$id]['obj'] = $menu; // FIND PARENT AND ADD TO SUBNAV
						} else { // THRID LEVEL
							$top_id = $id_menu[ $parent_id ]->menu_item_parent; // GET TOP PARENT
							if( array_key_exists( $top_id , $full_menu ) ){ // CHECK IF IT EXISTS - WILL BE FALSE IF FOURTH LEVEL
								$full_menu[$top_id]['subnav'][$parent_id]['subnav'][$id]['obj'] = $menu; // ADD TO PARENT SUBNAV
							} // END IF
						} // END IF
					} // END IF
			} // END FOREACH
			return $full_menu; // THERE BE A BIT OF LOGIC HERE
		}
	}
	
	/********************************************
	** START SERVICES **
	********************************************/
	
	public function service_legacy_more_check( $post ){
		if( strpos( '<!--more-->' ) ){
		}
	}
	
	/*private function service_sort_rows( $a , $b ){
		if( isset( $a['order'] ) ){ // IF ORDER IS SET
			if( $a['order'] > $b['order'] ) return 1;
			if( $a['order'] == $b['order'] ) return 0;
			if( $a['order'] < $b['order'] ) return -1; 
		} else {
			return -1;
		}
	}*/
	
};?>