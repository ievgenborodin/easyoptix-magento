jQuery(document).ready(function(){
	var topControlIcon = jQuery ('.top-icon-menu, .shadow, .block-cart-header, .top-search, .page, body, .header-button');

	var blockSliderMarker = jQuery('.products-grid, .products-list, .catalog-product-view');
 	if(blockSliderMarker.length===0 ) {
   		jQuery(".sidebar .block-slider-sidebar").remove();  
 	}
 	else {
  		jQuery(".sidebar .block-slider-sidebar").addClass('block-slider-start');
  	};

	/*************************************************************** Superfish Menu *********************************************************************/
	/* toggle nav */
	jQuery("#menu-icon").on("click", function(){
		jQuery(".sf-menu-phone").slideToggle();
		jQuery(this).toggleClass("active");
	});

	jQuery('.sf-menu-phone').find('li.parent').append('<strong></strong>');
	jQuery('.sf-menu-phone li.parent strong').on("click", function(){
		if (jQuery(this).attr('class') == 'opened') { jQuery(this).removeClass().parent('li.parent').find('> ul').slideToggle(); } 
			else {
				jQuery(this).addClass('opened').parent('li.parent').find('> ul').slideToggle();
			}
	});

	jQuery('.swipe-control, .block-cart-header, .top-search').on("click", function(){
		jQuery('.header .sf-menu-phone').slideUp()
		jQuery('#menu-icon').removeClass('active')
	});

	/***************************************************************** Cart Truncated *********************************************************************/
	
		jQuery('.truncated span').click(function(){
				jQuery(this).parent().find('.truncated_full_value').stop().slideToggle();
		});
		function truncateOptions() {
		    $$('.truncated').each(function(element){
		        Event.observe(element, 'mouseover', function(){
		            if (element.down('div.truncated_full_value')) {
		                element.down('div.truncated_full_value').removeClassName('show')
		            }
		        });
		    });
		}
	
		Event.observe(window, 'load', function(){
		   truncateOptions();
		});

		jQuery(".price-box.map-info a, .tier-price a").click(function() { 
	        jQuery(".map-popup").toggleClass("displayblock");
	    });

	    jQuery('.map-popup-close').on('click',function(){ 
	    	jQuery('.map-popup').removeClass('displayblock');
	    });
	/********************************************************** Product View Accordion *********************************************************************/
		jQuery.fn.slideFadeToggle = function(speed, easing, callback) {
		  return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);  
		};
		jQuery('.box-collateral').not('.box-up-sell').find('h2').append('<span class="toggle"></span>');
		jQuery('.form-add').find('.box-collateral-content').css({'display':'block'}).parents('.form-add').find('> h2 > span').addClass('opened');
		
		jQuery('.box-collateral > h2').click(function(){
			OpenedClass = jQuery(this).find('> span').attr('class');
			if (OpenedClass == 'toggle opened') { jQuery(this).find('> span').removeClass('opened'); }
			else { jQuery(this).find('> span').addClass('opened'); }
			jQuery(this).parents('.box-collateral').find(' > .box-collateral-content').slideFadeToggle()
		});
	/*************************************************************** Sidebar Accordion *********************************************************************/
		jQuery('.sidebar .block .block-title').append('<span class="toggle"></span>');
		jQuery('.sidebar .block .block-title').on("click", function(){
			if (jQuery(this).find('> span').attr('class') == 'toggle opened') { jQuery(this).find('> span').removeClass('opened').parents('.block').find('.block-content').slideToggle(); }
			else {
				jQuery(this).find('> span').addClass('opened').parents('.block').find('.block-content').slideToggle();
			}
		});

	/**************************************************************** Footer Accordion *********************************************************************/
		jQuery('.footer .footer-col > h4').append('<span class="toggle"></span>');
		jQuery('.footer h4').on("click", function(){
			if (jQuery(this).find('span').attr('class') == 'toggle opened') { jQuery(this).find('span').removeClass('opened').parents('.footer-col').find('.footer-col-content').slideToggle(); }
			else {
				jQuery(this).find('span').addClass('opened').parents('.footer-col').find('.footer-col-content').slideToggle();
			}
		});

	/******************************************************************** Header Buttons *********************************************************************/

		jQuery('.header-button, .switch-show').not('.top-login').on("click", function(e){
		    var ul=jQuery(this).find('ul')
		    if(ul.is(':hidden'))
		     ul.slideDown()
		     ,jQuery(this).addClass('active')
		    else
		     ul.slideUp()
		     ,jQuery(this).removeClass('active')
		     jQuery('.header-button, .switch-show').not(this).removeClass('active'),
		     jQuery('.header-button, .switch-show').not(this).find('ul').slideUp()
		     jQuery('.header-button ul li, .switch-show ul li').click(function(e){
		      	 e.stopPropagation(); 
		    	});
		    	return false
		});
		jQuery(document).on('click',function(){ 
		    jQuery('.header-button, .switch-show').removeClass('active').find('ul').slideUp()
		});
		jQuery('.block-cart-header, .top-search').on('click',function(){ 
		    jQuery('.header-button').removeClass('active').find('ul').slideUp()
		});
		   
/********************************************************************* swipe *****************************************************************************/
		function swipe_animate_true(){
			jQuery('.swipe-control').addClass('active');
			jQuery('.swipe').stop(true).animate({'left':'0'},400);
		}
		function swipe_animate_false(){
			jQuery('.swipe-control').removeClass('active');
			jQuery('.swipe').stop(true).animate({'left':'-100%'},400);
		}
	    jQuery('.swipe-control').click(function(){
	    	swipe_animate_true();
	    	mini_form_hide();
		    if(jQuery(this).parents('body').hasClass('ind')){
		    	jQuery(this).parents('body').removeClass('ind');
		    	swipe_animate_false()
		    	return false
		    }
		    else{
			    jQuery(this).parents('body').addClass('ind');
			    swipe_animate_true()
			    return false
		    }
	    })

	    jQuery(topControlIcon).not('.page').click(function(){
	    	swipe_animate_false();
		    if(jQuery(this).parents('body').hasClass('ind')){
		    	jQuery(this).parents('body').removeClass('ind');
		    	swipe_animate_false();
		    	return false
		    }
		});

	    jQuery('.swipe').height(jQuery(window).height());

	    jQuery(window).resize(function() {
	        jQuery('.swipe').height(jQuery(window).height());
	    });

	    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent),
	    $flag
        if(isMobile) {
				jQuery('body').removeClass('ps-static');
				jQuery('body').addClass('ps-phone');
				jQuery('.page').click(function(){
			    	jQuery('body').removeClass('ind');
			    	swipe_animate_false();
				});

		};
		var isiPhone = (navigator.userAgent.match(/iPhone/i) != null);

		jQuery('.swipe-menu .parent').on('click', function(e){
			jQuery(this).toggleClass('active');
			e.stopPropagation();
		});
			  	

