(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-57cd3754"],{"0123":function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"scroll",staticStyle:{width:"100%","max-height":"100%",position:"absolute","overflow-y":"auto","padding-bottom":"30px"}},[a("div",[a("a-card",{staticStyle:{padding:"0 20px","line-height":"50px","border-bottom":"1px solid #E2E2E2",height:"50px","min-width":"760px",width:"100%","margin-bottom":"12px"}},[a("label",{staticClass:"tpl-title"},[t._v("模版管理")])]),a("div",{staticStyle:{margin:"0 20px",background:"#FFF","padding-bottom":"20px"}},[a("a-row",{staticStyle:{padding:"30px 20px"}},[a("a-col",{staticStyle:{float:"left"}},[[a("span",{staticStyle:{"margin-right":"15px"}},[t._v("申请时间 ")]),a("a-range-picker",{attrs:{disabledDate:t.disabledDate,format:"YYYY-MM-DD"},on:{change:t.changeSelectTime},model:{value:t.dateValue,callback:function(e){t.dateValue=e},expression:"dateValue"}})],a("a-select",{staticStyle:{width:"150px","margin-right":"15px","margin-left":"15px"},on:{change:t.handleChangeStatus},model:{value:t.status,callback:function(e){t.status=e},expression:"status"}},[a("a-select-option",{attrs:{value:"-1"}},[t._v("全部状态")]),a("a-select-option",{attrs:{value:"0"}},[t._v("未审核")]),a("a-select-option",{attrs:{value:"1"}},[t._v("已通过")]),a("a-select-option",{attrs:{value:"2"}},[t._v("未通过")])],1),a("a-button",{staticStyle:{"margin-right":"15px"},attrs:{type:"primary"},on:{click:t.find}},[t._v("查找")]),a("a-button",{staticStyle:{"margin-right":"15px"},on:{click:t.clear}},[t._v("清空")])],2),a("a-button",{directives:[{name:"has",rawName:"v-has",value:"sms-template-add",expression:"'sms-template-add'"}],staticStyle:{float:"right","margin-right":"15px"},attrs:{icon:"plus",type:"primary"},on:{click:t.addPush}},[t._v("新建模版申请\n\t\t\t\t")])],1),a("div",{staticClass:"content-bd"},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("div",{staticClass:"spin-content"},[a("a-table",{directives:[{name:"has",rawName:"v-has",value:"sms-template-list",expression:"'sms-template-list'"}],attrs:{columns:t.columns,dataSource:t.accountList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"status_name",fn:function(e,n,s){return["未通过"!=n.status_name?a("span",[t._v(t._s(n.status_name))]):t._e(),a("a-tooltip",[a("template",{slot:"title"},[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(n.error_msg)+"\n\t\t\t\t\t\t\t\t\t")]),"未通过"==n.status_name?a("span",{staticStyle:{color:"red"}},[t._v("未通过")]):t._e()],2)]}},{key:"action",fn:function(e,n,s){return[0==n.status||2==n.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"sms-template-delete",expression:"'sms-template-delete'"}],on:{click:function(e){return t.deleteList(n.id)}}},[t._v("删除\n\t\t\t\t\t\t\t\t")]):t._e(),0==n.status||2==n.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"sms-template-edit",expression:"'sms-template-edit'"}],staticStyle:{"margin-left":"10px"},on:{click:function(e){return t.editDetail(n.id,n.content)}}},[t._v("修改\n\t\t\t\t\t\t\t\t")]):t._e(),0!=n.status&&2!=n.status?a("span",[t._v("--")]):t._e()]}}])}),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"sms-template-list",expression:"'sms-template-list'"}],staticClass:"pagination",staticStyle:{margin:"20px auto",height:"32px"}},[a("span",{staticStyle:{float:"left","margin-left":"20px"}},[t._v("共"+t._s(t.total)+"条")]),a("a-pagination",{staticStyle:{float:"right"},attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.page_size,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)],1)])],1),a("a-modal",{attrs:{title:"修改内容",width:"650px"},on:{cancel:t.handleCancel,ok:t.handleOk},model:{value:t.visible,callback:function(e){t.visible=e},expression:"visible"}},[a("div",{staticStyle:{height:"200px",overflow:"auto"}},[a("a-form-item",{attrs:{label:"","label-col":{span:8},"wrapper-col":{span:25}}},[a("a-textarea",{staticStyle:{height:"160px",border:"1px solid #D9D9D9",resize:"none",padding:"15px"},attrs:{placeholder:"请填写短信内容",maxLength:250,autosize:!1},model:{value:t.editContent,callback:function(e){t.editContent=e},expression:"editContent"}}),a("span",{staticStyle:{float:"right"}},[t._v(t._s(t.editContent.length)+"/250")])],1)],1)])],1)],1)])},s=[],i=(a("96cf"),a("3b8d")),r=a("c1df"),o=a.n(r),l=[{title:"申请时间",dataIndex:"apply_time",width:"10%",key:"apply_time"},{title:"短信签名",dataIndex:"sign_name",width:"10%",key:"sign_name"},{title:"短信类型",dataIndex:"type_name",width:"8%",key:"type_name"},{title:"短信内容",dataIndex:"content",width:"15%",key:"content"},{title:"审核状态",dataIndex:"status_name",width:"10%",key:"status_name",scopedSlots:{customRender:"status_name"}},{title:"操作",dataIndex:"action",width:"12%",key:"action",scopedSlots:{customRender:"action"}}],c={name:"smsTemplateList",data:function(){return{showTransition:1,accountList:[],flag:!1,isLoading:!0,visible:!1,status:"-1",edit_id:"",editContent:"",dateValue:[],start_date:"",end_date:"",columns:l,page:1,page_size:15,total:0,quickJumper:!1}},mounted:function(){this.initHelp()},methods:{moment:o.a,disabledDate:function(t){return(new Date).getTime()<t.valueOf()},changeSelectTime:function(t,e){this.start_date=e[0],this.end_date=e[1]},rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},initHelp:function(){var t=this;setInterval((function(){t.showTransition=(t.showTransition+1)%5}),1e3)},handleChangeStatus:function(t){this.status=t},getAccount:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n,s,i=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=i.length>0&&void 0!==i[0]?i[0]:1,a=i.length>1&&void 0!==i[1]?i[1]:this.page_size,t.next=4,this.axios.post("short-message/user-template",{uid:localStorage.getItem("uid"),status:this.status,start_date:this.start_date,end_date:this.end_date,page:e,pageSize:a});case 4:n=t.sent,s=n.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.message)):(this.accountList=s.data.templateArr,this.total=parseInt(s.data.count),this.page=e,this.page_size=a,this.quickJumper=this.total>this.page_size,this.isLoading=!1),0==this.accountList.length&&(this.flag=!0);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),find:function(){this.isLoading=!0,this.getAccount()},clear:function(){this.isLoading=!0,location.reload()},deleteList:function(t){var e=this;e.$confirm({title:"确定删除该记录?",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.isLoading=!0,e.delSign(t)}})},delSign:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(e){var a,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("short-message/template-delete",{uid:localStorage.getItem("uid"),template_id:e});case 2:a=t.sent,n=a.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):1==this.accountList.length&&this.page>1?this.getAccount(this.page-1,this.page_size):this.getAccount(this.page,this.page_size);case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),editDetail:function(t,e){this.edit_id=t,this.editContent=e,this.visible=!0},handleCancel:function(){},handleOk:function(){""==this.editContent?this.$message.warning("请填写短信内容"):(this.isLoading=!1,this.editTemplate())},editTemplate:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("short-message/edit-template",{uid:localStorage.getItem("uid"),template_id:this.edit_id,content:this.editContent});case 2:e=t.sent,a=e.data,0!=a.error?this.$message.error(a.error_msg):(this.visible=!1,this.getAccount(this.page,this.page_size));case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),addPush:function(){this.$router.push("/smsTemplate/add")},changePage:function(t,e){this.getAccount(t,e),document.getElementsByClassName("scroll")[0].scrollTo(0,0)},showSizeChange:function(t,e){this.getAccount(1,e)}},created:function(){this.getAccount()},beforeRouteEnter:function(t,e,a){("/smsTemplate/add"!=e.path||"undefined"!=typeof t.query.isRefresh&&"1"==t.query.isRefresh)&&a((function(t){t.isLoading=!0,t.start_date="",t.end_date="",t.status="-1",t.dateValue=[],t.page=1,t.page_size=15,t.getAccount()})),a()}},d=c,p=(a("a8f8"),a("2877")),u=Object(p["a"])(d,n,s,!1,null,"0341fe4a",null);e["default"]=u.exports},"22ac":function(t,e,a){var n=a("a313");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var s=a("499e").default;s("35128cfb",n,!0,{sourceMap:!1,shadowMode:!1})},a313:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"[data-v-0341fe4a] .ant-card-bordered{border:0}.tpl-title[data-v-0341fe4a]{float:left;font-size:16px;vertical-align:top}.help[data-v-0341fe4a]{float:left;margin-left:18px}.help-icon[data-v-0341fe4a]{margin-right:5px;font-size:14px;margin-top:4px}.help-transition[data-v-0341fe4a]{-webkit-animation:help-data-v-0341fe4a 1s infinite;animation:help-data-v-0341fe4a 1s infinite}.help a[data-v-0341fe4a]{font-size:14px}.help a[data-v-0341fe4a]:link,.help a[data-v-0341fe4a]:visited{color:#1890ff;text-decoration:none}.help-transition1[data-v-0341fe4a]{-webkit-animation:help1-data-v-0341fe4a 1s infinite;animation:help1-data-v-0341fe4a 1s infinite}@-webkit-keyframes help-data-v-0341fe4a{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@keyframes help-data-v-0341fe4a{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@-webkit-keyframes help1-data-v-0341fe4a{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}@keyframes help1-data-v-0341fe4a{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}.content-bd[data-v-0341fe4a]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;margin:0 20px}[data-v-0341fe4a] .dark-row{background:#fafafa}[data-v-0341fe4a] .light-row{background:#fff}",""])},a8f8:function(t,e,a){"use strict";var n=a("22ac"),s=a.n(n);s.a}}]);