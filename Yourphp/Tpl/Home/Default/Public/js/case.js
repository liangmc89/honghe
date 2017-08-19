
//图滚动
jQuery.noConflict();
jQuery(document).ready(function() {
	var $featured_content = jQuery('.featured'),
		et_disable_toptier = jQuery("meta[name=et_disable_toptier]").attr('content'),
		et_cufon = jQuery("meta[name=et_cufon]").attr('content'),
		et_featured_slider_pause = jQuery("meta[name=et_featured_slider_pause]").attr('content'),
		et_featured_slider_auto = jQuery("meta[name=et_featured_slider_auto]").attr('content'),
		et_featured_auto_speed = jQuery("meta[name=et_featured_auto_speed]").attr('content');
	jQuery("a.prevslide").css({left:jQuery(window).width()/2-266+"px"});
	jQuery("a.nextslide").css({left:jQuery(window).width()/2+266-24+"px"});			
	if ($featured_content.length) {
		var $featured_slides = $featured_content.find('.slide').show(),
			slides_pos = [],
			slides_zindex = [],
			active_slide_width = 532,
			small_slide_width = 270,
			slide_margin = 20,
			featured_animation = 'easeInOutQuad', //'easeInOutQuad','easeInOutQuint', 'easeInOutQuart'
			et_animation_running = false,
			last_slide = false,
			pause_scroll = false,
			top_slide_pos,
			left_slide_pos,
			slide_opacity;
		
		$featured_content.css( 'backgroundImage', 'none' );
		
		$featured_slides.each(function(index, domEle){
			var $this_slide = jQuery(domEle);
			
			top_slide_pos = 80;
			slide_opacity = 1;
			if ( index === 0 ) {
				top_slide_pos = 0;
				slide_opacity = 1;
				left_slide_pos = jQuery(window).width()/2-266;
			}
			if ( index === 1 ) {
				left_slide_pos = jQuery(window).width()/2+266+20;
			}
			if ( index === 2 ) {
				left_slide_pos = jQuery(window).width()/2-266-20-270;
			}
			if ( index > 2 ) {
				if ( index % 2 === 1 ) {
					left_slide_pos = slides_pos[index-2].left + small_slide_width;
					console.log(left_slide_pos);
				}
				else {
					left_slide_pos = slides_pos[index-2].left - small_slide_width;
					console.log(left_slide_pos);
				}
			}
			
			if ( index !== 0 ) {
				$this_slide.find('img').attr({
					width: '270',
					height: '151'
				});
			}
							
			slides_pos[index] = {
				width: $this_slide.width(),
				top: top_slide_pos,
				left: left_slide_pos,
				opacity: slide_opacity
			};
			
			$this_slide.css('zIndex',$featured_slides.length-index);
			slides_zindex[index] = $this_slide.css('zIndex');
			
			$this_slide.animate(slides_pos[index],100,function(){
				// fixes the slide title display bug in Opera
				jQuery(this).css( 'width', 'auto' );
			});
			jQuery(domEle).data('slide_pos',index);
		});
		
		jQuery('a.nextslide').on('click',function(event){
			event.preventDefault();
			if (!et_animation_running) rotate_slide('next');
			if ( typeof(et_auto_animation) !== 'undefined' ) clearInterval(et_auto_animation);
		});
		
		jQuery('a.prevslide').on('click',function(event){
			event.preventDefault();
			if (!et_animation_running) rotate_slide('prev');
			if ( typeof(et_auto_animation) !== 'undefined' ) clearInterval(et_auto_animation);
		});
	
		function rotate_slide(direction){
			$featured_slides.removeClass('active');
							
			$featured_slides.each(function(index, domEle){
				var $this_slide = jQuery(domEle),
					next_slide_num = $this_slide.data('slide_pos');
				
				et_animation_running = true;	
				last_slide = false;
				
				if ( direction === 'next' ){
					if ( next_slide_num === 0 ) next_slide_num = 2;
					else if ( next_slide_num === 1 ) next_slide_num = 0;
					else if ( $featured_slides.length % 2 === 0 && next_slide_num === ( $featured_slides.length - 2 ) ) {
						next_slide_num = $featured_slides.length - 1;
					}
					else {
						if ( next_slide_num !== ($featured_slides.length - 1) ) {
							if ( next_slide_num % 2 === 0 )  next_slide_num = next_slide_num + 2;
							else next_slide_num = next_slide_num - 2;
						} else {
							if ( $featured_slides.length % 2 === 0 ) {
								if ( next_slide_num % 2 === 0 )  next_slide_num = next_slide_num + 2;
								else next_slide_num = next_slide_num - 2;
							}
							else { 
								next_slide_num = $featured_slides.length - 2;
								last_slide = true;
							}
						}
					}
				} else {
					if ( next_slide_num === 0 ) next_slide_num = 1;
					else if ( $featured_slides.length % 2 === 0 && next_slide_num === ( $featured_slides.length - 1 ) ) {
						next_slide_num = $featured_slides.length - 2;
					}
					else {
						if ( $featured_slides.length % 2 === 0 ) {
							if ( next_slide_num % 2 === 0 ) next_slide_num = next_slide_num - 2;
							else next_slide_num = next_slide_num + 2;
						} else {
							if ( next_slide_num !== ($featured_slides.length - 2) ) {
								if ( next_slide_num % 2 === 0 ) next_slide_num = next_slide_num - 2;
								else next_slide_num = next_slide_num + 2;
							} else {
								next_slide_num = $featured_slides.length-1;
								last_slide = true;
							}
						}
					}
				}
							
				if ( last_slide ) {
					$this_slide.css('left',slides_pos[next_slide_num].left);
				}
									
				$this_slide.stop(true, true).animate(slides_pos[next_slide_num],600,featured_animation,function(){
					if ( index === $featured_slides.length - 1 ) et_animation_running = false;
									
					if ( !et_animation_running ) {
						// if ( et_mousex > featured_activeslide_x && et_mousex < (featured_activeslide_x + 500) && et_mousey > featured_activeslide_y && et_mousey < (featured_activeslide_y + 335) ){
						// 	if ( next_slide_num === 0 ) $featured_content.find('.slide').filter(':eq('+($featured_slides.length - 1)+')').find('.additional').stop(true, true).animate({'opacity':'show'},300);
						// 	else $featured_content.find('.active .additional').stop(true, true).animate({'opacity':'show'},300);
							
						// 	if ( et_featured_slider_pause == 1 ) pause_scroll = true;
						// }
					}
					
					// fixes the slide title display bug in Opera
					jQuery(this).css( 'width', 'auto' );
				});
				if ( next_slide_num != 0 ) {
					$this_slide.find("p").fadeOut(300);
					$this_slide.find('img').stop(true, true).animate({'width':'270px','height':'151px'},600,featured_animation,function(){
						$this_slide.find(".s_mask").fadeIn(300);
					});
				}
				else {
					$this_slide.find(".s_mask").fadeOut(300); 
					$this_slide.find('img').stop(true, true).animate({'width':'532px','height':'295px'},600,featured_animation,function(){
						$this_slide.find("p").fadeIn(300);
						$this_slide.addClass('active');
					});
				}
					
				setTimeout(function(){
					$this_slide.css({zIndex: slides_zindex[next_slide_num]});
				},300);
				
				$this_slide.data('slide_pos',next_slide_num);
			});
		}
		
		if ( et_featured_slider_auto == 1 ) {
			et_auto_animation = setInterval(function(){
				if ( !pause_scroll ) rotate_slide('next');
			}, et_featured_auto_speed);
		}
		
		if ( $featured_content.find('.slide').length == 1 ){
			$featured_content.find('.slide').css({'position':'absolute','top':'0','left':'0'}).show();
		}
	}
});