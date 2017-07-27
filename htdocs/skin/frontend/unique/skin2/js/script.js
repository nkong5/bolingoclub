/* Login ajax */
function ajaxLogin(ajaxUrl, clear){
	if(clear == true){
		clearHolder();
		jQuery(".ajax-box-overlay").removeClass('loaded');
	}
	jQuery("body").append("<div id='login-holder' />");
	if(!jQuery(".ajax-box-overlay").length){
		jQuery("#login-holder").after('<div class="ajax-box-overlay"><i class="load" /></div>');
		jQuery(".ajax-box-overlay").fadeIn('medium');
	}
	function overlayResizer(){
		jQuery(".ajax-box-overlay").css('height', jQuery(window).height());
	}
	overlayResizer();
	jQuery(window).resize(function(){overlayResizer()});
	
	jQuery.ajax({
		url: ajaxUrl,
		cache: false
	}).done(function(html){
		setTimeout(function(){
			jQuery("#login-holder").html(html).animate({
				opacity: 1,
				top: '100px'
			}, 500 );
			jQuery(".ajax-box-overlay").addClass('loaded');
			clearAll();
		}, 500);
	});
	
	var clearAll = function(){
		jQuery("#login-holder .close-button").on('click', function(){
			jQuery(".ajax-box-overlay").fadeOut('medium', function(){
				jQuery(this).remove();
			});
			clearHolder();
		});
	}
	function clearHolder(){
		jQuery("#login-holder").animate({
			opacity: 0,
			top: 0
		  }, 500, function() {
			jQuery(this).remove();
		});
	}
}

/* Top Cart */
function topCartListener(e){
	var touch = e.touches[0];
	if(jQuery(touch.target).parents('#topCartContent').length == 0 && jQuery(touch.target).parents('#cartHeader').length == 0 && !jQuery(touch.target).attr('id') != '#cartHeader'){
		jQuery('.top-cart #cartHeader').removeClass('active');
		jQuery('#topCartContent').slideUp(500).removeClass('active');
		document.removeEventListener('touchstart', topCartListener, false);
	}
}
function topCart(){
	jQuery('.top-cart #cartHeader').on('click', function(event){
		event.stopPropagation();
		jQuery(this).toggleClass('active');
		jQuery('#topCartContent').slideToggle(500).toggleClass('active');
		document.addEventListener('touchstart', topCartListener, false);
		
		jQuery(document).on('click.cartEvent', function(e) {
			if (jQuery(e.target).parents('#topCartContent').length == 0) {
				jQuery('.top-cart #cartHeader').removeClass('active');
				jQuery('#topCartContent').slideUp(500).removeClass('active');
				jQuery(document).off('click.cartEvent');
			}
		});
	});
}
/* Top Cart */

/* Top Link Wishlist Quantity */
function getWishlistCount(){
	toopLink = jQuery('header#header a.top-link-wishlist');
	url = toopLink.attr('href').replace("wishlist/","meigeeactions/wishlist/count");
	jQuery.ajax({
		url : url,
		success : function(data) {
			toopLink.find('.wishlist-items').html(data);
		}
	});
}

/* Product Hover Images */
function productHoverImages() {
	if(jQuery('span.hover-image').length){
		jQuery('span.hover-image').parent().addClass('hover-exists');
	}
}

/* Labels height */
function productLabels() {
	jQuery('.label-type-1 .label-new, .label-type-3 .label-new, .label-type-1 .label-sale, .label-type-3 .label-sale').each(function(){
		labelNewWidth = jQuery(this).innerWidth();
		if(jQuery(this).parents('.label-type-1').length){
			if(jQuery(this).hasClass('percentage')){
				lineHeight = labelNewWidth - labelNewWidth*0.2;
			}else{
				lineHeight = labelNewWidth;
			}
		}else if(jQuery(this).parents('.label-type-3').length){
			if(jQuery(this).hasClass('percentage')){
				lineHeight = labelNewWidth - labelNewWidth*0.2;
			}else{
				lineHeight = labelNewWidth - labelNewWidth*0.1;
			}
		} else {
			lineHeight = 1 + 'px';
		}
		jQuery(this).css({
			'height' : labelNewWidth,
			'line-height' : lineHeight + 'px'
		});
	});
	jQuery('.products-grid.label-type-1 .availability-only, .products-list.label-type-1 .availability-only').each(function(){
		labelWidth = jQuery(this).innerWidth();
		lineHeight = labelWidth - labelWidth*0.3;
		jQuery(this).css({
			'height' : labelWidth,
			'line-height' : lineHeight + 'px'
		});
	});
}

