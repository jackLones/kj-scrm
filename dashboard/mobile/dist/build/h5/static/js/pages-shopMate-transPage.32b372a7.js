(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-shopMate-transPage"],{1889:function(e,n,t){"use strict";t.r(n);var r=t("d156"),o=t("5d1b");for(var a in o)["default"].indexOf(a)<0&&function(e){t.d(n,e,(function(){return o[e]}))}(a);var u,c=t("f0c5"),i=Object(c["a"])(o["default"],r["b"],r["c"],!1,null,"6f94a9cb",null,!1,r["a"],u);n["default"]=i.exports},"5d1b":function(e,n,t){"use strict";t.r(n);var r=t("b719"),o=t.n(r);for(var a in r)["default"].indexOf(a)<0&&function(e){t.d(n,e,(function(){return r[e]}))}(a);n["default"]=o.a},b719:function(e,n,t){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var r={name:"transPage",inject:["initPage","getParameter"],data:function(){},onLoad:function(e){console.log("中转页参数",e),console.log(11,e.redirect_url),console.log(22,decodeURIComponent(e.redirect_url)),location.href=decodeURIComponent(e.redirect_url)},methods:{}};n.default=r},d156:function(e,n,t){"use strict";var r;t.d(n,"b",(function(){return o})),t.d(n,"c",(function(){return a})),t.d(n,"a",(function(){return r}));var o=function(){var e=this,n=e.$createElement,t=e._self._c||n;return t("v-uni-view",{staticClass:"trans-page"})},a=[]}}]);