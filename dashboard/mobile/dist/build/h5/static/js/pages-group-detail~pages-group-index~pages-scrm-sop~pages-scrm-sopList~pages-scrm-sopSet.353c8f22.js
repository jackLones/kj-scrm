(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-group-detail~pages-group-index~pages-scrm-sop~pages-scrm-sopList~pages-scrm-sopSet"],{"0671":function(e,t,i){"use strict";var n;i.d(t,"b",(function(){return a})),i.d(t,"c",(function(){return s})),i.d(t,"a",(function(){return n}));var a=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",[i("v-uni-video",{staticClass:"w-80 h-80",attrs:{controls:!1,"object-fit":"fill",src:e.src,disabled:"disabled","webkit-playsinline":!0,playsinline:!0,loop:!0,"x5-video-player-type":"h5"}})],1)},s=[]},"3de4":function(e,t,i){var n=i("24fb");t=n(!1),t.push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */.break-all[data-v-6378b00c]{word-break:break-all}',""]),e.exports=t},"47d8":function(e,t,i){"use strict";var n;i.d(t,"b",(function(){return a})),i.d(t,"c",(function(){return s})),i.d(t,"a",(function(){return n}));var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("v-uni-view",{staticClass:"text-left pt-20"},e._l(e.rules,(function(t,a){return n("v-uni-view",{key:a},[n("v-uni-view",{staticClass:"ohidden"},[t.context?n("v-uni-view",{staticClass:"bg-blue-201 px-20 py-20  br5  mb-20"},[n("v-uni-view",{staticClass:"text-26 text-gray-900 break-all"},[e._v(e._s(t.context))]),e.isSend&&e.is_mobile?n("v-uni-view",{staticClass:"flex justify-end mt-10"},[n("v-uni-view",{staticClass:"bg-blue-501 br5 py-10 px-40 text-26 text-white flex-none ml-20 cursor-pointer",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.sendMsg(7,t.context)}}},[e._v("发送")])],1):e._e()],1):e._e(),1==t.file_type&&t.uploadImg.length>0?n("v-uni-view",{staticClass:"bg-blue-201 px-20 py-10 br5 mb-20"},[n("v-uni-view",{staticClass:"py-20 flex flex-wrap"},e._l(t.uploadImg,(function(t,i){return n("v-uni-view",{key:i,staticClass:"mr-20 flex items-center"},[n("img",{staticClass:"w-80 h-80 object-cover flex-none",attrs:{src:e.commonUrl+t.local_path,alt:""}}),n("v-uni-view",{staticClass:"text-gray-900 text-24 ellipsis-2 break-all pl-10"},[e._v(e._s(t.name))])],1)})),1),e.isSend&&e.is_mobile?n("v-uni-view",{staticClass:"flex justify-end mt-10"},[n("v-uni-view",{staticClass:"bg-blue-501 br5 py-10 px-40 text-26 text-white flex-none ml-20 cursor-pointer",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.sendMsg(1,t.uploadImg)}}},[e._v("发送")])],1):e._e()],1):e._e(),0==t.add_type&&t.uploadVideo&&3==t.file_type?n("v-uni-view",{staticClass:"bg-blue-201 px-20 py-20 mb-20 br10"},[n("v-uni-view",{staticClass:"py-20 flex items-center"},[n("img",{staticClass:"w-80 h-80 flex-none",attrs:{src:i("7871")}}),n("v-uni-view",{staticClass:"text-gray-900 text-24 ellipsis-2 break-all pl-10"},[e._v(e._s(t.uploadVideo.file_name))])],1),e.isSend&&e.is_mobile?n("v-uni-view",{staticClass:"flex justify-end mt-10"},[n("v-uni-view",{staticClass:"bg-blue-501 br5 py-10 px-40 text-26 text-white flex-none ml-20 cursor-pointer",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.sendMsg(3,t.uploadVideo)}}},[e._v("发送")])],1):e._e()],1):e._e(),1==t.add_type&&t.materialVideo&&3==t.file_type?n("v-uni-view",{staticClass:"bg-blue-201 px-20 py-20 mb-20 br10"},[n("v-uni-view",{staticClass:"py-20  flex items-center"},[n("img",{staticClass:"w-80 h-80 flex-none",attrs:{src:i("7871")}}),n("v-uni-view",{staticClass:"text-gray-900 text-24 ellipsis-2 break-all pl-10"},[e._v(e._s(t.materialVideo.file_name))])],1),e.isSend&&e.is_mobile?n("v-uni-view",{staticClass:"flex justify-end mt-10"},[n("v-uni-view",{staticClass:"bg-blue-501 br5 py-10 px-40 text-26 text-white flex-none ml-20 cursor-pointer",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.sendMsg(3,t.materialVideo)}}},[e._v("发送")])],1):e._e()],1):e._e(),0==t.add_type&&t.uploadText&&4==t.file_type?n("v-uni-view",{staticClass:"bg-blue-201 p-20 mb-20 br10"},[t.uploadText.url||t.uploadText.title||t.uploadText.description?n("v-uni-view",{staticClass:"flex"},[n("v-uni-view",[t.uploadText.url?n("img",{staticClass:"w-80 h-80 block object-cover",attrs:{src:e.commonUrl+t.uploadText.url,alt:""}}):n("img",{staticClass:"w-80 h-80 block object-cover",attrs:{src:i("d096")}})]),n("v-uni-view",{staticClass:"ml-10 "},[n("v-uni-view",{staticClass:"text-26 text-gray-900   break-all ellipsis-1"},[e._v(e._s(t.uploadText.title))]),n("v-uni-view",{staticClass:"text-24 text-gray-501 break-all ellipsis-2"},[e._v(e._s(t.uploadText.description))])],1)],1):e._e(),e.isSend&&e.is_mobile?n("v-uni-view",{staticClass:"flex justify-end mt-10"},[n("v-uni-view",{staticClass:"bg-blue-501 br5 py-10 px-40 text-26 text-white flex-none ml-20 cursor-pointer",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.sendMsg(4,t.uploadText)}}},[e._v("发送")])],1):e._e()],1):e._e(),1==t.add_type&&t.materialText&&4==t.file_type?n("v-uni-view",{staticClass:"bg-blue-201 p-20 mb-20 br10"},[n("v-uni-view",{staticClass:" flex "},[n("v-uni-view",[t.materialText.url?n("img",{staticClass:"w-80 h-80 block object-cover",attrs:{src:e.commonUrl+t.materialText.url,alt:""}}):n("img",{staticClass:"w-80 h-80 block object-cover",attrs:{src:i("d096")}})]),n("v-uni-view",{staticClass:"ml-10"},[n("v-uni-view",{staticClass:"text-26 text-gray-900   break-all ellipsis-1"},[e._v(e._s(t.materialText.title))]),n("v-uni-view",{staticClass:"text-24 text-gray-501 break-all ellipsis-2"},[e._v(e._s(t.materialText.description))])],1)],1),e.isSend&&e.is_mobile?n("v-uni-view",{staticClass:"flex justify-end mt-10"},[n("v-uni-view",{staticClass:"bg-blue-501 br5 py-10 px-40 text-26 text-white flex-none ml-20 cursor-pointer",on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.sendMsg(4,t.materialText)}}},[e._v("发送")])],1):e._e()],1):e._e()],1)],1)})),1)},s=[]},"4da6":function(e,t,i){"use strict";i.r(t);var n=i("fdcb"),a=i.n(n);for(var s in n)["default"].indexOf(s)<0&&function(e){i.d(t,e,(function(){return n[e]}))}(s);t["default"]=a.a},"4fea":function(e,t,i){"use strict";i.r(t);var n=i("0671"),a=i("9f0f");for(var s in a)["default"].indexOf(s)<0&&function(e){i.d(t,e,(function(){return a[e]}))}(s);var r,l=i("f0c5"),o=Object(l["a"])(a["default"],n["b"],n["c"],!1,null,"ee1bf3ec",null,!1,n["a"],r);t["default"]=o.exports},"500a":function(e,t,i){"use strict";i.r(t);var n=i("47d8"),a=i("4da6");for(var s in a)["default"].indexOf(s)<0&&function(e){i.d(t,e,(function(){return a[e]}))}(s);i("a7e2");var r,l=i("f0c5"),o=Object(l["a"])(a["default"],n["b"],n["c"],!1,null,"6378b00c",null,!1,n["a"],r);t["default"]=o.exports},7871:function(e,t){e.exports="data:image/png;base64,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"},9314:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={name:"AppVideo",props:{src:{type:String,default:function(){return""}}},components:{},data:function(){return{}}};t.default=n},"9f0f":function(e,t,i){"use strict";i.r(t);var n=i("9314"),a=i.n(n);for(var s in n)["default"].indexOf(s)<0&&function(e){i.d(t,e,(function(){return n[e]}))}(s);t["default"]=a.a},a7e2:function(e,t,i){"use strict";var n=i("fae3"),a=i.n(n);a.a},d096:function(e,t){e.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAAzCAIAAAC1w6d9AAAACXBIWXMAAABAAAAAQAEVRFPNAAAABGdBTUEAALGOfPtRkwAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAEKElEQVR42uyY3UsyTRjG79bd1W3DPsWDKIg+IPswKcKUkCgpOlA0yZP6K/o/OpSog4o+kFiC6ihE6EAqkJSKoAjKrGRTZ8NVLHXL52BfHl7seSLFfIPXORqGYefHNXNf18xWIITgRzYMfmork5XJymRlsjLZdzf8+z4tCALP89lsVi6XEwTxI8gEQTg6OnK5XK+vrwCA47jBYBgdHcXxPJarKHqiPzw87O3t3d7e5owrlUqz2dza2lpRUfEfkCGEFhYWYrEYALS0tOh0OoIgvF7vxcUFANTW1s7NzZEkWWqyYDDodDqj0ShN0yaTSa1WY9g/FXZ+fr61tZVKpbRardVqLWltIoQ2Nzej0ahEIpmentZoNL+xAKCnp2dwcBAAjo+Pk8lk6ciCweDy8jLHcTRNz8zMdHZ2fpwzMDAgdiKRSIlqU1SL4zhRrT9iAUBlZaXYeXt7KwUZy7Krq6scx1EU9QkWAPj9frFTV1f37WTpdHp3dxchRJKk3W5XqVTZbBYAPvpCOBw+ODgAgNbW1pqamm8/Z6FQKBAISCQSs9msUqkSiQTDMC6XK2caz/MMwySTSZqmLRZLKTLA5/MJgqBUKnt7ewHg5ubG6/UCAIZhIyMjEokEADiOW1paQgjhOD42NqZQKEqR6OFwGAAUCoVUKgWAjo6O9vZ2APB4PE9PTwDw+vq6traGEJJKpTabbWho6IsBUAgZQuju7i6TyQCATCYDgEQiIR4vmUxmsViqq6uTyeTp6anoJqFQCMOw8fHxHIcrMlkwGHQ4HA6H4+TkBADa2tpE1mg0Kk6or69vbm4GgOvrazGgLBbL7OysTqf7ulp5kyGEnE5nPB6nKKqpqQkAent75XJ5PB7f399/eXkRq7KhoUEUEgAIgtBqtV1dXXmplR/Z4+PjwsKCGD42m62xsREAqqqq9Ho9AJydnR0eHgJANptlWRYAqqurS3FzFARhZ2cnFovRNP1vO8UwzGAwsCzLsqyoYigUur+/B4BPLLeYZB6PJxAIAIDJZMpZEsMwu92eSqUoiorH4wzDJBIJuVze3d397WSCILjdbtEU1Gr1Hw4EhlEUxXHc5ubm4+MjSZJGo/HrvlU4Gc/zqVQKAPR6/d8OMs/zKysrLMsSBGG1Wvv6+vKtxELIRK8CgL/dRZ+fnzc2NliWJUlyYmKir6+vgEospDblcrkogJg8H9VaXFwMBoM4jlut1k90LT4ZQRCiNfh8vqurq5x0Wl9fF8NncnKyKJuY3zsgk8nMz88jhGQy2fDwcH9/P0mSfr/f7XYnk0kcx202m0ajKSJWHi+Uy8tLhmF4ns8Zp2naaDRqtdpibWLeZNlsNp1Ob29v/76airE4NTWlUCiKq1aBrzqe5yORyPv7e319/RfvzSUiK/8LKpOVycpkZbIy2f+Y7NcAhq/SNMR6lYEAAAAASUVORK5CYII="},fae3:function(e,t,i){var n=i("3de4");"string"===typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);var a=i("4f06").default;a("0fe44992",n,!0,{sourceMap:!1,shadowMode:!1})},fdcb:function(e,t,i){"use strict";var n=i("ee27");i("4160"),i("c975"),i("a9e3"),i("ac1f"),i("466d"),i("159b"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,i("96cf");var a=n(i("c964")),s=n(i("4fea")),r=i("b970"),l={name:"sopRules",data:function(){return{commonUrl:this.$store.state.commonUrl,is_mobile:!1}},inject:["initPage"],components:{AppVideo:s.default},props:{rules:{type:Array,default:function(){return[]}},user_id:{type:[Number,String],default:function(){return""}},agent_id:{type:[Number,String],default:function(){return""}},external_userid:{type:[Number,String],default:function(){return 0}},isSend:{type:Boolean,value:!0},isSendAll:{type:Boolean,default:function(){return!0}},is_chat:{type:[Number,String],default:function(){return 0}}},created:function(){this.is_mobile=this.is_mobile_fn()},methods:{is_mobile_fn:function(){return!!window.navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)},allSendMeg:function(){var e=this;this.rules.forEach((function(t,i){t.context&&e.sendMsg(7,t.context,!0),1==t.file_type&&t.uploadImg.length>0&&e.sendMsg(1,t.uploadImg),0==t.add_type&&t.uploadVideo&&3==t.file_type&&e.sendMsg(3,t.uploadVideo),1==t.add_type&&t.materialVideo&&3==t.file_type&&e.sendMsg(3,t.materialVideo),0==t.add_type&&t.uploadText&&4==t.file_type&&e.sendMsg(4,t.uploadText),1==t.add_type&&t.materialText&&4==t.file_type&&e.sendMsg(4,t.materialText)}))},sendMsg:function(e,t){var i=arguments,n=this;return(0,a.default)(regeneratorRuntime.mark((function a(){var s,l,o;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:s=i.length>2&&void 0!==i[2]&&i[2],r.Toast.loading({forbidClick:!0,message:"上传中...",duration:0}),a.t0=1*e,a.next=1===a.t0?5:3===a.t0?7:4===a.t0?9:7===a.t0?11:15;break;case 5:for(l=0;l<t.length;l++)n.sendData(t[l].id);return a.abrupt("break",15);case 7:return n.sendData(t.id),a.abrupt("break",15);case 9:return n.sendData(t.id),a.abrupt("break",15);case 11:return o=t,o=s?t+"\n":t,n.sendChatMessage({msgtype:"text",text:{content:o}}),a.abrupt("break",15);case 15:case"end":return a.stop()}}),a)})))()},sendData:function(e){var t=this;return(0,a.default)(regeneratorRuntime.mark((function i(){var n,a;return regeneratorRuntime.wrap((function(i){while(1)switch(i.prev=i.next){case 0:return i.next=2,t.axios.post("chat-message/send-data",{ids:e,uid:localStorage.getItem("uid"),corpid:localStorage.getItem("corpid"),agent_id:t.agent_id,user_id:t.user_id,external_id:t.external_userid,chat_id:t.$store.state.chatId});case 2:n=i.sent,a=n.data,0!==a.error?r.Toast.fail(a.error_msg):(console.log(a.data[0],"接口返回的"),t.sendChatMessage(a.data[0]));case 5:case"end":return i.stop()}}),i)})))()},sendChatMessage:function(e){var t=this;r.Toast.clear(),this.$store.state.wx.invoke("sendChatMessage",e,(function(e){"sendChatMessage:ok"==e.err_msg?console.log(e,"发送成功"):(console.log(e,"发送失败"),e.err_msg&&e.err_msg.indexOf("without context of external contact")>-1&&r.Toast.fail("请到该".concat(1==t.is_chat?"群":"客户","的会话页面按原流程进行推送")),e.err_msg&&e.err_msg.indexOf("invalid param")>-1&&r.Toast.fail("没找到对应的素材"),e.err_msg&&e.err_msg.indexOf("fail_nosupport")>-1&&r.Toast.fail("请到该".concat(1==t.is_chat?"群":"客户","的会话页面按原流程进行推送")),e.err_msg&&e.err_msg.indexOf("fail_nopermission")>-1&&r.Toast.fail("请到该".concat(1==t.is_chat?"群":"客户","的会话页面按原流程进行推送")),e.errmsg&&e.errmsg.indexOf("fail_no permission")>-1&&r.Toast.fail("请到该".concat(1==t.is_chat?"群":"客户","的会话页面按原流程进行推送")),e.errmsg&&e.errmsg.indexOf("fail_nosupport")>-1&&r.Toast.fail("请到该".concat(1==t.is_chat?"群":"客户","的会话页面按原流程进行推送")))}))}}};t.default=l}}]);