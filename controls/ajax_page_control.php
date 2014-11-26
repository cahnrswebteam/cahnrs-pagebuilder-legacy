<?php namespace cahnrswp\pagebuilder;

class ajax_page_control {
	
	public function init(){
		\add_filter( 'template_include', array( $this ,  'pagebuilder_template' ), 99 );
	}
	
	public function pagebuilder_template( $template ){
		if( 'item' == $_GET['cahnrs-pagebuilder'] ){ return DIR.'/templates/build_item.php';}
		if( 'row' == $_GET['cahnrs-pagebuilder'] ){ return DIR.'/templates/build_row.php';}
		return $template;
	}
};?>