<?php namespace cahnrswp\pagebuilder;

class metabox_view {
	public $layout_model;
	public $meta_base = '_pagebuilder';
	
	public function __construct(){
		$this->layout_model = new layout_model();
	}
	
	public function render_editor_controls( $post ){
		?>
        <nav id="pagebuilder-edit-controls">
        	<span class="title-text">Editing Mode: </span>
        	<div class="pagebuilder-editor-options">
        		<a href="#pagelayout-editor" class="layout-editor" >Layout Editor</a><a href="#content-editor"  class="content-editor active" >Content Editor</a>
        	</div>
        </nav>
		<?php 
	}
	
	public function render_content_editors( $post, $items ){
		echo '<div id="pagebuilder-content-editor">';
		$the_content = explode( '<!-- PRIMARY CONTENT -->' ,  $post->post_content );
		if( 2 > count($the_content ) ){ // IF NO PRIMARY CONTENT WRITTEN IN THE CONTENT
			$legacy_content = \get_post_meta( $post->ID , '_cahnrs_page_content', true );  // GET PAGEBUILDER EDITORS META
			$primary_content = ( $legacy_content )? $legacy_content : $post->post_content;
			if( strpos( $post->post_content, '<!--more-->' ) ){
				$more_content = explode( '<!--more-->' , $post->post_content );
				$primary_content = $more_content[0];
				unset( $more_content[0] );
			}
		} else {
			$primary_content = $the_content[1];
		}
		//$primary_content = ( 1 == count($primary_content ) )? $post->post_content : $primary_content[1];
		echo '<div class="content-block-editor page_content-1 pagebuilder-primary-editor" data-id="page_content-1">';
			echo '<div class="inner-wrapper">';?>
            <header>
                Primary Content
            </header>
            <?php
			$primary_settings = array('textarea_name'=> '_pagebuilder_editor[primary_content]' );
			\wp_editor( $primary_content , 'primary_content', $primary_settings );?>
            <footer class="pagebuilder-lightbox-show">
                <a href="#" class="settings-done lb-close-action">Done</a><br>
                <a href="#" class="remove-item-action">Remove Item</a>
            </footer>
            <?php
			echo '</div>';
		echo '</div>';
		$all_meta = get_post_meta( $post->ID , '_pagebuilder_editor', true );
		/** CREATE EDITOR LIST *****************************************/
		$t = 0;
		foreach( $items as $item ){
			if( $item['instance'] > $t ) $t = $item['instance'];
		}
		echo $t;
		$t++;
		for( $e = $t ; $e < ( $t + 6 ); $e++ ){
			$items['content_block-'.$e] = array(
				'input-name' => '__dynamic__',
				'status' => 'inactive',
				'instance' => $e,
			);
		}
		/** END EDITOR LIST *****************************************/
		
		foreach( $items as $item_key => $item ){
			if( isset( $all_meta[ $item_key ] ) ){ // IF META IS SET FOR THE CONTENT EDITOR
				$editor_content = $all_meta[ $item_key ];
			} else { // NO META SET
				if( isset( $item['content'] ) ) {
					$editor_content = $item['content'];
				} else {
					if( 1000 == $item['instance'] && isset( $more_content ) && $more_content ) {
						$editor_content = implode('', $more_content );
					} else {
						$editor_content = '&nbsp;';
					}
				}
			}
				//$meta = ( $all_meta[ $item_key ]  )? $all_meta[ $item_key ]: '&nbsp;';
				$title = ( $item['settings']['title'] )? $item['settings']['title'] : 'Content Block '.$item['instance'];
				$status = ( $item['status'] )? $item['status'] : '';
				$item['settings']['is_content'] = ( isset( $item['settings']['is_content'] ) )? $item['settings']['is_content'] : 1 ;
				echo '<div class="content-block-editor '.$item_key.' '.$status.'" data-id="'.$item_key.'">';?>
					<div class="inner-wrapper">
					<header>
                    	<p>
                    		<label>Title: </label>
                    		<input class="input-title" type="text" name="<?php echo $item['input-name'];?>[settings][title]" value="<?php echo $title;?>" /><br />
                            
                        </p>
                        <p>
                            <label> Display Title: </label>
                            <select name="<?php echo $item['input-name'];?>[settings][title_tag]">
                            	<option value="0" <?php \selected( $item['settings']['title_tag'] , '0');?>>Do Not Display</option>
                            	<option value="h2" <?php \selected( $item['settings']['title_tag'] , 'h2');?>>Heading - H2</option>
                                <option value="h2" <?php \selected( $item['settings']['title_tag'] , 'h3');?>>Heading - H3</option>
                                <option value="h2" <?php \selected( $item['settings']['title_tag'] , 'h4');?>>Heading - H4</option>
                            </select>
                        </p>
                        <p>
                    		<label>CSS Hook: </label>
                    		<input type="text" name="<?php echo $item['input-name'];?>[settings][css_hook]" value="<?php echo $item['settings']['css_hook'];?>" />
                        </p>
                        <p>
                    		<input class="hidden-input" type="checkbox" name="<?php echo $item['input-name'];?>[settings][is_content]" value="0" checked="checked" />
                    		<input type="checkbox" name="<?php echo $item['input-name'];?>[settings][is_content]" value="1" <?php \checked( $item['settings']['is_content'] , 1);?> /> <label>Is Content</label>
                        </p>
                    </header>
                    <div class="header-settings">
                    	
                    </div>
                    <?php 
					$primary_settings = array('textarea_name'=> '_pagebuilder_editor['.$item_key.']' );
					$content_editor_id = str_replace('-','_', $item_key );
					\wp_editor( $editor_content , $content_editor_id , $primary_settings );?>
                    <footer class="pagebuilder-lightbox-show">
                    	<a href="#" class="settings-done lb-close-action">Done</a><br>
                        <a href="#" class="remove-item-action">Remove Item</a>
                    </footer>
                    <?php
					echo '</div>';
				echo '</div>';
		}
		
		echo '</div>';
	}
	
