/*!
* jQuery Cookie Plugin v1.4.0
* https://github.com/carhartl/jquery-cookie
*
* Copyright 2013 Klaus Hartl
* Released under the MIT license
*/
(function (factory) {
if (typeof define === 'function' && define.amd) {
// AMD
define(['jquery'], factory);
} else if (typeof exports === 'object') {
// CommonJS
factory(require('jquery'));
} else {
// Browser globals
factory(jQuery);
}
}(function ($) {

var pluses = /\+/g;

function encode(s) {
return config.raw ? s : encodeURIComponent(s);
}

function decode(s) {
return config.raw ? s : decodeURIComponent(s);
}

function stringifyCookieValue(value) {
return encode(config.json ? JSON.stringify(value) : String(value));
}

function parseCookieValue(s) {
if (s.indexOf('"') === 0) {
// This is a quoted cookie as according to RFC2068, unescape...
s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
}

try {
// Replace server-side written pluses with spaces.
// If we can't decode the cookie, ignore it, it's unusable.
// If we can't parse the cookie, ignore it, it's unusable.
s = decodeURIComponent(s.replace(pluses, ' '));
return config.json ? JSON.parse(s) : s;
} catch(e) {}
}

function read(s, converter) {
var value = config.raw ? s : parseCookieValue(s);
return $.isFunction(converter) ? converter(value) : value;
}

var config = $.cookie = function (key, value, options) {

// Write

if (value !== undefined && !$.isFunction(value)) {
options = $.extend({}, config.defaults, options);

if (typeof options.expires === 'number') {
var days = options.expires, t = options.expires = new Date();
t.setTime(+t + days * 864e+5);
}

return (document.cookie = [
encode(key), '=', stringifyCookieValue(value),
options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
options.path ? '; path=' + options.path : '',
options.domain ? '; domain=' + options.domain : '',
options.secure ? '; secure' : ''
].join(''));
}

// Read

var result = key ? undefined : {};

// To prevent the for loop in the first place assign an empty array
// in case there are no cookies at all. Also prevents odd result when
// calling $.cookie().
var cookies = document.cookie ? document.cookie.split('; ') : [];

for (var i = 0, l = cookies.length; i < l; i++) {
var parts = cookies[i].split('=');
var name = decode(parts.shift());
var cookie = parts.join('=');

if (key && key === name) {
// If second argument (value) is a function it's a converter...
result = read(cookie, value);
break;
}

// Prevent storing a cookie that we couldn't decode.
if (!key && (cookie = read(cookie)) !== undefined) {
result[name] = cookie;
}
}

return result;
};

config.defaults = {};

$.removeCookie = function (key, options) {
if ($.cookie(key) === undefined) {
return false;
}

// Must not alter options, thus extending a fresh object...
$.cookie(key, '', $.extend({}, options, { expires: -1 }));
return !$.cookie(key);
};

}));

