<?php
/*!

                          iiii  kkkkkkkk             iiii
                         i::::i k::::::k            i::::i
                          iiii  k::::::k             iiii
                                k::::::k
   mmmmmmm    mmmmmmm   iiiiiii  k:::::k    kkkkkkkiiiiiii
 mm:::::::m  m:::::::mm i:::::i  k:::::k   k:::::k i:::::i
m::::::::::mm::::::::::m i::::i  k:::::k  k:::::k   i::::i
m::::::::::::::::::::::m i::::i  k:::::k k:::::k    i::::i
m:::::mmm::::::mmm:::::m i::::i  k::::::k:::::k     i::::i
m::::m   m::::m   m::::m i::::i  k:::::::::::k      i::::i
m::::m   m::::m   m::::m i::::i  k:::::::::::k      i::::i
m::::m   m::::m   m::::m i::::i  k::::::k:::::k     i::::i
m::::m   m::::m   m::::mi::::::ik::::::k k:::::k   i::::::i
m::::m   m::::m   m::::mi::::::ik::::::k  k:::::k  i::::::i
m::::m   m::::m   m::::mi::::::ik::::::k   k:::::k i::::::i
mmmmmm   mmmmmm   mmmmmmiiiiiiiikkkkkkkk    kkkkkkkiiiiiiii

                                             by Xavi Esteve

	Miki is the smallest wiki system ever, just one file.
	 - One single PHP file
	 - No database needed
	 - Auto-installed automatically
	 - Full Markdown language support
	 - Super fast and lightweight

*/
session_start();
class Miki {

	/****************************************************************************************
	 * Basic config
	 */
	protected $config = [
		'app_name' => 'Miki',
		'app_url' => 'http://example.com/miki', // no trailing slash
		'app_version' => '3.1.24',
		'default_page' => 'welcome',
		'extension' => 'txt',
		'maxlength' => 100000, // file max characters, 1000 = 1KB
		'cache_time' => 86400, // 3600 = 1 hour, 86400 = 1 day, 604800 = 1 week, 18144000 = 1 month
		'auth_duration' => 31536000, // 3600 = 1 hour, 86400 = 1 day, 604800 = 1 week, 18144000 = 1 month, 31536000 = 1 year
	];


	/****************************************************************************************
	 * You are done, no more editing needed :)
	 */


	protected $page;
	protected $breadcrumb = [];
	protected $auth_key = false;


