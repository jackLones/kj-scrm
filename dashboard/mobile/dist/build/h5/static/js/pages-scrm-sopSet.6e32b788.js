(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-scrm-sopSet"],{"0955":function(t,a,e){var n=e("6db7");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=e("4f06").default;i("133d0ec0",n,!0,{sourceMap:!1,shadowMode:!1})},"175b":function(t,a,e){var n=e("cc97");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=e("4f06").default;i("46c9bdd7",n,!0,{sourceMap:!1,shadowMode:!1})},"1a2b":function(t,a,e){"use strict";e("a9e3"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var n={props:{loading:{type:Boolean,default:!0},row:{type:Number|String,default:3},title:{type:Boolean|Number,default:!1},avatar:{type:String,default:""},animate:{type:Boolean,default:!1},avatarSize:{type:String},rowWidth:{type:String|Number,default:"100%"},avatarShape:{type:String,default:"circle"},banner:{type:Boolean|String,default:!1}},computed:{avatarClass:function(){return"top"==this.avatar?["lx-skeleton_avator__top"]:"left"==this.avatar?["lx-skeleton_avator__left"]:""},animationClass:function(){return[this.animate?"lx-skeleton_animation":""]},slotClass:function(){return[this.loading?"hide":"show"]},avatarShapeClass:function(){return["round"==this.avatarShape?"lx-skeleton_avator__round":""]},bannerClass:function(){return[this.banner?"lx-skeleton_banner":""]}},data:function(){return{}}};a.default=n},2274:function(t,a,e){"use strict";var n=e("ee27");Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var i=n(e("f3f3"));e("96cf");var s=n(e("c964")),r=n(e("500a")),o=n(e("7671")),l={name:"sopSet",inject:["initPage","getExternalId","getChatId","getParameter","forbidden","getContext"],components:{sopRules:r.default,skeleton:o.default},data:function(){return{checked:!0,external_userid:0,userid:0,sop_msg_status:0,chat_id:0,is_chat:0,agent_id:"",isReady:!1}},computed:{status:{get:function(){return 1==this.sop_msg_status},set:function(){}}},onLoad:function(){this.external_userid=this.getParameter("external_userid")||localStorage.getItem("externalId")||"",this.userid=this.getParameter("user_id")||localStorage.getItem("user_id"),this.is_chat=this.getParameter("is_chat")||localStorage.getItem("is_chat"),this.agent_id=this.getParameter("agent_id")||localStorage.getItem("agent_id")},onShow:function(){var t=this;this.$store.dispatch("setWx",(function(){t.initPage(t.limit)}))},methods:{limit:function(){var t=this;return(0,s.default)(regeneratorRuntime.mark((function a(){return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:t.utils.setConfig.call(t,{agent_id:t.$route.query.agent_id||localStorage.getItem("agent_id")}).then((function(a){t.sopUserMsgStatus()}));case 1:case"end":return a.stop()}}),a)})))()},sopUserMsgStatus:function(){var t=this;return(0,s.default)(regeneratorRuntime.mark((function a(){var e,n,s;return regeneratorRuntime.wrap((function(a){while(1)switch(a.prev=a.next){case 0:return e={corp_id:localStorage.getItem("corpid"),userid:t.userid},e=0==t.is_chat?(0,i.default)((0,i.default)({},e),{},{external_userid:t.external_userid}):(0,i.default)((0,i.default)({},e),{},{chatid:t.chat_id}),a.next=4,t.axios.post("work-sop/sop-user".concat(1==t.is_chat?"-chat":"","-msg-status"),e);case 4:n=a.sent,s=n.data,0==s.error?t.sop_msg_status=s.data.sop_msg_status||s.data.sop_chat_msg_status:t.$toast(s.error_msg),t.isReady=!0;case 8:case"end":return a.stop()}}),a)})))()},sopUserMsgStatusSet:function(t){var a=this;return(0,s.default)(regeneratorRuntime.mark((function e(){var n,i,s;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return n={userid:a.userid,is_chat:a.is_chat,status:t?1:0,corp_id:localStorage.getItem("corpid")},e.next=3,a.axios.post("work-sop/sop-user-msg-status-set",n);case 3:i=e.sent,s=i.data,0==s.error?a.sopUserMsgStatus():a.$toast(s.error_msg);case 6:case"end":return e.stop()}}),e)})))()},onInputStatus:function(t){this.sopUserMsgStatusSet(t)},back:function(){history.back()}}};a.default=l},"24d5":function(t,a,e){"use strict";e.r(a);var n=e("2274"),i=e.n(n);for(var s in n)["default"].indexOf(s)<0&&function(t){e.d(a,t,(function(){return n[t]}))}(s);a["default"]=i.a},"2ef7":function(t,a,e){"use strict";e.r(a);var n=e("1a2b"),i=e.n(n);for(var s in n)["default"].indexOf(s)<0&&function(t){e.d(a,t,(function(){return n[t]}))}(s);a["default"]=i.a},"4d20":function(t,a,e){"use strict";e.d(a,"b",(function(){return i})),e.d(a,"c",(function(){return s})),e.d(a,"a",(function(){return n}));var n={uniNavBar:e("f31d").default},i=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("app-layout",{attrs:{loading:!t.isReady}},[e("v-uni-view",[e("uni-nav-bar",{attrs:{"left-icon":"back",title:"系统通知","background-color":"#5284ec",color:"#fff",fixed:!0},on:{clickLeft:function(a){arguments[0]=a=t.$handleEvent(a),t.back.apply(void 0,arguments)}}}),e("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:!t.isReady,expression:"!isReady"}]},[e("skeleton",{attrs:{title:!0,row:2,animate:!0,loading:!t.isReady}}),e("skeleton",{attrs:{title:!0,row:2,animate:!0,loading:!t.isReady}}),e("skeleton",{attrs:{title:!0,row:2,animate:!0,loading:!t.isReady}}),e("skeleton",{attrs:{title:!0,row:2,animate:!0,loading:!t.isReady}}),e("skeleton",{attrs:{title:!0,row:2,animate:!0,loading:!t.isReady}})],1),e("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:t.isReady,expression:"isReady"}]},[e("v-uni-view",{staticClass:"px-30 py-30 flex items-center"},[e("v-uni-view",{staticClass:"w-90 h-90 br100 ss-bell-box flex justify-center items-center"},[e("v-uni-text",{staticClass:"scrmIconfont icon_bell text-60 c-b5b5b5 mb-8"})],1),e("v-uni-view",{staticClass:"pl-20 text-left"},[e("v-uni-view",{staticClass:"text-38 text-gray-900 pb-10 font-bold"},[t._v("系统通知")]),e("v-uni-view",{staticClass:"text-26 text-gray-501"},[t._v("企业成员所收到的提醒通知")])],1)],1),e("v-uni-view",{staticClass:"bg-white px-30 py-20 flex justify-between items-center mb-30"},[e("v-uni-view",{staticClass:"text-left"},[e("v-uni-view",{staticClass:"text-30 text-gray-900 pb-10"},[t._v("消息提醒免打扰")]),e("v-uni-view",{staticClass:"text-24 text-gray-501"},[t._v("开启后，则在聊天侧边栏页面不会弹出提醒通知")])],1),e("van-switch",{attrs:{"active-color":"#5283ec"},on:{input:function(a){arguments[0]=a=t.$handleEvent(a),t.onInputStatus.apply(void 0,arguments)}},model:{value:t.status,callback:function(a){t.status=a},expression:"status"}})],1)],1)],1)],1)},s=[]},"6db7":function(t,a,e){var n=e("24fb");a=n(!1),a.push([t.i,".ss-bell-box[data-v-5bad2a76]{border:8px solid #eaeaea}uni-page-body[data-v-5bad2a76]{background:#f6f6f6}.c-b5b5b5[data-v-5bad2a76]{color:#b5b5b5}body.?%PAGE?%[data-v-5bad2a76]{background:#f6f6f6}",""]),t.exports=a},7671:function(t,a,e){"use strict";e.r(a);var n=e("9150"),i=e("2ef7");for(var s in i)["default"].indexOf(s)<0&&function(t){e.d(a,t,(function(){return i[t]}))}(s);e("a696");var r,o=e("f0c5"),l=Object(o["a"])(i["default"],n["b"],n["c"],!1,null,"7def53c4",null,!1,n["a"],r);a["default"]=l.exports},9150:function(t,a,e){"use strict";var n;e.d(a,"b",(function(){return i})),e.d(a,"c",(function(){return s})),e.d(a,"a",(function(){return n}));var i=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("v-uni-view",[e("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:t.loading,expression:"loading"}],staticClass:"lx-skeleton",class:[t.avatarClass,t.animationClass]},[e("v-uni-view",{staticClass:"avatar-class",class:[t.avatarShapeClass,t.bannerClass],style:{width:t.avatarSize,height:t.avatarSize}}),e("v-uni-view",{staticClass:"row",style:{width:t.rowWidth}},[t.title?e("v-uni-view",{staticClass:"row-class lx-skeleton_title"}):t._e(),t._l(t.row,(function(t,a){return e("v-uni-view",{key:t,staticClass:"row-class"})}))],2)],1),t.loading?t._e():t._t("default")],2)},s=[]},a696:function(t,a,e){"use strict";var n=e("175b"),i=e.n(n);i.a},cc97:function(t,a,e){var n=e("24fb");a=n(!1),a.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */.lx-skeleton[data-v-7def53c4]{background-color:#fff;padding:12px}.lx-skeleton_avator__left[data-v-7def53c4]{display:-webkit-box;display:-webkit-flex;display:flex;width:100%}.lx-skeleton_avator__left .avatar-class[data-v-7def53c4],\n.lx-skeleton_avator__top .avatar-class[data-v-7def53c4]{background-color:#f2f3f5;border-radius:50%;width:32px;height:32px}.lx-skeleton_avator__left .avatar-class.lx-skeleton_avator__round[data-v-7def53c4],\n.lx-skeleton_avator__top .avatar-class.lx-skeleton_avator__round[data-v-7def53c4]{border-radius:0;width:32px;height:32px}.lx-skeleton_avator__left .avatar-class[data-v-7def53c4]{margin-right:16px}.lx-skeleton_avator__top .avatar-class[data-v-7def53c4]{margin:0 auto 12px auto}.row-class[data-v-7def53c4]{width:100%;height:16px;background-color:#f2f3f5}.row-class[data-v-7def53c4]:not(:first-child){margin-top:12px}.row[data-v-7def53c4]{-webkit-box-flex:1;-webkit-flex:1;flex:1}.lx-skeleton_avator__left .row[data-v-7def53c4]{width:calc(100% - 48px)}.row-class[data-v-7def53c4]:nth-last-child(1){width:60%}.lx-skeleton_animation .row-class[data-v-7def53c4]{-webkit-animation-duration:1.5s;animation-duration:1.5s;-webkit-animation-name:blink-data-v-7def53c4;animation-name:blink-data-v-7def53c4;-webkit-animation-timing-function:ease-in-out;animation-timing-function:ease-in-out;-webkit-animation-iteration-count:infinite;animation-iteration-count:infinite}@-webkit-keyframes blink-data-v-7def53c4{50%{opacity:.6}}@keyframes blink-data-v-7def53c4{50%{opacity:.6}}.lx-skeleton_title[data-v-7def53c4]{width:40%}.show[data-v-7def53c4]{display:block}.hide[data-v-7def53c4]{display:none}.lx-skeleton .lx-skeleton_banner[data-v-7def53c4]{width:92%;margin:10px auto;height:64px;border-radius:0;background-color:#f2f3f5}',""]),t.exports=a},e181:function(t,a,e){"use strict";e.r(a);var n=e("4d20"),i=e("24d5");for(var s in i)["default"].indexOf(s)<0&&function(t){e.d(a,t,(function(){return i[t]}))}(s);e("e3635");var r,o=e("f0c5"),l=Object(o["a"])(i["default"],n["b"],n["c"],!1,null,"5bad2a76",null,!1,n["a"],r);a["default"]=l.exports},e3635:function(t,a,e){"use strict";var n=e("0955"),i=e.n(n);i.a}}]);