/* jQuery Storage API Plugin 1.7.0 https://github.com/julien-maurel/jQuery-Storage-API */
!function(e){function t(t){var r,n,i,o=arguments.length,s=window[t],a=arguments,u=a[1];if(2>o)throw Error("Minimum 2 arguments must be given");if(e.isArray(u)){n={};for(var f in u){r=u[f];try{n[r]=JSON.parse(s.getItem(r))}catch(c){n[r]=s.getItem(r)}}return n}if(2!=o){try{n=JSON.parse(s.getItem(u))}catch(c){throw new ReferenceError(u+" is not defined in this storage")}for(var f=2;o-1>f;f++)if(n=n[a[f]],void 0===n)throw new ReferenceError([].slice.call(a,1,f+1).join(".")+" is not defined in this storage");if(e.isArray(a[f])){i=n,n={};for(var m in a[f])n[a[f][m]]=i[a[f][m]];return n}return n[a[f]]}try{return JSON.parse(s.getItem(u))}catch(c){return s.getItem(u)}}function n(t){var r,n,i=arguments.length,o=window[t],s=arguments,a=s[1],u=s[2],f={};if(2>i||!e.isPlainObject(a)&&3>i)throw Error("Minimum 3 arguments must be given or second parameter must be an object");if(e.isPlainObject(a)){for(var c in a)r=a[c],e.isPlainObject(r)?o.setItem(c,JSON.stringify(r)):o.setItem(c,r);return a}if(3==i)return"object"==typeof u?o.setItem(a,JSON.stringify(u)):o.setItem(a,u),u;try{n=o.getItem(a),null!=n&&(f=JSON.parse(n))}catch(m){}n=f;for(var c=2;i-2>c;c++)r=s[c],n[r]&&e.isPlainObject(n[r])||(n[r]={}),n=n[r];return n[s[c]]=s[c+1],o.setItem(a,JSON.stringify(f)),f}function i(t){var r,n,i=arguments.length,o=window[t],s=arguments,a=s[1];if(2>i)throw Error("Minimum 2 arguments must be given");if(e.isArray(a)){for(var u in a)o.removeItem(a[u]);return!0}if(2==i)return o.removeItem(a),!0;try{r=n=JSON.parse(o.getItem(a))}catch(f){throw new ReferenceError(a+" is not defined in this storage")}for(var u=2;i-1>u;u++)if(n=n[s[u]],void 0===n)throw new ReferenceError([].slice.call(s,1,u).join(".")+" is not defined in this storage");if(e.isArray(s[u]))for(var c in s[u])delete n[s[u][c]];else delete n[s[u]];return o.setItem(a,JSON.stringify(r)),!0}function o(t,r){var n=u(t);for(var o in n)i(t,n[o]);if(r)for(var o in e.namespaceStorages)f(o)}function s(r){var n=arguments.length,i=arguments,o=(window[r],i[1]);if(1==n)return 0==u(r).length;if(e.isArray(o)){for(var a=0;a<o.length;a++)if(!s(r,o[a]))return!1;return!0}try{var f=t.apply(this,arguments);e.isArray(i[n-1])||(f={totest:f});for(var a in f)if(!(e.isPlainObject(f[a])&&e.isEmptyObject(f[a])||e.isArray(f[a])&&!f[a].length)&&f[a])return!1;return!0}catch(c){return!0}}function a(r){var n=arguments.length,i=arguments,o=(window[r],i[1]);if(2>n)throw Error("Minimum 2 arguments must be given");if(e.isArray(o)){for(var s=0;s<o.length;s++)if(!a(r,o[s]))return!1;return!0}try{var u=t.apply(this,arguments);e.isArray(i[n-1])||(u={totest:u});for(var s in u)if(void 0===u[s]||null===u[s])return!1;return!0}catch(f){return!1}}function u(r){var n=arguments.length,i=window[r],o=arguments,s=(o[1],[]),a={};if(a=n>1?t.apply(this,o):i,a._cookie)for(var u in e.cookie())""!=u&&s.push(u.replace(a._prefix,""));else for(var f in a)s.push(f);return s}function f(t){if(!t||"string"!=typeof t)throw Error("First parameter must be a string");window.localStorage.getItem(t)||window.localStorage.setItem(t,"{}"),window.sessionStorage.getItem(t)||window.sessionStorage.setItem(t,"{}");var r={localStorage:e.extend({},e.localStorage,{_ns:t}),sessionStorage:e.extend({},e.sessionStorage,{_ns:t})};return e.cookie&&(window.cookieStorage.getItem(t)||window.cookieStorage.setItem(t,"{}"),r.cookieStorage=e.extend({},e.cookieStorage,{_ns:t})),e.namespaceStorages[t]=r,r}var c="ls_",m="ss_",g={_type:"",_ns:"",_callMethod:function(e,t){var r=[this._type],t=Array.prototype.slice.call(t),n=t[0];return this._ns&&r.push(this._ns),"string"==typeof n&&-1!==n.indexOf(".")&&(t.shift(),[].unshift.apply(t,n.split("."))),[].push.apply(r,t),e.apply(this,r)},get:function(){return this._callMethod(t,arguments)},set:function(){var t=arguments.length,i=arguments,o=i[0];if(1>t||!e.isPlainObject(o)&&2>t)throw Error("Minimum 2 arguments must be given or first parameter must be an object");if(e.isPlainObject(o)&&this._ns){for(var s in o)n(this._type,this._ns,s,o[s]);return o}return r=this._callMethod(n,i),this._ns?r[o.split(".")[0]]:r},remove:function(){if(arguments.length<1)throw Error("Minimum 1 argument must be given");return this._callMethod(i,arguments)},removeAll:function(e){return this._ns?(n(this._type,this._ns,{}),!0):o(this._type,e)},isEmpty:function(){return this._callMethod(s,arguments)},isSet:function(){if(arguments.length<1)throw Error("Minimum 1 argument must be given");return this._callMethod(a,arguments)},keys:function(){return this._callMethod(u,arguments)}};if(e.cookie){window.name||(window.name=Math.floor(1e8*Math.random()));var h={_cookie:!0,_prefix:"",_expires:null,_path:null,_domain:null,setItem:function(t,r){e.cookie(this._prefix+t,r,{expires:this._expires,path:this._path,domain:this._domain})},getItem:function(t){return e.cookie(this._prefix+t)},removeItem:function(t){return e.removeCookie(this._prefix+t)},clear:function(){for(var t in e.cookie())""!=t&&(!this._prefix&&-1===t.indexOf(c)&&-1===t.indexOf(m)||this._prefix&&0===t.indexOf(this._prefix))&&e.removeCookie(t)},setExpires:function(e){return this._expires=e,this},setPath:function(e){return this._path=e,this},setDomain:function(e){return this._domain=e,this},setConf:function(e){return e.path&&(this._path=e.path),e.domain&&(this._domain=e.domain),e.expires&&(this._expires=e.expires),this},setDefaultConf:function(){this._path=this._domain=this._expires=null}};window.localStorage||(window.localStorage=e.extend({},h,{_prefix:c,_expires:3650}),window.sessionStorage=e.extend({},h,{_prefix:m+window.name+"_"})),window.cookieStorage=e.extend({},h),e.cookieStorage=e.extend({},g,{_type:"cookieStorage",setExpires:function(e){return window.cookieStorage.setExpires(e),this},setPath:function(e){return window.cookieStorage.setPath(e),this},setDomain:function(e){return window.cookieStorage.setDomain(e),this},setConf:function(e){return window.cookieStorage.setConf(e),this},setDefaultConf:function(){return window.cookieStorage.setDefaultConf(),this}})}e.initNamespaceStorage=function(e){return f(e)},e.localStorage=e.extend({},g,{_type:"localStorage"}),e.sessionStorage=e.extend({},g,{_type:"sessionStorage"}),e.namespaceStorages={},e.removeAllStorages=function(t){e.localStorage.removeAll(t),e.sessionStorage.removeAll(t),e.cookieStorage&&e.cookieStorage.removeAll(t),t||(e.namespaceStorages={})}}(jQuery);