/* Wide Menu */
function WideMenu() {
	if(jQuery('header#header').hasClass('header-2') || jQuery('header#header').hasClass('header-3')) {
		search = jQuery('header#header .header-search').position().top;
		jQuery('.header-bottom-wrapper').css({
			'top' : 'auto',
			'margin-bottom' : 0
		}).children('.container_12').css('padding-bottom', 0);
		if (jQuery(document.body).width() > 767){
			jQuery('.nav-wide li .menu-wrapper').each(function(){
				var WideMenuItemHeight = jQuery(this).parent().height();
				if(jQuery('header#header.floating').length){
					navHeight = jQuery('#nav-wide').height();
					itemHeight = jQuery('#nav-wide li.level-top').height();
					var WideMenuOffset = jQuery(this).parent().position().top;
					if(navHeight > itemHeight) {
						jQuery(this).css('top', (WideMenuItemHeight) + WideMenuOffset).parent('li.level-top').removeClass('positioned');
					} else {
						jQuery(this).css('top', 'auto').parent('li.level0').addClass('positioned');
					}
				}else{
					
					var WideMenuOffset = jQuery(this).parent().position().top;
					jQuery(this).css('top', WideMenuItemHeight + WideMenuOffset).parent('li.level-top').removeClass('positioned');
				}
			});
			if(jQuery('header#header').hasClass('floating')) {
				if(pixelRatio > 1) {
					menuHeight = jQuery('header#header .header-top').innerHeight();
					logo = jQuery('header#header .header-top-left').height();
					menu = jQuery('.header-bottom-wrapper .nav-container').height();
					if(logo > menu) {
						jQuery('.header-bottom-wrapper').css({
							'top' : -menuHeight + 'px',
							'margin-bottom' : -menuHeight + 'px'
						}).children('.container_12').css('padding-bottom', logo - menu);
					} else {
						jQuery('.header-bottom-wrapper').css({
							'top' : -menuHeight + 'px',
							'margin-bottom' : -menuHeight + 'px'
						}).children('.container_12').css('padding-bottom', 0);
					}
				} else {
					setTimeout(function(){
						menuHeight = jQuery('header#header .header-top').innerHeight();
						logo = jQuery('header#header .header-top-left').height();
						menu = jQuery('.header-bottom-wrapper .nav-container').height();
						if(logo > menu) {
							jQuery('.header-bottom-wrapper').css({
								'top' : -menuHeight + 'px',
								'margin-bottom' : -menuHeight + 'px'
							}).children('.container_12').css('padding-bottom', logo - menu);
						} else {
							jQuery('.header-bottom-wrapper').css({
								'top' : -menuHeight + 'px',
								'margin-bottom' : -menuHeight + 'px'
							}).children('.container_12').css('padding-bottom', 0);
						}
					}, 50);
				}
			} else {
				jQuery('.header-bottom-wrapper').css({
					'top' : 'auto',
					'margin-bottom' : 0
				}).children('.container_12').css('padding-bottom', 0);
			}
		} else {
			jQuery('.nav-wide li .menu-wrapper').css('top', 'auto');
			jQuery('.header-bottom-wrapper').attr('style', '');
		}
	} else {
		if(jQuery('.nav-container').length){
			jQuery('.nav-container li.level-top').each(function(){
				menuLeft = jQuery('.header-bottom-left').outerWidth();
				menuWidth = jQuery('.header-bottom-right').outerWidth();
				menuItem = jQuery(this).find('.menu-wrapper');
				floaingMenuInnerWidth = jQuery('header#header > .container_12').innerWidth();
				floaingMenuWidth = jQuery('header#header > .container_12').width();
				floatingMenuIndent = floaingMenuInnerWidth - floaingMenuWidth;
				if(!jQuery('header#header').hasClass('floating')) {
					menuItem.css({
						'left' : menuLeft + 20,
						'width' : menuWidth - 40,
						'top' : 0
					});
				} else {
					if(jQuery('body').hasClass('boxed-layout')) {
						menuItem.css({
							'left' : 0,
							'width' : floaingMenuInnerWidth - floatingMenuIndent*2,
							'top' : jQuery(this).innerHeight() + jQuery(this).position().top + 'px'
						});
					} else {
						menuItem.css({
							'left' : 0,
							'width' : floaingMenuInnerWidth - 20,
							'top' : jQuery(this).innerHeight() + jQuery(this).position().top + 'px'
						});
					}
				}
				if(menuItem.find('.grid_6.alpha').length || menuItem.find('.grid_9.alpha').length) {
					jQuery('.menu-wrapper .grid_6.right-content').removeClass('grid_6').addClass('grid_3');
					if(!jQuery('header#header').hasClass('floating')) {
						menuItem.find('.grid_9.alpha').removeClass('grid_9').addClass('grid_6');
					} else {
						menuItem.find('.grid_6.alpha').removeClass('grid_6').addClass('grid_9');
					}
				} else if (menuItem.find('.grid_4.alpha').length) {
					jQuery('.menu-wrapper .grid_8.right-content').removeClass('grid_8').addClass('grid_5');
					if(jQuery('header#header').hasClass('floating')) {
						jQuery('.menu-wrapper .grid_5.right-content').removeClass('grid_5').addClass('grid_8');
					}
				} else if (menuItem.find('.grid_8.alpha').length) {
					jQuery('.menu-wrapper .grid_8.right-content').removeClass('grid_8').addClass('grid_5');
					if(!jQuery('header#header').hasClass('floating')) {
						menuItem.find('.grid_8.alpha').removeClass('grid_9').addClass('grid_6');
					} else {
						menuItem.find('.grid_6.alpha').removeClass('grid_6').addClass('grid_9');
					}
				}
			});
		}
	}
}
/* Wide Menu */

/* Mobile Menu Blocks */
function mobileMenuBlocks() {
	mobileMenu = jQuery('header#header .nav-container > ul');
	mobileMenuButton = jQuery('header#header .menu-button');
	mobileMenuTopBlock = jQuery('header#header .header-top-right .second-line .header-search');
	if(jQuery(document.body).width() < 767 && !jQuery(document.body).hasClass('ajax-index-options')){
		if(!jQuery('header#header').hasClass('floating')){
		if(!jQuery('.header-top-right .header-search').hasClass('active')) {
			mobileMenu.css('padding-top', mobileMenuTopBlock.outerHeight(true));
			mobileMenuTopBlock.css({
				'top' : mobileMenuButton.innerHeight() + mobileMenuButton.offset().top + 'px',
				'width' : mobileMenuButton.outerWidth(true)
			});
		}
		}
	} else {
		mobileMenu.attr('style', '');
		mobileMenuTopBlock.removeClass('active').attr('style', '');
	}
}
/* Mobile Menu */

/* Mobile Sidebar on Product Page */
	function mobileSidebar() {
		sidebar = jQuery('.product-view .sidebar-left');
		content = jQuery('.product-view .product-essential');
		if(jQuery('.product-view .sidebar-left').length){
			setTimeout(function(){
				if(jQuery(document.body).width() < 767) {
					content.parent().css('padding-bottom', sidebar.outerHeight(true));
					sidebar.css({
						'top' : content.outerHeight(),
						'width' : content.width(),
						'opacity' : 1
					});
				} else {
					content.parent().attr('style', '');
					sidebar.attr('style', '');
				}
			}, 50);
		} 
	}

/* Wishlist Block Slider */
function wishlist_slider(){
  jQuery('#wishlist-slider .es-carousel').iosSlider({
	responsiveSlideWidth: true,
	snapToChildren: true,
	desktopClickDrag: true,
	infiniteSlider: false,
	navNextSelector: '#wishlist-slider .next',
	navPrevSelector: '#wishlist-slider .prev'
  });
}
 
function wishlist_set_height(){
	setTimeout(function(){
		var wishlist_height = 0;
		jQuery('#wishlist-slider .es-carousel li').each(function(){
			if(jQuery(this).height() > wishlist_height){
				wishlist_height = jQuery(this).height();
			}
		})
		jQuery('#wishlist-slider .es-carousel').css({
			'min-height' : wishlist_height+2,
			'height' : 'auto'
		})
	}, 200);
}
if(jQuery('#wishlist-slider').length){
  whs_first_set = true;
  wishlist_slider();
}
/* Wishlist Block Slider */

function logoResize(){
	jQuery('header#header h2.logo, header#header h2.small-logo').each(function(){
		jQuery(this).attr('style', '');
		jQuery(this).find('a.logo img').attr('style', '');
		jQuery(this).parents('.header-top-left').css('position', 'static');
		if(jQuery('#header.floating').length && pixelRatio > 1 && jQuery('#header .resized-logo').length) {
			jQuery(this).find('a.logo img').attr('style', '').css({
				'width' : (jQuery(this).find('a.logo img').width()/4),
				'max-width' : '100%'
			});
		} else if (jQuery('#header.floating').length && jQuery('#header .resized-logo').length){
			jQuery(this).find('a.logo img').attr('style', '').css({
				'width' : (jQuery(this).find('a.logo img').width()/2),
				'max-width' : '100%'
			});
		} else if(!jQuery('#header.floating').length && pixelRatio > 1 && jQuery('#header .resized-logo').length) {
			jQuery(this).find('a.logo img').attr('style', '').css({
				'width' : '100%',
				'max-width' : '100%'
			});
		} else if(jQuery(this).hasClass('small-logo') && jQuery(this).parents('#header').hasClass('floating') && pixelRatio > 1) {
			jQuery(this).find('a.logo img').attr('style', '').css({
				'width' : (jQuery(this).find('a.logo img').width()/2),
				'max-width' : '100%'
			});
		} else if (jQuery(this).hasClass('small-logo') && jQuery(this).parents('#header').hasClass('floating')) {
			jQuery(this).find('a.logo img').attr('style', '');
		} else if (pixelRatio > 1 && jQuery('#header .resized-logo').length == 0) {
			jQuery(this).find('a.logo img').attr('style', '').css({
				'width' : (jQuery(this).find('a.logo img').width()/2),
				'max-width' : '100%'
			});
		} else {
			jQuery(this).find('a.logo img').attr('style', '');
		}
		if(!jQuery(this).hasClass('small-logo')){
			jQuery(this).css({
				'position': 'static',
				'opacity': '1'
			});
		}
		if(jQuery(this).hasClass('small-logo')) {
			if (jQuery(this).parents('#header').hasClass('floating')){
				jQuery(this).css({
					'position': 'relative',
					'opacity': '1',
					'top' : 0
				});
			} else {
				jQuery(this).attr('style', '');
			}
		}
		jQuery(this).parents('.header-top-left').attr('style', '');
	});
}

