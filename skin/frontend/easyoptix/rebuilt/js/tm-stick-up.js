!function(o){o.fn.tmStickUp=function(t){function e(){a=parseInt(n.offset().top),d=parseInt(n.css("margin-top")),u=parseInt(n.outerHeight(!0)),o('<div class="pseudoStickyBlock"></div>').insertAfter(n),s=o(".pseudoStickyBlock"),s.css({position:"relative",display:"block"}),c()}function c(){p.on("scroll",function(){r=o(this).scrollTop(),k=r>h?"down":"up",h=r,correctionValue=i.correctionSelector.outerHeight(!0),f=parseInt(l.scrollTop()),a-correctionValue<f?(n.addClass("isStuck"),n.css({position:"fixed",top:correctionValue}),s.css({height:u})):(n.removeClass("isStuck"),n.css({position:"relative",top:0}),s.css({height:0}))}).trigger("scroll")}var i={correctionSelector:o(".correctionSelector")};o.extend(i,t);var s,r,n=o(this),l=o(window),p=o(document),a=0,u=0,d=0,f=0,h=0,k="";e()}}(jQuery);