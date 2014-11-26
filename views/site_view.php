<?php namespace cahnrswp\pagebuilder;

class site_view {

	public $meta_base = '_pagebuilder';
	public $content_base = '_pagebuilder_editors';

	public function get_site_view( $post, $layout_obj, $layout_model ) {
		
		/************************************************
		** START LAYOUT **
		*************************************************/
		?>

    <?php
	/***********************************************
	** Start Force First for Responsive Layouts **
	************************************************/
	echo '<div id="pagebuilder-force-first" class="pagebuilder-row" >';
	foreach( $layout_obj as $row ){
		if( isset( $row['columns'] ) ){
			foreach( $row['columns'] as $column ){
				if( isset( $column['items'] )){
					foreach( $column['items'] as $item_key => $item ){
						if( isset( $item['settings']['force_mobile_first'] ) && $item['settings']['force_mobile_first'] ){
							$is_content = ( isset( $item['settings']['is_content'] ) )? $item['settings']['is_content'] : false;
								if( $is_content || 'page_content' == $item['id'] || 'content_block' == $item['id'] ){
									$tag = 'div';
								} else {
									$tag = 'aside';
								}
								//$tag = ( $item['settings']['is_content'] )? 'div' : 'aside';
								//$tag = ( 'page_content' == $item['id'] || 'content_block' == $item['id'] )? 'article' : $tag;
								//$title = $this->get_title( $item );
								$args = array();
								$args['before_widget'] = $this->get_item_wrapper( $tag , 'before' , $item, $item_key );
								$args['after_widget'] = $this->get_item_wrapper( $tag );
								switch( $item['type'] ){
									case 'native' :
										echo $args['before_widget'];
										$item_obj = $layout_model->get_item_object( $item );
										$item_obj->item_render_site( $post , $item );
										echo $args['after_widget'];
										break;
									case 'widget' :
										\the_widget( $item['id'] , $item['settings'], $args );
										break;
								};
						}
					}
				}
			}
		}
	}
	echo '</div>';
	
	
    foreach( $layout_obj as $row ): ?>
        <?php 
			/** TO DO: CONSOLIDATE THE COLUMN COUNT AND COULUMN STYLES INTO ONE ARRAY - DB **/
			$column_count = $layout_model->get_columns_by_layout( $row['layout'] ); // GET COLUMN COUNT FOR NOW
			/*************************************
			** If the row is a two column with an empty right column then render as one column aka "pagbuilder-layout-aside-empty" **
			**************************************/
			$row['layout'] =( !isset( $row['columns']['column-2'] ) && 'pagbuilder-layout-aside' == $row['layout'] )? 
				$row['layout'].'-empty' : $row['layout'];
        	if( isset( $row['columns'] ) ):?>
				<?php $empty_aside = ( isset( $row['columns']['column-2'] ))? '' : 'empty-aside'; ?>
                <div id="<?php echo $row['id'];?>" class="pagebuilder-row <?php echo $row['id'].' '.$row['layout'].' '.$empty_aside.' '.$row['class'];?>" >
                    <?php for( $i = 1; $i <= $column_count; $i++ ):
						//if( 'pagbuilder-layout-aside' == $row['layout'] ){
							///if( 1 == $i ) $c = 2;
							//if( 2 == $i ) $c = 1;
						//} else {
							$c = $i;
						//}
						$column_id = 'column-'.$c;
						$column = ( isset( $row['columns'][$column_id]) )? $row['columns'][$column_id] : array();
						$column_style = $layout_model->layout_styles[ $row['layout'] ][ $column_id ];
                        ?><div id="<?php echo $row['id'].'-column-'.$c;?>" class="pagebuilder-column pagebuilder-column-<?php echo $c;?>" style="<?php echo $column_style;?>">
                        <?php if( $column['items']){
                        	foreach( $column['items'] as $item_key => $item ){
								$is_content = ( isset( $item['settings']['is_content'] ) )? $item['settings']['is_content'] : false;
								if( $is_content || 'page_content' == $item['id'] || 'content_block' == $item['id'] ){
									$tag = 'div';
								} else {
									$tag = 'aside';
								}
								//$tag = ( $item['settings']['is_content'] )? 'div' : 'aside';
								//$tag = ( 'page_content' == $item['id'] || 'content_block' == $item['id'] )? 'article' : $tag;
								//$title = $this->get_title( $item );
								$args = array();
								$args['before_widget'] = $this->get_item_wrapper( $tag , 'before' , $item, $item_key );
								$args['after_widget'] = $this->get_item_wrapper( $tag );
								switch( $item['type'] ){
									case 'native' :
										echo $args['before_widget'];
										$item_obj = $layout_model->get_item_object( $item );
										$item_obj->item_render_site( $post , $item );
										echo $args['after_widget'];
										break;
									case 'widget' :
										\the_widget( $item['id'] , $item['settings'], $args );
										break;
								};
							}
                        };?>
                        </div><?php 
					endfor;?>
                </div>
        	<?php endif;?>
        <?php endforeach;?>
        <?php
	}
	