	public function render_layout_editor( $post , $layout_obj ){ ?>
		<div id="pagebuilder-layout-editor">
            <div class="inner-wrapper">
				<?php foreach( $layout_obj as $row ){
					if( $row['id'] ){
                    	$this->render_layout_editor_row( $post , $row, $layout_obj );
					}
                };?>
            </div> 
            <?php $this->render_layout_editor_settings( $post );?>
		</div>
        <script type="text/javascript">var homeURL = "<?php echo \get_site_url();?>"; var post_id =<?php echo $post->ID;?>;</script>
        <?php
	}
	
	public function render_layout_editor_settings( $post ){
		$meta = \get_post_meta( $post->ID , '_pagebuilder_settings', true );
		$force_default = ( isset( $meta['force_default'] ) )? $meta['force_default']  : 0;
		$is_modified = ( isset( $meta['is_modified'] ) )? $meta['is_modified'] : 0;
		?>
		<section id="pagebuilder-layout-settings">
        	<header>Advanced Settings</header>
            <p>
            <input class="hidden-input" type="radio" name="_pagebuilder_settings[force_default]" value=0 <?php checked( $force_default, 0 ); ?> />
            <input type="radio" name="_pagebuilder_settings[force_default]" value=1 <?php checked( $force_default, 1 ); ?> /> Force Default Layout 
            </p>
            <p>
            <input class="hidden-input" type="radio" name="_pagebuilder_settings[reset_layout]" value=0 checked="checked"/>
            <input type="radio" name="_pagebuilder_settings[reset_layout]" value=1 /> Reset Layout 
            </p>
            <input  type="radio" class="input-pagebuilder-custom hidden-input" name="_pagebuilder_settings[is_modified]" value=0 <?php checked( $is_modified, 0 ); ?> />
            <input type="radio" class="input-pagebuilder-custom hidden-input" name="_pagebuilder_settings[is_modified]" value=1 <?php checked( $is_modified, 1 ); ?> />
		</section>
        <?php
	}
	
