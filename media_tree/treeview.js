/*
* Treeview 1.4 Copyright (c) 2007 Jörn Zaefferer
* Dual licensed under the MIT and GPL licenses:
*  http://www.opensource.org/licenses/mit-license.php
*  http://www.gnu.org/licenses/gpl.html
*/
(function(a){a.extend(a.fn,{swapClass:function(e,d){var c=this.filter("."+e);this.filter("."+d).removeClass(d).addClass(e);c.removeClass(e).addClass(d);return this},replaceClass:function(d,c){return this.filter("."+d).removeClass(d).addClass(c).end()},hoverClass:function(c){c=c||"hover";return this.hover(function(){a(this).addClass(c)},function(){a(this).removeClass(c)})},heightToggle:function(c,d){c?this.animate({height:"toggle"},c,d):this.each(function(){jQuery(this)[jQuery(this).is(":hidden")?"show":"hide"]();if(d){d.apply(this,arguments)}})},heightHide:function(c,d){if(c){this.animate({height:"hide"},c,d)}else{this.hide();if(d){this.each(d)}}},prepareBranches:function(c){if(!c.prerendered){this.filter(":last-child:not(ul)").addClass(b.last);this.filter((c.collapsed?"":"."+b.closed)+":not(."+b.open+")").find(">ul").hide()}return this.filter(":has(>ul)")},applyClasses:function(c,d){this.filter(":has(>ul):not(:has(>a))").find(">span").click(function(e){d.apply(a(this).next())}).add(a("a",this)).hoverClass();if(!c.prerendered){this.filter(":has(>ul:hidden)").addClass(b.expandable).replaceClass(b.last,b.lastExpandable);this.not(":has(>ul:hidden)").addClass(b.collapsable).replaceClass(b.last,b.lastCollapsable);this.prepend('<div class="'+b.hitarea+'"/>').find("div."+b.hitarea).each(function(){var e="";a.each(a(this).parent().attr("class").split(" "),function(){e+=this+"-hitarea "});a(this).addClass(e)})}this.find("div."+b.hitarea).click(d)},treeview:function(d){d=a.extend({cookieId:"treeview"},d);if(d.add){return this.trigger("add",[d.add])}if(d.toggle){var i=d.toggle;d.toggle=function(){return i.apply(a(this).parent()[0],arguments)}}function c(l,n){function m(o){return function(){f.apply(a("div."+b.hitarea,l).filter(function(){return o?a(this).parent("."+o).length:true}));return false}}a("a:eq(0)",n).click(m(b.collapsable));a("a:eq(1)",n).click(m(b.expandable));a("a:eq(2)",n).click(m())}function f(){a(this).parent().find(">.hitarea").swapClass(b.collapsableHitarea,b.expandableHitarea).swapClass(b.lastCollapsableHitarea,b.lastExpandableHitarea).end().swapClass(b.collapsable,b.expandable).swapClass(b.lastCollapsable,b.lastExpandable).find(">ul").heightToggle(d.animated,d.toggle);if(d.unique){a(this).parent().siblings().find(">.hitarea").replaceClass(b.collapsableHitarea,b.expandableHitarea).replaceClass(b.lastCollapsableHitarea,b.lastExpandableHitarea).end().replaceClass(b.collapsable,b.expandable).replaceClass(b.lastCollapsable,b.lastExpandable).find(">ul").heightHide(d.animated,d.toggle)}}function k(){function m(n){return n?1:0}var l=[];j.each(function(n,o){l[n]=a(o).is(":has(>ul:visible)")?1:0});a.cookie(d.cookieId,l.join(""))}function e(){var l=a.cookie(d.cookieId);if(l){var m=l.split("");j.each(function(n,o){a(o).find(">ul")[parseInt(m[n])?"show":"hide"]()})}}this.addClass("treeview");var j=this.find("li").prepareBranches(d);switch(d.persist){case"cookie":var h=d.toggle;d.toggle=function(){k();if(h){h.apply(this,arguments)}};e();break;case"location":var g=this.find("a").filter(function(){return this.href.toLowerCase()==location.href.toLowerCase()});if(g.length){g.addClass("selected").parents("ul, li").add(g.next()).show()}break}j.applyClasses(d,f);if(d.control){c(this,d.control);a(d.control).show()}return this.bind("add",function(m,l){a(l).prev().removeClass(b.last).removeClass(b.lastCollapsable).removeClass(b.lastExpandable).find(">.hitarea").removeClass(b.lastCollapsableHitarea).removeClass(b.lastExpandableHitarea);a(l).find("li").andSelf().prepareBranches(d).applyClasses(d,f)})}});var b=a.fn.treeview.classes={open:"open",closed:"closed",expandable:"expandable",expandableHitarea:"expandable-hitarea",lastExpandableHitarea:"lastExpandable-hitarea",collapsable:"collapsable",collapsableHitarea:"collapsable-hitarea",lastCollapsableHitarea:"lastCollapsable-hitarea",lastCollapsable:"lastCollapsable",lastExpandable:"lastExpandable",last:"last",hitarea:"hitarea"};a.fn.Treeview=a.fn.treeview})(jQuery);
/*
* Cookie plugin Copyright (c) 2006 Klaus Hartl (stilbuero.de) Dual licensed under the MIT and GPL licenses:
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*/
jQuery.cookie=function(b,j,m){if(typeof j!="undefined"){m=m||{};if(j===null){j="";m.expires=-1}var e="";if(m.expires&&(typeof m.expires=="number"||m.expires.toUTCString)){var f;if(typeof m.expires=="number"){f=new Date();f.setTime(f.getTime()+(m.expires*24*60*60*1000))}else{f=m.expires}e="; expires="+f.toUTCString()}var l=m.path?"; path="+m.path:"";var g=m.domain?"; domain="+m.domain:"";var a=m.secure?"; secure":"";document.cookie=[b,"=",encodeURIComponent(j),e,l,g,a].join("")}else{var d=null;if(document.cookie&&document.cookie!=""){var k=document.cookie.split(";");for(var h=0;h<k.length;h++){var c=jQuery.trim(k[h]);if(c.substring(0,b.length+1)==(b+"=")){d=decodeURIComponent(c.substring(b.length+1));break}}}return d}};


