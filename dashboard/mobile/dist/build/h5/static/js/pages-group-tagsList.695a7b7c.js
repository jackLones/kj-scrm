(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-group-tagsList"],{"018c":function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={props:{},data:function(){return{commonUrl:this.$store.state.commonUrl,copyrightUrl:localStorage.getItem("copyright_url")}},mounted:function(){this.copyrightUrl=localStorage.getItem("copyright_url")},methods:{}};e.default=a},"27e4":function(t,e,r){"use strict";r.r(e);var a=r("018c"),o=r.n(a);for(var n in a)["default"].indexOf(n)<0&&function(t){r.d(e,t,(function(){return a[t]}))}(n);e["default"]=o.a},"2c4b":function(t,e,r){"use strict";r.r(e);var a=r("3113"),o=r("27e4");for(var n in o)["default"].indexOf(n)<0&&function(t){r.d(e,t,(function(){return o[t]}))}(n);r("777e");var i,d=r("f0c5"),c=Object(d["a"])(o["default"],a["b"],a["c"],!1,null,"70ef260e",null,!1,a["a"],i);e["default"]=c.exports},3113:function(t,e,r){"use strict";var a;r.d(e,"b",(function(){return o})),r.d(e,"c",(function(){return n})),r.d(e,"a",(function(){return a}));var o=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-uni-view",{staticClass:"footer-background"},[r("img",{staticClass:"footer-img",attrs:{src:t.commonUrl+t.copyrightUrl,alt:""}})])},n=[]},"31aa":function(t,e,r){"use strict";r.r(e);var a=r("ad59"),o=r.n(a);for(var n in a)["default"].indexOf(n)<0&&function(t){r.d(e,t,(function(){return a[t]}))}(n);e["default"]=o.a},3300:function(t,e,r){"use strict";r.r(e);var a=r("7713"),o=r("e7e6");for(var n in o)["default"].indexOf(n)<0&&function(t){r.d(e,t,(function(){return o[t]}))}(n);r("ae1a");var i,d=r("f0c5"),c=Object(d["a"])(o["default"],a["b"],a["c"],!1,null,"68032788",null,!1,a["a"],i);e["default"]=c.exports},"5b25":function(t,e,r){"use strict";var a=r("ee27");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var o=a(r("2c4b")),n={name:"tagsList",components:{Footer:o.default},data:function(){return{tagList:[],agentId:"",is_show_copyright:localStorage.getItem("is_show_copyright")}},onLoad:function(){uni.setNavigationBarTitle({title:"标签列表"}),this.tagList=JSON.parse(decodeURIComponent(this.$route.query.tagList)),this.is_show_copyright=localStorage.getItem("is_show_copyright")},methods:{goBack:function(){history.back()}}};e.default=n},7513:function(t,e,r){var a=r("a144");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var o=r("4f06").default;o("72cd3616",a,!0,{sourceMap:!1,shadowMode:!1})},7713:function(t,e,r){"use strict";r.d(e,"b",(function(){return o})),r.d(e,"c",(function(){return n})),r.d(e,"a",(function(){return a}));var a={uniTag:r("d719").default},o=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-uni-view",{staticClass:"custom-tags"},[r("v-uni-view",{staticStyle:{"font-size":"0.8rem"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.goBack.apply(void 0,arguments)}}},[r("v-uni-text",{staticStyle:{color:"#007aff","margin-right":"0.533rem",float:"left"}},[t._v("< 返回")]),r("span",{staticStyle:{"margin-left":"-2.68rem"}},[t._v("当前拥有的标签")])],1),r("v-uni-view",[r("v-uni-view",{staticClass:"custom-tags-list"},t._l(t.tagList,(function(t){return r("uni-tag",{staticClass:"custom-tags-list-tag",attrs:{text:t.tagname,type:"primary",circle:!0}})})),1),1==t.is_show_copyright?r("Footer"):t._e()],1)],1)},n=[]},"777e":function(t,e,r){"use strict";var a=r("7513"),o=r.n(a);o.a},9138:function(t,e,r){"use strict";var a;r.d(e,"b",(function(){return o})),r.d(e,"c",(function(){return n})),r.d(e,"a",(function(){return a}));var o=function(){var t=this,e=t.$createElement,r=t._self._c||e;return t.text?r("v-uni-view",{staticClass:"uni-tag",class:["uni-tag--"+t.type,!0===t.disabled||"true"===t.disabled?"uni-tag--disabled":"",!0===t.inverted||"true"===t.inverted?t.type+"-uni-tag--inverted":"",!0===t.circle||"true"===t.circle?"uni-tag--circle":"",!0===t.mark||"true"===t.mark?"uni-tag--mark":"","uni-tag--"+t.size],on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.onClick()}}},[r("v-uni-text",{class:["default"===t.type?"uni-tag--default":"uni-tag-text",!0===t.inverted||"true"===t.inverted?"uni-tag-text--"+t.type:"","small"===t.size?"uni-tag-text--small":""]},[t._v(t._s(t.text))])],1):t._e()},n=[]},"9acb":function(t,e,r){var a=r("24fb");e=a(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */.uni-tag[data-v-2aed3d22]{display:-webkit-box;display:-webkit-flex;display:flex;padding:0 16px;height:30px;line-height:30px;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;color:#333;border-radius:%?6?%;background-color:#f8f8f8;border-width:%?1?%;border-style:solid;border-color:#f8f8f8}.uni-tag--circle[data-v-2aed3d22]{border-radius:15px}.uni-tag--mark[data-v-2aed3d22]{border-top-left-radius:0;border-bottom-left-radius:0;border-top-right-radius:15px;border-bottom-right-radius:15px}.uni-tag--disabled[data-v-2aed3d22]{opacity:.5}.uni-tag--small[data-v-2aed3d22]{height:20px;padding:0 8px;line-height:20px;font-size:%?24?%}.uni-tag--default[data-v-2aed3d22]{color:#333;font-size:%?28?%}.uni-tag-text--small[data-v-2aed3d22]{font-size:%?24?%!important}.uni-tag-text[data-v-2aed3d22]{color:#fff;font-size:%?28?%}.uni-tag-text--primary[data-v-2aed3d22]{color:#007aff!important}.uni-tag-text--success[data-v-2aed3d22]{color:#4cd964!important}.uni-tag-text--warning[data-v-2aed3d22]{color:#f0ad4e!important}.uni-tag-text--error[data-v-2aed3d22]{color:#dd524d!important}.uni-tag--primary[data-v-2aed3d22]{color:#fff;background-color:#007aff;border-width:%?1?%;border-style:solid;border-color:#007aff}.primary-uni-tag--inverted[data-v-2aed3d22]{color:#007aff;background-color:#fff;border-width:%?1?%;border-style:solid;border-color:#007aff}.uni-tag--success[data-v-2aed3d22]{color:#fff;background-color:#4cd964;border-width:%?1?%;border-style:solid;border-color:#4cd964}.success-uni-tag--inverted[data-v-2aed3d22]{color:#4cd964;background-color:#fff;border-width:%?1?%;border-style:solid;border-color:#4cd964}.uni-tag--warning[data-v-2aed3d22]{color:#fff;background-color:#f0ad4e;border-width:%?1?%;border-style:solid;border-color:#f0ad4e}.warning-uni-tag--inverted[data-v-2aed3d22]{color:#f0ad4e;background-color:#fff;border-width:%?1?%;border-style:solid;border-color:#f0ad4e}.uni-tag--error[data-v-2aed3d22]{color:#fff;background-color:#dd524d;border-width:%?1?%;border-style:solid;border-color:#dd524d}.error-uni-tag--inverted[data-v-2aed3d22]{color:#dd524d;background-color:#fff;border-width:%?1?%;border-style:solid;border-color:#dd524d}.uni-tag--inverted[data-v-2aed3d22]{color:#333;background-color:#fff;border-width:%?1?%;border-style:solid;border-color:#f8f8f8}',""]),t.exports=e},a144:function(t,e,r){var a=r("24fb");e=a(!1),e.push([t.i,".footer-background[data-v-70ef260e]{padding-bottom:.533rem ;text-align:center}.footer-img[data-v-70ef260e]{width:40%;margin:.533rem 0}",""]),t.exports=e},ad59:function(t,e,r){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var a={name:"UniTag",props:{type:{type:String,default:"default"},size:{type:String,default:"normal"},text:{type:String,default:""},disabled:{type:[Boolean,String],default:!1},inverted:{type:[Boolean,String],default:!1},circle:{type:[Boolean,String],default:!1},mark:{type:[Boolean,String],default:!1}},methods:{onClick:function(){!0!==this.disabled&&"true"!==this.disabled&&this.$emit("click")}}};e.default=a},ae1a:function(t,e,r){"use strict";var a=r("edca"),o=r.n(a);o.a},c24a:function(t,e,r){var a=r("24fb");e=a(!1),e.push([t.i,".custom-tags[data-v-68032788]{background:#fff;margin:.533rem 0;padding:.12rem .853rem 0;box-sizing:border-box}.custom-tags-title[data-v-68032788]{height:.853rem;line-height:.853rem;font-size:.8rem\n}.custom-tags-list[data-v-68032788]{margin-top:.96rem;overflow:hidden}.custom-tags-list-tag[data-v-68032788]{height:1.227rem;line-height:1.227rem;box-sizing:border-box;background:#e7effc;border-color:#e7effc;margin:0 .64rem .64rem 0;float:left}[data-v-68032788] .uni-tag-text{color:#5283ec}",""]),t.exports=e},d719:function(t,e,r){"use strict";r.r(e);var a=r("9138"),o=r("31aa");for(var n in o)["default"].indexOf(n)<0&&function(t){r.d(e,t,(function(){return o[t]}))}(n);r("f830");var i,d=r("f0c5"),c=Object(d["a"])(o["default"],a["b"],a["c"],!1,null,"2aed3d22",null,!1,a["a"],i);e["default"]=c.exports},e27f:function(t,e,r){var a=r("9acb");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var o=r("4f06").default;o("28be79cf",a,!0,{sourceMap:!1,shadowMode:!1})},e7e6:function(t,e,r){"use strict";r.r(e);var a=r("5b25"),o=r.n(a);for(var n in a)["default"].indexOf(n)<0&&function(t){r.d(e,t,(function(){return a[t]}))}(n);e["default"]=o.a},edca:function(t,e,r){var a=r("c24a");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var o=r("4f06").default;o("f37f9f68",a,!0,{sourceMap:!1,shadowMode:!1})},f830:function(t,e,r){"use strict";var a=r("e27f"),o=r.n(a);o.a}}]);