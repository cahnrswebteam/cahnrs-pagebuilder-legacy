<?php namespace cahnrswp\pagebuilder;

class item_sidebar_model {
	
	public $id = 'sidebar';
	public $name = 'Sidebar Selector';
	public $description = 'Select from list of Dynamic Widget Areas ( Sidebars ).';
	public $is_content = false;
	public $subtype = 'sidebar';
	
	public function get_form($instance, $ipt_name ){?>
    	<h4>Basic Settings</h4>
        <p>
        <label>Select a Sidebar</label><br />
        <select name="<?php echo $ipt_name.'[settings][sidebar]'; ?>" >
        <?php foreach( $GLOBALS['wp_registered_sidebars'] as $sidebar ):?>
        <option value="<?php echo $sidebar['id'];?>" <?php selected( $instance['sidebar'], $sidebar['id'] ); ?> ><?php echo $sidebar['name'];?></option>
        <?php endforeach;?>
        </select></p>
	<?php }
	
	public function render_site( $post ){
		echo 'test';
		
	}
	
	public function item_render_site( $post , $instance ){
		if ( is_active_sidebar( $instance['settings']['sidebar'] ) ) : ?>
			 <?php dynamic_sidebar( $instance['settings']['sidebar'] ); ?>
	  <?php endif;
	}
	
};?>