	protected $css = '/* Miki CSS file */
html {box-sizing: border-box;background: #fbfbfb;}
*, *:before, *:after {box-sizing: inherit;}
::selection {background:#fff37f}
::-moz-selection {background:#fff37f}
.hide {display:none;}
body {font-family:Georgia, serif;margin: 0 auto 20px;max-width:40em;padding: 0 1em;text-rendering:optimizeLegibility;hyphens:auto}
	.bc {color: #CCC;margin: 0;padding: 1px 20px;margin: 0 -20px;-webkit-transition: opacity 0.5s ease;height: 1.45em;}
	.bc a {color:#666;font-size:66%;font-weight:100;text-decoration:none;display:inline-block;opacity:.5;padding-left:.5em}
		.bc a:hover {opacity:1}
	.bc a::after {content:"›";opacity:.5;padding-left:.5em}
	.bc a:last-child::after,
	.bc a:first-child::after{content:""}
		.bc .start {color:#c00;margin-top: 0;font-family: sans-serif;font-weight: bold;}
		.bc .start:hover {color:#c00;}
	.box-view {font-size: 16px;line-height: 1.66em;min-height: 60vh;}
		.box-view a {color:#0085d5;text-decoration:none;}
		.box-view a:hover {text-decoration:underline}
		.title {color: #C00;font-size: 3em;line-height: 1.33em;margin: .3em 0;}
		h2,h3,h4,h5,h6 {margin: 2em 0 0.1em;padding-bottom: .1em;}
		h2 {border-bottom: 1px solid #eee;color: #333;padding-bottom:.33em;}
		h3 {color: #c00;font-family:\'Trebuchet MS\',sans-serif;font-size:1.33em}
		h4 {color:#000;font-size:1em}
		h5 {font-family:\'Trebuchet MS\',sans-serif;text-transform:uppercase;font-size: .7em;letter-spacing: .2em;}
		h5,h6 {color:#666;}
		.box-content em {background:#fffaca}

		.edit,button[type=submit] {display: block;border: none;color: #fff;cursor: pointer;padding: 1em;margin: 0;opacity: 0.05;font-size: 50%;position: fixed;top: -2em;right: 2em;text-align: center;background: #565656;font-family: inherit;-webkit-appearance: none;transform: rotate(270deg);transform-origin: right;width: 100vh;text-transform: uppercase;font-family: \'Helvetica Neue\';letter-spacing: 0.2em;}
			.edit:hover,button[type=submit]:hover {opacity:0.2}

		textarea {border:none;height:75vh;font-family:Georgia,sans-serif;font-size:inherit;line-height:1.66em;padding:.66em;width:100%;outline:none}

		pre {background: #EEE;color: #008200;overflow-x: scroll;padding: .33em .66em;line-height: 1.33em;border-radius: .5em;tab-size:2;border: 1px solid #DDD;}
		blockquote {font-style:italic;font-size:.95em;color:#666;padding:.66em 1em;margin:0 .66em;}
		button[type=submit] {}

		.allfiles {list-style:none;margin-top: 3em;padding:0;}
			.allfiles li {display: inline;}
				.allfiles a {padding: .3em;display: inline-block;line-height: 1em;margin: 0 1px;text-decoration: none;font-size: 11px;color: #666;background: #fff;}

/* Dark theme */
html.dark {background: #000;color: #b7b7b7}
	.dark h2 {color: #b7b7b7;border-bottom-color: #313131;}
	.dark a {color: #129aec}
	.dark em {background: transparent;}
	.dark .allfiles a {background:transparent}
	.dark textarea {background:#111;color:#fbfbfb}

/* Responsive */
@media screen and (max-width:520px) { /* Mobile */
	.title {font-size: 2em}
	body {padding: 0 .5em;}
}
';
	protected $js = '
// showdown.js -- A javascript port of Markdown.
// Copyright (c) 2007 John Fraser.
// Redistributable under a BSD-style open source license.
var Showdown={extensions:{}},forEach=Showdown.forEach=function(a,b){if(typeof a.forEach=="function")a.forEach(b);else{var c,d=a.length;for(c=0;c<d;c++)b(a[c],c,a)}},stdExtName=function(a){return a.replace(/[_-]||\\s/g,"").toLowerCase()};Showdown.converter=function(a){var b,c,d,e=0,f=[],g=[];if(typeof module!="undefind"&&typeof exports!="undefined"&&typeof require!="undefind"){var h=require("fs");if(h){var i=h.readdirSync((__dirname||".")+"/extensions").filter(function(a){return~a.indexOf(".js")}).map(function(a){return a.replace(/\\.js$/,"")});Showdown.forEach(i,function(a){var b=stdExtName(a);Showdown.extensions[b]=require("./extensions/"+a)})}}this.makeHtml=function(a){return b={},c={},d=[],a=a.replace(/~/g,"~T"),a=a.replace(/\\$/g,"~D"),a=a.replace(/\\r\\n/g,"\\n"),a=a.replace(/\\r/g,"\\n"),a="\\n\\n"+a+"\\n\\n",a=M(a),a=a.replace(/^[ \\t]+$/mg,""),Showdown.forEach(f,function(b){a=k(b,a)}),a=z(a),a=m(a),a=l(a),a=o(a),a=K(a),a=a.replace(/~D/g,"$$"),a=a.replace(/~T/g,"~"),Showdown.forEach(g,function(b){a=k(b,a)}),a};if(a&&a.extensions){var j=this;Showdown.forEach(a.extensions,function(a){typeof a=="string"&&(a=Showdown.extensions[stdExtName(a)]);if(typeof a!="function")throw"Extension \'"+a+"\' could not be loaded.  It was either not found or is not a valid extension.";Showdown.forEach(a(j),function(a){a.type?a.type==="language"||a.type==="lang"?f.push(a):(a.type==="output"||a.type==="html")&&g.push(a):g.push(a)})})}var k=function(a,b){if(a.regex){var c=new RegExp(a.regex,"g");return b.replace(c,a.replace)}if(a.filter)return a.filter(b)},l=function(a){return a+="~0",a=a.replace(/^[ ]{0,3}\\[(.+)\\]:[ \\t]*\\n?[ \\t]*<?(\\S+?)>?[ \\t]*\\n?[ \\t]*(?:(\\n*)["(](.+?)[")][ \\t]*)?(?:\\n+|(?=~0))/gm,function(a,d,e,f,g){return d=d.toLowerCase(),b[d]=G(e),f?f+g:(g&&(c[d]=g.replace(/"/g,"&quot;")),"")}),a=a.replace(/~0/,""),a},m=function(a){a=a.replace(/\\n/g,"\\n\\n");var b="p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math|ins|del|style|section|header|footer|nav|article|aside",c="p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math|style|section|header|footer|nav|article|aside";return a=a.replace(/^(<(p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math|ins|del)\\b[^\\r]*?\\n<\\/\\2>[ \\t]*(?=\\n+))/gm,n),a=a.replace(/^(<(p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|noscript|form|fieldset|iframe|math|style|section|header|footer|nav|article|aside)\\b[^\\r]*?<\\/\\2>[ \\t]*(?=\\n+)\\n)/gm,n),a=a.replace(/(\\n[ ]{0,3}(<(hr)\\b([^<>])*?\\/?>)[ \\t]*(?=\\n{2,}))/g,n),a=a.replace(/(\\n\\n[ ]{0,3}<!(--[^\\r]*?--\\s*)+>[ \\t]*(?=\\n{2,}))/g,n),a=a.replace(/(?:\\n\\n)([ ]{0,3}(?:<([?%])[^\\r]*?\\2>)[ \\t]*(?=\\n{2,}))/g,n),a=a.replace(/\\n\\n/g,"\\n"),a},n=function(a,b){var c=b;return c=c.replace(/\\n\\n/g,"\\n"),c=c.replace(/^\\n/,""),c=c.replace(/\\n+$/g,""),c="\\n\\n~K"+(d.push(c)-1)+"K\\n\\n",c},o=function(a){a=v(a);var b=A("<hr />");return a=a.replace(/^[ ]{0,2}([ ]?\\*[ ]?){3,}[ \\t]*$/gm,b),a=a.replace(/^[ ]{0,2}([ ]?\\-[ ]?){3,}[ \\t]*$/gm,b),a=a.replace(/^[ ]{0,2}([ ]?\\_[ ]?){3,}[ \\t]*$/gm,b),a=x(a),a=y(a),a=E(a),a=m(a),a=F(a),a},p=function(a){return a=B(a),a=q(a),a=H(a),a=t(a),a=r(a),a=I(a),a=G(a),a=D(a),a=a.replace(/  +\\n/g," <br />\\n"),a},q=function(a){var b=/(<[a-z\\/!$]("[^"]*"|\'[^\']*\'|[^\'">])*>|<!(--.*?--\\s*)+>)/gi;return a=a.replace(b,function(a){var b=a.replace(/(.)<\\/?code>(?=.)/g,"$1`");return b=N(b,"\\\\`*_"),b}),a},r=function(a){return a=a.replace(/(\\[((?:\\[[^\\]]*\\]|[^\\[\\]])*)\\][ ]?(?:\\n[ ]*)?\\[(.*?)\\])()()()()/g,s),a=a.replace(/(\\[((?:\\[[^\\]]*\\]|[^\\[\\]])*)\\]\\([ \\t]*()<?(.*?(?:\\(.*?\\).*?)?)>?[ \\t]*(([\'"])(.*?)\\6[ \\t]*)?\\))/g,s),a=a.replace(/(\\[([^\\[\\]]+)\\])()()()()()/g,s),a},s=function(a,d,e,f,g,h,i,j){j==undefined&&(j="");var k=d,l=e,m=f.toLowerCase(),n=g,o=j;if(n==""){m==""&&(m=l.toLowerCase().replace(/ ?\\n/g," ")),n="#"+m;if(b[m]!=undefined)n=b[m],c[m]!=undefined&&(o=c[m]);else{if(!(k.search(/\\(\\s*\\)$/m)>-1))return k;n=""}}n=N(n,"*_");var p=\'<a href="\'+n+\'"\';return o!=""&&(o=o.replace(/"/g,"&quot;"),o=N(o,"*_"),p+=\' title="\'+o+\'"\'),p+=">"+l+"</a>",p},t=function(a){return a=a.replace(/(!\\[(.*?)\\][ ]?(?:\\n[ ]*)?\\[(.*?)\\])()()()()/g,u),a=a.replace(/(!\\[(.*?)\\]\\s?\\([ \\t]*()<?(\\S+?)>?[ \\t]*(([\'"])(.*?)\\6[ \\t]*)?\\))/g,u),a},u=function(a,d,e,f,g,h,i,j){var k=d,l=e,m=f.toLowerCase(),n=g,o=j;o||(o="");if(n==""){m==""&&(m=l.toLowerCase().replace(/ ?\\n/g," ")),n="#"+m;if(b[m]==undefined)return k;n=b[m],c[m]!=undefined&&(o=c[m])}l=l.replace(/"/g,"&quot;"),n=N(n,"*_");var p=\'<img src="\'+n+\'" alt="\'+l+\'"\';return o=o.replace(/"/g,"&quot;"),o=N(o,"*_"),p+=\' title="\'+o+\'"\',p+=" />",p},v=function(a){function b(a){return a.replace(/[^\\w]/g,"").toLowerCase()}return a=a.replace(/^(.+)[ \\t]*\\n=+[ \\t]*\\n+/gm,function(a,c){return A(\'<h1 id="\'+b(c)+\'">\'+p(c)+"</h1>")}),a=a.replace(/^(.+)[ \\t]*\\n-+[ \\t]*\\n+/gm,function(a,c){return A(\'<h2 id="\'+b(c)+\'">\'+p(c)+"</h2>")}),a=a.replace(/^(\\#{1,6})[ \\t]*(.+?)[ \\t]*\\#*\\n+/gm,function(a,c,d){var e=c.length;return A("<h"+e+\' id="\'+b(d)+\'">\'+p(d)+"</h"+e+">")}),a},w,x=function(a){a+="~0";var b=/^(([ ]{0,3}([*+-]|\\d+[.])[ \\t]+)[^\\r]+?(~0|\\n{2,}(?=\\S)(?![ \\t]*(?:[*+-]|\\d+[.])[ \\t]+)))/gm;return e?a=a.replace(b,function(a,b,c){var d=b,e=c.search(/[*+-]/g)>-1?"ul":"ol";d=d.replace(/\\n{2,}/g,"\\n\\n\\n");var f=w(d);return f=f.replace(/\\s+$/,""),f="<"+e+">"+f+"</"+e+">\\n",f}):(b=/(\\n\\n|^\\n?)(([ ]{0,3}([*+-]|\\d+[.])[ \\t]+)[^\\r]+?(~0|\\n{2,}(?=\\S)(?![ \\t]*(?:[*+-]|\\d+[.])[ \\t]+)))/g,a=a.replace(b,function(a,b,c,d){var e=b,f=c,g=d.search(/[*+-]/g)>-1?"ul":"ol",f=f.replace(/\\n{2,}/g,"\\n\\n\\n"),h=w(f);return h=e+"<"+g+">\\n"+h+"</"+g+">\\n",h})),a=a.replace(/~0/,""),a};w=function(a){return e++,a=a.replace(/\\n{2,}$/,"\\n"),a+="~0",a=a.replace(/(\\n)?(^[ \\t]*)([*+-]|\\d+[.])[ \\t]+([^\\r]+?(\\n{1,2}))(?=\\n*(~0|\\2([*+-]|\\d+[.])[ \\t]+))/gm,function(a,b,c,d,e){var f=e,g=b,h=c;return g||f.search(/\\n{2,}/)>-1?f=o(L(f)):(f=x(L(f)),f=f.replace(/\\n$/,""),f=p(f)),"<li>"+f+"</li>\\n"}),a=a.replace(/~0/g,""),e--,a};var y=function(a){return a+="~0",a=a.replace(/(?:\\n\\n|^)((?:(?:[ ]{4}|\\t).*\\n+)+)(\\n*[ ]{0,3}[^ \\t\\n]|(?=~0))/g,function(a,b,c){var d=b,e=c;return d=C(L(d)),d=M(d),d=d.replace(/^\\n+/g,""),d=d.replace(/\\n+$/g,""),d="<pre><code>"+d+"\\n</code></pre>",A(d)+e}),a=a.replace(/~0/,""),a},z=function(a){return a+="~0",a=a.replace(/(?:^|\\n)```(.*)\\n([\\s\\S]*?)\\n```/g,function(a,b,c){var d=b,e=c;return e=C(e),e=M(e),e=e.replace(/^\\n+/g,""),e=e.replace(/\\n+$/g,""),e="<pre><code"+(d?\' class="\'+d+\'"\':"")+">"+e+"\\n</code></pre>",A(e)}),a=a.replace(/~0/,""),a},A=function(a){return a=a.replace(/(^\\n+|\\n+$)/g,""),"\\n\\n~K"+(d.push(a)-1)+"K\\n\\n"},B=function(a){return a=a.replace(/(^|[^\\\\])(`+)([^\\r]*?[^`])\\2(?!`)/gm,function(a,b,c,d,e){var f=d;return f=f.replace(/^([ \\t]*)/g,""),f=f.replace(/[ \\t]*$/g,""),f=C(f),b+"<code>"+f+"</code>"}),a},C=function(a){return a=a.replace(/&/g,"&amp;"),a=a.replace(/</g,"&lt;"),a=a.replace(/>/g,"&gt;"),a=N(a,"*_{}[]\\\\",!1),a},D=function(a){return a=a.replace(/(\\*\\*|__)(?=\\S)([^\\r]*?\\S[*_]*)\\1/g,"<strong>$2</strong>"),a=a.replace(/(\\*|_)(?=\\S)([^\\r]*?\\S)\\1/g,"<em>$2</em>"),a},E=function(a){return a=a.replace(/((^[ \\t]*>[ \\t]?.+\\n(.+\\n)*\\n*)+)/gm,function(a,b){var c=b;return c=c.replace(/^[ \\t]*>[ \\t]?/gm,"~0"),c=c.replace(/~0/g,""),c=c.replace(/^[ \\t]+$/gm,""),c=o(c),c=c.replace(/(^|\\n)/g,"$1  "),c=c.replace(/(\\s*<pre>[^\\r]+?<\\/pre>)/gm,function(a,b){var c=b;return c=c.replace(/^  /mg,"~0"),c=c.replace(/~0/g,""),c}),A("<blockquote>\\n"+c+"\\n</blockquote>")}),a},F=function(a){a=a.replace(/^\\n+/g,""),a=a.replace(/\\n+$/g,"");var b=a.split(/\\n{2,}/g),c=[],e=b.length;for(var f=0;f<e;f++){var g=b[f];g.search(/~K(\\d+)K/g)>=0?c.push(g):g.search(/\\S/)>=0&&(g=p(g),g=g.replace(/^([ \\t]*)/g,"<p>"),g+="</p>",c.push(g))}e=c.length;for(var f=0;f<e;f++)while(c[f].search(/~K(\\d+)K/)>=0){var h=d[RegExp.$1];h=h.replace(/\\$/g,"$$$$"),c[f]=c[f].replace(/~K\\d+K/,h)}return c.join("\\n\\n")},G=function(a){return a=a.replace(/&(?!#?[xX]?(?:[0-9a-fA-F]+|\\w+);)/g,"&amp;"),a=a.replace(/<(?![a-z\\/?\\$!])/gi,"&lt;"),a},H=function(a){return a=a.replace(/\\\\(\\\\)/g,O),a=a.replace(/\\\\([`*_{}\\[\\]()>#+-.!])/g,O),a},I=function(a){return a=a.replace(/<((https?|ftp|dict):[^\'">\\s]+)>/gi,\'<a href="$1">$1</a>\'),a=a.replace(/<(?:mailto:)?([-.\\w]+\\@[-a-z0-9]+(\\.[-a-z0-9]+)*\\.[a-z]+)>/gi,function(a,b){return J(K(b))}),a},J=function(a){var b=[function(a){return"&#"+a.charCodeAt(0)+";"},function(a){return"&#x"+a.charCodeAt(0).toString(16)+";"},function(a){return a}];return a="mailto:"+a,a=a.replace(/./g,function(a){if(a=="@")a=b[Math.floor(Math.random()*2)](a);else if(a!=":"){var c=Math.random();a=c>.9?b[2](a):c>.45?b[1](a):b[0](a)}return a}),a=\'<a href="\'+a+\'">\'+a+"</a>",a=a.replace(/">.+:/g,\'">\'),a},K=function(a){return a=a.replace(/~E(\\d+)E/g,function(a,b){var c=parseInt(b);return String.fromCharCode(c)}),a},L=function(a){return a=a.replace(/^(\\t|[ ]{1,4})/gm,"~0"),a=a.replace(/~0/g,""),a},M=function(a){return a=a.replace(/\\t(?=\\t)/g,"    "),a=a.replace(/\\t/g,"~A~B"),a=a.replace(/~B(.+?)~A/g,function(a,b,c){var d=b,e=4-d.length%4;for(var f=0;f<e;f++)d+=" ";return d}),a=a.replace(/~A/g,"    "),a=a.replace(/~B/g,""),a},N=function(a,b,c){var d="(["+b.replace(/([\\[\\]\\\\])/g,"\\\\$1")+"])";c&&(d="\\\\\\\\"+d);var e=new RegExp(d,"g");return a=a.replace(e,O),a},O=function(a,b){var c=b.charCodeAt(0);return"~E"+c+"E"}},typeof module!="undefined"&&(module.exports=Showdown),typeof define=="function"&&define.amd&&define("showdown",function(){return Showdown});

/* Miki JS file */

/* textarea to MarkDown */
var converter = new Showdown.converter();

document.getElementById("box-content").innerHTML = converter.makeHtml( document.getElementById("text").value )
	.replace(/\[(.*?)\]/gi, function(str, p1, offset, s){
		return \'<a href="[url]/\'+p1.replace(" ","-").toLowerCase()+\'" title="p1">\'+p1+\'</a>\';
	});

/* Edit button */
document.getElementById("edit").addEventListener("click", function(e){
	document.getElementById("edit").className = "hide";
	document.getElementById("box-content").className = "hide";
	document.getElementById("form").className = "";
	document.getElementById("text").focus();
});

/* shortcut ctrl+s to save */
window.addEventListener("keydown", function(e) {
	if (e.ctrlKey || e.metaKey) {
		switch (String.fromCharCode(e.which).toLowerCase()) {
		case "s":
			e.preventDefault();
			if ( document.getElementById("edit").className.indexOf("hide") === -1 ){
				document.getElementById("edit").click();
			}else{
				document.getElementById("save").click();
			}
		break;
		}
	}
});

/* Dark theme */
var d = new Date(), h = d.getHours();
if ( h > 21 || h < 7 ){ document.getElementById("html").className = "dark"; }

/* Auto-save drafts */
if ( localStorage[ "miki_page_" + miki.filename ] ){
	document.getElementById("edit").click();
	document.getElementById("text").value = localStorage[ "miki_page_" + miki.filename ];
}
document.getElementById("text").addEventListener("keydown", function(e) {
	localStorage[ "miki_page_" + miki.filename ] = document.getElementById("text").value;
});
document.getElementById("save").addEventListener("click", function(){
	localStorage.clear( "miki_page_" + miki.filename );
});

';

	protected $template = array(
		"page" => '<!DOCTYPE html>
<html id="html">
	<head>
		<title>[title]</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
		<link rel="stylesheet" type="text/css" media="screen" href="[url]/?special=css&v=[version]" />
		<link rel="apple-touch-icon" href="[url]/miki-logo.png?v=[version]" />
		<link rel="shortcut icon" href="[url]/favicon.png?v=[version]">
		<meta name="theme-color" content="#c00">
		<style>[customcss]</style>
	<head>
	<body>
		<p class="bc">[breadcrumb]</p>
		<div id="box-view" class="box-view">
			<h1 class="title">[filename]</h1>
			<form id="form" action="[url]/?p=[filename]" method="post" class="hide">
				<textarea id="text" name="text">[contentraw]</textarea>
				<button id="save" type="submit">Save</button>
			</form>
			<div id="box-content" class="box-content"></div>
		</div>
		<p id="edit" class="edit">Edit</p>
		<ul class="allfiles">[allfiles]</ul>
		<script>window.miki = {"url":"[url]","version":"[version]","filename":"[filename]"};</script>
		<script type="text/javascript" src="[url]/?special=js&v=[version]"></script>
	</body>
</html>',
	);


	protected $htaccess = 'Options All -Indexes
RewriteEngine on

RewriteRule ^.*?\.txt$ welcome [nc]
RewriteRule ^([a-zA-Z0-9\-]*)/?$ index.php?p=$1 [QSA,L]
';



	/******************************************************************************************
	 * Magic starts here
	 */
	public function __construct() {

		// Initial setup
		if ( !file_exists(__DIR__.'/.htaccess') ){
			file_put_contents('.htaccess', $this->htaccess );
		}


		// CSS file?
		if (isset($_GET['special']) && $_GET['special'] == 'css') {
			header("Expires: ".gmdate("D, d M Y H:i:s", time() + $this->config['cache_time'])." GMT");
			header("Pragma: cache");
			header("Cache-Control: max-age=".$this->config['cache_time']);
			header('Content-type: text/css');
			echo $this->css;
			die();
		}
		// JavaScript file?
		if (isset($_GET['special']) && $_GET['special'] == 'js') {
			header("Expires: ".gmdate("D, d M Y H:i:s", time() + $this->config['cache_time'])." GMT");
			header("Pragma: cache");
			header("Cache-Control: max-age=".$this->config['cache_time']);
			header('Content-type: application/x-javascript');
			$array = array(
				"url" => $this->config['app_url'],
				"version" => $this->config['app_version'],
			);
			$output = $this->js;
			foreach ($array as $key => $value) {
				$output = str_replace("[".$key."]", $value, $output);
			}
			echo $output;
			die();
		}
		// Logout
		if (isset($_GET['special']) && $_GET['special'] == 'logout') {
			session_destroy();
			$_COOKIE['miki_auth_key'] = $this->auth_key;
			setcookie('miki_auth_key', '', 0, '/' );
			header("Location: ". $this->config['app_url'] );
		}




		if ( isset($_COOKIE['miki_auth_key']) AND is_dir(__DIR__.'/'.$_COOKIE['miki_auth_key'].'/') ){
			$this->auth_key = $_COOKIE['miki_auth_key'];
		}else if ( @$_GET['p'] AND is_dir(__DIR__.'/'.$_GET['p'].'/') ) {
			session_destroy();
			session_start();
			$this->auth_key = $_GET['p'];
			$_COOKIE['miki_auth_key'] = $this->auth_key;
			setcookie('miki_auth_key', $this->auth_key, time() + $this->config['auth_duration'], '/' );
			$this->gotofile( $this->config['default_page'] );
		}

		if ( !@$_GET['p'] OR $_GET['p'] === '' OR $_GET['p'] === $_COOKIE['miki_auth_key'] ){
			$this->gotofile( $this->config['default_page'] );
		}


		// If no password then die
		if ( !$this->auth_key ) {
			die('<!DOCTYPE><html><link rel="shortcut icon" href="favicon.png"><body><h1 style="font-size:1000%;font-family:sans-serif;margin-top:33vh;text-transform:lowercase;letter-spacing:-.05em;text-align:center;color:#C00">'.$this->config['app_name'].'</h1></body></html>');
		}

		$this->page = preg_replace("[^a-z0-9\-]", "", strtolower($_GET['p']));

		// Editing page?
		if (isset($_POST['text']) AND $this->page) {
			$this->savefile($this->page, $_POST['text']);
			$this->gotofile($this->page);
		}

		// Load page
		if (!$this->page ) {
			if (!$this->is_file_created( $this->config['default_page'] )) {
				$this->savefile($this->config['default_page'], "This is your starting page, the first file you will see when opening Miki. You can edit this file and start writing whatever you want.

## Shortcuts

Press `Control+S` to Edit the current page and `Control+S` to Save your changes

## Formatting

To create a new page, [link it] by encapsulating your text in square brackets, then save it, follow the link and edit that page.

You can format everything using Markdown. For example:

- Hyphens to create lists
- Text in between two asterisks to make it **bold**
- Text in between one asterisk to make it _emphasized_
- Insert images: ![Miki](https://xaviesteve.com/pro/miki/favicon.png)

> Press `Control+S` to see the code or click on the right edge of the screen.

## Advanced stuff

### Custom CSS

If you know CSS code, you can create a file called `customcss` to write your own custom CSS code.

### Source code

You can check the source code of Miki and improve it on [GitHub](https://github.com/luckyshot/miki).");

			}
			$this->gotofile( $this->config['default_page'] );
		}else{
			$this->update_breadcrumb($this->page);
			echo $this->view($this->page);
		}
	}


	protected function fullpathandfilename( $filename = '' ){
		if (isset($this->auth_key)) {
			if (strlen($filename)>0) { // full path to filename including folder and extension
				return __DIR__.'/'.$this->auth_key.'/'.strtolower(preg_replace("[^A-Za-z0-9\-]", "", $filename)).".".$this->config['extension'];
			}else{ // return just the path
				return __DIR__.'/'.$this->auth_key.'/';
			}
		}else{
			return false;
		}
	}


	/******************************************************************************************
	 * Returns an array of the files
	 */
	protected function listfiles() {
		// display only in starting page
		if ($_GET['p'] != $this->config['default_page'] ) {return;}


		$files = [];
		$allfiles = scandir($this->fullpathandfilename());
		foreach ($allfiles as $allfile) {
			if ( substr($allfile, -(strlen($this->config['extension'])+1)) == ".".$this->config['extension'] ) {
				array_push($files, $allfile);
			}
		}
		$files = $this->arraytoli($files);
		return $files;
	}
	// Converts the array into a <li> list
	protected function arraytoli($array) {
		$output = '';
		foreach ($array as $item) {
			$name = substr($item, 0, -4);
			$output .= '<li><a href="'.$this->config['app_url'].'/'.$name.'">'.$name.'</li>';
		}
		$output = $output . '<li><a href="'.$this->config['app_url'].'/?special=logout" title="Logout">logout</a></li>';
		return $output;
	}


	protected function get_customcss(){
		return $this->loadfile('customcss');
	}



	/******************************************************************************************
	 * Generate the View
	 */
	protected function view($filename) {
		$output = $this->template['page'];
		$array = array(
			"title" => $filename." - ".$this->config['app_name'],
			"filename" => $filename,
			"contentraw" => $this->loadfile($filename),
			"app_name" => $this->config['app_name'],
			"url" => $this->config['app_url'],
			"version" => $this->config['app_version'],
			"breadcrumb" => $this->get_breadcrumb(),
			"allfiles" => $this->listfiles(),
			"customcss" => $this->get_customcss(),
		);
		foreach ($array as $key => $value) {
			$output = str_replace("[".$key."]", $value, $output);
		}
		return $output;
	}



	/******************************************************************************************
	 * Create the file
	 */
	protected function savefile($filename, $text = '') {
		if ($text) {
			$filehandle = fopen($this->fullpathandfilename($filename), 'w') or die("Error: Can't create or save files");
			$fwrite = fwrite($filehandle, substr($text, 0, $this->config['maxlength']));
			fclose($filehandle);
			if ($fwrite === false) {
				return $fwrite;
			}else{
				return true;
			}
		}else{
			// no text, delete file
			if (file_exists($this->fullpathandfilename($filename))) {
				unlink($this->fullpathandfilename($filename));
			}
		}
	}



	/******************************************************************************************
	 * Load contents of a file
	 */
	protected function loadfile($filename) {
		if (file_exists($this->fullpathandfilename($filename))) {
			return file_get_contents($this->fullpathandfilename($filename));
		}else{
			return '';
		}
	}


	/******************************************************************************************
	 * Redirects to the specified file
	 */
	protected function gotofile($filename) {
		header("Location: ".$this->config['app_url']."/".strtolower($filename)."/");
	}


	/******************************************************************************************
	 * Check if file has been created
	 */
	protected function is_file_created($filename) {
		if (file_exists($this->fullpathandfilename($filename))) {
			return true;
		}else{
			return false;
		}
	}





	/******************************************************************************************
	 * Update the breadcrumb
	 */
	protected function update_breadcrumb($filename) {
		if (!isset($_SESSION['breadcrumb'])) {$_SESSION['breadcrumb'] = [];}

		if (!in_array($filename, $_SESSION['breadcrumb']) && $filename != $this->auth_key && strlen($filename) < 30) {
			array_push($_SESSION['breadcrumb'], $filename );
		}
		if (sizeof($_SESSION['breadcrumb']) > 10) {
			$_SESSION['breadcrumb'] = array_slice($_SESSION['breadcrumb'], -10);
		}
	}

	/******************************************************************************************
	 * Update the breadcrumb
	 */
	protected function get_breadcrumb() {
		$output = '';
		// Don't show last one (current)
		$breadcrumb = $_SESSION['breadcrumb'];
		$output = '<a class="start" href="'.$this->config['app_url'].'/" title="Home">'.strtolower($this->config['app_name']).'</a>'; // home icon &#8962;
		foreach ($breadcrumb as $crumb) {
			$output = $output.'<a href="'.$this->config['app_url'].'/'.$crumb.'" title="'.$crumb.'">'.$crumb.'</a>';
		}

		return $output;
	}



}
$m = new Miki;
