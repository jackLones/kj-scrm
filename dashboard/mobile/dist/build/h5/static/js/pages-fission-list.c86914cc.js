(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-fission-list"],{"4e29":function(t,e,i){"use strict";var a=i("de9c"),n=i.n(a);n.a},"5f95":function(t,e,i){"use strict";var a;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return a}));var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:!t.isLoading,expression:"!isLoading"}],staticClass:"fission-list",staticStyle:{"background-color":"#d92c31","padding-bottom":"20px"},style:{minHeight:t.height+"px"}},[i("v-uni-view",{staticStyle:{"padding-top":"1rem"}},[i("v-uni-image",{staticStyle:{width:"4rem",height:"4rem","border-radius":"50%"},attrs:{src:t.fission.avatar}}),i("v-uni-view",{staticStyle:{color:"white","font-size":"0.7rem"}},[t._v(t._s(t.fission.help_num)+"人气 | 还差"+t._s(t.fission.rest_num)+"人气")])],1),i("v-uni-view",{staticStyle:{"background-color":"white",margin:"0.7rem 0.7rem 0 0.7rem","border-radius":"0.3rem",overflow:"hidden"}},[i("v-uni-view",{staticStyle:{padding:"1.07rem 1.07rem 0"}},[i("v-uni-button",{staticClass:"friend",class:1==t.type?"white":"red",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.friend.apply(void 0,arguments)}}},[t._v("我的好友")]),i("v-uni-button",{staticClass:"ranking",class:0==t.type?"white":"red",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.ranking.apply(void 0,arguments)}}},[t._v("人气排行Top100")])],1),i("v-uni-view",{staticStyle:{"margin-top":"0.5rem"}},[1==t.type?i("v-uni-view",{staticClass:"borderB",staticStyle:{"text-align":"left","margin-bottom":"0.2rem",height:"3.4rem","line-height":"3.4rem",padding:"0 1.07rem"}},[1==t.fission.ranking?i("v-uni-image",{staticClass:"rank-icon",attrs:{src:"/static/fissionList/No1.png"}}):t._e(),2==t.fission.ranking?i("v-uni-image",{staticClass:"rank-icon",attrs:{src:"/static/fissionList/No2.png"}}):t._e(),3==t.fission.ranking?i("v-uni-image",{staticClass:"rank-icon",attrs:{src:"/static/fissionList/No3.png"}}):t._e(),1!=t.fission.ranking&&2!=t.fission.ranking&&3!=t.fission.ranking?i("v-uni-text",{staticClass:"ranking123"},[t._v(t._s(t.fission.ranking))]):t._e(),i("v-uni-image",{staticClass:"advatar",attrs:{src:t.fission.avatar}}),i("v-uni-text",{staticClass:"nickname"},[t._v("我")]),i("v-uni-text",{staticClass:"help-num"},[t._v("已有"),i("span",{staticStyle:{color:"red"}},[t._v(t._s(t.fission.help_num||0))]),t._v("人气")])],1):t._e(),0==t.type?i("v-uni-view",{staticClass:"helpRanking",staticStyle:{"overflow-y":"auto"},style:t.maxHeight},t._l(t.list,(function(e,a){return i("v-uni-view",{key:a,staticClass:"borderB",staticStyle:{"text-align":"left",height:"3.4rem","line-height":"3.4rem",padding:"0 1.07rem"}},[i("v-uni-text",{staticClass:"ranking123"},[t._v(t._s(a+1))]),e.avatar?i("v-uni-image",{staticClass:"advatar",attrs:{src:e.avatar}}):t._e(),e.avatar?t._e():i("v-uni-image",{staticClass:"advatar",attrs:{src:"/static/useradvart.png"}}),i("v-uni-text",{staticClass:"nickname"},[t._v(t._s(e.name))]),0==t.type?i("v-uni-text",{staticClass:"help-num"},[t._v(t._s(e.help_time))]):t._e(),1==t.type?i("v-uni-text",{staticClass:"help-num"},[t._v("已有"),i("span",{staticStyle:{color:"red"}},[t._v(t._s(e.help_num||0))]),t._v("人气")]):t._e()],1)})),1):t._e(),1==t.type?i("v-uni-view",{staticClass:"helpRanking",staticStyle:{"overflow-y":"auto"},style:t.maxHeight},t._l(t.list,(function(e,a){return i("v-uni-view",{key:a,staticClass:"borderB",staticStyle:{"text-align":"left",height:"3.4rem","line-height":"3.4rem",padding:"0 1.07rem"}},[0==a?i("v-uni-image",{staticClass:"rank-icon",attrs:{src:"/static/fissionList/No1.png"}}):t._e(),1==a?i("v-uni-image",{staticClass:"rank-icon",attrs:{src:"/static/fissionList/No2.png"}}):t._e(),2==a?i("v-uni-image",{staticClass:"rank-icon",attrs:{src:"/static/fissionList/No3.png"}}):t._e(),0!=a&&1!=a&&2!=a?i("v-uni-text",{staticClass:"ranking123"},[t._v(t._s(a+1))]):t._e(),e.avatar?i("v-uni-image",{staticClass:"advatar",attrs:{src:e.avatar}}):t._e(),e.avatar?t._e():i("v-uni-image",{staticClass:"advatar",attrs:{src:"/static/useradvart.png"}}),i("v-uni-text",{staticClass:"nickname"},[t._v(t._s(e.name))]),0==t.type?i("v-uni-text",{staticClass:"help-num"},[t._v(t._s(e.help_time))]):t._e(),1==t.type?i("v-uni-text",{staticClass:"help-num"},[t._v("已有"),i("span",{staticStyle:{color:"red"}},[t._v(t._s(e.help_num||0))]),t._v("人气")]):t._e()],1)})),1):t._e()],1)],1),1==t.is_show_copyright?i("Footer"):t._e()],1)},s=[]},"9cd0":function(t,e,i){"use strict";i.r(e);var a=i("ff8e"),n=i.n(a);for(var s in a)["default"].indexOf(s)<0&&function(t){i.d(e,t,(function(){return a[t]}))}(s);e["default"]=n.a},b1c0:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,".white[data-v-3ce04406], .red[data-v-3ce04406]{display:inline-block;width:50%;border:1px solid red;height:1.8rem;line-height:1.8rem;font-size:.8rem}.white[data-v-3ce04406]{background-color:#fff;color:#000}.red[data-v-3ce04406]{background-color:red;color:#fff}\n/*.friend, .ranking {*/\n/*\tborder: 0px;*/\n/*}*/.friend[data-v-3ce04406]{border-radius:.9rem 0 0 .9rem}.ranking[data-v-3ce04406]{border-radius:0 .9rem .9rem 0}.friend[data-v-3ce04406]:after, .ranking[data-v-3ce04406]:after{border:0}.advatar[data-v-3ce04406]{height:1.87rem;width:1.87rem;margin:.765rem 5px;border-radius:50%}.help-num[data-v-3ce04406]{float:right;font-size:.7rem}.borderB[data-v-3ce04406]{border-bottom:1px solid rgba(0,0,0,.05)}.ranking123[data-v-3ce04406]{font-size:.9rem;margin-left:.3rem;margin-right:.4rem;float:left}.rank-icon[data-v-3ce04406]{width:1.18rem;height:1.36rem;vertical-align:top;margin-top:.99rem}.nickname[data-v-3ce04406]{vertical-align:top;display:inline-block;width:30%;font-size:.8rem;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}",""]),t.exports=e},de9c:function(t,e,i){var a=i("b1c0");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("99eca846",a,!0,{sourceMap:!1,shadowMode:!1})},e523:function(t,e,i){"use strict";i.r(e);var a=i("5f95"),n=i("9cd0");for(var s in n)["default"].indexOf(s)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(s);i("4e29");var r,o=i("f0c5"),c=Object(o["a"])(n["default"],a["b"],a["c"],!1,null,"3ce04406",null,!1,a["a"],r);e["default"]=c.exports},ff8e:function(t,e,i){"use strict";var a=i("ee27");i("99af"),i("a9e3"),i("e25e"),i("4d63"),i("ac1f"),i("25f0"),i("466d"),i("5319"),i("841c"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("96cf");var n=a(i("c964")),s=i("b970"),r=a(i("2c4b")),o={name:"list",components:{Footer:r.default},data:function(){return{jid:"",type:"0",fission:{avatar:"",name:"",help_num:"",rest_num:"",ranking:""},list:[],page:1,height:"",maxPage:0,maxHeight:{maxHeight:""},isLoading:!0,is_show_copyright:localStorage.getItem("is_show_copyright")}},onReachBottom:function(){this.getData(this.page)},methods:{friend:function(){if(0==this.type)return!1;this.type=0,this.page=1,this.list=[],this.getData(1)},ranking:function(){if(1==this.type)return!1;this.type=1,this.page=1,this.list=[],this.getData(1)},getData:function(){var t=arguments,e=this;return(0,n.default)(regeneratorRuntime.mark((function i(){var a,n,r,o,c;return regeneratorRuntime.wrap((function(i){while(1)switch(i.prev=i.next){case 0:return a=t.length>0&&void 0!==t[0]?t[0]:1,s.Toast.loading({message:"加载中...",forbidClick:!0,loadingType:"spinner"}),e.isLoading=!0,n=e,i.next=6,e.axios.post("chat-message/help-list",{jid:e.jid,type:e.type,page:a,pageSet:20});case 6:r=i.sent,o=r.data,0!=o.error?(s.Toast.clear(),e.isLoading=!1,s.Toast.fail(o.error_msg)):(e.page=a,e.maxPage=Math.ceil(o.data.count)>5?5:Math.ceil(o.data.count),e.list=e.list.concat(o.data.helpList),e.fission=o.data.info,1==a&&(c=document.getElementsByClassName("helpRanking")[0],c.onscroll=function(){var t=c.scrollTop,e=c.clientHeight||document.body.clientHeight,i=c.scrollHeight||document.body.scrollHeight;t+e==i&&n.maxPage>n.page&&(1==n.type?5!=n.page&&n.getData(n.page+1):n.getData(n.page+1))}),s.Toast.clear(),e.isLoading=!1);case 9:case"end":return i.stop()}}),i)})))()},getParameter:function(t){var e=new RegExp("(^|&)"+t+"=([^&]*)(&|$)"),i=window.location.search.substr(1).match(e);return null!=i?unescape(i[2]):null}},onLoad:function(){document.title=decodeURIComponent(this.getParameter("title"));var t=document.getElementsByTagName("html")[0].style.fontSize.replace("px",""),e=window.innerHeight/Number(t)-14.82;this.maxHeight.maxHeight=parseInt(e)+"rem";var i=this;uni.getSystemInfo({success:function(t){i.height=t.windowHeight-20}}),this.jid=this.getParameter("jid"),this.is_show_copyright=localStorage.getItem("is_show_copyright"),this.getData()}};e.default=o}}]);