/* Top Menu */
	function sidebarMenu(){
		slider = jQuery('.header-slider-container .iosSlider');
		menu = jQuery('#header .nav-container');
		menuButton = jQuery('header#header .menu-button span');
		menuCHeight = menu.children().innerHeight();
		menuIndent = parseFloat(menu.css('padding-top'));
		if(jQuery(document.body).width() > 977) {
			if(!jQuery('header#header').hasClass('header-2') && !jQuery('header#header').hasClass('header-3')) {
				if(jQuery('body').hasClass('accordion-menu') && jQuery('#header .header-bottom-right .boxed-slider').length){
					setTimeout(function(){
						menuDefault = slider.height() - ((menu.innerHeight() - menu.height())*2);
						menu.removeClass('open').attr('style', '').css({
							'padding-bottom' : 0,
							'margin-bottom' : 1.5 + 'em',
							'opacity' : 1,
							'position' : 'static',
							'height' : slider.height() - ((menu.innerHeight() - menu.height())*2)
						});
						menuWheight = slider.height() - menuIndent;
						menuAnimateIndent = menuCHeight - menuWheight;
						if(menu.innerHeight() >= menuCHeight) {
							menuButton.parent().off().css('display', 'none');
							menu.parent('.header-bottom-left').css({
								'height' : menu.outerHeight(true),
								'margin-bottom' : 0,
								'padding-bottom' : 0
							});
						} else {
							menuButton.parent().css({
								'display' : 'block',
								'opacity' : 1
							});
							menu.parent('.header-bottom-left').css({
								'height' : menuWheight + menuIndent + menuButton.outerHeight(),
								'margin-bottom' : 0,
								'padding-bottom' : 0
							});
							if(!jQuery('.content-wrapper .right-content').hasClass('positioned') && !menu.parent().hasClass('button-within-slider')){
								jQuery('.content-wrapper .right-content').addClass('positioned').css('margin-top', -menuButton.outerHeight());
							}
						}
						menuButton.off().on('click', function(){
							if(menu.hasClass('open')){
								menu.removeClass('open').attr('style', '').animate({
									'height' : menuWheight - menuIndent,
									'padding-bottom' : 0,
									'margin-bottom' : menuIndent
								}, 300).parent('.header-bottom-left').animate({
									'height' : menuWheight + menuIndent + menuButton.outerHeight(),
									'margin-bottom' : 0,
									'padding-bottom' : 0
								}, 300);
							} else {
								menuWheight = slider.height() - menuIndent;
								menu.addClass('open').attr('style', '').animate({
									'height' : menuCHeight + menuIndent/2,
									'margin-bottom' : -menuAnimateIndent,
									'padding-bottom' : menuAnimateIndent + (menuIndent/2)
								}, 200).parent('.header-bottom-left').animate({
									'height' : menu.height() + menuButton.outerHeight() + (menuIndent*2),
									'margin-bottom' : -menuAnimateIndent - menuIndent
								}, 300);
							}
						});
						if(menu.parent().hasClass('button-within-slider') ) {
							menu.removeClass('open').attr('style', '').css({
								'padding-bottom' : .75 + 'em',
								'margin-bottom' : .75 + 'em',
								'opacity' : 1,
								'position' : 'static',
								'height' : slider.height() - menuButton.outerHeight() - (menuIndent*2)
							}).parent('.header-bottom-left').css({
								'height' : menu.outerHeight(true) + menuButton.outerHeight()
							});
							menuButton.off().on('click', function(){
								if(menu.hasClass('open')){
									menu.removeClass('open').attr('style', '').animate({
										'height' : slider.height() - menuButton.outerHeight() - (menuIndent*2),
										'padding-bottom' : .75 + 'em',
									'margin-bottom' : .75 + 'em'
									}, 300).parent('.header-bottom-left').animate({
										'height' : slider.height(),
										'margin-bottom' : 0,
										'padding-bottom' : 0
									}, 300);
								} else {
									menuWheight = slider.height() - menuIndent;
									menu.addClass('open').attr('style', '').animate({
										'height' : menuCHeight + menuIndent/2,
										'margin-bottom' : -menuAnimateIndent,
										'padding-bottom' : menuAnimateIndent + (menuIndent/2)
									}, 200).parent('.header-bottom-left').animate({
										'height' : menu.height() + menuButton.outerHeight() + (menuIndent*2),
										'margin-bottom' : -((menu.height() + menuButton.outerHeight() + (menuIndent*2)) - slider.height())
									}, 300);
								}
							});
						}
					}, 200);
				} else {
					if(jQuery('#header .header-bottom-right .boxed-slider').length){
						menu.attr('style', '');
						menuButton.css('display', 'none');
						setTimeout(function(){
							contentIndent = menu.outerHeight(true) - slider.height();
							if(contentIndent > 0) {
								jQuery('.content-wrapper .right-content').css('margin-top', -contentIndent);
							} else {
								jQuery('.content-wrapper .right-content').css('margin-top', 0);
							}
						}, 100);
					} else {
						menuCStandard = menuCHeight + menuButton.outerHeight() + menuIndent;
						menu.addClass('close').css({
							'height' : 0,
							'margin-bottom' : 0,
							'padding-top' : 0,
							'opacity' : 1,
							'position' : 'static'
						}).parent().css('height', menuButton.outerHeight());
						menuButton.off().on('click', function(){
							if(menu.hasClass('open')){
								menu.removeClass('open').addClass('close').animate({
									'height' : 0,
									'margin-bottom' : 0,
									'padding-bottom' : 0,
									'padding-top' : 0
								}, 300).parent('.header-bottom-left').animate({
									'height' : menuButton.outerHeight(),
									'margin-bottom' : 0,
									'padding-bottom' : 0
								}, 300);
							} else {
								menu.addClass('open').removeClass('close').animate({
									'height' : menuCHeight,
									'margin-bottom' : -menuCHeight,
									'padding-bottom' : menuCHeight + menuIndent,
									'padding-top' : menuIndent
								}, 300).parent('.header-bottom-left').animate({
									'height' : menuCStandard,
									'margin-bottom' : -menuCStandard + menuButton.outerHeight()
								}, 300);
							}
						});
					}
				}
				if(jQuery('.breadcrumbs-wrapper').length) {
					jQuery('.breadcrumbs-wrapper').css({
						'height' : menuButton.outerHeight(),
						'line-height' : menuButton.outerHeight() + 'px'
					})
				}
			} else {
				if(jQuery('body').hasClass('accordion-menu')){
					setTimeout(function(){
						if(menuButton.parent().css('display', 'none').length == 0) {
							menu.css('height', menuButton.outerHeight());
						} else {
							menu.css('height', menu.children().children('li.level-top').innerHeight());
						};
						if(menu.innerHeight() >= menuCHeight) {
							menuButton.parent().off().css('display', 'none');
						} else {
							menuButton.parent().css({
								'display' : 'block',
								'opacity' : 1
							});
						};
					}, 100);
					menuButton.off().on('click', function(){
						if(menu.hasClass('open')){
							menu.removeClass('open').animate({'height' : menuButton.outerHeight()}, 300);
						} else {
							menu.addClass('open').animate({'height' : menuCHeight}, 300);
						}
					});
				} else {
					menu.attr('style', '').css('width', '100%');
					menuButton.css('display', 'none');
				}
			}
		} else {
			menuButton.off().parent().css({
				'display' : 'block',
				'opacity' : 1
			}).removeClass('active');
			menu.removeClass('open').attr('style', '');
			menu.parent().attr('style', '');
			jQuery('.breadcrumbs-wrapper').attr('style', '');
			jQuery('.content-wrapper .right-content').removeClass('positioned').attr('style', '');
			menuButton.on('click', function(){
				menu.attr('style', '');
				menu.parent().attr('style', '');
				if(menuButton.parent().hasClass('active')){
					menuButton.parent().removeClass('active');
				} else {
					menuButton.parent().addClass('active');
				}
			});
		}
	}

	/* Product Fancy */
	function productFancy(){
		jQuery(function(){
			jQuery('.more-views a.cloud-zoom-gallery').on('click.productFancy', function(){
				jQuery('.product-view .product-img-box a.fancybox').attr('href', jQuery(this).attr('href'));
			});
		});
	}

	function ajaxButtons(){
		if(jQuery('#crosssell-products-list').length == 0){
			[].slice.call(document.querySelectorAll('button.button.btn-cart')).forEach(function(bttn) {
				if(bttn.parentNode.parentNode.parentNode.getAttribute('id') != ('reorder-validate-detail')){
					if(bttn.className.indexOf('progress-buttons') == -1) {
						bttn.className += ' progress-buttons';
						new ProgressButton( bttn, {
							callback : function( instance ) {
								if(!jQuery('.validation-failed').length){
									var progress = 0,
									interval = setInterval( function() {
										progress = Math.min( progress + Math.random() * 0.1, 1 );
										instance._setProgress( progress );
										if( progress === 1 ) {
											instance._stop(1);
											clearInterval( interval );
										}
									}, 100 );
								} 
							}
						});
					}
				}
			});
		}
	}