	public function get_tertiary_nav( $post, $layout_obj, $layout_model ){
	}
	
	public function get_email_view( $post , $layout_obj, $layout_model ){ 
		/************************************************
		** START LAYOUT **
		*************************************************/
		$email_width = 700;?>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="<?php echo $email_width;?>" style="border-collapse: collapse;">
            <tr>
                <td>
                	<center>&nbsp;<br />
                    <?php if( is_user_logged_in() ):?><a href="#" style="font-size: 10px;">View in Browser</a> | <a href="#" style="font-size: 10px;">Visit Website</a><?php endif;?>
                    <br />&nbsp;</center>
                </td>
            </tr>
        </table>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="<?php echo $email_width;?>" style="border-collapse: collapse;">
        	<?php foreach( $layout_obj as $row ):?>
            	<?php if( 'row-100' == $row['id'] ){
					$row_style = ' style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px;"';
				} else if( 'row-200' == $row['id'] ) {
					$row_style = 'bgcolor="#dddddd" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px;"';
				} else {
					$row_style = 'style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px;"';
				}//if( isset( $row['columns'] ) ):?>
                <tr >
                    <td <?php echo $row_style;?>>
                    <?php if( isset( $row['columns'] ) ):?>
                    	<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                        	<tr>
                            <?php $column_count = $layout_model->get_columns_by_layout( $row['layout'] );
							$adj_width = $email_width;
                            for( $i = 1; $i <= $column_count; $i++ ){
								$c_style = '';
								$c_id = 'column-'.$i;
								$c_width = round( $adj_width * $layout_model->email_column_widths[ $row['layout'] ][ $c_id ] );
								if( 'pagbuilder-layout-aside' == $row['layout'] ){
									if( !isset( $row['columns']['column-2'] ) ) $c_width = $adj_width;
									if( 'column-2' == $c_id ) $c_style .= ' bgcolor="#eeeeee"';
								}
								$c_style .= ' width="'.$c_width.'"';
								$c_style .= ' align="center" valign="top"';
								if( isset( $row['columns'][ $c_id ] ) ){?>
									<td <?php echo $c_style;?>>
                                    <?php if( 'row-100' != $row['id'] ) echo' &nbsp;<br />';?>
                                    
                                    
                                    
                                    
                                    
                                    <?php if( $row['columns'][ $c_id ]['items']){
										foreach( $row['columns'][ $c_id ]['items'] as $item_key => $item ){
											$item['email-width'] = $c_width - 40;
											//$tag = ( $item['settings']['is_content'] )? 'article' : 'aside';
											//$tag = ( 'page_content' == $item['id'] || 'content_block' == $item['id'] )? 'article' : $tag;
											//$title = $this->get_title( $item );
											$args = array();
											$args['before_widget'] = $this->get_item_email_wrapper( 'before' , $item, $item_key );
											$args['after_widget'] = $this->get_item_email_wrapper();
											switch( $item['type'] ){
												case 'native' :
													echo $args['before_widget'];
													$item_obj = $layout_model->get_item_object( $item );
													$item_obj->item_render_site( $post , $item );
													echo $args['after_widget'];
													break;
												case 'widget' :
													\the_widget( $item['id'] , $item['settings'], $args );
													break;
											};
										}
									};?>
                                    
                                        
                                        
                                        
                                        <?php if( 'row-100' != $row['id'] ) echo '<br />&nbsp;';?>
                    				</td>
								<?php }
							};?> 
                    		</tr>
                    	</table>
                    <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
            	<tr>
                	<td>
                    &nbsp;<br />&nbsp;
            		</td>
                </tr>
        </table>
        <?php
	}
/********************************************
** START SERVICES **
*********************************************/
	/*public function get_third_level_nav( $post ){
		$menu_name = 'site';
		if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
			$current_menu_parent = false;
			$sub_items = array();
			$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
			$menu_items = wp_get_nav_menu_items( $menu->term_id );
			$menu_items = $this->service_get_third_level_nav( $menu_items );
			//var_dump( $menu );
			foreach( $menu_items as $menu_id => $menu ){
				if( ( $menu['obj']->object_id == $post->ID ) ){
						if( array_key_exists( $menu['parent'], $menu_items ) ){ // Is third or greater level
							$current_menu_parent = $menu['parent'];
						} else {
							$current_menu_parent = $menu_id;
						}
					break;
				}
			}
			if( $current_menu_parent && array_key_exists( $current_menu_parent, $menu_items ) ){
				$parent = $menu_items[$current_menu_parent];
				var_dump( $parent );
				if( isset( $parent['children']) && $parent['children'] ){
					echo $parent['obj']->title;
					foreach( $parent['children'] as $child ){
						echo $menu_items[$child]['obj']->title;
					}
				}
			}
			/*foreach ( $menu_items as $menu_item ) {
				if( $menu_item->menu_item_parent ){ // If second level nav - DB
					if( ( $menu_item->object_id == $post->ID ) ){
						$current_menu_item = $menu_item;
						$sub_items[] = $menu_item;
						break;
					}
				}
			}
			if( $current_menu_item ){
				echo '<div id="pagebuilder-third-level-nav">';
					foreach ( $menu_items as $menu_item ) {
						if( $menu_item->menu_item_parent == $current_menu_item->ID ){
							$sub_items[] = $menu_item;
						}
					}
					foreach( $sub_items as $sub_item ){
						$title = $sub_item->title;
						$url = $sub_item->url;
						$menu_list .= '<a href="' . $url . '">' . $title . '</a>';
					}
					echo $menu_list;
				echo '</div>';
			}*/
		//}
	//}
	
