"use strict";function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var _createClass=function(){function e(e,t){for(var n=0;n<t.length;n++){var a=t[n];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}return function(t,n,a){return n&&e(t.prototype,n),a&&e(t,a),t}}(),Tab=function($){var e="tab",t="4.0.0-alpha",n="bs.tab",a="."+n,r=".data-api",i=$.fn[e],o=150,s={HIDE:"hide"+a,HIDDEN:"hidden"+a,SHOW:"show"+a,SHOWN:"shown"+a,CLICK_DATA_API:"click"+a+r},l={DROPDOWN_MENU:"dropdown-menu",ACTIVE:"active",FADE:"fade",IN:"in"},d={A:"a",LI:"li",DROPDOWN:".dropdown",UL:"ul:not(.dropdown-menu)",FADE_CHILD:"> .nav-item .fade, > .fade",ACTIVE:".active",ACTIVE_CHILD:"> .nav-item > .active, > .active",DATA_TOGGLE:'[data-toggle="tab"], [data-toggle="pill"]',DROPDOWN_TOGGLE:".dropdown-toggle",DROPDOWN_ACTIVE_CHILD:"> .dropdown-menu .active"},u=function(){function e(t){_classCallCheck(this,e),this._element=t}return _createClass(e,[{key:"show",value:function e(){var t=this;if(!this._element.parentNode||this._element.parentNode.nodeType!==Node.ELEMENT_NODE||!$(this._element).hasClass(l.ACTIVE)){var n=void 0,a=void 0,r=$(this._element).closest(d.UL)[0],i=Util.getSelectorFromElement(this._element);r&&(a=$.makeArray($(r).find(d.ACTIVE)),a=a[a.length-1]);var o=$.Event(s.HIDE,{relatedTarget:this._element}),u=$.Event(s.SHOW,{relatedTarget:a});if(a&&$(a).trigger(o),$(this._element).trigger(u),!u.isDefaultPrevented()&&!o.isDefaultPrevented()){i&&(n=$(i)[0]),this._activate(this._element,r);var c=function e(){var n=$.Event(s.HIDDEN,{relatedTarget:t._element}),r=$.Event(s.SHOWN,{relatedTarget:a});$(a).trigger(n),$(t._element).trigger(r)};n?this._activate(n,n.parentNode,c):c()}}}},{key:"dispose",value:function e(){$.removeClass(this._element,n),this._element=null}},{key:"_activate",value:function e(t,n,a){var r=$(n).find(d.ACTIVE_CHILD)[0],i=a&&Util.supportsTransitionEnd()&&(r&&$(r).hasClass(l.FADE)||Boolean($(n).find(d.FADE_CHILD)[0])),s=$.proxy(this._transitionComplete,this,t,r,i,a);r&&i?$(r).one(Util.TRANSITION_END,s).emulateTransitionEnd(o):s(),r&&$(r).removeClass(l.IN)}},{key:"_transitionComplete",value:function e(t,n,a,r){if(n){$(n).removeClass(l.ACTIVE);var i=$(n).find(d.DROPDOWN_ACTIVE_CHILD)[0];i&&$(i).removeClass(l.ACTIVE),n.setAttribute("aria-expanded",!1)}if($(t).addClass(l.ACTIVE),t.setAttribute("aria-expanded",!0),a?(Util.reflow(t),$(t).addClass(l.IN)):$(t).removeClass(l.FADE),t.parentNode&&$(t.parentNode).hasClass(l.DROPDOWN_MENU)){var o=$(t).closest(d.DROPDOWN)[0];o&&$(o).find(d.DROPDOWN_TOGGLE).addClass(l.ACTIVE),t.setAttribute("aria-expanded",!0)}r&&r()}}],[{key:"_jQueryInterface",value:function t(a){return this.each(function(){var t=$(this),r=t.data(n);if(r||(r=r=new e(this),t.data(n,r)),"string"==typeof a){if(void 0===r[a])throw new Error('No method named "'+a+'"');r[a]()}})}},{key:"VERSION",get:function e(){return t}}]),e}();return $(document).on(s.CLICK_DATA_API,d.DATA_TOGGLE,function(e){e.preventDefault(),u._jQueryInterface.call($(this),"show")}),$.fn[e]=u._jQueryInterface,$.fn[e].Constructor=u,$.fn[e].noConflict=function(){return $.fn[e]=i,u._jQueryInterface},u}(jQuery);