jQuery(window).load(function() {
	
	/* Fix for IE */
    	if(navigator.userAgent.indexOf('IE')!=-1 && jQuery.support.noCloneEvent){
			jQuery.support.noCloneEvent = true;
		}
	/* End fix for IE */

	/* More Views Slider */
	if(jQuery('#more-views-slider').length){
		jQuery('#more-views-slider').iosSlider({
		   responsiveSlideWidth: true,
		   snapToChildren: true,
		   desktopClickDrag: true,
		   infiniteSlider: false,
		   navSlideSelector: '.sliderNavi .naviItem',
		   navNextSelector: '.more-views .next',
		   navPrevSelector: '.more-views .prev'
		 });
	 }
	 function more_view_set_height(){
		if(jQuery('#more-views-slider').length){
			var more_view_height = 0;
			jQuery('#more-views-slider li a').each(function(){
			 if(jQuery(this).height() > more_view_height){
			  more_view_height = jQuery(this).height();
			 }
			})
			jQuery('#more-views-slider').css('min-height', more_view_height+2);
		}
	 }
	 /* More Views Slider */

	 /* Related Block Slider */
	  if(jQuery('#block-related-slider').length) {
	  jQuery('#block-related-slider').iosSlider({
		   responsiveSlideWidth: true,
		   snapToChildren: true,
		   desktopClickDrag: true,
		   infiniteSlider: false,
		   navSlideSelector: '.sliderNavi .naviItem',
		   navNextSelector: '.block-related .next',
		   navPrevSelector: '.block-related .prev'
		 });
	 } 
	 
	 function related_set_height(){
		var related_height = 0;
		jQuery('#block-related-slider li.item').each(function(){
		 if(jQuery(this).height() > related_height){
		  related_height = jQuery(this).height();
		 }
		})
		jQuery('#block-related-slider').css('min-height', related_height+2);
	}
	 /* Related Block Slider */
	 
   /* Layered Navigation Accorion */
  if (jQuery('#layered_navigation_accordion').length) {
    jQuery('.filter-label').each(function(){
        jQuery(this).toggle(function(){
            jQuery(this).addClass('closed').next().slideToggle(200);
        },function(){
            jQuery(this).removeClass('closed').next().slideToggle(200);
        })
    });
  }
  /* Layered Navigation Accorion */


  /* Product Collateral Accordion */
  if (jQuery('#collateral-accordion').length) {
	  jQuery('#collateral-accordion > div.box-collateral').not(':first').hide();  
	  jQuery('#collateral-accordion > h2').click(function() {
		jQuery(this).parent().find('h2').removeClass('active');
		jQuery(this).addClass('active');
		
	    var nextDiv = jQuery(this).next();
	    var visibleSiblings = nextDiv.siblings('div:visible');
	 
	    if (visibleSiblings.length ) {
	      visibleSiblings.slideUp(300, function() {
	        nextDiv.slideToggle(500);
	      });
	    } else {
	       nextDiv.slideToggle(300, function(){
				if(!nextDiv.is(":visible")){
					jQuery(this).prev().removeClass('active');
				}
		   });
	    }
	  });
  }
  /* Product Collateral Accordion */

  /* My Cart Accordion */
  if (jQuery('#cart-accordion').length) {
	  jQuery('#cart-accordion > div.accordion-content').hide();	  
	  jQuery('#cart-accordion > h3.accordion-title').wrapInner('<span/>').click(function(){
		var accordion_title_check_flag = false;
		if(jQuery(this).hasClass('active')){accordion_title_check_flag = true;}
		jQuery('#cart-accordion > h3.accordion-title').removeClass('active');
		if(accordion_title_check_flag == false){
			jQuery(this).toggleClass('active');
	    }
		var nextDiv = jQuery(this).next();
	    var visibleSiblings = nextDiv.siblings('div:visible');
	    if (visibleSiblings.length ) {
	      visibleSiblings.slideUp(300, function() {
	        nextDiv.slideToggle(500);
	      });
	    } else {
	       nextDiv.slideToggle(300);
	    }
	  });
  }
  /* My Cart Accordion */
  
  /* Coin Slider */
	
	jQuery('header#header .form-search input').focusin(function(){
		jQuery(this).parent().parent().addClass('focus');
	});
	jQuery('header#header .form-search input').focusout(function(){
		jQuery(this).parent().parent().removeClass('focus');
	});
	/* Top Search */
	
	/* Fancybox */
	if (jQuery.fn.fancybox) {
		jQuery('.fancybox').fancybox();
	}
	/* Fancybox */

	/* Zoom */
	if (jQuery('#zoom').length) {
		jQuery('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
  	}
	/* Zoom */
	
	/* Responsive */
	var responsiveflag = false;
	var topSelectFlag = false;
	var menu_type = jQuery('#nav').attr('class');
	
	/* Menu */
	function mobileDevicesMenu(action){
		if(action == 'reset'){
			jQuery(".nav-container .nav li a, .nav-container .nav-wide li a").off();
		}else{
			function topMenuListener(e){
				var touch = e.touches[0];
				if(jQuery(touch.target).parents('.nav, .nav-wide').length == 0){
					jQuery(".nav-container:not('.mobile') .nav li, .nav-container:not('.mobile') .nav-wide li").each(function(){
						jQuery(this).removeClass('touched over').children('ul').removeClass('shown-sub');
					});
					document.removeEventListener('touchstart', topMenuListener, false);
				}
			}
			jQuery(".nav-container:not('.mobile') .nav li a, .nav-container:not('.mobile') .nav-wide li.level-top > a").on({
				click: function (e){
					if (jQuery(this).parent().children('ul, .menu-wrapper').length ){
						if(jQuery(this).parent().hasClass('touched')){
							isActive = true;
						}else{
							isActive = false;
						}
						jQuery(this).parent().addClass('touched over');
						document.addEventListener('touchstart', topMenuListener, false);
						if(!isActive){
							return false;
						}
					}
				}
			});
		}
	}
	
	var mobileDevice = false;
	if (jQuery('body').hasClass('retina-ready')) {
		/* Mobile Devices */
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i))){
			
			/* Mobile Devices Class */
			jQuery('body').addClass('mobile-device');
			
			/* Clear Touch Function */
			function clearTouch(handlerObject){
				jQuery('body').on('click', function(){
					handlerObject.removeClass('touched closed');
					if(handlerObject.parent().attr('id') == 'categories-accordion'){
						handlerObject.children('ul').slideToggle(200);
					}
					jQuery('body').off();
				});
				handlerObject.click(function(event){
					event.stopPropagation();
				});
				handlerObject.parent().click(function(){
					handlerObject.removeClass('touched');
				});
				handlerObject.siblings().click(function(){
					handlerObject.removeClass('touched');
				});
			}
			
			var mobileDevice = true;
		}else{
			var mobileDevice = false;
		}

		//images with custom attributes
		
		if (pixelRatio > 1) {
			function brandsWidget(){
				
					brands = jQuery('ul.brands li a img');
					brands.each(function(){
						jQuery(this).css('width', jQuery(this).width()/2);
					});
				
			}
			function footerLogo(){
				jQuery('#footer .footer-small-logo, #footer div.footer-logo').each(function(){
					if(jQuery(document.body).width() > 767) {
						logoWidth = jQuery(this).width()/2;
						jQuery(this).css('width', logoWidth).parents('li').css('width', jQuery(this).width()/2);
					} else {
						jQuery(this).css('width', 'auto');
					}
				});
			}
			if(jQuery('.header-slider-container .iosSlider .slider .item .slide-container img').length){
				jQuery('.header-slider-container .iosSlider .slider .item .slide-container img').each(function(){
					imageWidth = jQuery(this).width()/2;
					jQuery(this).css({
						'position' : 'relative',
						'width' : imageWidth
					});
				});
			}
			logoResize();
			brandsWidget();
			footerLogo();
			jQuery(window).resize(function(){
				logoResize();footerLogo();
			});
			jQuery(window).scroll(function(){logoResize(); WideMenu();});
			
			/* Top menu Bg */
			jQuery('#nav-wide.nav-wide .menu-wrapper').each(function(){
				stretchMode = false;
			
				if(jQuery(this).attr('style') != undefined && jQuery(this).attr('style').indexOf('background-size') !=-1){
					stretchMode = true;
				}
				
				jQuery(this).attr('style', jQuery(this).attr('dataX2'));
				if(stretchMode == false){
					imgUrl = jQuery(this).css('background-image').replace(/url\(|\)|\"/ig, '');
					if(imgUrl.indexOf('none')==-1){
						img = new Image;
						img.src = imgUrl;
						thisElement = jQuery(this);
						img.onload = function(){
							thisElement.css('background-size', img.width/2+'px');
						}
					}
				}
			});
		}
		
    }
	
	/* Products grid titles */
	function productTitlesHeight(){
		function titleSet(group){
			maxTitleHeight = 0;
			group.each(function(){
				if(jQuery(this).find('.product-name').length){
					title = jQuery(this).find('.product-name').height();
					if(title > maxTitleHeight){
						maxTitleHeight = title;
					}
				}
			});
			group.find('.product-name').css('min-height', maxTitleHeight);
		}
		jQuery('.products-grid:not(.carousel-ul, #upsell-product-table)').each(function(){
			gridWidth = jQuery(this).width();
			itemWidth = jQuery(this).find('li.item:first').width();
			columnsCount = Math.round(gridWidth/itemWidth);
			items = jQuery(this).find('li.item');
			if(items.find('.product-name').length){
				items.find('.product-name').css('min-height', 0); //clear title height
				groupsCount = items.size()/columnsCount;
				ratio = 1;
				for(i=0; i<groupsCount; i++){
					currentGroupe = items.slice((i*columnsCount), (columnsCount*ratio));
					/* set title height */
					titleSet(currentGroupe);
					/* ==== */
					ratio++;
				}
			}
		});
		jQuery('.products-grid.carousel-ul').each(function(){
			titleSet(jQuery(this).find('li.item'));
		});
	}
	
	function mobile_menu(mode){
		switch(mode)
		{
		case 'animate':
		   if(!jQuery('.nav-container').hasClass('mobile')){
				if(mobileDevice == true){
					mobileDevicesMenu('reset');
				}
				jQuery(".nav-container").addClass('mobile');
				jQuery('.nav-container > ul').slideUp('fast');
				jQuery('.nav-container > ul').removeClass('active');
				jQuery('header#header .header-top-right .second-line .header-search').removeClass('active');
				jQuery('header#header .menu-button').removeClass('active');
				
				function mobileMenuListener(e){
					var touch = e.touches[0];
					if(jQuery(touch.target).parents('.nav-container.mobile').length == 0 && jQuery(touch.target).parents('.menu-button').length == 0 && !jQuery(touch.target).hasClass('menu-button') && jQuery(touch.target).parents('.second-line').length == 0 && !jQuery(touch.target).hasClass('second-line')){
						jQuery('.nav-container.mobile > ul').slideUp('medium');
						document.removeEventListener('touchstart', mobileMenuListener, false);
						jQuery('header#header .header-top-right .second-line .header-search').removeClass('active').animate({'opacity' : 0}, 400, function(){jQuery(this).css('z-index', -1)});
						jQuery('header#header .menu-button').removeClass('active');
					}
				}
				jQuery('.menu-button').on('click', function(event){
					event.stopPropagation();
					jQuery('.nav-container > ul').toggleClass('active');
					jQuery('header#header .header-top-right .second-line .header-search').toggleClass('active');
					if(jQuery('header#header .header-top-right .second-line .header-search').hasClass('active')) {
						jQuery('header#header .header-top-right .second-line .header-search').css('z-index', 991).animate({'opacity' : 1}, 800);
					} else {
						jQuery('header#header .header-top-right .second-line .header-search').animate({'opacity' : 0}, 400, function(){jQuery(this).css('z-index', -1)});
					}
					jQuery('.nav-container > ul').slideToggle('medium');
					document.addEventListener('touchstart', mobileMenuListener, false);
					jQuery(document).on('click.mobileMenuEvent', function(e){
						if (jQuery(e.target).parents('.nav-container.mobile').length == 0 && jQuery(e.target).parents('.second-line').length == 0 && !jQuery(e.target).hasClass('second-line')) {
							jQuery('.nav-container.mobile > ul').slideUp('medium');
							jQuery(document).off('click.mobileMenuEvent');
							jQuery('.nav-container > ul').removeClass('active');
							jQuery('header#header .header-top-right .second-line .header-search').removeClass('active').animate({'opacity' : 0}, 400, function(){jQuery(this).css('z-index', -1)});
							jQuery('header#header .menu-button').removeClass('active');
						}
					});
				});
			   jQuery('.nav-container > ul a').each(function(){
					if(jQuery(this).next('ul').length || jQuery(this).next('div.menu-wrapper').length){
						jQuery(this).before('<span class="menu-item-button"><i class="fa fa-plus"></i><i class="fa fa-minus"></i></span>')
						jQuery(this).next('ul').slideUp('fast');
						jQuery(this).prev('.menu-item-button').on('click', function(){
							jQuery(this).toggleClass('active');
							jQuery(this).nextAll('ul, div.menu-wrapper').slideToggle('medium');
						});
					}
				});
		   }
		break;
		default:
				jQuery(".nav-container").removeClass('mobile');
				jQuery('.menu-button').off();
				jQuery(document).off('click.mobileMenuEvent');
				jQuery('.nav-container > ul').slideDown('fast');
				jQuery('.nav-container .menu-item-button').each(function(){
					jQuery(this).nextAll('ul').attr('style', '');
					jQuery(this).remove();
				});
				jQuery('.nav-container .menu-wrapper').slideUp('fast');
				if(mobileDevice == true){
					mobileDevicesMenu();
				}
		}
	}
	
	function backgroundWrapper(){
		if(jQuery('.background-wrapper').length){
			jQuery('.background-wrapper').each(function(){
				if(jQuery('body').hasClass('boxed-layout')) {
					blockWidth = jQuery(this).parent().width()+40;
				} else {
					blockWidth = jQuery(this).parent().width()+30;
				}
				siteWidth = jQuery(window).width();
				bg = jQuery(this);
				bg.parent().css('position', 'relative');
				bgIndent = jQuery(this).parent().offset().left;
				if(jQuery('body').hasClass('boxed-layout')){
					if(jQuery(document.body).width() < 767){
						bg.css({
							'left': '-'+bgIndent+'px',
							'width': siteWidth+'px'
						});
					} else if(jQuery(document.body).width() > 767 && jQuery(document.body).width() < 977){
						bg.css({
							'width': blockWidth - 20,
							'left': -10
						});
					} else {
						bg.css({
							'width': blockWidth,
							'left': -20
						});
					}
				}else{
					if(jQuery(document.body).width() > 767 && jQuery(document.body).width() < 977){
						bg.css({
							'width': blockWidth - 10,
							'left': '-'+bgIndent+'px'
						});
					} else {
						bg.css({
							'left': '-'+bgIndent+'px',
							'width': siteWidth+'px'
						});
					}
				}
			});
		}
	}
	
	function originalFooter() {
		if(jQuery('#footer').hasClass('original')){
			jQuery('.footer-links').each(function(){
				links = jQuery(this);
				linksOffset = links.offset().left;
				if(jQuery('body').hasClass('boxed-layout')) {
					links.css({
						'margin-left' : -20 + 'px',
						'margin-right' : -20 + 'px',
						'padding-left' : 20 + 'px',
						'padding-right' : 20 + 'px'
					})
				} else {
					links.css({
						'margin-left' : -linksOffset + 'px',
						'margin-right' : -linksOffset + 'px',
						'padding-left' : linksOffset + 'px',
						'padding-right' : linksOffset + 'px'
					})
				}
			});
		}
	}
	originalFooter();
	
	jQuery('.twitter-timeline').contents().find('head').append('<style>body{color: #ccc} body .p-author .profile .p-name{color: #fff}</style>');
	jQuery('.footer-bottom .twitter-timeline').contents().find('head').append('<style>body{color: #aaa} body .p-author .profile .p-name{color: #888}</style>');
	
	/* Product tabs */
 	if(jQuery('.products-tabs-wrapper').length){
		function productTabs(){
			jQuery('ul.product-tabs').on('click', 'li:not(.current)', function() {
				jQuery(this).addClass('current').siblings().removeClass('current')
				.parents('div.products-tabs-wrapper').find('div.product-tabs-box').eq(jQuery(this).index()).fadeIn(800).addClass('visible').siblings('div.product-tabs-box').hide().removeClass('visible');
				productLabels();
			});
		}
		function productTabsBg(){
			jQuery('.products-tabs-wrapper').each(function(){
				if(jQuery(this).children('.product-tabs-box').length){
					jQuery(this).children('.product-tabs-box').each(function(){
						maxHeight = 0;
						tabContent = jQuery(this).outerHeight(true);
						if(tabContent > maxHeight) {
							maxHeight = tabContent;
						}
					});
					blockIndents = parseFloat(jQuery(this).css('padding-top')) + parseFloat(jQuery(this).css('padding-bottom'));
					listHeight = jQuery(this).find('.product-tabs').outerHeight(true);
					blockHeight = maxHeight + listHeight + blockIndents;
					jQuery(this).children('.product-tabs-box:not(".visible")').css({
						'position' : 'static',
						'opacity' : 1,
						'display' : 'none'
					});
					if(jQuery('body').hasClass('boxed-layout')) {
						blockWidth = jQuery(this).width()+40;
					} else {
						blockWidth = jQuery(this).width()+30;
					}
					siteWidth = jQuery(window).width();
					bg = jQuery(this).find('.product-tabs-bg');
					bg.attr('style', '');
					bgIndent = bg.offset().left;
					if(jQuery('body').hasClass('boxed-layout')){
						if(jQuery(document.body).width() < 479){
							bg.css({
								'left': '-'+25+'px',
								'width': siteWidth+'px',
								'height': blockHeight
							});
						} else if(jQuery(document.body).width() > 479 && jQuery(document.body).width() < 767){
							bg.css({
								'left': '-'+bgIndent+'px',
								'width': siteWidth+'px',
								'height': blockHeight
							});
						} else if(jQuery(document.body).width() > 767 && jQuery(document.body).width() < 978){
							bg.css({
								'width': blockWidth - 20,
								'left': '-10px',
								'height': blockHeight
							}).parent().css('height', blockHeight - blockIndents);
						} else {
							bg.css({
								'width': blockWidth,
								'left': '-20px',
								'height': blockHeight
							}).parent().css('height', blockHeight - blockIndents);
						}
					}else{
						bg.css({
							'left': '-'+bgIndent+'px',
							'width': siteWidth+'px',
							'height': blockHeight
						}).parent().css('height', blockHeight - blockIndents);;
					}
					
				}
			});
		}
		productTabs();
		productTabsBg();
		jQuery(window).resize(function(){productTabsBg()});
	}
	
	/* Header Customer Block */
	function headerCustomer() {
		if(jQuery('#header .customer-name').length && jQuery('#header .links').hasClass('custom')){
			var custName = jQuery('#header .customer-name');
			var linksTop = jQuery('#header .header-blocks').outerHeight(true);
			jQuery('#header .links').hide();
			custName.off();
			custName.click(function(){
				jQuery(this).toggleClass('open');
				jQuery('#header .links').slideToggle();
				jQuery('#header .links').css('top', linksTop);
			});
		}
	}
	
	/* Slider Banners */
	function sliderBanners() {
		if(jQuery('.slider-banners').length) {
			if(jQuery(document.body).width() > 767){
				jQuery('.slider-banners > div').each(function(){
					imageHeight = jQuery(this).find('img').height();
					jQuery(this).css('height', imageHeight);
				});
			} else {
				jQuery('.slider-banners > div').attr('style', '');
			}
		}
	}
	/* Slider Banners */
	
	function toDo(){
		if (jQuery(document.body).width() < 767 && responsiveflag == false){
			responsiveflag = true;
			jQuery('body').addClass('boxed-mobile-device');
			jQuery('.container_12.boxed-site').removeClass('container_12');
		}
		else if (jQuery(document.body).width() > 767){
			responsiveflag = false;
			jQuery('body').removeClass('boxed-mobile-device');
			jQuery('.boxed-site').addClass('container_12');
		}
	}
	function replacingClass () {
	   if (jQuery(document.body).width() < 978) {
			mobile_menu('animate');
	   }
		if (jQuery(document.body).width() > 977){
			mobile_menu('reset');
		}
	}
	
	replacingClass();
	toDo();
	more_view_set_height();
	wishlist_set_height();
	related_set_height();
	WideMenu();
	headerCustomer();
	mobileMenuBlocks();
	sliderBanners();
	productLabels();
	backgroundWrapper();
	sidebarMenu();
	productTitlesHeight();
	mobileSidebar();
	jQuery(window).resize(function(){toDo(); replacingClass(); more_view_set_height(); wishlist_set_height(); related_set_height(); WideMenu(); headerCustomer(); mobileMenuBlocks(); sliderBanners(); backgroundWrapper(); sidebarMenu(); productTitlesHeight(); mobileSidebar()});
	/* Responsive */
	
	/* Top Menu */
	function menuHeight2 () {
		var menu_min_height = 0;
		jQuery('#nav li.tech').css('height', 'auto');
		jQuery('#nav li.tech').each(function(){
			if(jQuery(this).height() > menu_min_height){
				menu_min_height = jQuery(this).height();
			}
		});		
		jQuery('#nav li.tech').each(function(){
			jQuery(this).css('height', menu_min_height + 'px');
		});
	}
	
	/* Top Selects */
	function option_class_add(items, is_selector){
		jQuery(items).each(function(){
			if(is_selector){
				jQuery(this).removeAttr('class'); 
				jQuery(this).addClass('sbSelector');
			}			
			stripped_string = jQuery(this).html().replace(/(<([^>]+)>)/ig,"");
			RegEx=/\s/g;
			stripped_string=stripped_string.replace(RegEx,"");
			jQuery(this).addClass(stripped_string.toLowerCase());
			if(is_selector){
				tags_add();
			}
		});
	}
	option_class_add('.sbOptions li a, .sbSelector', false);
	jQuery('.sbOptions li a, .sbSelector').on('click', function(){
		option_class_add('.sbSelector', true);
	});	
	function tags_add(){
		jQuery('.sbSelector').each(function(){
			if(!jQuery(this).find('span.text').length){
				jQuery(this).wrapInner('<span class="text" />').append('<span />').find('span:last').wrapInner('<span />');
			}
		});
	}
	tags_add();
	/* //Top Selects */
	
	/* Categories Accorion */
	if (jQuery('#categories-accordion').length){
		jQuery('#categories-accordion li.parent ul').before('<div class="btn-cat"><i class="fa fa-plus-square-o"></i><i class="fa fa-minus-square-o"></i></div>');
		jQuery('#categories-accordion li:not(.parent) a').before('<i class="fa fa-caret-right"></i>');
		if(mobileDevice == true){
			jQuery('#categories-accordion li.parent').each(function(){
				jQuery(this).on({
					click: function (){
						if(!jQuery(this).hasClass('touched')){
							jQuery(this).addClass('touched closed').children('ul').slideToggle(200);
							clearTouch(jQuery(this));
							return false;
						}
					}
				});
			});
		}else{
			jQuery('#categories-accordion li.parent .btn-cat').each(function(){
				jQuery(this).toggle(function(){
					jQuery(this).addClass('closed').next().slideToggle(200);
					jQuery(this).prev().addClass('closed');
				},function(){
					jQuery(this).removeClass('closed').next().slideToggle(200);
					jQuery(this).prev().removeClass('closed');
				})
			});
		}
	}
	/* Categories Accorion */
	
	/* Menu Wide */
	if(jQuery('#nav-wide').length){
		/* Default Sub Menu in Wide Mode */
		elements = jQuery('#nav-wide .menu-wrapper.default-menu ul.level0 li');
		
		if(mobileDevice == false){
			jQuery('#nav-wide li.level-top').mouseenter(function(){
				jQuery(this).addClass('over');
			});
			jQuery('#nav-wide li.level-top').mouseleave(function(){
				jQuery(this).removeClass('over');
			});
			jQuery('.nav-wide#nav-wide .menu-wrapper').each(function(){
				jQuery(this).children('div.alpha.omega:first').addClass('first');
			});
			
			columnsWidth = function(columnsCount, currentGroupe){
				if(currentGroupe.size() > 1){
					currentGroupe.each(function(){
						jQuery(this).css('width', (100/currentGroupe.size())+'%');
					});
				}else{
					currentGroupe.css('width', (100/columnsCount)+'%');
				}
			}
			
			jQuery('.nav-wide#nav-wide .menu-wrapper').each(function(){
				columnsCount = jQuery(this).attr('columns');
				items = jQuery(this).find('ul.level0 > li');
				groupsCount = items.size()/columnsCount;
				ratio = 1;
				for(i=0; i<groupsCount; i++){
					currentGroupe = items.slice((i*columnsCount), (columnsCount*ratio));
					/* set columns width */
					columnsWidth(columnsCount, currentGroupe);
					/* ==== */
					ratio++;
				}
			});
			
			/* Default Sub Menu in Wide Mode */
			if(elements.length){
				elements.on('mouseenter mouseleave', function(){
					if(!jQuery('.nav-container').hasClass('mobile')){
						jQuery(this).children('ul').toggle();
					}
				});
				jQuery(window).resize(function(){
					if(!jQuery('.nav-container').hasClass('mobile')){
						elements.find('ul').hide();
					}
				});
			}
		}
		
		/* Default Sub Menu in Wide Mode */
		if(elements.length){
			elements.each(function(){
				if(jQuery(this).children('ul').length){
					jQuery(this).addClass('parent');
				}
			});
		}
	}
	
	/* Default dropdown menu in accordion mode */
	jQuery('header#header.header-1 #nav li.level0, header#header.header-2 .accordion-menu #nav li.level0, header#header.header-3 .accordion-menu #nav li.level0, #nav-wide li.level0').on('mouseenter mouseleave', function(){
		thisItem = jQuery(this);
		if(!jQuery('.nav-container').hasClass('mobile')){
			thisItem.children('ul.level0, .default-menu').css('opacity', 0);
		}
		function run(){
			if(thisItem.hasClass('over')){
				if(jQuery('header#header').hasClass('floating') || (!thisItem.parents('.header-bottom-left').length)){
					thisItem.children('ul.level0, .default-menu').css({
						'top': thisItem.position().top + thisItem.height(),
						'left': thisItem.position().left +10,
						'opacity': 1
					});
				}else{
					thisItem.children('ul.level0, .default-menu').css({
						'top': thisItem.position().top,
						'left': thisItem.parents('.header-bottom-left').outerWidth(),
						'opacity': 1
					});
				}
			}else{
				thisItem.children('ul.level0, .default-menu').css('left', '-10000px');
			}
		}
		if(mobileDevice == true){
			setTimeout(function(){
				run();
			}, 200);
		}else{
			run();
		}
	});
	
});
var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;
jQuery(document).ready(function(){
	
	var isApple = false;
	/* apple position fixed fix */
	if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))){
		isApple = true;
		
		function stickyPosition(clear){
			items = jQuery('header#header, .backstretch');
			if(clear == false){
				topIndent = jQuery(window).scrollTop();
				jQuery('.header-wrapper').css('position', 'static');
				items.css({
					'position': 'absolute',
					'top': topIndent
				});
			}else{
				jQuery('.header-wrapper').css('position', 'relative');
				items.css({
					'position': 'fixed',
					'top': '0'
				});
			}
		}
		
		jQuery('header#header .form-search input').on('focusin focusout', function(){
			jQuery(this).toggleClass('focus');
			if(jQuery('header#header').hasClass('floating')){
				if(jQuery(this).hasClass('focus')){
					setTimeout(function(){
						stickyPosition(false);
					}, 500);
				}else{
					stickyPosition(true);
				}
			}
		});
	}
	
	/* floating header */
	if(jQuery('body').hasClass('floating-header')){
		setTimeout(function(){
			headerHeight = jQuery('#header').height();
			jQuery(window).scroll(function(){
				if(!isApple){
					heightParam = headerHeight;
				}else{
					heightParam = headerHeight*2;
				}
				
				mobileMenuBlocks();
				sidebarMenu();
				if(jQuery(this).scrollTop() >= heightParam ){
					if(!jQuery('#header').hasClass('floating')){
						jQuery('body').css('padding-top', headerHeight + parseFloat(jQuery('body').css('padding-top')));
						jQuery('#header').addClass('floating');
						jQuery('#header').slideDown('fast');
						WideMenu();
						logoResize();
					}
				}
				if(jQuery(this).scrollTop() < headerHeight ){
					if(jQuery('#header').hasClass('floating')){
						jQuery('body').attr('style', '');
						jQuery('#header').removeClass('floating');
						jQuery('#header').attr('style', '');
						jQuery('#header h2.logo, #header h2.logo img').attr('style', '');
						sidebarMenu();
						WideMenu();
						logoResize();
					}
				}
			});
		}, 200);
	}
	
	if (jQuery('body').hasClass('retina-ready')) {
		if (pixelRatio > 1) {
			jQuery('img[data-srcX2]').each(function(){
				jQuery(this).attr('src',jQuery(this).attr('data-srcX2'));
			});
		}
	}
	
	/* Selects */
	jQuery(".form-language select, .form-currency select, .store-switcher  select").selectbox();
	
	
