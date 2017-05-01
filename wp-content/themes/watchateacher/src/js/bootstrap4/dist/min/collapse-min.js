"use strict";function _classCallCheck(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var _createClass=function(){function e(e,t){for(var n=0;n<t.length;n++){var i=t[n];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}return function(t,n,i){return n&&e(t.prototype,n),i&&e(t,i),t}}(),Collapse=function($){var e="collapse",t="4.0.0-alpha",n="bs.collapse",i="."+n,a=".data-api",s=$.fn[e],r=600,l={toggle:!0,parent:""},o={toggle:"boolean",parent:"string"},g={SHOW:"show"+i,SHOWN:"shown"+i,HIDE:"hide"+i,HIDDEN:"hidden"+i,CLICK_DATA_API:"click"+i+a},h={IN:"in",COLLAPSE:"collapse",COLLAPSING:"collapsing",COLLAPSED:"collapsed"},u={WIDTH:"width",HEIGHT:"height"},d={ACTIVES:".panel > .in, .panel > .collapsing",DATA_TOGGLE:'[data-toggle="collapse"]'},_=function(){function i(e,t){_classCallCheck(this,i),this._isTransitioning=!1,this._element=e,this._config=this._getConfig(t),this._triggerArray=$.makeArray($('[data-toggle="collapse"][href="#'+e.id+'"],'+('[data-toggle="collapse"][data-target="#'+e.id+'"]'))),this._parent=this._config.parent?this._getParent():null,this._config.parent||this._addAriaAndCollapsedClass(this._element,this._triggerArray),this._config.toggle&&this.toggle()}return _createClass(i,[{key:"toggle",value:function e(){$(this._element).hasClass(h.IN)?this.hide():this.show()}},{key:"show",value:function e(){var t=this;if(!this._isTransitioning&&!$(this._element).hasClass(h.IN)){var a=void 0,s=void 0;if(this._parent&&(a=$.makeArray($(d.ACTIVES)),a.length||(a=null)),!(a&&(s=$(a).data(n),s&&s._isTransitioning))){var l=$.Event(g.SHOW);if($(this._element).trigger(l),!l.isDefaultPrevented()){a&&(i._jQueryInterface.call($(a),"hide"),s||$(a).data(n,null));var o=this._getDimension();$(this._element).removeClass(h.COLLAPSE).addClass(h.COLLAPSING),this._element.style[o]=0,this._element.setAttribute("aria-expanded",!0),this._triggerArray.length&&$(this._triggerArray).removeClass(h.COLLAPSED).attr("aria-expanded",!0),this.setTransitioning(!0);var u=function e(){$(t._element).removeClass(h.COLLAPSING).addClass(h.COLLAPSE).addClass(h.IN),t._element.style[o]="",t.setTransitioning(!1),$(t._element).trigger(g.SHOWN)};if(!Util.supportsTransitionEnd())return void u();var _=o[0].toUpperCase()+o.slice(1),c="scroll"+_;$(this._element).one(Util.TRANSITION_END,u).emulateTransitionEnd(r),this._element.style[o]=this._element[c]+"px"}}}}},{key:"hide",value:function e(){var t=this;if(!this._isTransitioning&&$(this._element).hasClass(h.IN)){var n=$.Event(g.HIDE);if($(this._element).trigger(n),!n.isDefaultPrevented()){var i=this._getDimension(),a=i===u.WIDTH?"offsetWidth":"offsetHeight";this._element.style[i]=this._element[a]+"px",Util.reflow(this._element),$(this._element).addClass(h.COLLAPSING).removeClass(h.COLLAPSE).removeClass(h.IN),this._element.setAttribute("aria-expanded",!1),this._triggerArray.length&&$(this._triggerArray).addClass(h.COLLAPSED).attr("aria-expanded",!1),this.setTransitioning(!0);var s=function e(){t.setTransitioning(!1),$(t._element).removeClass(h.COLLAPSING).addClass(h.COLLAPSE).trigger(g.HIDDEN)};return this._element.style[i]=0,Util.supportsTransitionEnd()?void $(this._element).one(Util.TRANSITION_END,s).emulateTransitionEnd(r):void s()}}}},{key:"setTransitioning",value:function e(t){this._isTransitioning=t}},{key:"dispose",value:function e(){$.removeData(this._element,n),this._config=null,this._parent=null,this._element=null,this._triggerArray=null,this._isTransitioning=null}},{key:"_getConfig",value:function t(n){return n=$.extend({},l,n),n.toggle=Boolean(n.toggle),Util.typeCheckConfig(e,n,o),n}},{key:"_getDimension",value:function e(){var t=$(this._element).hasClass(u.WIDTH);return t?u.WIDTH:u.HEIGHT}},{key:"_getParent",value:function e(){var t=this,n=$(this._config.parent)[0],a='[data-toggle="collapse"][data-parent="'+this._config.parent+'"]';return $(n).find(a).each(function(e,n){t._addAriaAndCollapsedClass(i._getTargetFromElement(n),[n])}),n}},{key:"_addAriaAndCollapsedClass",value:function e(t,n){if(t){var i=$(t).hasClass(h.IN);t.setAttribute("aria-expanded",i),n.length&&$(n).toggleClass(h.COLLAPSED,!i).attr("aria-expanded",i)}}}],[{key:"_getTargetFromElement",value:function e(t){var n=Util.getSelectorFromElement(t);return n?$(n)[0]:null}},{key:"_jQueryInterface",value:function e(t){return this.each(function(){var e=$(this),a=e.data(n),s=$.extend({},l,e.data(),"object"==typeof t&&t);if(!a&&s.toggle&&/show|hide/.test(t)&&(s.toggle=!1),a||(a=new i(this,s),e.data(n,a)),"string"==typeof t){if(void 0===a[t])throw new Error('No method named "'+t+'"');a[t]()}})}},{key:"VERSION",get:function e(){return t}},{key:"Default",get:function e(){return l}}]),i}();return $(document).on(g.CLICK_DATA_API,d.DATA_TOGGLE,function(e){e.preventDefault();var t=_._getTargetFromElement(this),i=$(t).data(n),a=i?"toggle":$(this).data();_._jQueryInterface.call($(t),a)}),$.fn[e]=_._jQueryInterface,$.fn[e].Constructor=_,$.fn[e].noConflict=function(){return $.fn[e]=s,_._jQueryInterface},_}(jQuery);