jQuery(document).ready(function($){
	// Enable the file tree jq plug-in
	$("#browser").treeview({
		collapsed: true,
		//animated: "fast",
		unique: true,
		persist: "cookie",
		expires: 7,
		cookieId: "tv-stream"
	});
	// Open the first tree
	//$("#uplds").click();
	// Get the HREF
	$("#browser a").click(function(e) {
		e.preventDefault();
		var href = absPath($(this).attr("href"));

		// send to the form
		parent.addMedia(href);
		// show the file selected
		var cfpath = (href.substring(0, href.lastIndexOf('/')));
		if(href == cfpath+'/'){
			href = cfpath;
		}
		var cfar = href.split('/');
		fln = cfar[cfar.length-1];
		if(fln.length>22){
			fln = fln.substring(0,22)+'&hellip;';
		}
		$('#fl').html(fln);
	});
	// Change classes for file type icons
	$("#browser a").each(function() {
		var dat = $(this).parent().attr("class");
		var ext = $(this).attr("rel");
		$(this).parent().attr("class",dat+' '+ext);
	});
	// The Absolute Path Fixer
	function absPath(url){
		if(url.substring(0,7)=='http://' || url.substring(0,8)=='https://'){
			return url;
		}

		var Loc = location.href;
		Loc = Loc.substring(0, Loc.lastIndexOf('/'));
		while (/^\.\./.test(url)){
			Loc = Loc.substring(0, Loc.lastIndexOf('/'));
			url= url.substring(3);
		}
		return Loc + '/' + url;
	}
});