"use strict";function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var _createClass=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),Alert=function($){var e="alert",t="4.0.0-alpha",n="bs.alert",r="."+n,a=".data-api",i=$.fn[e],l=150,o={DISMISS:'[data-dismiss="alert"]'},s={CLOSE:"close"+r,CLOSED:"closed"+r,CLICK_DATA_API:"click"+r+a},u={ALERT:"alert",FADE:"fade",IN:"in"},c=function(){function e(t){_classCallCheck(this,e),this._element=t}return _createClass(e,[{key:"close",value:function e(t){t=t||this._element;var n=this._getRootElement(t),r=this._triggerCloseEvent(n);r.isDefaultPrevented()||this._removeElement(n)}},{key:"dispose",value:function e(){$.removeData(this._element,n),this._element=null}},{key:"_getRootElement",value:function e(t){var n=Util.getSelectorFromElement(t),r=!1;return n&&(r=$(n)[0]),r||(r=$(t).closest("."+u.ALERT)[0]),r}},{key:"_triggerCloseEvent",value:function e(t){var n=$.Event(s.CLOSE);return $(t).trigger(n),n}},{key:"_removeElement",value:function e(t){return $(t).removeClass(u.IN),Util.supportsTransitionEnd()&&$(t).hasClass(u.FADE)?void $(t).one(Util.TRANSITION_END,$.proxy(this._destroyElement,this,t)).emulateTransitionEnd(l):void this._destroyElement(t)}},{key:"_destroyElement",value:function e(t){$(t).detach().trigger(s.CLOSED).remove()}}],[{key:"_jQueryInterface",value:function t(r){return this.each(function(){var t=$(this),a=t.data(n);a||(a=new e(this),t.data(n,a)),"close"===r&&a[r](this)})}},{key:"_handleDismiss",value:function e(t){return function(e){e&&e.preventDefault(),t.close(this)}}},{key:"VERSION",get:function e(){return t}}]),e}();return $(document).on(s.CLICK_DATA_API,o.DISMISS,c._handleDismiss(new c)),$.fn[e]=c._jQueryInterface,$.fn[e].Constructor=c,$.fn[e].noConflict=function(){return $.fn[e]=i,c._jQueryInterface},c}(jQuery);