<?php namespace cahnrswp\pagebuilder;

$layout_model = new layout_model();
$metabox_view = new metabox_view();

$post = ( isset( $_GET['post_id'] ) )? \get_post( $_GET['post_id'] ) : false ;
$item_array = array();
$item_array['settings'] = array();
$item_array['type'] = ( isset( $_GET['type'] ))? $_GET['type'] : 'native';
$item_array['id'] = ( isset( $_GET['id'] ))? $_GET['id'] : 'page_content';
if( isset( $_GET['n'] ) ) $item_array['settings']['title'] = urldecode ( $_GET['n'] );
$row_obj['id'] = ( isset( $_GET['row'] ))? $_GET['row'] : 'row-1';
$row_obj['id'] = ( isset( $_GET['row'] ))? $_GET['row'] : 'row-1';
$i = ( isset( $_GET['column'] ))? $_GET['column'] : 'column-1';
$item_array['instance'] = ( isset( $_GET['instance'] ))? $_GET['instance'] : '1';
$input_name = '_pagebuilder['.$row_obj['id'].'][columns]['.$i.']';

$metabox_view->render_layout_editor_item( $item_array, $input_name );


//$layout_control = new layout_control();
//$item = $layout_control->get_item_object( $item_array );
//include DIR.'views/editor_item_include_view.phtml';?>