(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-485c122c"],{"15db":function(t,a,e){"use strict";var n=e("d2e0"),i=e.n(n);i.a},"3f6c":function(t,a,e){a=t.exports=e("2350")(!1),a.push([t.i,"[data-v-d9ede7ba] .ant-card-bordered{border:0}.tpl-title[data-v-d9ede7ba]{float:left;font-size:16px;vertical-align:top}.help[data-v-d9ede7ba]{float:left;margin-left:18px}.help-icon[data-v-d9ede7ba]{margin-right:5px;font-size:14px;margin-top:4px}.help-transition[data-v-d9ede7ba]{-webkit-animation:help-data-v-d9ede7ba 1s infinite;animation:help-data-v-d9ede7ba 1s infinite}.help a[data-v-d9ede7ba]{font-size:14px}.help a[data-v-d9ede7ba]:link,.help a[data-v-d9ede7ba]:visited{color:#1890ff;text-decoration:none}.help-transition1[data-v-d9ede7ba]{-webkit-animation:help1-data-v-d9ede7ba 1s infinite;animation:help1-data-v-d9ede7ba 1s infinite}@-webkit-keyframes help-data-v-d9ede7ba{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@keyframes help-data-v-d9ede7ba{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@-webkit-keyframes help1-data-v-d9ede7ba{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}@keyframes help1-data-v-d9ede7ba{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}.content-bd[data-v-d9ede7ba]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;margin:0 20px}[data-v-d9ede7ba] .dark-row{background:#fafafa}[data-v-d9ede7ba] .light-row{background:#fff}.content-msg[data-v-d9ede7ba]{min-width:845px;width:calc(100% - 40px);border:1px solid #ffdda6;background:#fff2db;padding:10px;margin:20px 0 0 20px;text-align:left}",""])},"49ee":function(t,a,e){var n=e("c2df");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=e("499e").default;i("8ef9ab92",n,!0,{sourceMap:!1,shadowMode:!1})},"74ea":function(t,a,e){"use strict";var n=e("8781"),i=e.n(n);i.a},"7bf6":function(t,a,e){"use strict";e.r(a);var n=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticStyle:{width:"100%","max-height":"100%",position:"absolute","overflow-y":"auto","padding-bottom":"30px"}},[e("div",[e("a-card",{staticStyle:{"margin-bottom":"20px",padding:"10px 20px"}},[e("label",{staticClass:"tpl-title"},[t._v("小程序管理")]),e("help-icon",{attrs:{"help-url":"https://support.qq.com/products/312071/faqs/90051"}})],1),e("div",{staticClass:"content-msg"},[e("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t1、授权小程序后，可在【企业微信】模块下，进入【消息互通】，将授权的小程序和企业成员通过自建应用进行关联。关联后，但凡通过小程序用户发送消息，都会及时通过企业微信推送消息通知触达到指定的企业成员，点击消息通知，跳转到H5实时对话页，即可与小程序用户实时会话。\n\t\t\t")]),e("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t2、关联后，也可以在"+t._s(t.$store.state.siteName)+"后台【公众号】模块，进入【客服中心】对小程序用户实时会话。\n\t\t\t")])]),e("a-row",{staticStyle:{margin:"20px 0px",padding:"0 20px"}},[e("a-col",{staticStyle:{float:"left"}},[e("a-select",{staticStyle:{width:"150px","margin-right":"15px"},attrs:{defaultValue:"全部认证状态"},on:{change:t.handleChange}},[e("a-select-option",{attrs:{value:"all"}},[t._v("全部认证状态")]),e("a-select-option",{attrs:{value:"1"}},[t._v("已认证")]),e("a-select-option",{attrs:{value:"2"}},[t._v("未认证")])],1),e("a-input-search",{staticStyle:{width:"200px","margin-right":"15px"},attrs:{placeholder:"搜索小程序"},on:{search:t.onSearch},model:{value:t.searchInput,callback:function(a){t.searchInput=a},expression:"searchInput"}}),e("a-button",{staticStyle:{"margin-right":"15px"},attrs:{type:"primary"},on:{click:t.find}},[t._v("查找")]),e("a-button",{on:{click:t.clear}},[t._v("清空")])],1),e("a-col",{staticStyle:{float:"right"}},[e("a-button",{directives:[{name:"has",rawName:"v-has",value:"mini-add",expression:"'mini-add'"}],staticStyle:{width:"150px","font-size":"14px"},attrs:{type:"primary",icon:"plus"},on:{click:t.addAccount}},[t._v("添加小程序\n\t\t\t\t")])],1)],1),e("div",{staticClass:"content-bd"},[e("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[e("div",{staticClass:"spin-content"},[e("a-table",{directives:[{name:"has",rawName:"v-has",value:"mini-list",expression:"'mini-list'"}],attrs:{columns:t.columns,dataSource:t.accountList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"nick_name",fn:function(a,n,i){return[e("a-avatar",{attrs:{shape:"square",src:n.head_img}}),e("span",{staticStyle:{"margin-left":"10px"}},[t._v(t._s(n.nick_name))])]}},{key:"action",fn:function(a,n,i){return[e("a-button",{staticStyle:{"margin-right":"5px"},on:{click:t.addAccount}},[t._v("\n\t\t\t\t\t\t\t\t"+t._s(["authorized","updateauthorized"].includes(n.authorizer_type)?"更新":"重新")+"授权\n\t\t\t\t\t\t\t")]),"unauthorized"!=n.authorizer_type?e("a-button",{on:{click:function(a){return t.refreshAuthor(n.wx_id)}}},[t._v("\n\t\t\t\t\t\t\t\t刷新\n\t\t\t\t\t\t\t")]):t._e()]}}])}),e("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"mini-list",expression:"'mini-list'"}],staticClass:"pagination",staticStyle:{margin:"20px auto",height:"32px"}},[e("span",{staticStyle:{float:"left","margin-left":"20px"}},[t._v("共"+t._s(t.total)+"条")]),e("a-pagination",{staticStyle:{float:"right"},attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.page_size,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)],1)])],1)],1),e("authorize",{attrs:{show:t.showWxModal},on:{cancel:t.cancel}})],1)},i=[],s=(e("96cf"),e("3b8d")),o=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",[e("a-modal",{staticStyle:{top:"65px"},attrs:{footer:null,width:"984px"},on:{cancel:t.handleCancel},model:{value:t.showModal,callback:function(a){t.showModal=a},expression:"showModal"}},[e("div",{staticClass:"addAccount-head"},[e("p",[t._v("授权后管理更高效")]),e("p",[t._v("仅支持认证号授权")])]),e("ul",{staticClass:"addAccount-body"},[e("li",[e("img",{attrs:{src:"//s.weituibao.com/static/1545879105817/3.png",alt:""}}),e("p",{staticClass:"txt-1"},[t._v("多账号同时管理")]),e("p",{staticClass:"txt-2"},[t._v("同时登录多个小程序、一键切换，无需重复扫码")])]),e("li",[e("img",{attrs:{src:"//s.weituibao.com/static/1545879105817/2.png",alt:""}}),e("p",{staticClass:"txt-1"},[t._v("一键同步素材")]),e("p",{staticClass:"txt-2"},[t._v("一键同步到多个公众号，无需重复编辑")])]),e("li",[e("img",{attrs:{src:"//s.weituibao.com/static/1545879105817/5.png",alt:""}}),e("p",{staticClass:"txt-1"},[t._v("数据实时更新")]),e("p",{staticClass:"txt-2"},[t._v("粉丝数据实时更新，图文详情及时分析")])]),e("li",[e("img",{attrs:{src:"//s.weituibao.com/static/1545879105817/1.png",alt:""}}),e("p",{staticClass:"txt-1"},[t._v("素材编辑更顺畅")]),e("p",{staticClass:"txt-2"},[t._v("一键抓取图文、添加模版、精选样式排版")])]),e("li",[e("img",{attrs:{src:"//s.weituibao.com/static/1545879105817/6.png",alt:""}}),e("p",{staticClass:"txt-1"},[t._v("多样化推送")]),e("p",{staticClass:"txt-2"},[t._v("素材定时定向推送&客服消息，服务号可以发送模版消息")])]),e("li",[e("img",{attrs:{src:"//s.weituibao.com/static/1545879105817/4.png",alt:""}}),e("p",{staticClass:"txt-1"},[t._v("发送预览即视")]),e("p",{staticClass:"txt-2"},[t._v("可随时发送到微信预览，避免发送后再纠正")])])]),e("div",{staticClass:"addAccount-footer"},[e("a-checkbox",{staticClass:"checkbox",on:{change:t.onChangeCheck}},[t._v("\n        授权即表示知晓并同意\n        "),e("a",{attrs:{href:"https://support.qq.com/products/312071/faqs/90148",target:"_blank"}},[t._v("《授权相关事项和风险》")])]),e("a-button",{staticClass:"aButton",attrs:{type:"primary",disabled:t.disabled},on:{click:t.authorize}},[t._v("立即授权")])],1)])],1)},r=[],c={name:"authorizeMini",props:{show:{type:Boolean,default:!1}},data:function(){return{disabled:!0,commonUrl:this.$store.state.commonUrl}},computed:{showModal:{get:function(){return this.show},set:function(t){}}},methods:{onChangeCheck:function(t){1==t.target.checked?this.disabled=!1:this.disabled=!0},authorize:function(){var t=localStorage.getItem("uid"),a=this.$store.state.siteUrl+"/mini";window.open("".concat(this.commonUrl,"/bind/index?uid=").concat(t,"&cnf_id=1&auth_type=2&redirect_uri=").concat(a),"_blank")},handleCancel:function(){this.$emit("cancel")}},watch:{show:function(t,a){this.showModal=t}},created:function(){}},l=c,d=(e("e102"),e("2877")),p=Object(d["a"])(l,o,r,!1,null,"36f6f141",null),h=p.exports,f=e("f0eb"),u=[{title:"小程序",dataIndex:"nick_name",width:"40%",key:"nick_name",scopedSlots:{customRender:"nick_name"}},{title:"认证状态",dataIndex:"verify_type",width:"15%",key:"verify_type"},{title:"授权时间",dataIndex:"create_time",width:"15%",key:"create_time"},{title:"授权状态",dataIndex:"authorizer_type_name",width:"15%",key:"authorizer_type_name"},{title:"操作",dataIndex:"action",width:"15%",key:"action",scopedSlots:{customRender:"action"}}],m={components:{authorize:h,helpIcon:f["a"]},data:function(){return{auth_status:"",nick_name:"",accountList:[],flag:!1,searchInput:"",isLoading:!0,commonUrl:this.$store.state.commonUrl,showWxModal:!1,columns:u,page:1,page_size:15,total:0,quickJumper:!1}},methods:{rowClassName:function(t,a){var e="dark-row";return a%2===0&&(e="light-row"),e},handleChange:function(t){this.auth_status=t},onSearch:function(t){this.nick_name=t,this.getAccount()},getAccount:function(){var t=Object(s["a"])(regeneratorRuntime.mark((function t(){var a,e,n,i,s=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=s.length>0&&void 0!==s[0]?s[0]:1,e=s.length>1&&void 0!==s[1]?s[1]:this.page_size,t.next=4,this.axios.post("wx-authorize-info/get-authrize-info",{uid:localStorage.getItem("uid"),isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id"),auth_status:this.auth_status,nick_name:this.nick_name,is_page:1,page:a,pageSize:e,type:1});case 4:n=t.sent,i=n.data,0!=i.error?(this.isLoading=!1,this.$message.error(i.message)):(this.accountList=i.data.info,this.total=parseInt(i.data.count),this.page=a,this.page_size=e,this.quickJumper=this.total>this.page_size,this.isLoading=!1),0==this.accountList.length&&(this.flag=!0);case 8:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),refreshAuthor:function(){var t=Object(s["a"])(regeneratorRuntime.mark((function t(a){var e,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.post("wx-authorize-info/refresh-authorize",{refresh_id:a,wx_id:localStorage.getItem("wxNum")});case 3:e=t.sent,n=e.data,0!=n.error?(this.isLoading=!1,"undefined"===typeof n.message?this.$message.error("刷新失败"):this.$message.error(n.message)):this.getAccount();case 6:case"end":return t.stop()}}),t,this)})));function a(a){return t.apply(this,arguments)}return a}(),find:function(){this.nick_name=this.searchInput,this.getAccount()},clear:function(){location.reload(),this.getAccount(),this.flag=!1},changePage:function(t,a){this.getAccount(t,a)},showSizeChange:function(t,a){this.getAccount(1,a)},addAccount:function(){var t=localStorage.getItem("uid"),a=this.$store.state.siteUrl+"/mini";window.open("".concat(this.commonUrl,"/bind/index?uid=").concat(t,"&cnf_id=1&auth_type=2&redirect_uri=").concat(a),"_blank")},cancel:function(){this.showWxModal=!1}},created:function(){this.getAccount()}},b=m,g=(e("74ea"),Object(d["a"])(b,n,i,!1,null,"d9ede7ba",null));a["default"]=g.exports},8781:function(t,a,e){var n=e("3f6c");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=e("499e").default;i("00921bc9",n,!0,{sourceMap:!1,shadowMode:!1})},c2df:function(t,a,e){a=t.exports=e("2350")(!1),a.push([t.i,".addAccount-head[data-v-36f6f141]{height:105px;overflow:hidden}.addAccount-head p[data-v-36f6f141]:first-child{font-size:20px;color:#333;text-align:center;margin:32px 0 10px}.addAccount-head p[data-v-36f6f141]:nth-child(2){font-size:14px;color:#666;text-align:center}.addAccount-body[data-v-36f6f141]{height:298px;padding:0}.addAccount-body li[data-v-36f6f141]{background-color:#f5f7ff;width:49.5%;height:94px;float:left;margin-bottom:8px;border-radius:4px;border:1px solid #c9daff}.addAccount-body li img[data-v-36f6f141]{width:48px;height:48px;margin:23px 25px;float:left}.addAccount-body li .txt-1[data-v-36f6f141]{font-size:16px;color:#1a1a1a;margin:22px 0 6px}.addAccount-body li .txt-2[data-v-36f6f141]{font-size:14px;color:#767676}.addAccount-body li[data-v-36f6f141]:first-child,.addAccount-body li[data-v-36f6f141]:nth-child(3),.addAccount-body li[data-v-36f6f141]:nth-child(5){margin-right:8px}.addAccount-footer[data-v-36f6f141]{height:60px;background-color:#f2f2f2;padding:0 50px;line-height:60px;margin-top:10px}.addAccount-footer .checkbox[data-v-36f6f141]{color:#5d5d5d;font-size:14px;border-top:1px solid #e9eae9;float:left}.addAccount-footer .aButton[data-v-36f6f141]{float:right;margin-top:14px}.addAccount-footer .aButton[disabled][data-v-36f6f141]{color:silver;border:1px solid silver}",""])},d2e0:function(t,a,e){var n=e("d875");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var i=e("499e").default;i("2aaebe52",n,!0,{sourceMap:!1,shadowMode:!1})},d875:function(t,a,e){a=t.exports=e("2350")(!1),a.push([t.i,".help[data-v-4b0dbc7c]{display:inline-block;margin-left:5px}.help a[data-v-4b0dbc7c]{font-size:14px}.help a[data-v-4b0dbc7c]:link,.help a[data-v-4b0dbc7c]:visited{color:#1890ff;text-decoration:none}.help-icon[data-v-4b0dbc7c]{margin-right:5px;font-size:14px;margin-top:4px}.help-transition[data-v-4b0dbc7c]{-webkit-animation:help-data-v-4b0dbc7c 1s infinite;animation:help-data-v-4b0dbc7c 1s infinite}.help-transition1[data-v-4b0dbc7c]{-webkit-animation:help1-data-v-4b0dbc7c 1s infinite;animation:help1-data-v-4b0dbc7c 1s infinite}@-webkit-keyframes help-data-v-4b0dbc7c{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@keyframes help-data-v-4b0dbc7c{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@-webkit-keyframes help1-data-v-4b0dbc7c{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}@keyframes help1-data-v-4b0dbc7c{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}",""])},e102:function(t,a,e){"use strict";var n=e("49ee"),i=e.n(n);i.a},f0eb:function(t,a,e){"use strict";var n=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"help",class:0==t.showTransition?"help-transition":1==t.showTransition?"help-transition1":""},[e("a",{attrs:{href:t.helpUrl,target:"_blank"}},[e("a-icon",{staticClass:"help-icon",attrs:{type:"question-circle"}}),e("span",{staticStyle:{float:"right"}},[t._v("点我帮助")])],1)])},i=[],s={name:"helpIcon",props:{helpUrl:{type:String,default:"https://support.qq.com/products/104790"}},data:function(){return{showTransition:1}},methods:{initHelp:function(){var t=this;setInterval((function(){t.showTransition=(t.showTransition+1)%5}),1e3)}},created:function(){this.initHelp()},watch:{helpUrl:function(t,a){this.helpUrl!=t&&(this.helpUrl=t)}}},o=s,r=(e("15db"),e("2877")),c=Object(r["a"])(o,n,i,!1,null,"4b0dbc7c",null);a["a"]=c.exports}}]);