	/*private function service_get_parent_id( $nav_items ){
		foreach ( $menu_items as $menu_item ) {
			if( $menu_item->menu_item_parent ){ // If second or thrid level nav - DB
				if( ( $menu_item->object_id == $post->ID ) ){
					return $menu_item->ID;
				}
			}
		}
	}*/
	
	/*private function service_nav_items_by_id( $nav_items ){
		$new_nav = array();
		foreach( $nav_items as $nav_item ){
			$new_nav[ $nav_item->ID ] = $nav_item;
		}
	}*/
	
	/*public function service_get_third_level_nav( $menu_items ){
		$menu  = array();
		$current_id = false;
		$parent_id = false;
		foreach ( $menu_items as $menu_item ) {
			$menu[ $menu_item->ID ]['obj'] = $menu_item;
			if( $menu_item->menu_item_parent ) $menu[ $menu_item->ID ]['parent'] = $menu_item->ID;
			if( array_key_exists ( $menu_item->menu_item_parent , $menu ) ) $menu[ $menu_item->menu_item_parent ]['children'][] = $menu_item->ID;
		}
		return $menu;
	}*/
	
	
	private function get_title( $item_instance ){
		if( $item_instance['settings']['title_tag'] && $item_instance['settings']['title'] ){
			$tag = $item_instance['settings']['title_tag'];
			$title = $item_instance['settings']['title'];
			return '<'.$tag.'>'.$title.'</'.$tag.'>';
		} else {
			return '';
		}
	}
	
	private function get_item_wrapper( $tag , $position = 'after' , $item = array(), $item_key = '' ){
		switch( $position ){
			case 'before':
				$force_first = ( isset( $item['settings']['force_mobile_first'] ) && $item['settings']['force_mobile_first'] )? ' pagebuilder-force-first' : '';
				$title = $this->get_title( $item );
				$wrapper = '<'.$tag.' id="'.$item_key.'" class="pagebuilder-item widget_'.$item['id'].' '.$item['settings']['css_hook'].$force_first.'"><div class="item-inner-wrapper" >'.$title;
				break;
			default:
				$wrapper = '<div style="clear:both"></div></div></'.$tag.'>';
				break;
		}
		return $wrapper;
	}
	
	private function get_item_email_wrapper($position = 'after' , $item = array(), $item_key = '' ){
		switch( $position ){
			case 'before':
				//$title = $this->get_title( $item );
				$wrapper = '<table width="'.$item['email-width'].'" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;"><tr><td>';
				break;
			default:
				$wrapper = '</td></tr></table>';
				break;
		}
		return $wrapper;
	}
};?>