/********************************************************************* top-icon-menu ***********************************************************************/
		function mini_form_hide(){
			if(!$flag){	return false}		
				jQuery('#search_mini_form').animate({height: 'hide', opacity:0}, 300);		
				jQuery('.top-search').removeClass('active');
			
		}
		function mini_form_show(){
			jQuery('#search_mini_form').animate({height: 'show', opacity:1}, 300);
			jQuery('.top-search').addClass('active');
			jQuery('.form-search .input-text').trigger('focus');
			if (isiPhone) {
				jQuery('#search_mini_form').css({'top':'55px'});
			}
		};
		
		jQuery('.top-search').on("click", function(){
			if ( jQuery('#search_mini_form').css('display') == 'none' ) {
				mini_form_show()
			} else {
				mini_form_hide()
			}
		}); 

	/********************************************************************** Header Cart *********************************************************************/

		jQuery('.header .block-cart-header ').click(function(){
			jQuery('.block-cart-header .cart-content').stop(true, true).slideToggle(300);
			jQuery(this).toggleClass('active');
			return false
		});
		jQuery(topControlIcon).not('.block-cart-header').on('click',function(){ 
			jQuery('.block-cart-header .cart-content').slideUp();
			jQuery('.block-cart-header').removeClass('active');
		});
		jQuery('.swipe-control').on('click',function(){ 
			jQuery('.block-cart-header .cart-content').slideUp();
			jQuery('.block-cart-header').removeClass('active');
		});
		jQuery('.header .block-cart-header a').on('click touchend', function(e) {
		    var el = jQuery(this);
		    var link = el.attr('href');
		    window.location = link;
		});

!function($){
 var top_search=$('.top-search')
 $(window).bind('load resize',function(){
  var bodyWidth=$('.container').width()
  if(bodyWidth>=1023){    
    if($flag===true)
  		$('#search_mini_form').show().css({opacity:1})
  	$flag = false;
  }else{    
    if($flag===false&&!top_search.hasClass('active'))
  		$('#search_mini_form').hide().css({opacity:0})
  	$flag = true;
  }
  })
}(jQuery);
});



