(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-21d947c2"],{4278:function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticStyle:{width:"100%","max-height":"100%",position:"absolute","overflow-y":"auto","padding-bottom":"30px"}},[n("div",[n("a-card",{staticStyle:{"margin-bottom":"20px",padding:"10px 20px","font-size":"16px"}},[n("label",{staticClass:"tpl-title"},[t._v("通知")])]),n("div",{staticClass:"content-bd"},[n("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[n("div",{staticClass:"spin-content"},[n("a-row",[n("div",{staticClass:"content-msg"},[n("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t\t用于将规则内的消息通过选择的应用提醒给规则接收者")])])]),n("a-row",{staticStyle:{"margin-bottom":"20px",padding:"0 20px"}},[t.corpInfo.length>1?n("a-col",{staticStyle:{float:"left"}},[n("a-select",{staticStyle:{width:"200px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleChange},model:{value:t.corpId,callback:function(e){t.corpId=e},expression:"corpId"}},[t._l(t.corpInfo,(function(e){return[n("a-select-option",{attrs:{value:e.corpid}},[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(e.corp_full_name||e.corp_name)+"\n\t\t\t\t\t\t\t\t\t")])]}))],2)],1):t._e(),n("a-col",{staticStyle:{float:"right"}},[n("a-button",{directives:[{name:"has",rawName:"v-has",value:"redirect-add",expression:"'redirect-add'"}],staticStyle:{width:"150px","font-size":"14px"},attrs:{type:"primary",icon:"plus",disabled:t.forbidden},on:{click:t.add}},[t._v("\n\t\t\t\t\t\t\t\t新建\n\t\t\t\t\t\t\t")])],1)],1),n("a-row",{staticStyle:{"margin-bottom":"20px",padding:"0 20px"}},[n("a-col",[n("div",{staticClass:"content-bd"},[n("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isTabLoading}},[n("a-table",{directives:[{name:"has",rawName:"v-has",value:"redirect-list",expression:"'redirect-list'"}],attrs:{columns:t.columns,dataSource:t.relationList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"notice_name",fn:function(e,a,r){return[n("span",[t._v(t._s(a.notice_name))])]}},{key:"agent_name",fn:function(e,r,s){return[t._l(r.info,(function(e){return[e.agent_info.square_logo_url?n("a-avatar",{staticStyle:{float:"left"},attrs:{shape:"square",src:e.agent_info.square_logo_url}}):t._e(),e.agent_info.square_logo_url?t._e():n("img",{staticStyle:{width:"32px",height:"32px",float:"left"},attrs:{src:a("4bef")}}),n("div",{staticStyle:{float:"left","max-width":"230px","word-wrap":"break-word","line-height":"32px",height:"32px"}},[n("p",{staticStyle:{"margin-left":"10px"}},[t._v(t._s(e.agent_info.name))])])]}))]}},{key:"categories",fn:function(e,a,r){return t._l(t.relationCategoryList["rule_"+a.id],(function(e){return n("a-tag",{attrs:{color:"orange"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e)+"\n\t\t\t\t\t\t\t\t\t\t\t")])}))}},{key:"notice_user",fn:function(e,a,r){return t._l(t.relationUserList["rule_"+a.id],(function(e){return n("a-tag",{attrs:{color:"blue"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e)+"\n\t\t\t\t\t\t\t\t\t\t\t")])}))}},{key:"status",fn:function(e,a,r){return[0==a.status?n("span",[t._v("已关闭")]):t._e(),1==a.status?n("span",[t._v("已开启")]):t._e()]}},{key:"action",fn:function(e,a,r){return[n("a-popconfirm",{attrs:{title:"确定开启吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.changeRelation(a.id,1)}}},[0==a.status?n("a-button",{directives:[{name:"has",rawName:"v-has",value:"redirect-close",expression:"'redirect-close'"}],staticStyle:{margin:"0px 5px 5px 0px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t开启\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1),n("a-popconfirm",{attrs:{title:"确定关闭吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.changeRelation(a.id,0)}}},[1==a.status?n("a-button",{directives:[{name:"has",rawName:"v-has",value:"redirect-close",expression:"'redirect-close'"}],staticStyle:{margin:"0px 5px 5px 0px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t关闭\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1),n("a-popconfirm",{attrs:{title:"确定删除吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.delRelation(a.id)}}},[n("a-button",{directives:[{name:"has",rawName:"v-has",value:"redirect-delete",expression:"'redirect-delete'"}],staticStyle:{margin:"0px 5px 5px 0px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t删除\n\t\t\t\t\t\t\t\t\t\t\t\t")])],1)]}}])})],1)],1),n("a-modal",{attrs:{title:"新建通知关系",width:"750px"},on:{cancel:t.handleCancel,ok:t.handleOk},model:{value:t.showAddModal,callback:function(e){t.showAddModal=e},expression:"showAddModal"}},[n("div",{staticStyle:{padding:"30px 10px","min-height":"300px",overflow:"auto"}},[n("a-form-item",{staticStyle:{"padding-top":"15px"},attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[n("span",{staticStyle:{color:"red"}},[t._v(" * ")]),t._v("规则名称\n\t\t\t\t\t\t\t\t\t\t")]),n("a-input",{staticStyle:{width:"60%"},attrs:{placeholder:"请填写规则名称",maxLength:16},model:{value:t.noticeName,callback:function(e){t.noticeName=e},expression:"noticeName"}},[n("span",{attrs:{slot:"suffix"},slot:"suffix"},[n("span",[t._v(t._s(t.noticeName.length))]),t._v("/16\n\t\t\t\t\t\t\t\t\t\t\t")])])],1),n("a-form-item",{staticStyle:{"padding-top":"15px"},attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[n("span",{staticStyle:{color:"red"}},[t._v(" * ")]),t._v("消息类别\n\t\t\t\t\t\t\t\t\t\t")]),t._l(t.categories,(function(e,a){return[n("a-checkable-tag",{key:e,attrs:{checked:t.selectedCategories.indexOf(e)>-1},on:{change:function(n){return t.handleChangeCategory(e,n,a)}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e)+"\n\t\t\t\t\t\t\t\t\t\t\t")])]}))],2),n("a-form-item",{staticStyle:{"padding-top":"15px"},attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[n("span",{staticStyle:{color:"red"}},[t._v(" * ")]),t._v("发送应用\n\t\t\t\t\t\t\t\t\t\t")]),n("a-select",{staticStyle:{width:"60%"},attrs:{showSearch:"",optionFilterProp:"children",placeholder:"请选择发送应用"},model:{value:t.agentId,callback:function(e){t.agentId=e},expression:"agentId"}},t._l(t.agentList,(function(e,a){return n("a-select-option",{key:e.id},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.name)+"\n\t\t\t\t\t\t\t\t\t\t\t")])})),1),n("div",[n("p",{staticStyle:{height:"20px","line-height":"20px","margin-bottom":"2px"}},[t._v("1、前往"),n("a",{attrs:{target:"_blank",href:"https://work.weixin.qq.com/wework_admin/loginpage_wx?from=myhome_baidu"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t企业微信后台")]),t._v("创建自建应用。"),n("a",{attrs:{target:"_blank",href:"https://support.qq.com/products/312071/faqs/90047"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t教程")])]),n("p",{staticStyle:{height:"20px","line-height":"20px","margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t2、选择此应用，请确认已设置过应用的可信域名。"),n("a",{staticClass:"el-link el-link--primary",attrs:{href:t.$store.state.commonUrl+"/upload/slider.png",target:"_blank"}},[n("span",{staticClass:"el-link--inner"},[t._v("查看图示")])])])])],1),n("a-form-item",{staticStyle:{"padding-top":"15px"},attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[n("span",{staticStyle:{color:"red"}},[t._v(" * ")]),t._v("接收成员\n\t\t\t\t\t\t\t\t\t\t")]),n("a-select",{staticStyle:{width:"60%"},attrs:{showSearch:"",mode:"multiple",value:t.userIds,optionFilterProp:"children",placeholder:"请选择"},on:{change:t.handleChangeUser}},t._l(t.userList,(function(e){return n("a-select-option",{key:e.id},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.name)+"\n\t\t\t\t\t\t\t\t\t\t\t")])})),1)],1),""!=t.agentId?[n("a-form-item",[n("a-alert",{attrs:{banner:""}},[n("span",{attrs:{slot:"message"},slot:"message"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t请务必保证该应用已开启 "),n("b",[t._v("接收消息")]),t._v(" 并且已经将下边的信息配置到 "),n("b",[t._v("API接收消息")])])])],1),n("a-form-item",{attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[t._v("应用接收消息URL")]),n("a-input",{staticStyle:{width:"calc(100% - 100px)"},attrs:{disabled:""},model:{value:t.$store.state.commonUrl+"/work/event/agent/"+t.agentId,callback:function(e){t.$set(t.$store.state,"commonUrl + '/work/event/agent/' + agentId",e)},expression:"$store.state.commonUrl + '/work/event/agent/' + agentId"}}),n("a-button",{staticStyle:{"margin-left":"5px"},attrs:{type:"primary"},on:{click:function(e){return t.copyText(t.$store.state.commonUrl+"/work/event/agent/"+t.agentId)}}},[t._v("复制")])],1),n("a-form-item",{attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[t._v("Token")]),n("a-input",{staticStyle:{width:"calc(100% - 100px)"},attrs:{disabled:""},model:{value:t.agentToken,callback:function(e){t.agentToken=e},expression:"agentToken"}}),n("a-button",{staticStyle:{"margin-left":"5px"},attrs:{type:"primary"},on:{click:function(e){return t.copyText(t.agentToken)}}},[t._v("复制")])],1),n("a-form-item",{attrs:{"label-col":{span:6},"wrapper-col":{span:18}}},[n("span",{attrs:{slot:"label"},slot:"label"},[t._v("EncodingAESKey")]),n("a-input",{staticStyle:{width:"calc(100% - 100px)"},attrs:{disabled:""},model:{value:t.agentEncodingAESKey,callback:function(e){t.agentEncodingAESKey=e},expression:"agentEncodingAESKey"}}),n("a-button",{staticStyle:{"margin-left":"5px"},attrs:{type:"primary"},on:{click:function(e){return t.copyText(t.agentEncodingAESKey)}}},[t._v("复制")])],1)]:t._e()],2)])],1)],1)],1)])],1)],1)])},r=[],s=a("75fc"),i=(a("7f7f"),a("96cf"),a("3b8d")),o=[{title:"规则名称",dataIndex:"notice_name",width:200,key:"notice_name",scopedSlots:{customRender:"notice_name"}},{title:"发送应用",dataIndex:"agent_name",key:"agent_name",scopedSlots:{customRender:"agent_name"}},{title:"类别",dataIndex:"categories",key:"categories",scopedSlots:{customRender:"categories"}},{title:"通知人",dataIndex:"notice_user",key:"notice_user",scopedSlots:{customRender:"notice_user"}},{title:"状态",dataIndex:"status",width:80,key:"status",scopedSlots:{customRender:"status"}},{title:"操作",dataIndex:"action",width:"18%",key:"action",scopedSlots:{customRender:"action"}}],c={name:"Rule",data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{forbidden:!1,isLoading:!0,isTabLoading:!1,corpId:t,corpInfo:[],categories:[],selectedCategories:[],selectedCategoryIds:[],agentId:[],agentList:[],agentToken:"",agentEncodingAESKey:"",noticeName:"",userIds:[],userList:[],columns:o,relationList:[],relationCategoryList:[],relationUserList:[],showAddModal:!1}},methods:{rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},getList:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-msg-audit/get-rule-list",{corp_id:this.corpId});case 2:e=t.sent,a=e.data,0!=a.error?(this.forbidden=!0,this.$message.destroy(),this.$message.error(a.error_msg)):(this.forbidden=!1,this.relationList=a.data,n=this,n.relationList.map((function(t){for(var e in"undefined"==typeof n.relationCategoryList["rule_"+t.id]&&(n.relationCategoryList["rule_"+t.id]=[]),"undefined"==typeof n.relationUserList["rule_"+t.id]&&(n.relationUserList["rule_"+t.id]=[]),t.info)for(var a in t.info[e].notice_info)for(var r in-1==n.relationCategoryList["rule_"+t.id].indexOf(t.info[e].notice_info[a].category_name)&&n.relationCategoryList["rule_"+t.id].push(t.info[e].notice_info[a].category_name),t.info[e].notice_info[a].user_info)-1==n.relationUserList["rule_"+t.id].indexOf(t.info[e].notice_info[a].user_info[r].user_info.name)&&n.relationUserList["rule_"+t.id].push(t.info[e].notice_info[a].user_info[r].user_info.name)}))),this.isLoading=!1,this.isTabLoading=!1;case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getCategories:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-msg-audit/get-categories");case 2:e=t.sent,a=e.data,0!=a.error?this.$message.error(a.error_msg):this.categories=a.data;case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),handleChange:function(t){var e=this;this.corpInfo.map((function(a){t==a.corpid&&(e.$store.commit("changeCorpAuthType",a.auth_type),e.$store.commit("changeCorpName",a.corp_name))})),this.$store.commit("changeCorpId",t),this.isTabLoading=!0,this.getList(),2==localStorage.getItem("isMasterAccount")&&this.$store.dispatch("getPermissionButton")},add:function(){this.isLoading=!0,this.addFunction()},addFunction:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-agent/list",{corp_id:this.corpId});case 2:if(e=t.sent,a=e.data,0==a.error){t.next=8;break}this.$message.error(a.error_msg),t.next=18;break;case 8:if(this.isLoading=!1,!(a.data.normal.custom.length>0)){t.next=15;break}this.agentList=a.data.normal.custom,this.agentToken=a.data.normal.token,this.agentEncodingAESKey=a.data.normal.encoding_AES_key,t.next=18;break;case 15:return n=this,this.$confirm({title:"无法创建",content:"你还没有创建可用的自建应用，前往【自建应用】创建",onOk:function(){n.$router.push("/agent/list")},onCancel:function(){console.log("cancel")},class:"choose-confirm-modal"}),t.abrupt("return",!1);case 18:this.showAddModal=!0;case 19:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getAgentUser:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-agent/get-user",{corp_id:this.corpId,agent_id:this.agentId});case 2:e=t.sent,a=e.data,0!=a.error?this.$message.error(a.error_msg):(this.userIds=[],this.userList=a.data.user_info);case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),handleCancel:function(){this.showAddModal=!1,this.noticeName="",this.userIds=[],this.agentId=[],this.agentList=[],this.userList=[],this.selectedCategories=[],this.selectedCategoryIds=[]},handleOk:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(""!=this.noticeName){t.next=3;break}return this.$message.error("规则名称不能为空"),t.abrupt("return",!1);case 3:if(0!=this.selectedCategoryIds.length){t.next=6;break}return this.$message.error("请选择消息类别"),t.abrupt("return",!1);case 6:if(""!=this.agentId){t.next=9;break}return this.$message.error("请选择发送应用"),t.abrupt("return",!1);case 9:if(0!=this.userIds.length){t.next=12;break}return this.$message.error("请选择接收成员"),t.abrupt("return",!1);case 12:return t.next=14,this.axios.post("work-msg-audit/set-rule",{corp_id:this.corpId,title:this.noticeName,agent_id:this.agentId,category_ids:this.selectedCategoryIds.join(","),users:this.userIds.join(",")});case 14:e=t.sent,a=e.data,0!=a.error?this.$message.error(a.error_msg):(this.$message.success("添加成功"),this.getList(),this.handleCancel());case 17:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changeRelation:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(e,a){var n,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-msg-audit/change-rule",{corp_id:this.corpId,rule_id:e,status:a});case 2:n=t.sent,r=n.data,0!=r.error?this.$message.error(r.error_msg):this.getList();case 5:case"end":return t.stop()}}),t,this)})));function e(e,a){return t.apply(this,arguments)}return e}(),delRelation:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(e){var a,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-msg-audit/del-rule",{corp_id:this.corpId,rule_id:e});case 2:a=t.sent,n=a.data,0!=n.error?this.$message.error(n.error_msg):this.getList();case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),handleChangeCategory:function(t,e,a){var n=this.selectedCategories,r=this.selectedCategoryIds,i=e?[].concat(Object(s["a"])(n),[t]):n.filter((function(e){return e!==t})),o=e?[].concat(Object(s["a"])(r),[a]):r.filter((function(t){return t!==a}));this.selectedCategories=i,this.selectedCategoryIds=o},handleChangeUser:function(t){this.userIds=t},copyText:function(t){var e=this,a=document.createElement("input");document.body.appendChild(a),a.setAttribute("value",t),a.select(),document.execCommand("copy")&&(document.execCommand("copy"),e.$message.success("复制成功！")),document.body.removeChild(a)}},created:function(){var t=this;this.getCategories(),this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.$emit("changeCorpId",t.corpId),t.getList()}))},watch:{agentId:function(t,e){""!=t&&t!=e&&this.getAgentUser()}}},l=c,d=(a("796f"),a("2877")),u=Object(d["a"])(l,n,r,!1,null,"3caffee8",null);e["default"]=u.exports},"4bef":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAABAElEQVR42s3VIY7CUBAG4LkEliC4xKJ6BA6AognXQBLCAVBYNBK3pim7zP8XBAJHSLhAZVURi+jC2+681xD4R89nZiYjIhojQRlQicYiolFQ8600EqZNAKbSpB0lyqcCBeYccIA5ihDgtO3JLdseTr7Ap9wFRx+g2HTuge8PH+AsjuBiBzZO4MsOHJzAwQ7kTiD3mcLsoX3muQcaV9s1DtnENUfssssR1i+6hRcD3GPFBaeccoEV9z7AUodoP4yxrUMs/wd26EtN0MeuBuBYDOH4LyDPWhYga1WXugpMxBhMnIBGVqD6S34B9nqvTUwaAUnNpZue689/DnuwTDUSuQJ1pL7YQ6lKkQAAAABJRU5ErkJggg=="},"796f":function(t,e,a){"use strict";var n=a("af14"),r=a.n(n);r.a},af14:function(t,e,a){var n=a("d49d");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var r=a("499e").default;r("65ac68c7",n,!0,{sourceMap:!1,shadowMode:!1})},d49d:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"[data-v-3caffee8] .dark-row{background:#fafafa}[data-v-3caffee8] .light-row{background:#fff}.content-msg[data-v-3caffee8]{border:1px solid #ffdda6;background:#fff2db;padding:10px;margin:0 20px 20px 20px}",""])}}]);