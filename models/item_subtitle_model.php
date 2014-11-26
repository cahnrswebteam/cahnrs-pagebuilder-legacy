<?php namespace cahnrswp\pagebuilder;

class item_subtitle_model {
	
	public $id = 'subtitle';
	public $name = 'Subtitle';
	public $description = 'Adds a Subtitle to the page (H2).';
	public $is_content = true;
	public $subtype = 'page_item';
	
	public function get_form( $instance, $ipt_name ){?>
    	<h4>Subtitle</h4>
			<p><input type="text" style="width: 90%; max-width: 100%;" name="<?php echo $ipt_name .'[settings][title]'; ?>" value="<?php echo $instance['settings']['title'];?>" /></p>
	<?php }
	
	public function render_site( $post ){
		//if( has_post_thumbnail( $post->ID ) ){
			//the_post_thumbnail('large');
		//} else {
			//echo 'No featured image set';
		//}
		
	}
	
	public function item_render_site( $post , $instance ){?>
		<h2 class="site-subtitle"><?php echo $instance['settings']['title'];?></h2>
	<?php }
	
};?>