/**********************************************************************back-top*****************************************************************************/
jQuery(function () {
 jQuery(window).scroll(function () {
  if (jQuery(this).scrollTop() > 100) {
   jQuery('#back-top').fadeIn();
  } else {
   jQuery('#back-top').fadeOut();
  }
 });

 // scroll body to 0px on click
 jQuery('#back-top a').click(function () {
  jQuery('body,html').stop(false, false).animate({
   scrollTop: 0
  }, 800);
  return false;
 });
});

/***************************************************************************************************** Magento class **************************************************************************/
jQuery(document).ready(function() {
	jQuery('.sidebar .block').last().addClass('last_block');
	jQuery('.sidebar .block').first().addClass('first');
	jQuery('.box-up-sell li').eq(2).addClass('last');
	jQuery('.form-alt li:last-child').addClass('last');
	jQuery('.product-collateral #customer-reviews dl dd, #cart-sidebar .item').last().addClass('last');
	jQuery('#checkout-progress-state li:odd').addClass('odd');
	jQuery('.product-view .product-img-box .product-image').append('<span></span>');
    jQuery('.links a.top-link-cart').parent().addClass('top-car');
    jQuery('.footer-cols-wrapper .footer-col').last().addClass('last'); 
    if(jQuery('.footer .facebook-fanbox')){ jQuery('.footer .footer-col').addClass('footer-col-ex')};
    jQuery('.input-box select, .input-box input, input.qty, .data-table textarea, .input-box textarea, .advanced-search .input-range input').not('input.radio, input.checkbox').addClass('form-control');
    if(jQuery('.new + .sale').each(function(index){
    	jQuery(this).parent('.label-product').addClass('label-indent');
    }));
  
	if (jQuery('.container').width() < 766) {
		if(jQuery(".my-account").length){
			jQuery('.my-account table td.order-id').prepend('<strong>Order #:</strong>');
			jQuery('.my-account table td.order-date').prepend('<strong>Date: </strong>');
			jQuery('.my-account table td.order-ship').prepend('<strong>Ship To: </strong>');
			jQuery('.my-account table td.order-total').prepend('<strong>Order Total: </strong>');
			jQuery('.my-account table td.order-status').prepend('<strong>Status: </strong>');
			jQuery('.my-account table td.order-sku').prepend('<strong>SKU: </strong>');
			jQuery('.my-account table td.order-price').prepend('<strong>Price: </strong>');
			jQuery('.my-account table td.order-subtotal').prepend('<strong>Subtotal: </strong>');
		};
		
		if(jQuery(".multiple-checkout").length){
			jQuery('.multiple-checkout td.order-qty, .multiple-checkout th.order-qty').prepend('<strong>Qty: </strong>');
			jQuery('.multiple-checkout td.order-shipping, .multiple-checkout th.order-shipping, ').prepend('<strong>Send To: </strong>');
			jQuery('.multiple-checkout td.order-subtotal, .multiple-checkout th.order-subtotal').prepend('<strong>Subtotal: </strong>');
			jQuery('.multiple-checkout td.order-price, .multiple-checkout th.order-price').prepend('<strong>Price: </strong>');
		};
	}

		jQuery(function() {
		//	Scrolled by user interaction
			if(jQuery(".up-sell-carousel").length){ 
				jQuery('.up-sell-carousel').carouFredSel({
					responsive: true,
					width: '100%',
					prev: '.carousel-prev',
					next: '.carousel-next',
					scroll: 1,
					auto	: {
			    		play	: 1,
				    	timeoutDuration :15000
				    },
					items: {
						visible: {
							min: 1,
							max: 3
						},
						width:260,
					},
					mousewheel: true,
					swipe: {
						onMouse: false,
						onTouch: true
					}
				});
			};

			if(jQuery(".slider-sidebar").length){ 
				jQuery('.slider-sidebar').carouFredSel({
					responsive: true,
					width: '100%',
					prev: '.slider-sidebar-prev',
					next: '.slider-sidebar-next',
					pagination:'.slider-sidebar-pager',
					scroll: 1,
					auto	: {
			    		play	: 1,
				    	timeoutDuration :15000
				    },
					items: {
						visible: {
							min: 1,
							max: 1
						},
						width:270,
					},
					mousewheel: true,
					swipe: {
						onMouse: true,
						onTouch: true
					}
				});
			};
			
		});
		if(jQuery("#gallery-swipe").length){ 
				jQuery('#gallery-swipe').bxSlider({
					pager:false,
					controls:true,
					minSlides: 1,
					maxSlides: 1,
					infiniteLoop:false,
					moveSlides:1
					});
			};

		if(jQuery("#gallery-swipe").length){  
			var myPhotoSwipe = jQuery("#gallery-swipe a").photoSwipe({ enableMouseWheel: false , enableKeyboard: false, captionAndToolbarAutoHideDelay:0 });
		};
		
		var tempImg = new Image();
		jQuery('.view-child-img').hover(function(e){
        var that = jQuery(this), parent;
        parent = jQuery('.' + that.data('parent')).eq(0);
        tempImg.src =  parent.attr('src');
        parent
            .attr('src', that.attr('src'))
            .addClass('slowFade');
        var pw, ph, cm;
       	pw = parent.outerWidth()-1;
       	ph = parent.outerHeight()-1;
       	cm = (pw - ph) / 2;
        parent.css('margin', cm + 'px ' + '0');
        
	    }, function(e){
	        var that = jQuery(this), parent;
	        parent = jQuery('.' + that.data('parent')).eq(0);
	        parent
	            .attr('src', tempImg.src)
	            .css('margin', '0')
	            .removeClass('slowFade');   
	    });


/* =================  navigation  ========================= */

		var currId, currSubMenu, currSubMenuTitle,
			theWindow = jQuery(window),
			innerNav = jQuery('#inner-nav'),
			navParent = jQuery('#nav > .parent');

		// trigger on title hover
		navParent.hover(function(e){
			var that = jQuery(this),
				thisId = that.data('id');

			if (thisId != currId) {
				// timer
				jQuery.data(theWindow, 'hoverIn', setTimeout(function() {
					setSubMenu(thisId, that);
				}, 200));
			}
		}, function(e){
			//timer
			clearTimeout(jQuery.data(theWindow, 'hoverIn'));
		});

		// trigger on leaving header
		jQuery('.nav-container-inner').mouseleave(function(e){
			//timer
			jQuery.data(theWindow, 'hoverOut', setTimeout(function() {
				//secure height
				(currSubMenu) && secureHeight(currSubMenuTitle);
				//set default values
				currId = '';
				currSubMenu = '';
				currSubMenuTitle = '';
				//udjust height
				innerNav.stop(true).animate({ height: 0 }, 400).css('border-bottom', 'none');
			}, 400));
		});

		jQuery('.nav-container-inner').mouseenter(function(e){
			//timer
			clearTimeout(jQuery.data(theWindow, 'hoverOut'));
		});

		function setSubMenu(id, that) {
			var newHeight;
			//secure height
			(currSubMenu) && secureHeight(that);
			//update values
			currId = id;
			currSubMenu = jQuery('.cat-' + id);
			currSubMenuTitle = that;
			newHeight = currSubMenu.height();
			//setlink set active
			that.find('span').hide();
			that.addClass('active').find('a').show();
			//show new submenu
			currSubMenu.stop(true).fadeIn(400).addClass('active');
			//udjust height
			innerNav.stop(true).animate({ height: newHeight }, 400).css('border-bottom', '1px solid #909090');
		};

		function secureHeight(that){
			var oldHeight = currSubMenu.height();
			
			innerNav.css('height', oldHeight + 'px');
			//unset link, remove active
			navParent.find('span').show();
			navParent.removeClass('active').find('a').hide();
			//hide old submenu
			currSubMenu.stop(true).fadeOut(400).removeClass('active');
		}

		/* ========================== */
		

	    /*jQuery('.ajax-child').on('click', function (e) {
	    	var that = jQuery(this);

	    	jQuery.ajax({
			    url: "/m1/easyoptix/product/index",
			    type: "POST",
			    data: "size=datas",//$j(this).val(),
			    success: function(data) {
			    console.log(data);//$j('#results').html(data);
			    }
			});*/
	    	/*jQuery.post("/easyoptix/product/index", {someval: 'value'}, function(data){
	    		console.log(data);
	    	});*/

/*	    	jQuery.ajax({
	            url: "/easyoptix/product/index",//that.data('href'),
	            success: function(response) {
	                result = response;
	                console.log(result);
	                //jQuery('body').html(response);
	            }
        	});
	    });*/
});


(function(doc) {

	var addEvent = 'addEventListener',
	    type = 'gesturestart',
	    qsa = 'querySelectorAll',
	    scales = [1, 1],
	    meta = qsa in doc ? doc[qsa]('meta[name=viewport]') : [];

	function fix() {
		meta.content = 'width=device-width,minimum-scale=' + scales[0] + ',maximum-scale=' + scales[1];
		doc.removeEventListener(type, fix, true);
	}

	if ((meta = meta[meta.length - 1]) && addEvent in doc) {
		fix();
		scales = [.25, 1.6];
		doc[addEvent](type, fix, true);
	}

}(document));
