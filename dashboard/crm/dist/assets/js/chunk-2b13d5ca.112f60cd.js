(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2b13d5ca"],{"20d6":function(t,e,a){"use strict";var n=a("5ca1"),s=a("0a49")(6),i="findIndex",o=!0;i in[]&&Array(1)[i]((function(){o=!1})),n(n.P+n.F*o,"Array",{findIndex:function(t){return s(this,t,arguments.length>1?arguments[1]:void 0)}}),a("9c6c")(i)},"36bd":function(t,e,a){"use strict";var n=a("4bf8"),s=a("77f1"),i=a("9def");t.exports=function(t){var e=n(this),a=i(e.length),o=arguments.length,r=s(o>1?arguments[1]:void 0,a),c=o>2?arguments[2]:void 0,l=void 0===c?a:s(c,a);while(l>r)e[r++]=t;return e}},"4bef":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAABAElEQVR42s3VIY7CUBAG4LkEliC4xKJ6BA6AognXQBLCAVBYNBK3pim7zP8XBAJHSLhAZVURi+jC2+681xD4R89nZiYjIhojQRlQicYiolFQ8600EqZNAKbSpB0lyqcCBeYccIA5ihDgtO3JLdseTr7Ap9wFRx+g2HTuge8PH+AsjuBiBzZO4MsOHJzAwQ7kTiD3mcLsoX3muQcaV9s1DtnENUfssssR1i+6hRcD3GPFBaeccoEV9z7AUodoP4yxrUMs/wd26EtN0MeuBuBYDOH4LyDPWhYga1WXugpMxBhMnIBGVqD6S34B9nqvTUwaAUnNpZue689/DnuwTDUSuQJ1pL7YQ6lKkQAAAABJRU5ErkJggg=="},5056:function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"list"},[n("div",{attrs:{id:"components-layout-demo-basic"}},[n("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[n("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",top:"0",left:"0",bottom:"0",right:"0","overflow-x":"hidden","overflow-y":"auto"}},[n("a-layout-header",[t._v("跟进提醒")]),n("a-layout-content",[n("div",{staticClass:"content-msg"},[n("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t使用场景：一方面可以有效的帮助销售人员把握跟单节奏，系统会自动提醒销售该跟进的客户，让销售做好合理的安排调度，再也不用担心因为工作忙而忘记跟进客户，一方面也有利于管理层每日对销售人员客户跟进的情况了如指掌，在一定程度上起到监督作用。"),n("span",{staticStyle:{color:"#FF562D"}},[t._v("在设置前，需要先有自建应用，只有在该自建应用下的可见成员才具备接收应用消息通知的能力。")])])]),n("div",{staticClass:"content-hd"},[n("a-col",{staticStyle:{float:"left"}},[t.corpLen>1?n("a-select",{staticStyle:{width:"170px","margin-right":"5px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleWxId},model:{value:t.corpId,callback:function(e){t.corpId=e},expression:"corpId"}},[t._l(t.corpInfo,(function(e){return[n("a-select-option",{attrs:{value:e.corpid}},[t._v(t._s(e.corp_full_name||e.corp_name)+"\n\t\t\t\t\t\t\t\t\t")])]}))],2):t._e(),t.agentList.length>0?n("a-select",{staticStyle:{width:"170px","margin-bottom":"20px","margin-right":"5px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.changeAgentId},model:{value:t.agentId,callback:function(e){t.agentId=e},expression:"agentId"}},[t._l(t.agentList,(function(e){return[n("a-select-option",{attrs:{value:e.id}},[t._v(t._s(e.name))])]}))],2):t._e(),n("a-select",{staticStyle:{width:"165px","margin-bottom":"20px"},attrs:{showSearch:"",optionFilterProp:"children"},model:{value:t.status,callback:function(e){t.status=e},expression:"status"}},[n("a-select-option",{attrs:{value:-1}},[t._v("全部状态")]),n("a-select-option",{attrs:{value:1}},[t._v("已开启")]),n("a-select-option",{attrs:{value:0}},[t._v("已关闭")])],1),n("a-input",{staticStyle:{width:"165px",margin:"0 5px"},attrs:{placeholder:"员工姓名搜索"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.find(e)}},model:{value:t.name,callback:function(e){t.name=e},expression:"name"}}),n("a-button",{staticStyle:{margin:"0px 5px"},attrs:{type:"primary"},on:{click:t.find}},[t._v("查找")]),n("a-button",{on:{click:t.clear}},[t._v("清空")])],1),n("a-col",{staticStyle:{float:"right"}},[n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-hign",expression:"'follow-hign'"}],staticClass:"btn-primary",attrs:{icon:"setting",type:"primary"},on:{click:t.addSetting}},[t._v("\n\t\t\t\t\t\t\t\t高级设置\n\t\t\t\t\t\t\t")]),n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-rule",expression:"'follow-rule'"}],staticClass:"btn-primary",attrs:{icon:"plus",type:"primary"},on:{click:t.addFollow}},[t._v("\n\t\t\t\t\t\t\t\t新增\n\t\t\t\t\t\t\t")])],1)],1),n("div",{staticStyle:{"padding-bottom":"10px"}},[t._v("\n\t\t\t\t\t\t共\n\t\t\t\t\t\t"),n("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("\n\t\t\t\t\t\t条\n\t\t\t\t\t")]),n("div",{staticClass:"content-bd"},[n("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[n("a-table",{directives:[{name:"has",rawName:"v-has",value:"follow-list",expression:"'follow-list'"}],attrs:{columns:t.columns,dataSource:t.userList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"user",fn:function(e,s,i){return n("span",{},[s.avatar?n("a-avatar",{staticStyle:{float:"left",height:"42px",width:"42px"},attrs:{shape:"square",src:s.avatar}}):t._e(),s.avatar?t._e():n("img",{staticStyle:{width:"42px",height:"42px",float:"left"},attrs:{src:a("4bef")}}),n("div",{staticStyle:{float:"left","max-width":"270px","word-wrap":"break-word","line-height":"32px"}},[n("a-popover",{attrs:{placement:"top"}},[n("span",{attrs:{slot:"content"},slot:"content"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(s.name)+"\n\t\t\t\t\t\t\t\t\t\t\t\t")]),n("p",{staticStyle:{display:"inline-block","margin-bottom":"0px","margin-left":"10px","max-width":"140px",overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis"}},[t._v(t._s(s.name))])])],1),n("span",{staticStyle:{"line-height":"32px"}},[1==s.sex?n("a-icon",{staticStyle:{"margin-left":"10px",color:"#427EBA"},attrs:{slot:"prefix",type:"man"},slot:"prefix"}):t._e(),2==s.sex?n("a-icon",{staticStyle:{"margin-left":"10px",color:"#ED4997"},attrs:{slot:"prefix",type:"woman"},slot:"prefix"}):t._e()],1)],1)}},{key:"checkedBox",fn:function(e,a,s){return n("span",{},[n("a-checkbox",{on:{click:function(e){return t.changeSelectKey(a.user_id)}},model:{value:t.checkBoxValue[s],callback:function(e){t.$set(t.checkBoxValue,s,e)},expression:"checkBoxValue[index]"}})],1)}},{key:"follow_name",fn:function(e,a){return n("span",{},[a.follow_name?n("span",[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(a.follow_name)+"\n\t\t\t\t\t\t\t\t\t")]):t._e(),a.follow_name?t._e():n("span",[t._v("--")])])}},{key:"send_time",fn:function(e,a){return n("span",{},[t._l(a.send_time,(function(e,s){return n("span",[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(e)),s!=a.send_time.length-1?n("span",[t._v("、")]):t._e()])})),0==a.send_time.length?n("span",[t._v("--")]):t._e()],2)}},{key:"send_content",fn:function(e,a){return n("span",{},[0!=a.send_content.length?n("a-popover",{attrs:{placement:"left"}},[n("span",{attrs:{slot:"content"},slot:"content"},[n("div",{staticStyle:{"max-height":"500px","overflow-y":"auto"}},[n("p",{staticStyle:{display:"inline-block","max-width":"500px","word-wrap":"break-word","word-break":"break-all"},domProps:{innerHTML:t._s(t.replaceContent(a.send_content))}})])]),n("span",{staticStyle:{color:"#1890FF",cursor:"pointer"}},[t._v("预览")])]):t._e(),0==a.send_content.length?n("span",[t._v("--")]):t._e()],1)}},{key:"status",fn:function(e,a){return n("span",{},[0==a.status?n("span",[t._v("已关闭")]):t._e(),1==a.status?n("span",[t._v("已开启")]):t._e()])}},{key:"action",fn:function(e,a,s){return n("span",{},[a.follow_id&&1==a.status?n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-open",expression:"'follow-open'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.failure(a.user_id)}}},[t._v("关闭")]):t._e(),a.follow_id&&0==a.status?n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-open",expression:"'follow-open'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.releaseFollow(a.user_id)}}},[t._v("开启")]):t._e(),n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-edit",expression:"'follow-edit'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.edit(a.user_id)}}},[t._v("编辑")])],1)}}])},[n("span",{attrs:{slot:"checkedBoxTitle"},slot:"checkedBoxTitle"})]),n("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"follow-list",expression:"'follow-list'"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[n("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[n("a-checkbox",{on:{click:t.batchTypeChange},model:{value:t.batchTypeValue,callback:function(e){t.batchTypeValue=e},expression:"batchTypeValue"}}),n("a-select",{staticStyle:{width:"150px",margin:"0 5px"},attrs:{optionFilterProp:"children"},on:{change:t.changeBatchType},model:{value:t.batchType,callback:function(e){t.batchType=e},expression:"batchType"}},[n("a-select-option",{attrs:{value:"0"}},[t._v("选择当前页")]),n("a-select-option",{attrs:{value:"1"}},[t._v("选择所有")])],1),n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-edit",expression:"'follow-edit'"}],staticClass:"btn-primary",attrs:{icon:"edit",disabled:0==t.selectedRowKeys.length,type:"primary"},on:{click:t.addFollowMore}},[t._v("\n\t\t\t\t\t\t\t\t\t\t批量编辑\n\t\t\t\t\t\t\t\t\t")]),n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-open",expression:"'follow-open'"}],staticClass:"btn-primary",attrs:{icon:"unlock",disabled:0==t.selectedRowKeys.length,type:"primary"},on:{click:function(e){return t.releaseFollow("")}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t批量开启\n\t\t\t\t\t\t\t\t\t")]),n("a-button",{directives:[{name:"has",rawName:"v-has",value:"follow-open",expression:"'follow-open'"}],staticClass:"btn-primary",attrs:{icon:"lock",disabled:0==t.selectedRowKeys.length,type:"primary"},on:{click:function(e){return t.failure("")}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t批量关闭\n\t\t\t\t\t\t\t\t\t")])],1),n("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[n("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])],1)],1)])],1)],1),n("a-modal",{attrs:{title:"高级设置"},on:{cancel:t.cancleSetting},model:{value:t.settingVisible,callback:function(e){t.settingVisible=e},expression:"settingVisible"}},[n("template",{slot:"footer"},[n("a-button",{key:"back",on:{click:t.cancleSetting}},[t._v("取消")]),n("a-button",{key:"submit",attrs:{type:"primary",loading:t.setLoading},on:{click:t.handleSetitng}},[t._v("提交\n\t\t\t\t")])],1),n("div",{staticStyle:{color:"#FF562D"}},[t._v("\n\t\t\t\t超过天数统计未跟进人数：距上次添加跟进记录的时间到当前的时间差。比如上次跟进时间是1号10:30，当前时间是2号09:30，未超过1天，但是如果当前时间在2号10:31，则超过1天未跟进\n\t\t\t")]),n("div",{staticClass:"setting-day"},[t._v("\n\t\t\t\t超过 1 天未跟进人数\n\t\t\t")]),n("div",{staticClass:"setting-day"},[t._v("\n\t\t\t\t超过 3 天未跟进人数\n\t\t\t")]),t._l(t.settingList1,(function(e,a){return n("div",{staticClass:"setting-day"},[t._v("\n\t\t\t\t超过\n\t\t\t\t"),n("a-input-number",{staticStyle:{width:"100px","margin-right":"5px"},attrs:{min:1,step:0,precision:0},model:{value:e.day,callback:function(a){t.$set(e,"day",a)},expression:"item.day"}}),t._v("\n\t\t\t\t天未跟进人数\n\t\t\t\t"),n("span",{staticStyle:{"margin-left":"15px",cursor:"pointer",color:"#FF562D"},on:{click:function(e){return t.removeSet(a)}}},[t._v("删除\n\t\t\t\t")])],1)})),n("div",{staticStyle:{"text-align":"center"}},[n("a-button",{attrs:{type:"primary"},on:{click:t.addSet}},[t._v("添加")])],1)],2),n("linkAgent",{attrs:{corpId:t.corpId,showAddAgent:t.showAddAgentModal,agent_is_money:0},on:{addOk:t.addOk,linkOk:t.linkOk,addCancel:t.addCancel}})],1)])},s=[],i=(a("a481"),a("7f7f"),a("ac4d"),a("8a81"),a("ac6a"),a("96cf"),a("3b8d")),o=(a("20d6"),a("6c7b"),a("d77d")),r=a("641c"),c=(a("c1df"),a("8ba3")),l=[{dataIndex:"checkedBox",key:"checkedBox",width:"9%",scopedSlots:{customRender:"checkedBox",title:"checkedBoxTitle"}},{title:"企业成员",dataIndex:"user",key:"user",width:270,scopedSlots:{customRender:"user"}},{title:"所属部门",dataIndex:"department_name",key:"department_name",width:"12%"},{title:"查看员工数据",dataIndex:"follow_name",key:"follow_name",width:"16%",scopedSlots:{customRender:"follow_name"}},{title:"推送时间",dataIndex:"send_time",width:"10%",key:"send_time",scopedSlots:{customRender:"send_time"}},{title:"提醒文案",dataIndex:"send_content",width:"12.8%",key:"send_content",scopedSlots:{customRender:"send_content"}},{title:"状态",dataIndex:"status",width:"10%",key:"status",scopedSlots:{customRender:"status"}},{title:"操作",dataIndex:"action",key:"action",width:"12.8%",scopedSlots:{customRender:"action"}}],d={name:"fissionList",components:{eWechat:o["a"],MyIcon:r["a"],linkAgent:c["a"]},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{suite_id:1,corpId:t,corpInfo:[],agentId:"",status:-1,name:"",agentList:[],settingVisible:!1,showAddAgentModal:!1,setLoading:!1,settingList:[],settingList1:[],batchType:"0",batchTypeValue:!1,selectedRowKeys:[],checkBoxValue:[],userKeys:[],userList:[],isLoading:!0,corpLen:JSON.parse(localStorage.getItem("corpArr")).length,columns:l,total:0,quickJumper:!1,page:1,page_size:15,pageSize:15}},methods:{handleWxId:function(t){this.corpId=t,this.getAgentList()},rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},find:function(){this.isLoading=!0,this.getUserList()},clear:function(){this.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",this.isLoading=!0,this.name="",this.status=-1,this.page=1,this.pageSize=15,this.getAgentList()},changeSelectKey:function(t){for(var e=0;e<this.selectedRowKeys.length;e++)if(t==this.selectedRowKeys[e])return this.selectedRowKeys.splice(e,1),this.setBox(),!1;this.selectedRowKeys.push(t),this.setBox()},batchTypeChange:function(){this.batchTypeValue?(0==this.batchType?(this.removeKey(this.userList),this.setCheckedBox(this.userList)):1==this.batchType&&(this.selectedRowKeys=[],this.setBox()),this.batchTypeValue=!1):(0==this.batchType?(this.addKey(this.userList),this.setCheckedBox(this.userList)):1==this.batchType&&(this.selectedRowKeys=JSON.parse(JSON.stringify(this.userKeys)),this.setBox()),this.batchTypeValue=!0)},setBox:function(){this.setCheckedBox(this.userList)},setCheckedBox:function(t){var e=this;this.checkBoxValue=new Array(t.length),this.checkBoxValue.fill(!1);for(var a=0;a<t.length;a++)for(var n=0;n<this.selectedRowKeys.length;n++)t[a].user_id==this.selectedRowKeys[n]&&(this.checkBoxValue[a]=!0);0==this.batchType?(this.batchTypeValue=!0,this.checkBoxValue.map((function(t){t||(e.batchTypeValue=!1)}))):this.selectedRowKeys.length==this.userKeys.length&&0!=this.userKeys.length?this.batchTypeValue=!0:this.batchTypeValue=!1,0==t.length&&(this.batchTypeValue=!1)},changeBatchType:function(t){this.batchType=t,this.batchTypeValue?(0==this.batchType&&(this.selectedRowKeys=[]),this.batchTypeValue=!1,this.batchTypeChange()):this.setBox()},addKey:function(t){for(var e=this,a=function(a){var n=e.selectedRowKeys.findIndex((function(e){return e===t[a].user_id}));-1==n&&e.selectedRowKeys.push(t[a].user_id)},n=0;n<t.length;n++)a(n)},removeKey:function(t){for(var e=this,a=function(a){var n=e.selectedRowKeys.findIndex((function(e){return e===t[a].user_id}));-1!=n&&e.selectedRowKeys.splice(n,1)},n=0;n<t.length;n++)a(n)},cancleSetting:function(){this.settingList1=JSON.parse(JSON.stringify(this.settingList)),this.settingVisible=!1},handleSetitng:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n,s,i,o,r,c,l,d,p;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:this.setLoading=!0,e=[],a=!0,n=!1,s=void 0,t.prev=5,i=this.settingList1[Symbol.iterator]();case 7:if(a=(o=i.next()).done){t.next=17;break}if(r=o.value,!(r.day<1)){t.next=13;break}return this.$message.warning("请输入正确的天数"),this.setLoading=!1,t.abrupt("return",!1);case 13:e.push(r.day);case 14:a=!0,t.next=7;break;case 17:t.next=23;break;case 19:t.prev=19,t.t0=t["catch"](5),n=!0,s=t.t0;case 23:t.prev=23,t.prev=24,a||null==i.return||i.return();case 26:if(t.prev=26,!n){t.next=29;break}throw s;case 29:return t.finish(26);case 30:return t.finish(23);case 31:c=0;case 32:if(!(c<e.length)){t.next=49;break}if(1!=e[c]&&3!=e[c]){t.next=37;break}return this.$message.warning("时间存在重复"),this.setLoading=!1,t.abrupt("return",!1);case 37:l=c+1;case 38:if(!(l<e.length)){t.next=46;break}if(e[c]!=e[l]){t.next=43;break}return this.$message.warning("时间存在重复"),this.setLoading=!1,t.abrupt("return",!1);case 43:l++,t.next=38;break;case 46:c++,t.next=32;break;case 49:return t.next=51,this.axios.post("work-follow-msg/not-follow-day-post",{uid:localStorage.getItem("uid"),dayArr:e});case 51:d=t.sent,p=d.data,0!=p.error?(this.setLoading=!1,this.$message.error(p.error_msg)):(this.settingList=JSON.parse(JSON.stringify(this.settingList1)),this.settingVisible=!1,this.setLoading=!1);case 54:case"end":return t.stop()}}),t,this,[[5,19,23,31],[24,,26,30]])})));function e(){return t.apply(this,arguments)}return e}(),addSetting:function(){0==this.agentList.length?this.showAddAgentModal=!0:(this.settingList1=JSON.parse(JSON.stringify(this.settingList)),this.settingVisible=!0)},addOk:function(){this.showAddAgentModal=!1,this.getAgentList(),this.settingList1=JSON.parse(JSON.stringify(this.settingList)),this.settingVisible=!0},linkOk:function(){this.showAddAgentModal=!1,this.getAgentList()},addCancel:function(){this.showAddAgentModal=!1},removeSet:function(t){this.settingList1.splice(t,1)},addSet:function(){this.settingList1.push({day:1})},addFollow:function(){0==this.agentList.length?this.showAddAgentModal=!0:this.$router.push("/follow/add?agentid="+this.agentId+"&corpid="+this.corpId)},addFollowMore:function(){if(0==this.agentList.length)this.showAddAgentModal=!0;else{var t="",e=!0,a=!1,n=void 0;try{for(var s,i=this.selectedRowKeys[Symbol.iterator]();!(e=(s=i.next()).done);e=!0){var o=s.value;t+=o+","}}catch(r){a=!0,n=r}finally{try{e||null==i.return||i.return()}finally{if(a)throw n}}t.length>0&&(t=t.substring(0,t.length-1)),this.$router.push("/follow/add?id="+t+"&agentid="+this.agentId+"&corpid="+this.corpId)}},getAgentList:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-group-sending/agent-list",{corp_id:this.corpId,agent_type:2,suite_id:this.suite_id});case 2:e=t.sent,a=e.data,0!=a.error?(this.isLoading=!1,this.$message.error(a.error_msg)):(this.agentList=a.data,this.agentList.length>0?(this.agentId=this.agentList[0].id,this.getUserList()):(this.agentList=a.data,this.isLoading=!1));case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getSettingList:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n,s,i,o,r,c;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-follow-msg/not-follow-day-list",{uid:localStorage.getItem("uid")});case 2:if(e=t.sent,a=e.data,0==a.error){t.next=9;break}this.isLoading=!1,this.$message.error(a.error_msg),t.next=29;break;case 9:for(this.settingList=[],n=!0,s=!1,i=void 0,t.prev=13,o=a.data[Symbol.iterator]();!(n=(r=o.next()).done);n=!0)c=r.value,this.settingList.push({day:c});t.next=21;break;case 17:t.prev=17,t.t0=t["catch"](13),s=!0,i=t.t0;case 21:t.prev=21,t.prev=22,n||null==o.return||o.return();case 24:if(t.prev=24,!s){t.next=27;break}throw i;case 27:return t.finish(24);case 28:return t.finish(21);case 29:case"end":return t.stop()}}),t,this,[[13,17,21,29],[22,,24,28]])})));function e(){return t.apply(this,arguments)}return e}(),changeAgentId:function(){this.selectedRowKeys=[],this.getUserList()},getUserList:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n,s,i=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=i.length>0&&void 0!==i[0]?i[0]:1,a=i.length>1&&void 0!==i[1]?i[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("work-follow-msg/follow-user-list",{corp_id:this.corpId,agentid:this.agentId,name:this.name,status:this.status,page:e,page_size:a});case 5:n=t.sent,s=n.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.error_msg)):(this.userList=s.data.list,this.userKeys=s.data.keys,this.isLoading=!1,this.total=parseInt(s.data.count),this.page=e,this.pageSize=a,this.quickJumper=this.total>this.pageSize,this.setCheckedBox(this.userList));case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),replaceContent:function(t){return"<p>"+t.replace(/{username}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">员工姓名</span>&nbsp;</span>').replace(/{newMemberNum}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">新增客户数</span>&nbsp;</span>').replace(/{followMemberNum}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">已跟进人数</span>&nbsp;</span>').replace(/{followNum}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">已跟进条数</span>&nbsp;</span>').replace(/{changeFollowNum}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">当前状态变化人数</span>&nbsp;</span>').replace(/{notChangeNum}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">当前阶段状态未改人数</span>&nbsp;</span>').replace(/{sendTime}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">发送时间点</span>&nbsp;</span>').replace(/{followUser}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">可见员工</span>&nbsp;</span>').replace(/{notFollowDay_([\d]*)}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">超过$1天数未跟进人数</span>&nbsp;</span>').replace(/{follow_id}/g,'<span>&nbsp;<span contenteditable="false" class="ant-tag ant-tag-orange">当前状态</span>&nbsp;</span>').replace(/\n/g,"<br>")+"</p>"},failure:function(t){var e=this;e.$confirm({title:"确定关闭该提醒？",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.isLoading=!0;var a=[];t?a.push(t):a=e.selectedRowKeys,e.changeStatus(a,0)}})},releaseFollow:function(t){var e=this;e.$confirm({title:"确定开启该提醒？",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.isLoading=!0;var a=[];t?a.push(t):a=e.selectedRowKeys,e.changeStatus(a,1)}})},changeStatus:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(e,a){var n,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-follow-msg/follow-user-set-status",{corp_id:this.corpId,agentid:this.agentId,follow_id:e,status:a});case 2:n=t.sent,s=n.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.error_msg)):(this.selectedRowKeys=[],this.getUserList(this.page,this.pageSize));case 5:case"end":return t.stop()}}),t,this)})));function e(e,a){return t.apply(this,arguments)}return e}(),edit:function(t){this.$router.push("/follow/add?id="+t+"&agentid="+this.agentId+"&corpid="+this.corpId)},changePage:function(t,e){this.isLoading=!0,this.getUserList(t,e),document.getElementsByClassName("scroll")[0].scrollTo(0,0)},showSizeChange:function(t,e){this.isLoading=!0,this.getUserList(1,e)}},mounted:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.isLoading=!0,t.selectedRowKeys=[],t.corpInfo.length>0&&(t.getSettingList(),t.getAgentList())}))},beforeRouteEnter:function(t,e,a){"/follow/add"==e.path&&"1"==t.query.isRefresh?a((function(t){t.selectedRowKeys=[],t.getUserList(t.page,t.pageSize)})):a((function(t){t.isLoading=!0,t.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",t.name="",t.selectedRowKeys=[],t.page=1,t.pageSize=15,t.getAgentList()}))}},p=d,h=(a("e2f9"),a("2877")),u=Object(h["a"])(p,n,s,!1,null,"201e7b8f",null);e["default"]=u.exports},"56b6":function(t,e,a){"use strict";var n=a("b0fe"),s=a.n(n);s.a},"6c7b":function(t,e,a){var n=a("5ca1");n(n.P,"Array",{fill:a("36bd")}),a("9c6c")("fill")},"8ab1":function(t,e,a){var n=a("b736");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var s=a("499e").default;s("578407e8",n,!0,{sourceMap:!1,shadowMode:!1})},"8ba3":function(t,e,a){"use strict";var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("a-modal",{attrs:{title:"关联应用",width:"600px"},on:{ok:t.addAgentOk,cancel:t.addAgentCancel},model:{value:t.showAddAgentModal,callback:function(e){t.showAddAgentModal=e},expression:"showAddAgentModal"}},[a("div",{staticClass:"content-msg",staticStyle:{"margin-top":"0","margin-bottom":"15px"}},[a("p",{staticStyle:{"margin-bottom":"5px"}},[t._v("\n\t\t\t\t请登录企业微信官方后台，在应用管理-应用-自建应用，找到您已建好应用的AgentId和Secret，并复制到下面的输入框。提交后将该应用添加到本系统里。\n\t\t\t")]),a("p",{staticStyle:{height:"20px","line-height":"20px","margin-bottom":"2px"}},[t._v("1、前往"),a("a",{attrs:{target:"_blank",href:"https://work.weixin.qq.com/wework_admin/loginpage_wx?from=myhome_baidu"}},[t._v("\n\t\t\t\t企业微信后台")]),t._v("创建自建应用。"),a("a",{attrs:{target:"_blank",href:"https://support.qq.com/products/312071/faqs/90047"}},[t._v("\n\t\t\t\t教程")])]),a("p",{staticStyle:{"line-height":"20px",height:"20px","margin-bottom":"0px"}},[t._v("\n\t\t\t\t2、选择此应用，请确认已设置过应用的可信域名。\n\t\t\t\t"),a("a",{staticClass:"el-link el-link--primary",attrs:{href:t.$store.state.commonUrl+"/upload/slider.png",target:"_blank"}},[a("span",{staticClass:"el-link--inner"},[t._v("查看图示")])])])]),t.agentList.length>0?a("a-form-item",{attrs:{"label-col":{span:4},"wrapper-col":{span:18}}},[a("template",{slot:"label"},[t._v("选择应用\n\t\t\t")]),a("a-radio-group",{model:{value:t.agentType,callback:function(e){t.agentType=e},expression:"agentType"}},[a("a-radio",{attrs:{value:1}},[t._v("选择已有")]),a("a-radio",{attrs:{value:2}},[t._v("新建应用")])],1)],2):t._e(),1==t.agentType?[a("a-select",{staticStyle:{width:"200px","margin-left":"90px"},attrs:{showSearch:"",optionFilterProp:"children"},model:{value:t.agent_id,callback:function(e){t.agent_id=e},expression:"agent_id"}},[t._l(t.agentList,(function(e){return[a("a-select-option",{attrs:{value:e.id}},[t._v(t._s(e.name)+"\n\t\t\t\t\t")])]}))],2)]:t._e(),2==t.agentType?[a("a-form-item",{attrs:{"label-col":{span:4},"wrapper-col":{span:18}}},[a("template",{slot:"label"},[a("span",{staticStyle:{color:"red"}},[t._v(" * ")]),t._v("应用Id\n\t\t\t\t")]),a("a-input",{attrs:{placeholder:"请输入自建应用的AgentId"},model:{value:t.addAgentId,callback:function(e){t.addAgentId=e},expression:"addAgentId"}})],2),a("a-form-item",{attrs:{"label-col":{span:4},"wrapper-col":{span:18}}},[a("template",{slot:"label"},[a("span",{staticStyle:{color:"red"}},[t._v(" * ")]),t._v("应用Secret\n\t\t\t\t")]),a("a-input",{attrs:{placeholder:"请输入自建应用的Secret"},model:{value:t.addAgentSecret,callback:function(e){t.addAgentSecret=e},expression:"addAgentSecret"}})],2)]:t._e()],2)],1)},s=[],i=(a("96cf"),a("3b8d")),o=(a("c5f6"),{props:{showAddAgent:{type:Boolean,default:!1},agent_is_money:{type:Number,default:1},corpId:{type:String,default:localStorage.getItem("corpId")}},name:"index",data:function(){return{addAgentId:"",addAgentSecret:"",agentList:[],agent_id:"",suite_id:1,agentType:1,showAddAgentModal:!1}},mounted:function(){this.showAddAgentModal=this.showAddAgent},methods:{getAgentList:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-group-sending/agent-list",{corp_id:this.corpId,suite_id:this.suite_id,agent_type:2});case 2:e=t.sent,a=e.data,0!=a.error?(this.isLoading=!1,this.$message.error(a.error_msg)):a.data&&a.data.length>0?(this.agentList=a.data,this.agent_id=this.agentList[0].id,this.agentType=1):this.agentType=2;case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),addAgentOk:function(){if(1==this.agentType)this.linkAgent();else if(2==this.agentType){if(""==this.addAgentId)return this.$message.error("请输入应用Id！"),!1;if(""==this.addAgentSecret)return this.$message.error("请输入应用Secret！"),!1;this.addAgent()}},linkAgent:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-agent/update-agent-use",{corp_id:this.corpId,agent_id:this.agent_id,agent_is_money:this.agent_is_money});case 2:e=t.sent,a=e.data,0!=a.error?this.$message.error("关联失败，请检查是否正确！"):(this.$message.success("关联成功！"),this.$emit("linkOk"));case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),addAgent:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-agent/add",{corp_id:this.corpId,agent_id:this.addAgentId,agent_secret:this.addAgentSecret,agent_is_money:this.agent_is_money});case 2:if(e=t.sent,a=e.data,0==a.error){t.next=8;break}this.$message.error("添加失败，请检查是否正确！"),t.next=13;break;case 8:return t.next=10,this.axios.post("work-agent/get",{corp_id:this.corpId,agent_id:a.data.agent_id});case 10:n=t.sent,s=n.data,0!=s.error?this.$message.error("添加失败，请检查是否正确！错误描述："+s.error_msg):(this.addAgentId="",this.addAgentSecret="",this.$message.success("添加成功！"),this.$emit("addOk"));case 13:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),addAgentCancel:function(){this.addAgentId="",this.addAgentSecret="",this.$emit("addCancel")}},watch:{showAddAgent:{handler:function(t){this.showAddAgentModal=t,this.getAgentList()},deep:!0}}}),r=o,c=a("2877"),l=Object(c["a"])(r,n,s,!1,null,"775cf187",null);e["a"]=l.exports},b0fe:function(t,e,a){var n=a("fe92");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var s=a("499e").default;s("1ee44db8",n,!0,{sourceMap:!1,shadowMode:!1})},b736:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"#components-layout-demo-basic[data-v-201e7b8f]{height:100%}#components-layout-demo-basic .ant-layout-header[data-v-201e7b8f]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px}[data-v-201e7b8f] .ant-layout-header{padding:0 20px;font-size:16px;text-align:left}#components-layout-demo-basic .ant-layout-sider[data-v-201e7b8f]{background:#fff;-webkit-box-flex:0!important;-ms-flex:0 0 250px!important;flex:0 0 250px!important;max-width:250px!important;min-width:250px!important;width:250px!important;border-right:1px solid #e2e2e2}#components-layout-demo-basic .ant-layout-content[data-v-201e7b8f]{margin:0 20px 20px;min-width:885px;width:100%;padding-right:40px}.content-hd[data-v-201e7b8f]{height:60px;width:100%;min-width:885px;line-height:60px;overflow:hidden}.content-msg[data-v-201e7b8f]{width:100%;border:1px solid #ffdda6;background:#fff2db;padding:10px;margin-top:12px}.content-bd[data-v-201e7b8f]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;width:100%}#components-layout-demo-basic>.ant-layout[data-v-201e7b8f]{margin-bottom:48px}#components-layout-demo-basic>.ant-layout[data-v-201e7b8f]:last-child{margin:0}.ant-layout.ant-layout-has-sider[data-v-201e7b8f],.list[data-v-201e7b8f]{height:100%;overflow:hidden}.btn-primary[data-v-201e7b8f]{margin-left:20px}.setting-day[data-v-201e7b8f]{margin:10px}[data-v-201e7b8f] .dark-row{background:#fafafa}[data-v-201e7b8f] .light-row{background:#fff}[data-v-201e7b8f] .ant-radio-button-wrapper{width:90px;margin:0;text-align:center}[data-v-201e7b8f] .ant-input-number-handler-wrap{display:none}",""])},d77d:function(t,e,a){"use strict";var n=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"sider-one"},[n("div",{staticClass:"sider-one-txt"},[t._v("选择企业微信")]),t.corpInfo[0]?n("a-select",{staticStyle:{width:"200px","margin-bottom":"20px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleChange},model:{value:t.changeBackground,callback:function(e){t.changeBackground=e},expression:"changeBackground"}},[t._l(t.corpInfo,(function(e){return[n("a-select-option",{attrs:{value:e.corpid}},[t._v(t._s(e.corp_name))])]}))],2):t._e(),n("div",{ref:"corpInfo",staticClass:"wx-info",staticStyle:{position:"absolute"},on:{mousemove:t.corpInfoMouseOver,mouseout:t.corpInfoMouseOut}},[t._l(t.corpInfo,(function(e,s){return[n("div",{staticClass:"selectWx",class:{active:t.changeBackground==e.corpid},on:{click:function(a){return t.selectWx(e.corpid)}}},[n("img",{staticStyle:{width:"32px","border-radius":"4px"},attrs:{src:a("de05"),alt:""}}),n("span",{staticStyle:{"text-overflow":"ellipsis","white-space":"nowrap",overflow:"hidden",float:"right",width:"calc(100% - 35px)"}},[t._v(t._s(e.corp_name))])])]}))],2)],1)},s=[],i={name:"eWechat",props:{callback:{type:Function,default:null}},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpInfo:[],changeBackground:t}},created:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,null!==t.callback&&"function"===typeof t.callback&&t.callback(t.changeBackground)}))},methods:{handleChange:function(t){var e=this,a="";this.corpInfo.map((function(n){t==n.corpid&&(a=n.corp_name,e.$store.dispatch("changeCorp",n))})),this.changeBackground=t,this.$emit("changeWxId",t,a),2==localStorage.getItem("isMasterAccount")&&this.$store.dispatch("getPermissionButton")},selectWx:function(t){this.handleChange(t)},corpInfoMouseOver:function(){this.$refs.corpInfo.style.overflowY="auto"},corpInfoMouseOut:function(){this.$refs.corpInfo.style.overflowY="hidden"}}},o=i,r=(a("56b6"),a("2877")),c=Object(r["a"])(o,n,s,!1,null,"2e9ecca2",null);e["a"]=c.exports},e2f9:function(t,e,a){"use strict";var n=a("8ab1"),s=a.n(n);s.a},fe92:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".sider-one[data-v-2e9ecca2]{padding:0 20px}.sider-one-txt[data-v-2e9ecca2]{height:60px;line-height:60px;text-align:left}.wx-info[data-v-2e9ecca2]{width:100%;position:absolute;left:0;top:100px;bottom:0;overflow:hidden}.wx-info[data-v-2e9ecca2]::-webkit-scrollbar{width:2px;height:100%}.wx-info[data-v-2e9ecca2]::-webkit-scrollbar-track-piece{background-color:#fff}.wx-info[data-v-2e9ecca2]::-webkit-scrollbar-thumb{background:#fff}.selectWx[data-v-2e9ecca2]{width:200px;margin-left:20px;height:50px;line-height:50px;cursor:pointer;padding-left:10px}.active[data-v-2e9ecca2]{background:#1e90ff;color:#fff}",""])}}]);