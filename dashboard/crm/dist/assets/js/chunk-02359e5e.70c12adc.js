(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-02359e5e"],{"1a40":function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"i[data-v-cef5695e]{font-style:normal}.content[data-v-cef5695e]{width:100%}[data-v-cef5695e] .ant-card-bordered{border:0}.tpl-title[data-v-cef5695e]{font-size:16px}.help[data-v-cef5695e]{float:left;margin-left:18px}.help-icon[data-v-cef5695e]{margin-right:5px;font-size:14px;margin-top:4px}.help-transition[data-v-cef5695e]{-webkit-animation:help-data-v-cef5695e 1s infinite;animation:help-data-v-cef5695e 1s infinite}.help a[data-v-cef5695e]{font-size:14px}.help a[data-v-cef5695e]:link,.help a[data-v-cef5695e]:visited{color:#1890ff;text-decoration:none}.help-transition1[data-v-cef5695e]{-webkit-animation:help1-data-v-cef5695e 1s infinite;animation:help1-data-v-cef5695e 1s infinite}@-webkit-keyframes help-data-v-cef5695e{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@keyframes help-data-v-cef5695e{0%{-webkit-transform:translate(0);transform:translate(0)}50%{-webkit-transform:translateY(-8px);transform:translateY(-8px)}to{-webkit-transform:translateY(3px);transform:translateY(3px)}}@-webkit-keyframes help1-data-v-cef5695e{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}@keyframes help1-data-v-cef5695e{0%{-webkit-transform:translateY(3px);transform:translateY(3px)}50%{-webkit-transform:translateY(-4px);transform:translateY(-4px)}to{-webkit-transform:translate(0);transform:translate(0)}}.content-bd[data-v-cef5695e]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;margin:0 20px}[data-v-cef5695e] .dark-row{background:#fafafa}.content-box[data-v-cef5695e],[data-v-cef5695e] .light-row{background:#fff}.content-box[data-v-cef5695e]{margin:20px 20px;padding-bottom:20px;height:100%}.search-input[data-v-cef5695e]{width:170px;margin-right:15px}.settingModel .ant-table-row-cell-break-word a[data-v-cef5695e]{color:rgba(0,0,0,.65)}.ant-pagination[data-v-cef5695e]{text-align:right}[data-v-cef5695e] .inputText{margin-right:20px}.result[data-v-cef5695e]{width:100px;color:rgb(24 144 255);cursor:pointer}.result[data-v-cef5695e]:hover{color:#9e9e9e}.settingModel .ant-modal-header[data-v-cef5695e],[data-v-cef5695e] .settingModel .ant-modal-footer{background:#fff!important}[data-v-cef5695e] .ant-btn-sm{font-size:12px}.settingModel .ant-modal-header[data-v-cef5695e]{border-bottom:none}.settingModel .ant-modal-footer[data-v-cef5695e]{border-top:none}.ruleBox[data-v-cef5695e]{border:1px solid #ccc;border-radius:4px}.settingModel .ant-input[data-v-cef5695e]:focus{-webkit-box-shadow:0 0 0 transparent;box-shadow:0 0 0 transparent}.settingModel .ant-input[data-v-cef5695e]{border:none;height:32px}textarea[data-v-cef5695e]{resize:none}",""])},2038:function(t,e,a){var n=a("1a40");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var s=a("499e").default;s("52f69e38",n,!0,{sourceMap:!1,shadowMode:!1})},5314:function(t,e,a){"use strict";a.r(e);var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"content scroll absolute",staticStyle:{width:"100%","max-height":"100%",position:"absolute","overflow-y":"auto","padding-bottom":"30px"}},[a("div",[a("a-card",{staticClass:"px-0 py-20 content",staticStyle:{padding:"0 20px","line-height":"50px","border-bottom":"1px solid #E2E2E2",height:"50px","min-width":"760px",width:"100%"}},[a("label",{staticClass:"tpl-title align-top float-left"},[t._v("质检提醒")])]),a("div",{staticClass:"content-box"},[a("a-row",{staticStyle:{padding:"30px 20px"}},[a("a-col",{staticStyle:{float:"left"}},[a("a-select",{staticStyle:{width:"150px","margin-right":"15px"},on:{change:t.handleChangeStatus},model:{value:t.searchStatus,callback:function(e){t.searchStatus=e},expression:"searchStatus"}},[a("a-select-option",{attrs:{value:""}},[t._v("全部状态")]),a("a-select-option",{attrs:{value:"0"}},[t._v("关闭接收 ")]),a("a-select-option",{attrs:{value:"1"}},[t._v("开启接收")])],1),a("a-input",{staticClass:"search-input mr-15",attrs:{placeholder:"输入汇报对象姓名搜索"},model:{value:t.searchText,callback:function(e){t.searchText=e},expression:"searchText"}}),a("a-button",{staticStyle:{"margin-right":"15px"},attrs:{type:"primary"},on:{click:t.find}},[t._v("查找")]),a("a-button",{staticStyle:{"margin-right":"15px"},on:{click:t.clear}},[t._v("清空")])],1),a("a-button",{staticStyle:{float:"right","margin-right":"15px"},attrs:{type:"primary"},on:{click:t.setAnswer}},[t._v("设置违规原因")]),a("a-button",{staticStyle:{float:"right","margin-right":"15px"},attrs:{type:"primary"},on:{click:t.addPush}},[t._v("添加")])],1),a("div",{staticClass:"content-bd"},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("div",{staticClass:"spin-content"},[a("a-table",{attrs:{columns:t.columns,"data-source":t.dataList,"row-selection":{selectedRowKeys:t.selectedRowKeys,onChange:t.onSelectChange},pagination:!1,rowKey:function(t){return t.id}},scopedSlots:t._u([{key:"quality_name",fn:function(e){return[e.length>5?a("a-popover",{attrs:{placement:"topLeft"}},[a("template",{slot:"content"},t._l(e,(function(e,n){return a("a-tag",{staticClass:"mb-4",attrs:{color:"orange"}},[t._v("\n                      "+t._s(e)+"\n                    ")])})),1),t._l(e.slice(0,5),(function(e,n){return a("a-tag",{staticClass:"mb-4",attrs:{color:"orange"}},[t._v("\n                    "+t._s(e)+"\n                  ")])})),e.length>5?a("i",[t._v("等共计"+t._s(e.length)+"位成员")]):t._e()],2):a("p",[t._l(e.slice(0,5),(function(e,n){return a("a-tag",{staticClass:"mb-4",attrs:{color:"orange"}},[t._v("\n                    "+t._s(e)+"\n                  ")])})),e.length>5?a("i",[t._v("等共计"+t._s(e.length)+"位成员")]):t._e()],2)]}},{key:"report_name",fn:function(e){return[e.length>5?a("a-popover",{attrs:{placement:"topLeft"}},[a("template",{slot:"content"},t._l(e,(function(e,n){return a("a-tag",{staticClass:"mb-4",attrs:{color:"orange"}},[t._v("\n                      "+t._s(e)+"\n                    ")])})),1),t._l(e.slice(0,5),(function(e,n){return a("a-tag",{staticClass:"mb-4",attrs:{color:"orange"}},[t._v("\n                    "+t._s(e)+"\n                  ")])})),e.length>5?a("i",[t._v("等共计"+t._s(e.length)+"位成员")]):t._e()],2):a("p",[t._l(e.slice(0,5),(function(e,n){return a("a-tag",{staticClass:"mb-4",attrs:{color:"orange"}},[t._v("\n                    "+t._s(e)+"\n                  ")])})),e.length>5?a("i",[t._v("等共计"+t._s(e.length)+"位成员")]):t._e()],2)]}},{key:"is_cycle",fn:function(e){return[0==e?a("p",[t._v("\n                  每天上午9:00推送\n                ")]):1==e?a("p",[t._v("\n                 每周一上午9:00推送\n                ")]):t._e()]}},{key:"status",fn:function(e,n){return[a("a-switch",{attrs:{"checked-children":"开启","un-checked-children":"关闭","default-checked":"",checked:e},on:{change:function(e){return t.changeStatus(e,n)}}})]}},{key:"action",fn:function(e,n){return[a("a-row",[a("a-col",{attrs:{span:"12"}},[a("a-button",{on:{click:function(e){return t.editColumon(n)}}},[t._v("编辑")])],1),a("a-col",{attrs:{span:"12"}},[a("a-button",{on:{click:function(e){return t.showDeleteConfirm(n)}}},[t._v("\n                      删除\n                    ")])],1)],1)]}}])})],1)])],1),a("div",{staticClass:"pt-40 pr-20 pb-0 justify-between flex"},[a("div",{staticClass:"pl-20"},[a("a-dropdown",[a("a-menu",{attrs:{slot:"overlay"},on:{click:t.handleMenuClick},slot:"overlay"},[a("a-menu-item",{key:"1"},[t._v("当前页")]),a("a-menu-item",{key:"2"},[t._v("所有结果")])],1),a("a-button",{staticStyle:{width:"108px"}},[t._v(t._s(t.activePage)),a("a-icon",{attrs:{type:"down"}})],1)],1),a("a-button",{staticClass:"mx-10",attrs:{type:"primary"},on:{click:t.batchOpen}},[t._v("批量开启")]),a("a-button",{attrs:{type:"primary"},on:{click:t.batchClose}},[t._v("批量关闭")]),a("a-button",{staticClass:"mx-10",attrs:{type:"primary"},on:{click:t.batchRemove}},[t._v("批量移除")])],1),a("a-pagination",{staticClass:"text-right",attrs:{total:t.total,"show-less-items":""},on:{change:t.onChange},model:{value:t.current,callback:function(e){t.current=e},expression:"current"}})],1),a("a-modal",{staticClass:"settingModel py-20 pl-5 pr-10",attrs:{title:"设置违规原因",footer:null,width:550},model:{value:t.settingRuleShow,callback:function(e){t.settingRuleShow=e},expression:"settingRuleShow"}},[a("a-spin",{attrs:{spinning:t.spinningModal}},[a("div",{staticClass:"h-300 px-20 py-20 overflow-y-scroll"},t._l(t.ruleArr,(function(e,n){return a("div",{key:n,staticClass:"flex justify-between items-center mt-10 mb-12"},[a("div",{staticClass:"ruleBox flex justify-between mr-10 items-center px-15 py-4 w-300"},[a("a-textarea",{attrs:{type:"textarea","max-length":20},nativeOn:{"!blur":function(a){return t.onSaveInput(e.id,e.content)}},model:{value:e.content,callback:function(a){t.$set(e,"content",a)},expression:"item.content"}}),a("i",{staticClass:"not-italic"},[t._v(t._s(e.content.length)+"/20")])],1),a("a-button",{on:{click:function(a){return t.delRule(e.id,e.content)}}},[t._v("删除")])],1)})),0),a("div",{staticClass:"result pt-10 ml-20 mb-30",on:{click:t.addRule}},[t._v("+添加违规原因")])])],1)],1)],1)])},s=[],i=(a("ac6a"),a("75fc")),r=(a("96cf"),a("3b8d")),o=[{title:"质检人",dataIndex:"user_name",width:"8%",key:"user_name"},{title:"质检对象",dataIndex:"quality_name",width:"20%",key:"quality_name",scopedSlots:{customRender:"quality_name"}},{title:"汇报对象",dataIndex:"report_name",key:"report_name",width:"180px",scopedSlots:{customRender:"report_name"}},{title:"推送周期",dataIndex:"is_cycle",width:"20%",key:"is_cycle",scopedSlots:{customRender:"is_cycle"}},{title:"推送状态",dataIndex:"status",width:"15%",key:"status",scopedSlots:{customRender:"status"}},{title:"操作",dataIndex:"action",align:"center",width:"160px",key:"action",scopedSlots:{customRender:"action"}}],c={name:"archiveReminder",data:function(){return{isLoading:!1,commonUrl:this.$store.state.commonUrl,searchStatus:"",dataList:[],columns:[],current:1,corpId:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",settingRuleShow:!1,searchText:"",ruleArr:[],spinningModal:!1,total:0,selectedRowKeys:[],activePage:"当前页",allList:[]}},mounted:function(){this.columns=o},methods:{batchOpen:function(){this.setBatchOperation(this.selectedRowKeys.join(","),1),this.selectedRowKeys=[]},batchClose:function(){this.setBatchOperation(this.selectedRowKeys.join(","),2),this.selectedRowKeys=[]},batchRemove:function(){this.setBatchOperation(this.selectedRowKeys.join(","),3),this.selectedRowKeys=[]},onSelectChange:function(t){this.selectedRowKeys=t},addRule:function(){this.ruleArr.push({content:"",id:""})},editColumon:function(t){this.$router.push("/archive/reminderAdd?id=".concat(t.id))},showDeleteConfirm:function(t){var e=this;this.$confirm({title:"确认删除?",content:"",okText:"确认",okType:"primary",cancelText:"取消",onOk:function(){e.setBatchOperation(t.id,3)},onCancel:function(){}})},delRule:function(t,e){console.log(t,e),this.setRuleList(t,1,"")},onSaveInput:function(t,e){this.setRuleList(t,0,e)},handleMenuClick:function(t){1==t.key?(this.activePage="当前页",this.selectedRowKeys=this.dataList.map((function(t){return t.id})).slice(0,10),console.log(this.selectedRowKeys)):2==t.key&&(this.activePage="所有结果",this.selectedRowKeys=this.allList.map((function(t){return t.id})),console.log(this.selectedRowKeys))},changeStatus:function(t,e){e.status=!e.status;var a=-1;a=t?1:2,this.setBatchOperation(e.id,a)},setAnswer:function(){this.settingRuleShow=!0,this.getRuleList()},handleChangeStatus:function(t){this.searchStatus=t},find:function(){this.getList(this.searchText,this.searchStatus)},clear:function(){this.getList("",""),this.searchStatus="",this.searchText=""},addPush:function(){this.$router.push("/archive/reminderAdd")},onChange:function(t){this.current=t,this.getList("","")},setBatchOperation:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e,a){var n,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("inspection/batch-operation",{id:e,type:a});case 2:n=t.sent,s=n.data,0==s.error&&this.getList("","");case 5:case"end":return t.stop()}}),t,this)})));function e(e,a){return t.apply(this,arguments)}return e}(),setRuleList:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e,a,n){var s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("inspection/inspection-violation-classify-add",{id:e,is_del:a,corp_id:this.corpId,content:n});case 2:s=t.sent,s.data,this.getRuleList();case 5:case"end":return t.stop()}}),t,this)})));function e(e,a,n){return t.apply(this,arguments)}return e}(),getRuleList:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.spinningModal=!0,t.next=3,this.axios.post("inspection/inspection-violation-classify-list",{corp_id:this.corpId});case 3:e=t.sent,a=e.data,this.ruleArr=a.data,this.spinningModal=!1;case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getList:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e,a){var n,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.get("inspection/inspector-remind-list",{params:{corp_id:this.corpId,user:e,status:a,page:this.current,pageSize:10}});case 3:n=t.sent,s=n.data,0==s.error&&(this.allList=[].concat(Object(i["a"])(this.dataList),Object(i["a"])(s.data.data)),this.dataList=s.data.data,this.total=s.data.total_count,this.isLoading=!1,this.dataList.forEach((function(t){0==t.status?t.status=!1:t.status=!0})));case 6:case"end":return t.stop()}}),t,this)})));function e(e,a){return t.apply(this,arguments)}return e}()},created:function(){this.getList("","")}},l=c,d=(a("7363"),a("2877")),u=Object(d["a"])(l,n,s,!1,null,"cef5695e",null);e["default"]=u.exports},7363:function(t,e,a){"use strict";var n=a("2038"),s=a.n(n);s.a}}]);