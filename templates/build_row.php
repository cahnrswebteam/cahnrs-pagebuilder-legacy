<?php namespace cahnrswp\pagebuilder;

$layout_model = new layout_model();
$metabox_view = new metabox_view();

$row = array();
$row['id'] = ( isset( $_GET['i'] ))? 'row-'.$_GET['i'] : 'row-'.rand(10,100);
$row['name'] = ( isset( $_GET['n'] ))? urldecode( $_GET['n'] ) : 'Content Row '. $row['id'];
$row['class'] = ( isset( $_GET['c'] ))? urldecode( $_GET['c'] ) : '';
$row['layout'] = ( isset( $_GET['l'] ))? urldecode( $_GET['l'] ) : '';

$layout_obj = array( $row );


$metabox_view->render_layout_editor_row( false , $row, $layout_obj );


//$layout_control = new layout_control();
//$item = $layout_control->get_item_object( $item_array );
//include DIR.'views/editor_item_include_view.phtml';?>