/* Messages button */
	if(jQuery('ul.messages').length){
		jQuery('ul.messages > li').each(function(){
			switch (jQuery(this).attr('class')){
				case 'success-msg':
					messageIcon = '<i class="fa fa-check" />';
				break;
				case 'error-msg':
					messageIcon = '<i class="fa fa-times" />';
				break;
				case 'note-msg':
					messageIcon = '<i class="fa fa-exclamation" />';
				break;
				case 'notice-msg':
					messageIcon = '<i class="fa fa-exclamation" />';
				break;
				default:
					messageIcon = '';
			}
			jQuery(this).prepend('<div class="messages-close-btn" />', messageIcon);
			jQuery('ul.messages .messages-close-btn').on('click', function(){
				jQuery('ul.messages').remove();
			});
		});
	}

	productHoverImages();
	if(jQuery('body').hasClass('category-number')) {
		jQuery('#nav-wide li.level-top, #nav li.level-top').each(function(index){
				index++;
				if(index < 10){nl='0';}else{nl='';};
				if(!jQuery(this).find('> a.level-top em:not(".category-label")').length){
					jQuery(this).find('> a.level-top').prepend('<em>' + nl + index + '</em>');
				}
		});
	}
	
	/* Top links */
	if(jQuery('#header .links').hasClass('custom')){
		var wishlistLink = jQuery('.top-link-wishlist').parent();
		wishlistLink.addClass('top-link-wishlist');
		jQuery('.wishlist-items').appendTo(wishlistLink.children());
		jQuery('header#header .top-cart').after(wishlistLink); 
	}
	if(jQuery('#header .form-currency .sbHolder').length) {
		jQuery('header#header .form-currency').addClass('select');
	}
	
	jQuery('.nav-container li.level-top').each(function(){
		if(jQuery(this).hasClass('parent')){
			jQuery(this).find('a.level-top span').after('<i class="fa fa-caret-down" />');
		}
	});

	ajaxButtons();
	
	jQuery('.product-essential .meigee-tabs a').on('click', function(){
		mobileSidebar();
	});
	
});