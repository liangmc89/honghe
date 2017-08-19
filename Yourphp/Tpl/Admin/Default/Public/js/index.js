jQuery(function(){
	  var swiper = new Swiper('.swiper-container1', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        slidesPerView: 1,
        spaceBetween: 0,
        autoplay: 3500
    });
    

  //返回顶部按钮
  var jQuerytoTop = jQuery("#toTop");
  jQuerytoTop.hide();
  jQuery(window).scroll(function () {
    if (jQuery(window).scrollTop() > 100) {
      if (jQuerytoTop.is(":hidden")) {
        jQuerytoTop.stop().fadeIn(500);
      }
    }
    else {
      if (jQuerytoTop.is(":visible")) {
        jQuerytoTop.stop().fadeOut(500);
      }
    }
  });
  jQuerytoTop.click(function () {
    jQuery('body,html').stop().animate({scrollTop: 0}, 300);
    return false;
  });

  jQuery(".menu").click(function(){
      jQuery("#tel-nav").show();
      jQuery("#tel-nav ul").animate({left:'0'},"100");
    });
    jQuery(document).bind("click",function(e){ 
      var target = jQuery(e.target); 
      if(target.closest("#tel-nav ul,.menu").length == 0){ 
       jQuery("#tel-nav ul").animate({left:'-65%'},"100",function(){
         jQuery("#tel-nav").hide();
       });        
    } 
   });
   // jQuery("#tel-nav").click(function(){
   //    jQuery("#tel-nav ul").animate({left:'-65%'},"100",function(){
   //        jQuery("#tel-nav").hide();
   //    });
   //  });
  jQuery(".tel-nav>ul>li>a").click(function(){
      jQuery(this).toggleClass("active");
      jQuery(this).siblings(".tel-down-nav").toggle();
      jQuery(this).parents("li").siblings("li").find(".tel-down-nav").hide();
      jQuery(this).parents("li").siblings("li").children("a").removeClass("active");
  });


  jQuery(".xgfa li").eq(0).addClass("cur").css({ width: 340 });
  jQuery(".xgfa li").eq(0).find(".textbox").css({ "height": 188, "padding-top": 19, "padding-bottom": 40 });
  var oldli = 0;
  var t;
  jQuery(".xgfa li").hover(function () {
      var _this = jQuery(this);
      var _thisI = jQuery(this).index();
      clearTimeout(t);
      t = setTimeout(function () {
          if (_thisI == oldli) { return false } else {
              jQuery(".xgfa li").eq(oldli).removeClass("cur").stop(true).animate({ width: 158 }, 300);
              jQuery(".xgfa li").eq(oldli).find(".textbox").stop(true).animate({ "height": 56, "padding-top": 0, "padding-bottom": 0 }, 300);
              _this.addClass("cur").stop(true).animate({ width: 340 }, 300);
              _this.find(".textbox").stop(true).animate({ "height": 240, "padding-top": 19, "padding-bottom": 40 }, 300)
              oldli = _this.index();
          };
      }, 200);
  }, function () {
      clearTimeout(t);
  })

  //合作伙伴
  scroll(jQuery(".small-pic"));
  
});
function togglePic(pics,togglePic){//参数：要的切换图片集，要切换的目标图片
    pics.bind("click",function() {
      var toggleImg = jQuery(this).children('img')
      jQuery(this).addClass('on').siblings('li').removeClass('on');
      togglePic.children('img')[0].src = toggleImg[0].src;
    }); 
}

function scroll(obj){
    var jQueryobj=jQuery(obj); 
    var liArr =jQueryobj.children("ul").children("li");
    var mysw = jQueryobj.width();
    var blw=liArr.outerWidth(true);
    var mus = parseInt(mysw/blw);
    var length = liArr.length-mus;
    var listBox=jQueryobj.children("ul");
    var i=0;


    listBox.width(blw*liArr.length);

    jQueryobj.siblings(".pright").click(function(){
        i++
        //点击i加1
        if(i<length){
            listBox.animate({"left": -(blw*i)});//.css("left",-(blw*i));
          //子元素集合向左移动，距离为子元素的宽度乘以i。
        }else{
          i=length;
          listBox.animate({"left": -(blw*length)});//.css("left",-(blw*length));
          //超出可移动范围后点击不再移动。最后几个隐藏的元素显示时i数值固定位已经移走的子元素数量。
          }
        });
      jQueryobj.siblings(".pleft").click(function(){
        i--
        //点击i减1
        if(i>=0){
           listBox.animate({"left": -(blw*i)});//.css("left",-(blw*i));
         //子元素集合向右移动，距离为子元素的宽度乘以i。
        }else{
         i=0;
            listBox.animate({"left":0});//.css("left",0);
         //超出可移动范围后点击不再移动。最前几个子元素被显示时i为0。
          }
      });

}