	public function render_layout_editor_row( $post , $row, $layout_obj ){
		$layout = ( $row['layout'] )? $row['layout'] : 'pagbuilder-layout-full';
		$input_base = $this->meta_base.'['.$row['id'].']';
		?>
        <?php if( 'row-100' !== $row['id'] ):?>
        	<a href="#" class="add-row-action"></a>
            <div class="pagebuilder-settings-form add-row-settings pagebuilder-lightbox-window">
            
            
            <div class="settings-wrapper">
                <header>
                    Add New Row<br />
                    <span>Row Options</span>
                </header>
                
                <section class="form-content">
                <label>Row Name</label><br />
                <input class="input-row-name" type="text"  value="" />
                <p>
                <label>Columns</label><br />
                <select class="input-column-layout" >
                	<option value="pagbuilder-layout-full">One</option>
                    <option value="pagbuilder-layout-aside">Two - Sidebar Right</option>
                    <option value="pagbuilder-layout-half">Two - Half</option>
                    <option value="pagbuilder-layout-third-right">Two - 1/3 Right</option>
                    <option value="pagbuilder-layout-third-left">Two - 1/3 Left</option>
                    <option value="pagbuilder-layout-thirds">Three</option>
                    <option value="pagbuilder-layout-fourths">Four</option>
                </select></p>
                <h4>Advanced Settings</h4>
                <p>CSS Hook: <input class="input-css-hook" type="text"  value="" /></p> 
                </section>
                <footer>
                    <a href="#" class="insert-row-action lb-close-action">Insert Row</a><br />
                    <a href="#" class="lb-cancel-action lb-close-action">Cancel</a>
                </footer>
            </div>
            
            
            
            </div>
        <?php endif;?>
        <div class="layout-row <?php echo $layout;?>" data-id="<?php echo $row['id'];?>">
            <header class="row-header">
                <a href="#" class="row-settings-action title-text"><?php echo $row['name'];?></a>
                <div class="pagebuilder-settings-form add-row-settings pagebuilder-lightbox-window">
                
                
                <div class="settings-wrapper">
                    <header>
                        Row Settings
                    </header>
                    
                    <section class="form-content">
                    <label>Row Name</label><br />
                    <input class="input-row-name" type="text" name="<?php echo $input_base;?>[name]" value="<?php echo $row['name'];?>" />
                    <input class="hidden-input" type="text" name="<?php echo $input_base;?>[id]" value="<?php echo $row['id'];?>" />
                    <input class="input-row-order hidden-input" type="text" name="<?php echo $input_base;?>[order]" value="<?php echo $row['order'];?>" />
                    <p>
                    <label>Columns</label><br />
                    <select class="input-column-layout" name="<?php echo $input_base;?>[layout]">
                        <option value="pagbuilder-layout-full" <?php \selected( $row['layout'] , 'pagbuilder-layout-full' );?>>One</option>
                        <option value="pagbuilder-layout-aside" <?php \selected( $row['layout'] , 'pagbuilder-layout-aside' );?>>Two - Sidebar Right</option>
                        <option value="pagbuilder-layout-half" <?php \selected( $row['layout'] , 'pagbuilder-layout-half' );?>>Two - Half</option>
                        <option value="pagbuilder-layout-third-right" <?php \selected( $row['layout'] , 'pagbuilder-layout-third-right' );?>>Two - 1/3 Right</option>
                        <option value="pagbuilder-layout-third-left" <?php \selected( $row['layout'] , 'pagbuilder-layout-third-left' );?>>Two - 1/3 Left</option>
                        <option value="pagbuilder-layout-thirds" <?php \selected( $row['layout'] , 'pagbuilder-layout-thirds' );?>>Three</option>
                        <option value="pagbuilder-layout-fourths" <?php \selected( $row['layout'] , 'pagbuilder-layout-fourths' );?>>Four</option>
                    </select></p>
                    <h4>Advanced Settings</h4>
                    <p>CSS Hook: <input class="input-css-hook" type="text"   name="<?php echo $input_base;?>[class]" value="<?php echo $row['class'];?>" /></p> 
                    </section>
                    <footer>
                        <a href="#" class="update-row-action lb-close-action">Done</a><br />
                        <?php if( 'row-100' != $row['id'] && 'row-200' != $row['id'] ):?>
                        <a href="#" class="delete-row-action lb-close-action">Delete Row</a>
                        <?php endif;?>
                    </footer>
                </div>
                
                
                
                </div>
            </header>
            <div class="column-wrapper">
            	<?php $this->render_layout_editor_columns( $post , $row, $layout_obj );?>
                <div style="clear: both;"></div>
            </div>
        </div>
		<?php 
	}
	
