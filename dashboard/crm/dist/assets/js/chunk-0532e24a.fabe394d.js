(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0532e24a"],{"0f93":function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticStyle:{width:"100%",height:"100%",position:"absolute","overflow-y":"auto","padding-bottom":"30px"}},[a("div",[a("div",{staticStyle:{height:"50px","line-height":"50px","background-color":"#FFFFFF"}},[a("label",{staticStyle:{"margin-left":"20px"}},[t._v("\n\t\t\t\t\t"+t._s(t.title)+"：\n\t\t\t\t")]),a("a-button",{staticStyle:{float:"right",margin:"9px 20px 0 0"},attrs:{type:"primary",icon:"rollback"},on:{click:t.rollback}},[t._v("\n\t\t\t\t\t返回列表\n\t\t\t\t")])],1)]),a("div",{staticClass:"participants"},[a("div",{staticStyle:{"margin-bottom":"10px"}},[a("span",{directives:[{name:"has",rawName:"v-has",value:"record-list",expression:"'record-list'"}],staticClass:"tabBtn",class:{activeBtn:1==t.tabKey},on:{click:function(e){return t.changeTab("1")}}},[t._v("获奖名单")]),a("span",{directives:[{name:"has",rawName:"v-has",value:"player-list",expression:"'player-list'"}],staticClass:"tabBtn",class:{activeBtn:2==t.tabKey},on:{click:function(e){return t.changeTab("2")}}},[t._v("玩家列表")])]),a("div",{directives:[{name:"show",rawName:"v-show",value:1==t.tabKey,expression:"tabKey == 1"},{name:"has",rawName:"v-has",value:"record-list",expression:"'record-list'"}]},[a("a-input-search",{staticStyle:{width:"270px"},attrs:{placeholder:"请输入关键字",enterButton:"搜索"},on:{search:t.onSearch},model:{value:t.nick_name,callback:function(e){t.nick_name=e},expression:"nick_name"}}),a("div",{staticStyle:{"margin-bottom":"16px",float:"right"}},[a("a-popconfirm",{attrs:{okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.start("")}}},[a("template",{slot:"title"},[a("div",[t._v("确定核销吗？")])]),a("a-button",{directives:[{name:"has",rawName:"v-has",value:"record-destroy",expression:"'record-destroy'"}],attrs:{type:"primary",disabled:!t.hasSelected,loading:t.loading}},[t._v("\n\t\t\t\t\t\t\t核销/领取\n\t\t\t\t\t\t")])],2)],1),a("div",{staticClass:"content-bd"},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("a-table",{attrs:{rowSelection:t.rowSelection,columns:t.columns1,dataSource:t.participantsList,rowClassName:t.rowClassName,pagination:!1},scopedSlots:t._u([{key:"avatar",fn:function(t,e){return a("span",{},[a("a-avatar",{attrs:{shape:"square",size:42,src:t}})],1)}},{key:"status",fn:function(e,i){return a("span",{},[0==e?a("span",[0==i.prize_type?a("span",[t._v("未核销")]):t._e(),1==i.prize_type?a("span",[t._v("未领取")]):t._e()]):t._e(),1==e?a("span",[0==i.prize_type?a("span",[t._v("已核销")]):t._e(),1==i.prize_type?a("span",[t._v("已领取")]):t._e()]):t._e()])}},{key:"action",fn:function(e,i){return a("span",{},[a("a-popconfirm",{attrs:{okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.start(i.id)}}},[a("template",{slot:"title"},[0==i.prize_type?a("div",[t._v("确定核销吗？")]):t._e(),1==i.prize_type?a("div",[t._v("确定发放吗？")]):t._e()]),0==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"record-destroy",expression:"'record-destroy'"}],staticStyle:{margin:"0 5px 5px 0"}},[0==i.prize_type?a("span",[t._v("核销")]):t._e(),1==i.prize_type?a("span",[t._v("发放")]):t._e()]):t._e()],2),0!=i.status?a("span",[t._v("--")]):t._e()],1)}}])}),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"40px 0px 20px"}},[a("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t"),a("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("条\n\t\t\t\t\t\t\t")]),a("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[a("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])],1)],1)],1),a("div",{directives:[{name:"show",rawName:"v-show",value:2==t.tabKey,expression:"tabKey == 2"},{name:"has",rawName:"v-has",value:"player-list",expression:"'player-list'"}]},[a("a-input-search",{staticStyle:{width:"270px","margin-bottom":"16px"},attrs:{placeholder:"请输入关键字",enterButton:"搜索"},on:{search:t.onSearch},model:{value:t.nick_name2,callback:function(e){t.nick_name2=e},expression:"nick_name2"}}),a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("a-table",{attrs:{columns:t.columns2,dataSource:t.participantsList2,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"nick_name",fn:function(e,i){return a("span",{},[a("a-avatar",{staticStyle:{"margin-right":"5px"},attrs:{shape:"square",size:42,src:i.avatar}}),t._v("\n\t\t\t\t\t\t\t\t"+t._s(i.nick_name)+"\n\t\t\t\t\t\t\t")],1)}},{key:"has_num",fn:function(e,i){return a("span",{},[t._v("\n\t\t\t\t\t\t\t\t"+t._s(i.has_num)+"次 / "+t._s(i.total_num)+"次\n\t\t\t\t\t\t\t")])}},{key:"action",fn:function(e,i){return a("span",{},[a("a-button",{staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.lookDraw(i.award_id,i.key,i.nick_name)}}},[t._v("查看抽奖")]),a("a-button",{staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.lookHelp(i.key,i.nick_name)}}},[t._v("查看助力")])],1)}}])}),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total2>0,expression:"total2 > 0"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"40px 0px 20px"}},[a("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t"),a("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total2))]),t._v("条\n\t\t\t\t\t\t")]),a("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[a("a-pagination",{attrs:{total:t.total2,showSizeChanger:"",showQuickJumper:t.quickJumper2,current:t.page2,pageSize:t.pageSize2,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage2,showSizeChange:t.showSizeChange2}})],1)])],1)],1)])])},n=[],s=a("75fc"),r=(a("20d6"),a("ac6a"),a("96cf"),a("3b8d")),o=[{title:"昵称",dataIndex:"nick_name",key:"nick_name",width:"200px"},{title:"头像",dataIndex:"avatar",key:"avatar",scopedSlots:{customRender:"avatar"}},{title:"奖品",dataIndex:"award_name",key:"award_name"},{title:"参与时间",dataIndex:"create_time",key:"create_time"},{title:"领取状态",dataIndex:"status",key:"status",scopedSlots:{customRender:"status"}},{title:"操作",dataIndex:"action",key:"action",width:"15%",scopedSlots:{customRender:"action"}}],c=[{title:"参与人信息（头像+昵称）",dataIndex:"nick_name",key:"nick_name",scopedSlots:{customRender:"nick_name"}},{title:"已中奖/当前总抽奖次数",dataIndex:"has_num",key:"has_num",scopedSlots:{customRender:"has_num"}},{title:"最后一次抽奖时间",dataIndex:"last_time",key:"last_time"},{title:"操作",dataIndex:"action",key:"action",scopedSlots:{customRender:"action"}}],p={name:"raffleParticipants",data:function(){var t=localStorage.getItem("permissionButton")&&localStorage.getItem("permissionButton").indexOf("record-list")>0?this.$store.state.raffleParticipantsTabKey:"2";return{isLoading:!1,fid:"",title:"",tabKey:t,columns1:o,columns2:c,participantsList:[],nick_name:"",selectedRowKeys:[],userKeys:[],loading:!1,total:0,quickJumper:!1,page:1,pageSize:15,participantsList2:[],nick_name2:"",total2:0,quickJumper2:!1,page2:1,pageSize2:15,is_record:1}},methods:{rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},rollback:function(){this.$router.go(-1)},participants:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a,i,n,s=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=s.length>0&&void 0!==s[0]?s[0]:1,a=s.length>1&&void 0!==s[1]?s[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("awards-activity/records",{uid:localStorage.getItem("uid"),award_id:this.fid,page:e,pageSize:a,is_record:this.is_record,nick_name:this.nick_name});case 5:i=t.sent,n=i.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):(this.participantsList=n.data.info,this.userKeys=n.data.keys,this.isLoading=!1,this.total=parseInt(n.data.count),this.page=e,this.pageSize=a,this.quickJumper=this.total>this.pageSize);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),participants2:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a,i,n,s=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=s.length>0&&void 0!==s[0]?s[0]:1,a=s.length>1&&void 0!==s[1]?s[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("awards-activity/awards-join",{id:this.fid,page:e,pageSize:a,nick_name:this.nick_name2});case 5:i=t.sent,n=i.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):(this.participantsList2=n.data.info,this.isLoading=!1,this.total2=parseInt(n.data.count),this.page2=e,this.pageSize2=a,this.quickJumper2=this.total2>this.pageSize2);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changePage:function(t,e){this.participants(t,e)},showSizeChange:function(t,e){this.participants(1,e)},changePage2:function(t,e){this.participants2(t,e)},showSizeChange2:function(t,e){this.participants2(1,e)},changeTab:function(t){this.tabKey=t,1==t?(this.is_record=1,this.nick_name="",this.participants()):2==t&&(this.is_record=0,this.nick_name2="",this.participants2()),this.$store.commit("changeRaffleParticipantsTabKey",t)},onSearch:function(t){1==this.tabKey?this.participants():2==this.tabKey&&this.participants2()},start:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var a,i,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=[],a=""==e||null==e?this.selectedRowKeys:e,t.next=4,this.axios.post("awards-activity/update-status",{id:a,award_id:this.fid});case 4:i=t.sent,n=i.data,0!=n.error?(this.loading=!1,this.$message.error(n.error_msg)):(this.$message.success(n.data.textHtml),this.loading=!1,this.selectedRowKeys=[],this.participants());case 7:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),onSelectChange:function(t){this.selectedRowKeys=t},lookDraw:function(t,e,a){this.$router.push("/raffle/drawDetail?award_id="+t+"&join_id="+e+"&title="+a)},lookHelp:function(t,e){this.$router.push("/raffle/helpDetail?join_id="+t+"&title="+e)}},mounted:function(){this.title=decodeURIComponent(this.$route.query.title),this.fid=decodeURIComponent(this.$route.query.fid),1==this.tabKey?this.participants():2==this.tabKey&&this.participants2()},beforeRouteEnter:function(t,e,a){"/raffle/drawDetail"!=e.path&&"/raffle/helpDetail"!=e.path&&a((function(t){t.title=decodeURIComponent(t.$route.query.title),t.fid=decodeURIComponent(t.$route.query.fid),t.nick_name="",t.nick_name2="",t.page=1,t.pageSize=15,t.tabKey=localStorage.getItem("permissionButton")&&localStorage.getItem("permissionButton").indexOf("record-list")>0?t.$store.state.raffleParticipantsTabKey:"2",1==t.tabKey?t.participants():2==t.tabKey&&t.participants2()})),a()},computed:{rowSelection:function(){var t=this,e=this,a=this.selectedRowKeys;return{selectedRowKeys:a,onChange:this.onSelectChange,hideDefaultSelections:!0,selections:[{key:"current-data",text:"当前页",onSelect:function(e){var a=t;t.participantsList.map((function(t){var e=a.selectedRowKeys.findIndex((function(e){return e===t.id}));e>=0&&a.selectedRowKeys.splice(e,1)}));for(var i=0;i<e.length;i++)a.selectedRowKeys.push(e[i])}},{key:"all-data",text:"选择所有项",onSelect:function(){e.selectedRowKeys=Object(s["a"])(e.userKeys.valueOf())}}],getCheckboxProps:function(t){return{props:{disabled:1==t.status}}}}},hasSelected:function(){return this.selectedRowKeys.length>0}}},l=p,d=(a("b931"),a("2877")),u=Object(d["a"])(l,i,n,!1,null,"4b8f0e4c",null);e["default"]=u.exports},"9d9b":function(t,e,a){var i=a("bab5");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("499e").default;n("57fa3ee4",i,!0,{sourceMap:!1,shadowMode:!1})},b931:function(t,e,a){"use strict";var i=a("9d9b"),n=a.n(i);n.a},bab5:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".participants[data-v-4b8f0e4c]{margin:10px 20px;background-color:#fff;padding:20px}.content-bd[data-v-4b8f0e4c],[data-v-4b8f0e4c] .ant-tabs-bar{background:#fff}.content-bd[data-v-4b8f0e4c]{min-height:120px;min-width:885px}[data-v-4b8f0e4c] .ant-tabs-top-content{padding-bottom:60px}.tabBtn[data-v-4b8f0e4c]{margin:0;margin-right:2px;padding:10px 16px;line-height:38px;background:#fafafa;border:1px solid #e8e8e8;border-bottom:0;border-radius:4px 4px 0 0;cursor:pointer}.activeBtn[data-v-4b8f0e4c]{color:#1890ff;background:#fff}",""])}}]);