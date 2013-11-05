/* ------------------------------------------------------------------------
    plugin-name:jQuery commonSlider
    Developped By: ZHAO Xudong -> http://html5beta.com/jquery-commonslider/
    Version: 1.1
    Copyright: Feel free to redistribute the script/modify it, as
               long as you leave my infos at the top.
------------------------------------------------------------------------ */

(function($){
	$.fn.commonSlider = function(userOptions) {
		
		//option init
		var defaultOptions = {
			width:960,
			height:240,
			autoStart:true,
			pauseOnHover:true,
			timer:3000,
			speed:2000,
			zIndex:10,
			hasPageNav:true,
			hasIndicator:true
		}
		var th = this;
		th.find('.jcs-pageNav').remove();
		th.find('.jcs-dots').remove();
		var countSlider = th.children('ul').children('li').length;
		th.data({
			opts:(userOptions == undefined)?defaultOptions:userOptions,
			current:0,
			countSlider:countSlider,
			onAction:true,
			pause:false,
			res:null
		});
		
		
		//wrap css
		th.css({
			width:th.data('opts').width,
			height:th.data('opts').height,
			position:'relative',
			'z-index':th.data('opts').zIndex 
		})
		
		//sliders css
		var ul = th.children('.jcs-slides');
		ul.css({
			'list-style':'none',
			margin:0,
			padding:0,
			width:th.data('opts').width,
			height:th.data('opts').height,
			position:'relative',
			overflow:'hidden',
			'z-index':th.data('opts').zIndex 
		}).children('li').hide().addClass('jcs-slider').css({
			width:th.data('opts').width,
			height:th.data('opts').height,
			position:'absolute',
			left:0,
			top:0
		}).eq(0).show();
		th.data('res',th.find('.jcs-slider'));
		
		
		//indicators
		if(th.data('opts').hasIndicator) {
			th.append('<div class="jcs-dots"></div>');
			var res1 = th.find('.jcs-dots');
			for(var i = 0;i < countSlider;i ++) {
				res1.append('<div class="jcs-dot jsc-dot-' +
				i + ' ' +
				(i == 0?'jcs-on':'') +
				'"></div>');
			}
			var res2 = th.find('.jcs-dot');
			var w = parseInt(res2.css('width'));
			var mr = parseInt(res2.css('margin-right'));
			var ww = (w + mr) * th.data('countSlider');
			var le = (th.data('opts').width - (w + mr) * th.data('countSlider'))/10;
			res1.css({
				'z-index':th.data('opts').zIndex + 1,
				width:ww,
				left:le
			})
			th.find('.jcs-dot').on('click',function() {
				if(th.data('onAction') || $(this).hasClass('jcs-on')) return;
				th.data('onAction',true);
				var index = parseInt($(this).prop('class').split(' ')[1].split('-')[2])%th.data('countSlider');
				var isNext = index > th.data('current')?true:false;
				action(isNext,index);
			})
		}

		//pauseOnHover
		if(th.data('opts').pauseOnHover) {
			th.hover(function() {
				th.addClass('jcs-hover').data('pause',true);
			},function() {
				th.removeClass('jcs-hover').data('pause',false);
			})
		}
		
		//now ready to work
		th.data('onAction',false);
		var t;
		
		//auto start
		if(th.data('opts').autoStart) {
			t = setTimeout(function() {
				autoSlides();
			},th.data('opts').timer);
		}
		
		//autoSlides
		function autoSlides() {
			if(!th.data('onAction') && !th.data('pause')) {
				if(th.data('pause')) {
					clearTimeout(t);
					t = setTimeout(function() {
						autoSlides();
					},th.data('opts').timer);
				}
				else {
					th.data('onAction',true);
					var index = (th.data('current') + 1 + th.data('countSlider'))%th.data('countSlider');
					action(true,index);
				}
			}
		}
		
		//action 
		function action(isNext,index) {
			var speed = th.data('opts').speed;
			var c = th.data('current');
			var step = isNext?th.data('opts').width: -th.data('opts').width;
			th.find('.jcs-slide').eq(index).css({
				left:step
			}).show();
			th.find('.jcs-slide').eq(c).animate({
				left:-step
			},speed);
			th.find('.jcs-slide').eq(index).animate({
				left:0
			},speed,function() {
				th.find('.jcs-slide').eq(index).css({
					left:0
				});
				th.data('current',index);
				th.find('.jcs-dot').removeClass('jcs-on').eq(index).addClass('jcs-on');
				th.data('onAction',false);
				if(th.data('opts').autoStart) {
					clearTimeout(t);
					t = setTimeout(function() {
						autoSlides();
					},th.data('opts').timer);
				}
			});
		}
		
		//end	
	}
})(jQuery); 