	public function render_layout_editor_columns( $post , $row, $layout_obj ){
		for( $i = 1; $i < 5; $i++ ):
			$c = $i;
			if( 'pagbuilder-layout-aside' == $row['layout'] ){
				if( 1 == $i ) $c = 2;
				if( 2 == $i ) $c = 1;
			}
			$column_id = 'column-'.$c;
			$active = ( isset( $row['columns'][ $column_id ] ) )? ' active': '';
			if( !$active && 1 == $c ) $active = ' active';
			 ?><div class="layout-column <?php echo $column_id.$active;?>" data-id="<?php echo $column_id;?>">
             	<div class="inner-wrapper">
                	<?php if( isset( $row['columns'][ $column_id ] ) ):?>
                    	<?php foreach( $row['columns'][ $column_id ]['items'] as $item ){
							$input_base = $this->meta_base.'['.$row['id'].'][columns]['.$column_id.']';
                        	$this->render_layout_editor_item( $item, $input_base );
                        };?>
                    <?php endif;?>
                    <a href="#" class="add-item-action top-level"><span class="default">+ Add New Item</span></a>
                </div>
            </div><?php 
		endfor;
	}
	
	public function render_layout_editor_item( $item_array, $input_name ){
		$item = $this->layout_model->get_item_object( $item_array );
		$item_type = ( $item->sub_type )? $item->sub_type : $item_array['type']; 
		$base_id = $item_array['id'].'-'.$item_array['instance'];
		$input_name .='[items]['.$base_id.']';
		if( $item_array['settings']['title'] ){
			$item_title = $item_array['settings']['title'];
			$item_description = $item->name;
		} else {
			$in = ( 1  == $item_array['instance'] )? '': $item_array['instance'];
			$item_title = $item->name.' '.$in;
			$item_description = $item->description;
		}; ?>
        <a href="#" class="add-item-action inline-add-item <?php echo $base_id;?>"><span>+ Add New Item</span></a>
        <div class="pagebuilder-item-wrapper">
            <a class="select-add-item pagebuilder-item <?php echo $item_type;?> <?php echo $item_array['id'];?> <?php echo $base_id;?>" href="#" data-id="<?php echo $item_array['id'];?>" data-type="native" data-baseid="<?php echo $base_id;?>" data-instance="<?php echo $item_array['instance'];?>">
                <span class="title"><?php echo $item_title;?></span>
                <span class="summary"><?php echo $item_description;?></span>
            </a>
            <?php if( 'widget' == $item_array['type'] || method_exists( $item ,'get_form' ) ):?>
            <div class="pagebuilder-settings-form pagebuilder-lightbox-window">
            
            
            
            
                <div class="settings-wrapper">
                	<header>
                    	<?php echo $item->name;?>
                    </header>
                    <section class="form-content">
                    <?php 
					if( 'widget' == $item_array['type'] ){
						$item_name = 'widget-'.$item->id_base.'[1]';
						ob_start();
						$item->form( $item_array['settings'] );
						$form = ob_get_clean();
						echo str_replace( $item_name , $input_name.'[settings]' , $form );
                    } else {
                        echo $item->get_form( $item_array , $input_name );
                    }
                    ?>
                    <div class="cc-form-section-advanced">
						<header>Advanced Settings +</header>
    					<div class="section-wrapper">
                            <div class="form-sub-section cc-form-advanced-settings">
                    <p>
                    <?php
                    if( isset( $item_array['settings']['is_content'] ) ) { $content_checked = $item_array['settings']['is_content'];} 
                    else if( $item->is_content ) { $content_checked = 1;} 
                    else { $content_checked = 0;}?>
                    <input style="display: none;" type="checkbox" name="<?php echo $input_name;?>[settings][is_content]" value="0" checked="checked" />
                    <input type="checkbox" name="<?php echo $input_name;?>[settings][is_content]" value="1"  <?php checked( $content_checked, 1 ); ?> /> Include as Content</p>
                    <p>
                    <?php $force_first = ( isset( $item_array['settings']['force_mobile_first'] ) )? $item_array['settings']['force_mobile_first'] : 0;?>
                    <input style="display: none;" type="checkbox" name="<?php echo $input_name;?>[settings][force_mobile_first]" value="0" checked="checked" />
                    <input type="checkbox" name="<?php echo $input_name;?>[settings][force_mobile_first]" value="1"  <?php checked( $force_first, 1 ); ?> /> Force First - Responsive
                    </p>
                    <p>CSS Hook: <input type="text" name="<?php echo $input_name;?>[settings][css_hook]" value="<?php echo $item_array['settings']['css_hook'];?>" /></p> 
                    		</div>
                    	</div>
                    </div>
                    
                    
                    
                    
                    </section>
                    
                	<footer>
                    	<a href="#" class="settings-done lb-close-action">Done</a><br />
                        <a href="#" class="remove-item-action">Remove Item</a>
                    </footer>
                </div>
            </div>
            <?php endif;?>
            <input type="text" class="hidden-input" name="<?php echo $input_name;?>[type]" value="<?php echo $item_array['type'];?>" />
            <input type="text" class="hidden-input" name="<?php echo $input_name;?>[id]" value="<?php echo $item_array['id'];?>" />
            <input type="text" class="hidden-input" name="<?php echo $input_name;?>[instance]" value="<?php echo $item_array['instance'];?>" />
        </div>
        <?php
	}
	
