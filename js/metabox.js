var init_pagebuilder;

jQuery(document).ready(function(){
	init_pagebuilder = new pagebuilder();
});
var pagebuilder = function(){
	this.lay_con = jQuery( '#pagebuilder-edit-controls a' );
	this.edtr_con = jQuery( '#pagebuilder-content-editor');
	this.edtr_lay = jQuery( '#pagebuilder-layout-editor');
	this.is_cus = jQuery( '#pagebuilder-layout-editor input.input-pagebuilder-custom');
	this.lb_add_itm = jQuery( '#pagebuilder-add-item');
	this.lb_wids = jQuery('.pagebuilder-lightbox-window');
	this.add_itms = jQuery('#pagebuilder-add-item .pagebuilder-item');
	this.exst_itms = new Object;
	this.cur_add_itm;
	this.cur_add_row;
	this.cur_edt_itm;
	this.cur_row;
	var s = this;
	
	s.b_e = function(){
		s.lay_con.click( function( event ){ 
			event.preventDefault(); 
			s.chng_edtr( jQuery( this ) ); 
			});
		s.edtr_lay.on('mouseover' ,'a.add-item-action', function(){
			jQuery(this).addClass('active');
			});
		s.edtr_lay.on('mouseout' ,'a.add-item-action', function(){
			jQuery(this).removeClass('active');
			});
		s.edtr_lay.on('click' ,'a.add-item-action', function( event ){
			event.preventDefault(); s.sel_add_itm( jQuery(this) );
			});
		jQuery('body').on('click' ,'.pagebuilder-lb-bg', function(){
			s.hdl_cls_lb( jQuery(this) );
			});
		jQuery('body').on('click', '.lb-close-action', function( event ){
				event.preventDefault(); s.hdl_cls_lb();
			});
		s.lb_add_itm.on('click','nav a', function( event ){
			event.preventDefault(); s.tog_add_ops( jQuery(this) );
			});
		s.lb_add_itm.on('click','.pagebuilder-item',function( event ){
			event.preventDefault(); s.hlt_add_item( jQuery(this) );
			});
		s.lb_add_itm.on('click','.insert-item-action',function( event ){
			event.preventDefault(); s.hdl_ins_itm();
			});
		s.edtr_lay.on('click','.pagebuilder-item',function( event ){
			event.preventDefault(); s.hlt_itm_set( jQuery(this) );
			});
		s.edtr_lay.on('click','.remove-item-action',function( event ){
			event.preventDefault(); s.hdl_rmv_itm( jQuery(this) );
			});
		s.edtr_lay.on('click','.add-row-action',function( event ){
			event.preventDefault(); s.add_row_set( jQuery(this) );
			});
		s.edtr_lay.on('click','.insert-row-action',function( event ){
			event.preventDefault(); s.hdl_inst_row( jQuery(this) );
			});
		s.edtr_lay.on('click','.row-settings-action',function( event ){
			event.preventDefault(); s.hdl_row_set( jQuery(this) );
			});
		s.edtr_lay.on('click','.update-row-action',function( event ){
			event.preventDefault(); s.up_row( jQuery(this) );
			});
		s.edtr_lay.on('click','.delete-row-action',function( event ){
			event.preventDefault(); s.del_row( jQuery(this) );
			});
		s.edtr_con.on('click','.remove-item-action',function( event ){
			event.preventDefault(); s.hdl_con_rmv_itm( jQuery(this) );
			});
	}
	
	s.bld_exst_itms = function(){
		var exst = jQuery('.layout-row .pagebuilder-item');
		exst.each( function(){
			var dataid = jQuery( this).data('id');
			var datain = jQuery( this).data('instance');
			if( dataid in s.exst_itms ){
				if( datain > s.exst_itms[ dataid ] ) s.exst_itms[ dataid ] = datain;
				//s.exst_itms[ dataid ] = s.exst_itms[ dataid ] + 1;
			} else {
				s.exst_itms[ dataid ] = datain;
			}
		});
	}
	
	s.del_row = function(){
		var row = s.cur_row.parents('.layout-row');
		var add_row = row.next('.add-row-action');
		var row_set = add_row.next('.add-row-settings');
		row_set.remove();
		row.delay(300).slideUp('medium',function(){
			row.remove();
		})
		add_row.delay(300).slideUp('medium',function(){
			add_row.remove();
		})
	}
	
	s.up_row = function(){
		var row = s.cur_row.parents('.layout-row');
		var sf = s.cur_row.next('.pagebuilder-settings-form');
		var n = sf.find('.input-row-name').val();
		var l = sf.find('.input-column-layout').val();
		var c = sf.find('.input-css-hook').val();
		row.attr('class','');
		row.addClass( 'layout-row '+l );
		row.find('.row-header .title-text').text( n );
	}
	
	s.hdl_row_set = function( i_c ){
		s.cur_row = i_c;
		s.tog_bg();
		i_c.next('.pagebuilder-settings-form').addClass('active-lb');
	}
	
	s.hdl_inst_row = function(){
		if( homeURL ){
			var rs = jQuery('.layout-row').length;
			var sf = s.cur_add_row.next('.pagebuilder-settings-form');
			var l = encodeURIComponent( sf.find('.input-column-layout').val() );
			var c = encodeURIComponent( sf.find('.input-css-hook').val() );
			var n = encodeURIComponent( sf.find('.input-row-name').val() );
			var query = homeURL + '?cahnrs-pagebuilder=row&l='+l+'&i='+rs+'&n='+n+'&c='+c;
			jQuery.post( query , function( data ) {
					s.cur_add_row.before( data );
					//s.up_row_ord();
				});
		}
	}
	
	s.add_row_set = function( i_c ){
		s.cur_add_row = i_c;
		s.tog_bg();
		i_c.next('.pagebuilder-settings-form').addClass('active-lb');
	}
	
	s.hdl_con_rmv_itm = function( i_c ){
		var par = i_c.parents('.content-block-editor');
		//var itm = jQuery('.'+par.data('id') );
		s.hdl_rmv_itm( s.cur_edt_itm );
		if( !par.hasClass('pagebuilder-primary-editor') ) par.remove();
	}
	
	s.hdl_rmv_itm = function( i_c ){
		s.hdl_cls_lb();
		var par = i_c.parents('.pagebuilder-item-wrapper');
		var add = par.prev('.add-item-action')
		par.delay(200).slideUp('medium', function(){ par.remove();});
		add.delay(200).slideUp('medium', function(){ add.remove();});
	}
	
	s.hlt_itm_set = function( i_c ){
		s.cur_edt_itm = i_c;
		if( i_c.hasClass('content_block') ){
			var fc = '.'+i_c.data('baseid');
			var s_f = s.edtr_con.find( fc );
		} 
		else if ( i_c.hasClass('page_content') ){
			var s_f = s.edtr_con.find( '.page_content-1' );
		}
		else {
			var s_f = i_c.siblings('.pagebuilder-settings-form');
		}
		if( s_f.length > 0 ){
			s.tog_bg();
			s_f.addClass('active-lb');
		}
	}
	
	s.tog_add_ops = function( i_c ){
		i_c.addClass('active').siblings().removeClass('active');
		var type = i_c.data('type');
		switch( type ){
			case 'sidebar-items':
				var id = '.sidebar-items';
				break;
			case 'widget-items':
				var id = '.widget-items';
				break;
			default:
				var id = '.popular-items';
				break
		}
		var add_itms = s.lb_add_itm.find('.section-content');
		add_itms.filter( id ).show();
		add_itms.not( id ).hide();
	}
	
	s.hlt_add_item = function( i_c ){
		var itms = s.add_itms;
		if( !i_c.hasClass('selected') ){
			itms.filter('.selected').removeClass('selected');
			jQuery('#pagebuilder-add-item .input-title-wrap').remove();
			itms.addClass('item-inactive');
			//jQuery('#pagebuilder-add-item .pagebuilder-item.selected').removeClass('selected');
			i_c.addClass('selected');
			var ttl = '<div class="input-title-wrap"><span class="input-arrow"></span><label><strong>Item Title</strong></label><input type="text" class="input-add-item-title" value="" /><br />Give your new item a name so it\'s easier to find later.</div>';
			i_c.after( ttl );
		} else {
			i_c.removeClass('selected');
			jQuery('#pagebuilder-add-item .input-title-wrap').remove();
			itms.removeClass('item-inactive');
		}
	}
	
	s.sel_add_itm = function( i_c ){
		s.cur_add_itm = i_c;
		s.lb_wids.filter('#pagebuilder-add-item').addClass('active-lb');
		s.tog_bg();
	}
	
	s.hdl_cls_lb = function(){
		var lb = jQuery('.active-lb');
		lb.removeClass('active-lb');
		s.tog_bg();
	}
	
	s.tog_bg = function(){
		var lb_bj = jQuery('.pagebuilder-lb-bg');
		if( lb_bj.length > 0 ){
			lb_bj.remove();
		} else {
			var bg_style = 'position: fixed; background: rgba( 0,0,0,0.7); width: 100%; height: 100%; top: 0; left: 0; z-index: 500;';
			var bg = '<div class="pagebuilder-lb-bg" style="'+bg_style+'" ></div>';
			jQuery('body').append( bg );
			
			s.is_cus.filter('[value=1]').prop('checked', true);
		}
		
		
	}
	s.hdl_ins_itm = function(){
		var i_c = s.lb_add_itm.find('.pagebuilder-item.selected');
		var i_c_p = i_c.parents('.pagebuilder-item-wrapper');
		if( i_c.length > 0 ){
			i_c.removeClass('selected');
			var id = i_c.data('id');
			var type = i_c.data('type');
			if( id in s.exst_itms ){
				s.exst_itms[ id ] = s.exst_itms[ id ] + 1;
			} else {
				s.exst_itms[ id ] = 1;
			}
			var item_count = s.exst_itms[ id ];
			//var item_count = jQuery('.'+id ).length + 1;
			var row_id = s.cur_add_itm.parents('.layout-row').data('id');
			var col = s.cur_add_itm.parents('.layout-column').data('id');
			var n = i_c_p.find('.input-add-item-title').val();
			var name = encodeURIComponent( n );
			var post = (typeof post_id !== 'undefined')? '&post_id='+post_id : '';
			var query_args = '&type='+type+'&id='+id+'&instance='+item_count+'&row='+row_id+'&column='+col+'&n='+name;
			if( typeof homeURL !== 'undefined' ){
				var query = homeURL + '?cahnrs-pagebuilder=item'+post+query_args;
				jQuery.post( query , function( data ) {
					s.cur_add_itm.before( data );
				});
			}
			if( 'content_block' == id ){
				var new_c = s.edtr_con.find('.'+id+'-'+item_count);
				new_c.removeClass('inactive');
				var c_inp = new_c.find('[name*="__dynamic__"]');
				new_c.find('.input-title').val( n );
				c_inp.each( function(){
					var ci = jQuery( this );
					var name = ci.attr('name');
					var inp = '_pagebuilder['+row_id+'][columns]['+col+'][items]['+id+'-'+item_count+']';
					name = name.replace( '__dynamic__' , inp );
					ci.attr('name',name);
				});
			}
			s.hdl_cls_lb();
			jQuery('#pagebuilder-add-item .input-title-wrap').remove();
			s.add_itms.removeClass('item-inactive');
		}
	}
	
	s.chng_edtr = function( i_c ){
		if( !i_c.hasClass('active') ) {
			if( i_c.hasClass('layout-editor') ){
				s.edtr_con.find('.content-block-editor').hide();
				s.edtr_lay.show();
			} else {
				s.edtr_con.find('.content-block-editor').show();
				s.edtr_lay.hide();
			}
			i_c.addClass('active').siblings().removeClass('active');
		}
	}
	
	s.b_e();
	s.bld_exst_itms();
}