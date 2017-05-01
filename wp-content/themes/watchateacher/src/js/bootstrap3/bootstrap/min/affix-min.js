+function($){"use strict";function t(t){return this.each(function(){var e=$(this),o=e.data("bs.affix"),f="object"==typeof t&&t;o||e.data("bs.affix",o=new i(this,f)),"string"==typeof t&&o[t]()})}var i=function(t,e){this.options=$.extend({},i.DEFAULTS,e),this.$target=$(this.options.target).on("scroll.bs.affix.data-api",$.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",$.proxy(this.checkPositionWithEventLoop,this)),this.$element=$(t),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};i.VERSION="3.3.7",i.RESET="affix affix-top affix-bottom",i.DEFAULTS={offset:0,target:window},i.prototype.getState=function(t,i,e,o){var f=this.$target.scrollTop(),n=this.$element.offset(),s=this.$target.height();if(null!=e&&"top"==this.affixed)return f<e&&"top";if("bottom"==this.affixed)return null!=e?!(f+this.unpin<=n.top)&&"bottom":!(f+s<=t-o)&&"bottom";var a=null==this.affixed,h=a?f:n.top,r=a?s:i;return null!=e&&f<=e?"top":null!=o&&h+r>=t-o&&"bottom"},i.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(i.RESET).addClass("affix");var t=this.$target.scrollTop(),e=this.$element.offset();return this.pinnedOffset=e.top-t},i.prototype.checkPositionWithEventLoop=function(){setTimeout($.proxy(this.checkPosition,this),1)},i.prototype.checkPosition=function(){if(this.$element.is(":visible")){var t=this.$element.height(),e=this.options.offset,o=e.top,f=e.bottom,n=Math.max($(document).height(),$(document.body).height());"object"!=typeof e&&(f=o=e),"function"==typeof o&&(o=e.top(this.$element)),"function"==typeof f&&(f=e.bottom(this.$element));var s=this.getState(n,t,o,f);if(this.affixed!=s){null!=this.unpin&&this.$element.css("top","");var a="affix"+(s?"-"+s:""),h=$.Event(a+".bs.affix");if(this.$element.trigger(h),h.isDefaultPrevented())return;this.affixed=s,this.unpin="bottom"==s?this.getPinnedOffset():null,this.$element.removeClass(i.RESET).addClass(a).trigger(a.replace("affix","affixed")+".bs.affix")}"bottom"==s&&this.$element.offset({top:n-t-f})}};var e=$.fn.affix;$.fn.affix=t,$.fn.affix.Constructor=i,$.fn.affix.noConflict=function(){return $.fn.affix=e,this},$(window).on("load",function(){$('[data-spy="affix"]').each(function(){var i=$(this),e=i.data();e.offset=e.offset||{},null!=e.offsetBottom&&(e.offset.bottom=e.offsetBottom),null!=e.offsetTop&&(e.offset.top=e.offsetTop),t.call(i,e)})})}(jQuery);