	public function render_add_item_window( $post ){
		?>
		<section id="pagebuilder-add-item" class="pagebuilder-lightbox-window">
        	<header class="section-header">
            	Add New Item
                <a href="#" class="lb-close-action">x</a>
            </header>
            <nav>
            	<a href="#" class="add-item-pop active" data-type="popular-items">
                	Popular Items
                </a><a href="#" class="add-item-widgets" data-type="widget-items">
                	All Widgets
                </a><a href="#" class="add-item-sidebar" data-type="sidebar-items">
                	Sidebars & Widget Areas
                </a>
            </nav>
            <?php $this->render_add_item_popular( $post );?>
            <?php $this->render_add_item_widgets( $post );?>
   			<?php $this->render_add_item_sidebar( $post );?>
            <footer>
            <a href="#" class="lb-close-action">Cancel</a><a href="#" class="insert-item-action">Insert Item</a>
            </footer>
		</section>
        <?php
	}
	
	public function render_add_item_popular( $post ){?>
		<!-- START SECTION POPULAR-->
            <div class="section-content popular-items active">
            	<div class="sub-section">
                	<header>
                    	Page/Post Items
                    </header>
                    <div class="sub-section-content">
                    	<?php 
						foreach( $this->layout_model->registered_items as $item_id ):
						$item = $this->layout_model->get_item_object( array('type' => 'native' , 'id' => $item_id ) );?>
                        <?php if( isset( $item->subtype ) && 'page_item' == $item->subtype  ):?>
                            <div class="pagebuilder-item-wrapper">
                            <a class="select-add-item pagebuilder-item native" href="#" data-id="<?php echo $item_id;?>" data-type="native" >
                                <span class="title"><?php echo $item->name;?></span>
                                <span class="summary"><?php echo $item->description;?></span>
                            </a>
                            </div>
                        <?php endif;?>
                    	<?php endforeach;?>
                    	<div style="clear: both"></div>
                    </div>
                    <footer>
                    </footer>
                </div>
                <!-- START WIDGETS - POPULAR -->
                <?php $pop_widg = $this->layout_model->get_popular_widgets();?>
                <div class="sub-section">
                	<header>
                    	Frequently Used Items
                    </header>
                    <div class="sub-section-content">
					<?php if ( !empty( $GLOBALS['wp_widget_factory'] ) ):?>
                      <?php $widgets = $GLOBALS['wp_widget_factory']->widgets;?>
                      <?php foreach( $widgets as $widget_key => $widget ):?>
                      	<?php if( in_array( $widget_key , $pop_widg ) ):?>
                        <div class="pagebuilder-item-wrapper">
                        <a class="select-add-item pagebuilder-item widget" href="#" data-id="<?php echo $widget_key;?>"  data-type="widget" >
                        	<span class="title"><?php echo $widget->name;?></span>
                            <span class="summary"><?php echo $widget->widget_options['description'];?></span>
                        </a>
                        </div>
                        <?php endif;?>
                    		<?php endforeach;?>
                		<?php endif;?>
                    	<div style="clear: both"></div>
                    </div>
                    <footer>
                    </footer>
                </div>
            </div>
	<?php }
	
