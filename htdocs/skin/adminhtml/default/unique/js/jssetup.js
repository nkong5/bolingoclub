Event.observe(window, 'load', function() {
	function jsColor(mainId, exceptions, onlyForName){
		if($$(mainId).length){
			if(onlyForName){
				var selection = 'input.input-text';
			}else{
				var selection = 'input.input-text:not('+ exceptions +')';
			}
			var selected_items = $$(mainId)[0].select(selection);
			if(onlyForName){
				selected_items.each(function(val){
					if(val.readAttribute('name') == exceptions){
						new jscolor.color(val);
					}
				});
			}else{
				selected_items.each(function(val){
					new jscolor.color(val);
				});
			}
		}
	}
	jsColor('#meigee_unique_design_base');
	jsColor('#meigee_unique_design_catlabels');
	jsColor('#meigee_unique_design_header', '#meigee_unique_design_header_header_borders_width, #meigee_unique_design_header_search_border_width, #meigee_unique_design_header_dropdown_border_width, #meigee_unique_design_header_account_submenu_link_divider_width, #meigee_unique_design_header_switchers_border_width, #meigee_unique_design_header_cart_border_width, #meigee_unique_design_header_login_border_width, #meigee_unique_design_header_account_border_width, #meigee_unique_design_header_search_transparent_bg_value, #meigee_unique_design_header_search_button_transparent_bg_value, #meigee_unique_design_header_search_border_transparent_value, #meigee_unique_design_header_search_active_transparent_bg_value, #meigee_unique_design_header_search_active_transparent_button_bg_value, #meigee_unique_design_header_search_active_border_transparent_value, #meigee_unique_design_header_search_border_h_transparent_value, #meigee_unique_design_header_search_button_h_transparent_value, #meigee_unique_design_header_search_transparent_bg_h_value, #meigee_unique_design_header_cart_transparent_bg_value, #meigee_unique_design_header_login_transparent_bg_value, #meigee_unique_design_header_wishlist_transparent_bg_value, #meigee_unique_design_header_account_transparent_bg_value, #meigee_unique_design_header_header_top_block_transparent_bg_value');
	jsColor('#meigee_unique_design_headerslider', '#meigee_unique_design_headerslider_type1_button_border_width, #meigee_unique_design_headerslider_type2_button_border_width');
	jsColor('#meigee_unique_design_menu', '#meigee_unique_design_menu_submenu_link_border_width, #meigee_unique_design_menu_submenu_borders_width, #meigee_unique_design_menu_menu_transparent_bg_value, #meigee_unique_design_menu_button_transparent_bg_value, #meigee_unique_design_menu_button_transparent_bg_h_value, #meigee_unique_design_menu_link_transparent_bg_value, #meigee_unique_design_menu_link_transparent_bg_h_value, #meigee_unique_design_menu_sticky_link_transparent_bg_value, #meigee_unique_design_menu_sticky_link_transparent_bg_h_value');
	jsColor('#meigee_unique_design_content', '#meigee_unique_design_content_page_title_border_width');
	jsColor('#meigee_unique_design_buttons', '#meigee_unique_design_buttons_buttons_border_width, #meigee_unique_design_buttons_buttons2_border_width, #meigee_unique_design_buttons_wsubscribe_button_border_width');
	jsColor('#meigee_unique_design_products', '#meigee_unique_design_products_product_border_width');
	jsColor('#meigee_unique_design_social_links', '#meigee_unique_design_social_links_social_links_border_width, #meigee_unique_design_social_links_social_links_divider_width');
	jsColor('#meigee_unique_design_footer', '#meigee_unique_design_footer_top_block_borders_width, #meigee_unique_design_footer_top_block_button_border_width, #meigee_unique_design_footer_top_block_list_link_border_width, #meigee_unique_design_footer_bottom_block_list_link_border_width, #meigee_unique_design_footer_bottom_block_borders_width, #meigee_unique_design_footer_top_block_title_border_width, #meigee_unique_design_footer_bottom_block_title_border_width, #meigee_unique_design_footer_default_links_wrapper_border_width, #meigee_unique_design_footer_store_switcher_border_width');
	
	jsColor('#meigee_unique_design_skin1_base');
	jsColor('#meigee_unique_design_skin1_catlabels');
	jsColor('#meigee_unique_design_skin1_header', '#meigee_unique_design_skin1_header_header_borders_width, #meigee_unique_design_skin1_header_search_border_width, #meigee_unique_design_skin1_header_dropdown_border_width, #meigee_unique_design_skin1_header_account_submenu_link_divider_width, #meigee_unique_design_skin1_header_switchers_border_width, #meigee_unique_design_skin1_header_cart_border_width, #meigee_unique_design_skin1_header_login_border_width, #meigee_unique_design_skin1_header_account_border_width, #meigee_unique_design_skin1_header_search_transparent_bg_value, #meigee_unique_design_skin1_header_search_button_transparent_bg_value, #meigee_unique_design_skin1_header_search_border_transparent_value, #meigee_unique_design_skin1_header_search_active_transparent_bg_value, #meigee_unique_design_skin1_header_search_active_transparent_button_bg_value, #meigee_unique_design_ski1n_header_search_active_border_transparent_value, #meigee_unique_design_skin1_header_search_border_h_transparent_value, #meigee_unique_design_skin1_header_search_button_h_transparent_value, #meigee_unique_design_skin1_header_search_transparent_bg_h_value, #meigee_unique_design_skin1_header_cart_transparent_bg_value, #meigee_unique_design_skin1_header_login_transparent_bg_value, #meigee_unique_design_skin1_header_wishlist_transparent_bg_value, #meigee_unique_design_skin1_header_account_transparent_bg_value, #meigee_unique_design_skin1_header_header_top_block_transparent_bg_value');
	jsColor('#meigee_unique_design_skin1_headerslider', '#meigee_unique_design_skin1_headerslider_type1_button_border_width, #meigee_unique_design_skin1_headerslider_type2_button_border_width');
	jsColor('#meigee_unique_design_skin1_menu', '#meigee_unique_design_skin1_menu_submenu_link_border_width, #meigee_unique_design_skin1_menu_submenu_borders_width, #meigee_unique_design_skin1_menu_menu_transparent_bg_value, #meigee_unique_design_skin1_menu_button_transparent_bg_value, #meigee_unique_design_skin1_menu_button_transparent_bg_h_value, #meigee_unique_design_skin1_menu_link_transparent_bg_value, #meigee_unique_design_skin1_menu_link_transparent_bg_h_value, #meigee_unique_design_skin1_menu_sticky_link_transparent_bg_value, #meigee_unique_design_skin1_menu_sticky_link_transparent_bg_h_value');
	jsColor('#meigee_unique_design_skin1_content', '#meigee_unique_design_skin1_content_page_title_border_width');
	jsColor('#meigee_unique_design_skin1_buttons', '#meigee_unique_design_skin1_buttons_buttons_border_width, #meigee_unique_design_skin1_buttons_buttons2_border_width, #meigee_unique_design_skin1_buttons_wsubscribe_button_border_width');
	jsColor('#meigee_unique_design_skin1_products', '#meigee_unique_design_skin1_products_product_border_width');
	jsColor('#meigee_unique_design_skin1_social_links', '#meigee_unique_design_skin1_social_links_social_links_border_width, #meigee_unique_design_skin1_social_links_social_links_divider_width');
	jsColor('#meigee_unique_design_skin1_footer', '#meigee_unique_design_skin1_footer_top_block_borders_width, #meigee_unique_design_skin1_footer_top_block_button_border_width, #meigee_unique_design_skin1_footer_top_block_list_link_border_width, #meigee_unique_design_skin1_footer_bottom_block_list_link_border_width, #meigee_unique_design_skin1_footer_bottom_block_borders_width, #meigee_unique_design_skin1_footer_top_block_title_border_width, #meigee_unique_design_skin1_footer_bottom_block_title_border_width, #meigee_unique_design_skin1_footer_default_links_wrapper_border_width,	#meigee_unique_design_skin1_footer_store_switcher_border_width');
	
	jsColor('#meigee_unique_design_skin2_base');
	jsColor('#meigee_unique_design_skin2_catlabels');
	jsColor('#meigee_unique_design_skin2_header', '#meigee_unique_design_skin2_header_header_borders_width, #meigee_unique_design_skin2_header_search_border_width, #meigee_unique_design_skin2_header_dropdown_border_width, #meigee_unique_design_skin2_header_account_submenu_link_divider_width, #meigee_unique_design_skin2_header_switchers_border_width, #meigee_unique_design_skin2_header_cart_border_width, #meigee_unique_design_skin2_header_login_border_width, #meigee_unique_design_skin2_header_account_border_width, #meigee_unique_design_skin2_header_search_transparent_bg_value, #meigee_unique_design_skin2_header_search_button_transparent_bg_value, #meigee_unique_design_skin2_header_search_border_transparent_value, #meigee_unique_design_skin2_header_search_active_transparent_bg_value, #meigee_unique_design_skin2_header_search_active_transparent_button_bg_value, #meigee_unique_design_ski1n_header_search_active_border_transparent_value, #meigee_unique_design_skin2_header_search_border_h_transparent_value, #meigee_unique_design_skin2_header_search_button_h_transparent_value, #meigee_unique_design_skin2_header_search_transparent_bg_h_value, #meigee_unique_design_skin2_header_cart_transparent_bg_value, #meigee_unique_design_skin2_header_login_transparent_bg_value, #meigee_unique_design_skin2_header_wishlist_transparent_bg_value, #meigee_unique_design_skin2_header_account_transparent_bg_value, #meigee_unique_design_skin2_header_header_top_block_transparent_bg_value');
	jsColor('#meigee_unique_design_skin2_headerslider', '#meigee_unique_design_skin2_headerslider_type1_button_border_width, #meigee_unique_design_skin2_headerslider_type2_button_border_width');
	jsColor('#meigee_unique_design_skin2_menu', '#meigee_unique_design_skin2_menu_submenu_link_border_width, #meigee_unique_design_skin2_menu_submenu_borders_width, #meigee_unique_design_skin2_menu_menu_transparent_bg_value, #meigee_unique_design_skin2_menu_button_transparent_bg_value, #meigee_unique_design_skin2_menu_button_transparent_bg_h_value, #meigee_unique_design_skin2_menu_link_transparent_bg_value, #meigee_unique_design_skin2_menu_link_transparent_bg_h_value, #meigee_unique_design_skin2_menu_sticky_link_transparent_bg_value, #meigee_unique_design_skin2_menu_sticky_link_transparent_bg_h_value');
	jsColor('#meigee_unique_design_skin2_content', '#meigee_unique_design_skin2_content_page_title_border_width');
	jsColor('#meigee_unique_design_skin2_buttons', '#meigee_unique_design_skin2_buttons_buttons_border_width, #meigee_unique_design_skin2_buttons_buttons2_border_width, #meigee_unique_design_skin2_buttons_wsubscribe_button_border_width');
	jsColor('#meigee_unique_design_skin2_products', '#meigee_unique_design_skin2_products_product_border_width');
	jsColor('#meigee_unique_design_skin2_social_links', '#meigee_unique_design_skin2_social_links_social_links_border_width, #meigee_unique_design_skin2_social_links_social_links_divider_width');
	jsColor('#meigee_unique_design_skin2_footer', '#meigee_unique_design_skin2_footer_top_block_borders_width, #meigee_unique_design_skin2_footer_top_block_button_border_width, #meigee_unique_design_skin2_footer_top_block_list_link_border_width, #meigee_unique_design_skin2_footer_bottom_block_list_link_border_width, #meigee_unique_design_skin2_footer_bottom_block_borders_width, #meigee_unique_design_skin2_footer_top_block_title_border_width, #meigee_unique_design_skin2_footer_bottom_block_title_border_width, #meigee_unique_design_skin2_footer_default_links_wrapper_border_width, #meigee_unique_design_skin2_footer_store_switcher_border_width');
	
	jsColor('#meigee_unique_design_skin3_base');
	jsColor('#meigee_unique_design_skin3_catlabels');
	jsColor('#meigee_unique_design_skin3_header', '#meigee_unique_design_skin3_header_header_borders_width, #meigee_unique_design_skin3_header_search_border_width, #meigee_unique_design_skin3_header_dropdown_border_width, #meigee_unique_design_skin3_header_account_submenu_link_divider_width, #meigee_unique_design_skin3_header_switchers_border_width, #meigee_unique_design_skin3_header_cart_border_width, #meigee_unique_design_skin3_header_login_border_width, #meigee_unique_design_skin3_header_account_border_width, #meigee_unique_design_skin3_header_search_transparent_bg_value, #meigee_unique_design_skin3_header_search_button_transparent_bg_value, #meigee_unique_design_skin3_header_search_border_transparent_value, #meigee_unique_design_skin3_header_search_active_transparent_bg_value, #meigee_unique_design_skin3_header_search_active_transparent_button_bg_value, #meigee_unique_design_ski1n_header_search_active_border_transparent_value, #meigee_unique_design_skin3_header_search_border_h_transparent_value, #meigee_unique_design_skin3_header_search_button_h_transparent_value, #meigee_unique_design_skin3_header_search_transparent_bg_h_value, #meigee_unique_design_skin3_header_cart_transparent_bg_value, #meigee_unique_design_skin3_header_login_transparent_bg_value, #meigee_unique_design_skin3_header_wishlist_transparent_bg_value, #meigee_unique_design_skin3_header_account_transparent_bg_value, #meigee_unique_design_skin3_header_header_top_block_transparent_bg_value');
	jsColor('#meigee_unique_design_skin3_headerslider', '#meigee_unique_design_skin3_headerslider_type1_button_border_width, #meigee_unique_design_skin3_headerslider_type2_button_border_width');
	jsColor('#meigee_unique_design_skin3_menu', '#meigee_unique_design_skin3_menu_submenu_link_border_width, #meigee_unique_design_skin3_menu_submenu_borders_width, #meigee_unique_design_skin3_menu_menu_transparent_bg_value, #meigee_unique_design_skin3_menu_button_transparent_bg_value, #meigee_unique_design_skin3_menu_button_transparent_bg_h_value, #meigee_unique_design_skin3_menu_link_transparent_bg_value, #meigee_unique_design_skin3_menu_link_transparent_bg_h_value, #meigee_unique_design_skin3_menu_sticky_link_transparent_bg_value, #meigee_unique_design_skin3_menu_sticky_link_transparent_bg_h_value');
	jsColor('#meigee_unique_design_skin3_content', '#meigee_unique_design_skin3_content_page_title_border_width');
	jsColor('#meigee_unique_design_skin3_buttons', '#meigee_unique_design_skin3_buttons_buttons_border_width, #meigee_unique_design_skin3_buttons_buttons2_border_width, #meigee_unique_design_skin3_buttons_wsubscribe_button_border_width');
	jsColor('#meigee_unique_design_skin3_products', '#meigee_unique_design_skin3_products_product_border_width');
	jsColor('#meigee_unique_design_skin3_social_links', '#meigee_unique_design_skin3_social_links_social_links_border_width, #meigee_unique_design_skin3_social_links_social_links_divider_width');
	jsColor('#meigee_unique_design_skin3_footer', '#meigee_unique_design_skin3_footer_top_block_borders_width, #meigee_unique_design_skin3_footer_top_block_button_border_width, #meigee_unique_design_skin3_footer_top_block_list_link_border_width, #meigee_unique_design_skin3_footer_bottom_block_list_link_border_width, #meigee_unique_design_skin3_footer_bottom_block_borders_width, #meigee_unique_design_skin3_footer_top_block_title_border_width, #meigee_unique_design_skin3_footer_bottom_block_title_border_width, #meigee_unique_design_skin3_footer_default_links_wrapper_border_width, #meigee_unique_design_skin3_footer_store_switcher_border_width');
	
	jsColor('#meigee_unique_design_skin4_base');
	jsColor('#meigee_unique_design_skin4_catlabels');
	jsColor('#meigee_unique_design_skin4_header', '#meigee_unique_design_skin4_header_header_borders_width, #meigee_unique_design_skin4_header_search_border_width, #meigee_unique_design_skin4_header_dropdown_border_width, #meigee_unique_design_skin4_header_account_submenu_link_divider_width, #meigee_unique_design_skin4_header_switchers_border_width, #meigee_unique_design_skin4_header_cart_border_width, #meigee_unique_design_skin4_header_login_border_width, #meigee_unique_design_skin4_header_account_border_width, #meigee_unique_design_skin4_header_search_transparent_bg_value, #meigee_unique_design_skin4_header_search_button_transparent_bg_value, #meigee_unique_design_skin4_header_search_border_transparent_value, #meigee_unique_design_skin4_header_search_active_transparent_bg_value, #meigee_unique_design_skin4_header_search_active_transparent_button_bg_value, #meigee_unique_design_ski1n_header_search_active_border_transparent_value, #meigee_unique_design_skin4_header_search_border_h_transparent_value, #meigee_unique_design_skin4_header_search_button_h_transparent_value, #meigee_unique_design_skin4_header_search_transparent_bg_h_value, #meigee_unique_design_skin4_header_cart_transparent_bg_value, #meigee_unique_design_skin4_header_login_transparent_bg_value, #meigee_unique_design_skin4_header_wishlist_transparent_bg_value, #meigee_unique_design_skin4_header_account_transparent_bg_value, #meigee_unique_design_skin4_header_header_top_block_transparent_bg_value');
	jsColor('#meigee_unique_design_skin4_headerslider', '#meigee_unique_design_skin4_headerslider_type1_button_border_width, #meigee_unique_design_skin4_headerslider_type2_button_border_width');
	jsColor('#meigee_unique_design_skin4_menu', '#meigee_unique_design_skin4_menu_submenu_link_border_width, #meigee_unique_design_skin4_menu_submenu_borders_width, #meigee_unique_design_skin4_menu_menu_transparent_bg_value, #meigee_unique_design_skin4_menu_button_transparent_bg_value, #meigee_unique_design_skin4_menu_button_transparent_bg_h_value, #meigee_unique_design_skin4_menu_link_transparent_bg_value, #meigee_unique_design_skin4_menu_link_transparent_bg_h_value, #meigee_unique_design_skin4_menu_sticky_link_transparent_bg_value, #meigee_unique_design_skin4_menu_sticky_link_transparent_bg_h_value');
	jsColor('#meigee_unique_design_skin4_content', '#meigee_unique_design_skin4_content_page_title_border_width');
	jsColor('#meigee_unique_design_skin4_buttons', '#meigee_unique_design_skin4_buttons_buttons_border_width, #meigee_unique_design_skin4_buttons_buttons2_border_width, #meigee_unique_design_skin4_buttons_wsubscribe_button_border_width');
	jsColor('#meigee_unique_design_skin4_products', '#meigee_unique_design_skin4_products_product_border_width');
	jsColor('#meigee_unique_design_skin4_social_links', '#meigee_unique_design_skin4_social_links_social_links_border_width, #meigee_unique_design_skin4_social_links_social_links_divider_width');
	jsColor('#meigee_unique_design_skin4_footer', '#meigee_unique_design_skin4_footer_top_block_borders_width, #meigee_unique_design_skin4_footer_top_block_button_border_width, #meigee_unique_design_skin4_footer_top_block_list_link_border_width, #meigee_unique_design_skin4_footer_bottom_block_list_link_border_width, #meigee_unique_design_skin4_footer_bottom_block_borders_width, #meigee_unique_design_skin4_footer_top_block_title_border_width, #meigee_unique_design_skin4_footer_bottom_block_title_border_width, #meigee_unique_design_skin4_footer_default_links_wrapper_border_width, #meigee_unique_design_skin4_footer_store_switcher_border_width');
	
	/* Comments changer */
	commentChanger = {
		load: function(elementId, comments){
			if($$(elementId).length){
				element = $$(elementId)[0];
				commentChanger.changer(element, comments);
				element.observe('change', function(event){
					commentChanger.changer(element, comments);
				});
			}
		},
		changer: function(element, comments){
			elementValue = element.options[element.selectedIndex].value;
			comment = $$(comments[elementValue])[0].innerHTML;
			element.nextSiblings('p.note')[0].select('span')[0].innerHTML = comment;
		}
	}
	
	/* Comments changer. first parameter: set element Id (select), second parameter: set array of the elements Id's that contains comments */
	new commentChanger.load('#meigee_unique_general_header_and_menu_headertype', ['#meigee_unique_general_header_and_menu_headertype1_comments', '#meigee_unique_general_header_and_menu_headertype2_comments', '#meigee_unique_general_header_and_menu_headertype3_comments']);
	new commentChanger.load('#meigee_unique_headerslider_coin_slidertype', ['#meigee_unique_headerslider_coin_wideslider_comment', '#meigee_unique_headerslider_coin_boxedslider_comment', '#meigee_unique_headerslider_coin_boxedbannerslider_comment', '#meigee_unique_headerslider_coin_header2boxedslider_comment', '#meigee_unique_headerslider_coin_header2boxedsbannerlider_comment']);
	
	function jsColorWidget(isInstance){
		function widgetColorAction(id, name){
			if(isInstance){
				jsColor('#widget_instace_tabs_properties_section_content', name, true);
			}else{
				jsColor(id, name, true);
			}
		}
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[twitter_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[twitter_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[twitter_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[rss_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[rss_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[rss_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[facebook_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[facebook_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[facebook_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[googleplus_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[googleplus_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[googleplus_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[linkedin_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[linkedin_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[linkedin_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[pinterest_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[pinterest_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[pinterest_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[vimeo_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[vimeo_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[vimeo_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[youtube_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[youtube_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[youtube_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[flickr_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[flickr_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[flickr_color]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[instagram_border]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[instagram_bg]');
		widgetColorAction('#widget_options_meigeewidgetsunique_sociallinks', 'parameters[instagram_color]');
	}
	function contentChange(){
		var holder = $$("#widget_options")[0];
		var emptyHolder = holder.innerHTML;
		
		function changeChecker(){
			setTimeout(function(){
				fullHolder = holder.innerHTML;
				if(emptyHolder != fullHolder){
					jsColorWidget();
				}
				else{
					changeChecker();
				}
			}, 500);
		}
		changeChecker();
	}
	document.onclick = function(e){
		var event = e || window.event;
		var target = event.target || event.srcElement;
		if(target.id == 'select_widget_type'){
			$$("#select_widget_type")[0].observe("change", contentChange);
		}
	}
	
	if($$("#widget_instace_tabs_properties_section_content").length){
		jsColorWidget(true);
	}
});