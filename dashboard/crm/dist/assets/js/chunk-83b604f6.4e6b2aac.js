(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-83b604f6"],{"2edd":function(t,e,a){var i=a("f2cb");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=a("499e").default;s("70ce2975",i,!0,{sourceMap:!1,shadowMode:!1})},"49a9":function(t,e,a){"use strict";var i=a("2edd"),s=a.n(i);s.a},8111:function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"list"},[i("div",{attrs:{id:"components-layout-demo-basic"}},[i("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[i("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",top:"0",bottom:"0",right:"0",left:"0","overflow-x":"hidden","overflow-y":"auto"}},[i("a-layout-header",[t._v("欢迎语")]),i("a-layout-content",[i("div",{staticClass:"content-msg"},[i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("1、欢迎语又称新好友自动回复，此处可添加文字、图片、图文链接及小程序，客户来了不用担心冷场！")]),i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t\t2、每个企业成员均可以拥有不同的欢迎语。当通用的欢迎语和个人专属的欢迎语并存的情况下，优先自动回复个人专属的欢迎语。")]),i("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t\t3、"),i("span",{staticStyle:{color:"#F56C6C"}},[t._v("如果企业在企业微信后台为相关成员配置了可用的欢迎语，使用第三方系统配置欢迎语，则均不起效，推送的还是企业微信官方的。")])])]),i("div",{staticClass:"content-hd"},[t.corpInfo.length>1?i("a-col",{staticStyle:{float:"left"}},[i("a-select",{staticStyle:{width:"200px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleChange},model:{value:t.corpId,callback:function(e){t.corpId=e},expression:"corpId"}},[t._l(t.corpInfo,(function(e){return[i("a-select-option",{attrs:{value:e.corpid}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.corp_full_name||e.corp_name)+"\n\t\t\t\t\t\t\t\t\t\t")])]}))],2)],1):t._e(),i("a-col",{staticStyle:{float:"right"}},[i("a-button",{directives:[{name:"has",rawName:"v-has",value:"welcome-add",expression:"'welcome-add'"}],staticClass:"btn-primary",attrs:{icon:"plus",type:"primary"},on:{click:function(e){return t.addWelcomeText()}}},[t._v("\n\t\t\t\t\t\t\t\t\t新建欢迎语\n\t\t\t\t\t\t\t\t")])],1)],1),i("div",{staticClass:"content-bd"},[i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[i("a-table",{directives:[{name:"has",rawName:"v-has",value:"welcome-list",expression:"'welcome-list'"}],attrs:{columns:t.columns,dataSource:t.welcomeList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"content",fn:function(e,a,s){return i("span",{},[a.text_content?i("div",[i("span",{staticStyle:{display:"inline-block","vertical-align":"top"}},[t._v("文本：")]),i("a-popover",[i("template",{slot:"content"},[i("p",{staticStyle:{"max-width":"500px","word-break":"break-all","word-wrapL":"break-word"},domProps:{innerHTML:t._s(a.text_content.replace(/{nickname}/g,"&nbsp;<span contenteditable='false' class='ant-tag ant-tag-orange'>客户名称</span>&nbsp;").replace(/\n/g,"<br>"))}})]),i("span",{staticStyle:{"text-overflow":"ellipsis",overflow:"hidden",display:"inline-block","-webkit-line-clamp":"2","line-clamp":"2","-webkit-box-orient":"vertical",width:"235px"},domProps:{innerHTML:t._s(a.text_content.replace(/{nickname}/g,"&nbsp;<span contenteditable='false' class='ant-tag ant-tag-orange'>客户名称</span>&nbsp;").replace(/\n/g,"<br>"))}})],2)],1):t._e(),1==a.add_type?[i("div",{staticStyle:{"margin-top":"10px"}},[t._v("图片：\n\t\t\t\t\t\t\t\t\t\t\t"),i("img",{staticStyle:{width:"105px","object-fit":"cover"},attrs:{src:t.commonUrl+a.image_url,alt:""}})])]:t._e(),2==a.add_type?[i("div",{staticStyle:{"margin-top":"10px"}},[i("span",{staticStyle:{"vertical-align":"top","line-height":"121px"}},[t._v("链接：")]),i("div",{staticStyle:{width:"235px",border:"1px solid #E5E5E5",padding:"10px",display:"inline-block"}},[i("p",{staticClass:"url-title",staticStyle:{"font-size":"14px"}},[t._v(t._s(a.link_title))]),i("div",{staticStyle:{overflow:"hidden"}},[i("div",{staticClass:"url-text",staticStyle:{"font-size":"12px"}},[t._v(t._s(a.link_desc))]),i("img",{staticClass:"url-img",staticStyle:{"object-fit":"cover"},attrs:{src:t.commonUrl+a.link_pic_url,alt:""}})])])])]:t._e(),3==a.add_type?[i("div",{staticStyle:{"margin-top":"10px"}},[i("span",{staticStyle:{"vertical-align":"top"}},[t._v("小程序：")]),i("div",{staticStyle:{width:"calc(100% - 60px)",display:"inline-block"}},[i("MyIcon",{attrs:{type:"icon-miniapp"}}),i("span",[t._v(t._s(a.mini_title))])],1)])]:t._e()],2)}},{key:"users",fn:function(e,a,s){return i("span",{},[1==a.type?i("a-tag",[t._v(t._s(a.users))]):t._e(),2==a.type?t._l(a.users,(function(e){return i("a-tag",{staticStyle:{"margin-bottom":"5px"},attrs:{color:"orange"}},[t._v(t._s(e))])})):t._e()],2)}},{key:"action",fn:function(e,a){return i("span",{},[i("a-button",{directives:[{name:"has",rawName:"v-has",value:"welcome-edit",expression:"'welcome-edit'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.editList(a.id)}}},[t._v("编辑")]),i("a-button",{directives:[{name:"has",rawName:"v-has",value:"welcome-delete",expression:"'welcome-delete'"}],on:{click:function(e){return t.deleteList(a.id)}}},[t._v("删除")])],1)}}])}),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"welcome-list",expression:"'welcome-list'"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[i("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t\t\t"),i("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("条\n\t\t\t\t\t\t\t\t\t")]),i("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)]),i("a-drawer",{attrs:{placement:"right",closable:!1,visible:t.visible},on:{close:t.onClose}},[i("a-tabs",{attrs:{defaultActiveKey:"1"}},[i("a-tab-pane",{key:"1",attrs:{tab:"消息内容"}},[i("div",{staticClass:"msg_content"},[i("img",{staticClass:"msg_content_header",attrs:{src:a("eb7e"),alt:""}}),i("div",{staticStyle:{padding:"20px 15px"}},[t.text?i("div",{staticClass:"mt"},[i("a-avatar",{staticStyle:{"margin-right":"10px",float:"left"},attrs:{src:"https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",size:36}}),i("span",{staticClass:"item-info msg_content_txt"},[t._v(t._s(t.text))])],1):t._e(),1==t.add_type?i("div",{staticClass:"mt"},[i("a-avatar",{staticStyle:{"margin-right":"10px",float:"left"},attrs:{src:"https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",size:36}}),i("img",{staticStyle:{width:"150px","border-radius":"4px"},attrs:{src:t.commonUrl+t.img,alt:""}})],1):t._e(),2==t.add_type?i("div",{staticClass:"mt"},[i("a-avatar",{staticStyle:{"margin-right":"10px",float:"left"},attrs:{src:"https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",size:36}}),i("div",{staticClass:"item-info msg_content_txt2"},[i("p",{staticClass:"url-title"},[t._v(t._s(t.inputTitle))]),i("div",{staticStyle:{overflow:"hidden"}},[i("div",{staticClass:"url-text"},[t._v(t._s(t.digest))]),i("img",{staticClass:"url-img",attrs:{src:t.commonUrl+t.msgUrl,alt:""}})])])],1):t._e(),3==t.add_type?i("div",{staticClass:"mt"},[i("a-avatar",{staticStyle:{"margin-right":"10px",float:"left"},attrs:{src:"https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png",size:36}}),i("div",{staticClass:"item-info msg_content_txt2"},[i("p",{staticClass:"url-title",staticStyle:{color:"#A3A3A3"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(t.appletInputTitle))]),i("p",{staticClass:"applet-title"},[t._v(t._s(t.appletInputTitle))]),i("img",{staticStyle:{width:"100%"},attrs:{src:t.commonUrl+t.appletUrl,alt:""}})])],1):t._e()])])])],1)],1)],1)],1)])],1)],1)],1)])},s=[],o=(a("96cf"),a("3b8d")),n=a("641c"),r=[{title:"欢迎语类型",dataIndex:"wel_type",key:"wel_type"},{title:"欢迎语内容",dataIndex:"content",key:"content",width:"350px",scopedSlots:{customRender:"content"}},{title:"适用成员",dataIndex:"users",key:"users",width:"20%",scopedSlots:{customRender:"users"}},{title:"创建时间",dataIndex:"time",key:"time"},{title:"操作",dataIndex:"action",key:"action",width:"20%",scopedSlots:{customRender:"action"}}],l={name:"welcomeList",components:{MyIcon:n["a"]},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpInfo:[],suite_id:1,corpId:t,welcomeText:"",welcomeList:[],isLoading:!0,hasAll:0,columns:r,total:0,quickJumper:!1,page:1,page_size:15,pageSize:15,visible:!1,add_type:0,text:"",img:"",inputTitle:"",digest:"",msgUrl:"",appletInputTitle:"",appletUrl:"",commonUrl:this.$store.state.commonUrl}},methods:{handleChange:function(t){var e=this;this.corpInfo.map((function(a){t==a.corpid&&(e.$store.commit("changeCorpAuthType",a.auth_type),e.$store.commit("changeCorpName",a.corp_name))})),this.$store.commit("changeCorpId",t),this.isTabLoading=!0,this.getWelcomeList(),2==localStorage.getItem("isMasterAccount")&&this.$store.dispatch("getPermissionButton")},handleWxId:function(t){this.isLoading=!0,this.corpId=t,this.getWelcomeList()},rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},find:function(){this.isLoading=!0,this.getWelcomeList()},clear:function(){this.isLoading=!0,location.reload()},addWelcomeText:function(){this.$router.push("/welcome/add?hasAll="+this.hasAll)},getWelcomeList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,a=o.length>1&&void 0!==o[1]?o[1]:this.pageSize,t.next=4,this.axios.post("work-welcome/list",{suite_id:this.suite_id,corp_id:this.corpId,page:e,pageSize:a});case 4:i=t.sent,s=i.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.error_msg)):(this.welcomeList=s.data.info,this.hasAll=s.data.hasAll,this.isLoading=!1,this.total=parseInt(s.data.count),this.page=e,this.pageSize=a,this.quickJumper=this.total>this.pageSize);case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),handleShowDetail:function(t){this.visible=!0,this.detail(t)},detail:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e){var a,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-welcome/detail",{id:e});case 2:a=t.sent,i=a.data,0!=i.error?(this.isLoading=!1,this.$message.error(i.error_msg)):(this.isLoading=!1,this.add_type=i.data.add_type,this.text=i.data.text_content,1==this.add_type?this.img=i.data.image_url:2==this.add_type?(this.msgUrl=i.data.link_pic_url,this.inputTitle=i.data.link_title,this.digest=i.data.link_desc):3==this.add_type&&(this.appletInputTitle=i.data.mini_title,this.appletUrl=i.data.mini_pic_url));case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),onClose:function(){this.visible=!1},editList:function(t){this.$router.push({path:"/welcome/add",query:{id:t}})},deleteList:function(t){var e=this;e.$confirm({title:"确定删除该欢迎语?",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.isLoading=!0,e.delWelcome(t)}})},delWelcome:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e){var a,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-welcome/delete",{id:e});case 2:a=t.sent,i=a.data,0!=i.error?(this.isLoading=!1,this.$message.error(i.error_msg)):(this.isLoading=!1,this.page>1&&1==this.welcomeList.length?this.getWelcomeList(this.page-1,this.pageSize):this.getWelcomeList(this.page,this.pageSize));case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),changePage:function(t,e){this.getWelcomeList(t,e),this.$nextTick((function(){document.getElementsByClassName("scroll")[0].scrollTo(0,110)}))},showSizeChange:function(t,e){this.getWelcomeList(1,e)}},created:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.$emit("changeCorpId",t.corpId),t.getWelcomeList()}))},beforeRouteEnter:function(t,e,a){"/welcome/add"==e.path&&"undefined"!=typeof t.query.isRefresh&&"1"==t.query.isRefresh?a((function(t){t.getWelcomeList(t.page,t.pageSize)})):a((function(t){t.isLoading=!0,t.page=1,t.page_size=15,t.pageSize=15,t.getWelcomeList()})),a()}},c=l,d=(a("49a9"),a("2877")),p=Object(d["a"])(c,i,s,!1,null,"237af330",null);e["default"]=p.exports},eb7e:function(t,e){t.exports="data:image/png;base64,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"},f2cb:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"#components-layout-demo-basic[data-v-237af330]{height:100%}#components-layout-demo-basic .ant-layout-header[data-v-237af330]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px}[data-v-237af330] .ant-layout-header{padding:0 20px;font-size:16px;text-align:left}#components-layout-demo-basic .ant-layout-sider[data-v-237af330]{background:#fff;-webkit-box-flex:0!important;-ms-flex:0 0 250px!important;flex:0 0 250px!important;max-width:250px!important;min-width:250px!important;width:250px!important;border-right:1px solid #e2e2e2}#components-layout-demo-basic .ant-layout-content[data-v-237af330]{margin:0 20px 20px;min-width:885px;width:100%;padding-right:40px}.content-hd[data-v-237af330]{height:60px;width:100%;min-width:885px;line-height:60px}.content-msg[data-v-237af330]{width:100%;border:1px solid #ffdda6;background:#fff2db;padding:10px;margin-top:12px}.content-bd[data-v-237af330]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;width:100%}#components-layout-demo-basic>.ant-layout[data-v-237af330]{margin-bottom:48px}#components-layout-demo-basic>.ant-layout[data-v-237af330]:last-child{margin:0}.ant-layout.ant-layout-has-sider[data-v-237af330],.list[data-v-237af330]{height:100%}.btn-primary[data-v-237af330]{margin-left:20px}.tag-name[data-v-237af330]{padding:6px 14px;height:34px;font-size:13px;border:1px solid #d9d9d9;background-color:transparent!important;color:rgba(0,0,0,.65)}[data-v-237af330] .dark-row{background:#fafafa}[data-v-237af330] .light-row{background:#fff}.msg_content[data-v-237af330]{border:1px solid #e2d6d6;height:100%;width:420px;margin:auto;overflow-y:auto}.msg_content .msg_content_header[data-v-237af330]{width:100%}.msg_content_txt[data-v-237af330]{float:right;width:340px;border:1px solid #e9e9e9;line-height:21px;padding:15px;overflow-wrap:break-word;-webkit-hyphens:auto;-ms-hyphens:auto;hyphens:auto}.mt[data-v-237af330]{margin-bottom:15px;overflow:hidden}.item-info[data-v-237af330] p{margin:0;word-break:break-word}.msg_content_txt2[data-v-237af330]{width:calc(100% - 46px);border:1px solid #e9e9e9;padding:10px 16px;border-radius:4px;font-size:14px;color:#1a1a1a;background:#fff;float:left;word-break:break-word}.url-title[data-v-237af330]{white-space:nowrap;font-size:18px}.url-text[data-v-237af330],.url-title[data-v-237af330]{overflow:hidden;text-overflow:ellipsis}.url-text[data-v-237af330]{float:left;max-width:calc(100% - 74px);word-break:break-word;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;color:#a3a3a3}.url-img[data-v-237af330]{float:right;width:64px;height:64px}.applet-title[data-v-237af330]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:10px}",""])}}]);