	public function render_add_item_widgets( $post ){?>
    <?php $widgets = $GLOBALS['wp_widget_factory']->widgets;?>
    <!-- START SECTION WIDGETS-->
            <div class="section-content widget-items">
                <!-- START WIDGETS - REST-->
                <div class="sub-section">
                	<header>
                    	Widgets
                    </header>
                    <div class="sub-section-content">
					<?php if ( !empty( $widgets ) ):?>
                      <?php foreach( $widgets as $widget_key => $widget ):?>
                      <div class="pagebuilder-item-wrapper">
                        <a class="select-add-item pagebuilder-item widget" href="#" data-id="<?php echo $widget_key;?>"  data-type="widget" >
                        	<span class="title"><?php echo $widget->name;?></span>
                            <span class="summary"><?php echo $widget->widget_options['description'];?></span>
                        </a>
                        </div>
                    		<?php endforeach;?>
                		<?php endif;?>
                    	<div style="clear: both"></div>
                    </div>
                    <footer>
                    </footer>
                </div>
            </div>
		
	<?php }
	
	public function render_add_item_sidebar( $post ){?>
		<!-- START SECTION POPULAR-->
            <div class="section-content sidebar-items">
            	<div class="sub-section">
                	<header>
                    	Sidebars & Widget Areas
                    </header>
                    <div class="sub-section-content">
                    	<?php 
						foreach( $this->layout_model->registered_sidebars as $item_id ):
						$item = $this->layout_model->get_item_object( array('type' => 'native' , 'id' => $item_id ) );?>
                        <div class="pagebuilder-item-wrapper">
                        <a class="select-add-item pagebuilder-item sidebar" href="#" data-id="<?php echo $item_id;?>"  data-type="native" >
                        	<span class="title"><?php echo $item->name;?></span>
                            <span class="summary"><?php echo $item->description;?></span>
                        </a>
                        </div>
                    	<?php endforeach;?>
                    	<div style="clear: both"></div>
                    </div>
                    <footer>
                    </footer>
                </div>
            </div>
	<?php }
	
};?>