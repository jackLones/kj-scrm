(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-379a23bb"],{"18ba":function(t,e,a){var i=a("7ff0");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var n=a("499e").default;n("0f424804",i,!0,{sourceMap:!1,shadowMode:!1})},6001:function(t,e,a){"use strict";var i=a("18ba"),n=a.n(i);n.a},"62a7":function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"list"},[a("div",{attrs:{id:"components-layout-demo-basic"}},[a("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[a("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",top:"0",bottom:"0",right:"0",left:"0","overflow-x":"hidden","overflow-y":"auto"}},[a("a-layout-header",[t._v("抽奖引流")]),a("a-layout-content",[a("div",{staticClass:"content-msg"},[a("div",{staticStyle:{margin:"10px 0 2px"}},[t._v("自动发送欢迎语，可能失败的原因\n\t\t\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"bottom"}},[a("template",{slot:"title"},[a("p",{staticStyle:{"margin-bottom":"2px","font-size":"13px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t1、如果企业在企业微信后台为相关成员配置了可用的欢迎语，使用第三方系统配置欢迎语，均不起效，推送的还是企业微信官方的。")]),a("p",{staticStyle:{"margin-bottom":"10px","font-size":"13px"}}),a("p",{staticStyle:{"margin-bottom":"2px","font-size":"13px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t2、客户和企业成员已经开始聊天的场景下，不能发送欢迎语。")]),a("p",{staticStyle:{"margin-bottom":"10px","font-size":"13px"}}),a("p",{staticStyle:{"margin-bottom":"2px","font-size":"13px"}},[t._v("3、客户之前添加过A企业成员，\n\t\t\t\t\t\t\t\t\t\t不论客户是否将该成员删除，但凡再通过裂变活动添加上该A企业成员，受限于企业微信官方规则，可能会造成推送其他渠道（包含正在进行中的裂变活动）的欢迎语，也有可能不再推送。")]),a("p",{staticStyle:{"margin-bottom":"10px","font-size":"13px"}}),a("p",{staticStyle:{"margin-bottom":"0","font-size":"13px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t4、每次添加新的客户时，当存在多个企业自建应用或是第三方应用设置了欢迎语，那么企业微信官方采取优先权的推送，所以存在有的客户接收不到欢迎语。请商户根据实际使用需求，合理配置，避免冲突。")])]),a("a-icon",{attrs:{type:"question-circle"}})],2)],1),a("p",{staticStyle:{margin:"10px 0 2px",color:"red"}},[t._v("\n\t\t\t\t\t\t\t在使用派发红包功能前，需要商户完成以下配置：\n\t\t\t\t\t\t")]),a("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t1、前往"),a("a",{attrs:{href:"https://pay.weixin.qq.com/index.php/core/home/login?return_url=%2F",target:"_blank"}},[t._v("【微信支付商户平台】")]),t._v("注册微信支付商户号\n\t\t\t\t\t\t")]),a("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t2、登录"),a("a",{attrs:{href:"https://work.weixin.qq.com/wework_admin/loginpage_wx?from=myhome_baidu",target:"_blank"}},[t._v("【企业微信后台】")]),t._v("开通企业支付，绑定已有商户号（"),a("a",{attrs:{href:"https://support.qq.com/products/104790/faqs/66072",target:"_blank"}},[t._v("查看教程")]),t._v("）\n\t\t\t\t\t\t")]),a("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t3、登录"),a("a",{attrs:{href:"https://pay.weixin.qq.com/index.php/core/home/login?return_url=%2F",target:"_blank"}},[t._v("【微信支付商户平台】")]),t._v("开通【企业付款到零钱】（"),a("a",{attrs:{href:"https://support.qq.com/products/104790/faqs/66076",target:"_blank"}},[t._v("查看教程")]),t._v("）\n\t\t\t\t\t\t")]),a("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t4、在本系统，进入【应用中心】--【企业支付】，点击，填入企业支付的应用ID和Secret\n\t\t\t\t\t\t")]),a("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t5、在本系统，进入【应用中心】--【企业支付】，点击，完成支付配置。（"),a("a",{attrs:{href:"https://support.qq.com/products/104790/faqs/66058",target:"_blank"}},[t._v("查看教程")]),t._v("）\n\t\t\t\t\t\t")])]),a("div",{staticClass:"content-hd"},[a("div",{staticStyle:{overflow:"hidden",margin:"20px 0"}},[a("a-col",{staticStyle:{float:"left"}},[t.corpLen>1?a("a-select",{staticStyle:{width:"210px","margin-right":"10px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleChange},model:{value:t.corpId,callback:function(e){t.corpId=e},expression:"corpId"}},[t._l(t.corpInfo,(function(e){return[a("a-select-option",{attrs:{value:e.corpid}},[t._v(t._s(e.corp_full_name||e.corp_name)+"\n\t\t\t\t\t\t\t\t\t\t")])]}))],2):t._e(),a("a-select",{staticStyle:{width:"120px"},attrs:{placeholder:"活动状态",allowClear:""},model:{value:t.status,callback:function(e){t.status=e},expression:"status"}},[a("a-select-option",{attrs:{value:"1"}},[t._v("未开始")]),a("a-select-option",{attrs:{value:"2"}},[t._v("进行中")]),a("a-select-option",{attrs:{value:"3"}},[t._v("已结束")])],1),a("a-input",{staticStyle:{width:"174px",margin:"0px 10px"},attrs:{placeholder:"搜索活动名称"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.find(e)}},model:{value:t.title,callback:function(e){t.title=e},expression:"title"}}),a("a-range-picker",{staticStyle:{width:"250px"},on:{change:t.changeTime},model:{value:t.activityTime,callback:function(e){t.activityTime=e},expression:"activityTime"}}),a("a-button",{staticStyle:{margin:"0px 10px"},attrs:{type:"primary"},on:{click:t.find}},[t._v("查找")]),a("a-button",{on:{click:t.clear}},[t._v("清空")])],1),a("a-col",{directives:[{name:"has",rawName:"v-has",value:"raffle-add",expression:"'raffle-add'"}],staticStyle:{float:"right"}},[a("a-button",{staticClass:"btn-primary",attrs:{icon:"plus",type:"primary"},on:{click:t.addWelcomeText}},[t._v("\n\t\t\t\t\t\t\t\t\t新建活动\n\t\t\t\t\t\t\t\t")])],1)],1),t.raffleNum>0?a("div",{staticClass:"content-msg",staticStyle:{"margin-bottom":"20px"}},[t._v("当前套餐版本仅支持"),a("span",{staticStyle:{color:"red"}},[t._v("单场活动")]),t._v("用户参与人数上限"),a("span",{staticStyle:{color:"red"}},[t._v(t._s(t.raffleNum))]),t._v("人，达到上限后活动将自动结束。更多套餐信息可联系平台了解哦！\n\t\t\t\t\t\t")]):t._e()]),a("div",{staticClass:"content-bd"},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("a-table",{directives:[{name:"has",rawName:"v-has",value:"raffle-list",expression:"'raffle-list'"}],attrs:{columns:t.columns,dataSource:t.activityList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"qr_code",fn:function(t,e,i){return a("span",{},[a("div",{ref:"qrcode"+i,attrs:{id:"qrcode"+i}})])}},{key:"title2",fn:function(e,i){return a("span",{},[a("div",{staticStyle:{width:"140px"}},[t._v(t._s(i.title))])])}},{key:"time",fn:function(e,i){return a("span",{},[a("div",{staticStyle:{width:"125px"}},[t._v(t._s(i.start_time)+"至")]),a("div",[t._v(t._s(i.end_time))])])}},{key:"user",fn:function(e,i){return a("span",{},[t._l(i.user_key,(function(e){return[e.scopedSlots&&e.scopedSlots.title&&"custom"==e.scopedSlots.title?a("a-tag",{staticStyle:{"margin-top":"5px"},attrs:{color:"orange"}},[t._v(t._s(e.title))]):t._e()]})),t._l(i.user_key,(function(e){return[e.scopedSlots&&e.scopedSlots.title&&"custom"!=e.scopedSlots.title?a("a-tag",{staticStyle:{"margin-top":"5px"},attrs:{color:"blue"}},[t._v(t._s(e.title))]):t._e()]})),0==i.user_key.length?a("span",[t._v("--")]):t._e()],2)}},{key:"status_str",fn:function(e,i){return a("span",{},[a("div",[t._v(t._s(i.status_str))]),"已结束"==i.status_str?a("div",{staticStyle:{width:"128px"}},[t._v("（"+t._s(i.reason_str)+"）")]):t._e()])}},{key:"content",fn:function(e,i){return a("span",{},[a("a-popover",{attrs:{placement:"left"}},[a("template",{slot:"content"},[a("div",{staticStyle:{"max-height":"500px","overflow-y":"auto"}},t._l(i.content,(function(e,i){return a("div",[t._v("\n\t\t\t\t\t\t\t\t\t\t奖项"+t._s(i+1)+"："+t._s(e.name)+"（"+t._s(e.last_num||0)+"/"+t._s(e.num)+"）\n\t\t\t\t\t\t\t\t\t")])})),0)]),a("span",{staticStyle:{color:"#1890FF",cursor:"pointer"}},[t._v("预览")])],2)],1)}},{key:"share_setting",fn:function(e,i){return a("span",{},[i.is_share_open?[t._v("\n\t\t\t\t\t\t\t\t\t分享成功后\n\t\t\t\t\t\t\t\t\t"),a("div",{staticStyle:{width:"90px"}},[t._v("增加"+t._s(e[0].total_num)+"次机会")])]:t._e(),i.is_share_open?t._e():[t._v("--")]],2)}},{key:"action",fn:function(e,i,n){return a("span",{},[a("div",{staticStyle:{width:"150px"}},[1==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"raffle-invalid",expression:"'raffle-invalid'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.failure(i.id)}}},[t._v("使失效")]):t._e(),0==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"raffle-release",expression:"'raffle-release'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.releaseActivity(i.id)}}},[t._v("发布")]):t._e(),0==i.status||1==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"raffle-edit",expression:"'raffle-edit'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.edit(i.id)}}},[t._v("编辑")]):t._e(),0==i.status||1==i.status||2==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"raffle-part",expression:"'raffle-part'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.participants(i.id,i.title)}}},[t._v("参与者")]):t._e(),0==i.status||2==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"raffle-del",expression:"'raffle-del'"}],staticStyle:{margin:"0 5px 5px 0"},on:{click:function(e){return t.deleteActivity(i.id)}}},[t._v("删除")]):t._e(),1==i.status?a("a-button",{directives:[{name:"has",rawName:"v-has",value:"raffle-down",expression:"'raffle-down'"}],staticClass:"actionBtn",attrs:{"data-url":i.qr_code,"data-name":i.title,"data-id":"qrcode"+n},on:{click:t.downLoadWay}},[t._v("下载")]):t._e()],1)])}}])}),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"raffle-list",expression:"'raffle-list'"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[a("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t\t"),a("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("条\n\t\t\t\t\t\t\t\t")]),a("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[a("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])],1)],1)])],1)],1)],1)])},n=[],s=(a("7f7f"),a("a481"),a("96cf"),a("3b8d")),o=(a("c5f6"),a("d044")),r=a.n(o),c=[{title:"二维码",dataIndex:"qr_code",key:"qr_code",width:"9%",scopedSlots:{customRender:"qr_code"}},{title:"活动名称",dataIndex:"title",key:"title",scopedSlots:{customRender:"title2"}},{title:"活动时间",dataIndex:"time",key:"time",scopedSlots:{customRender:"time"}},{title:"奖品（剩余/总库存）",dataIndex:"content",key:"content",scopedSlots:{customRender:"content"}},{title:"活动限制",dataIndex:"share_setting",key:"share_setting",width:"10%",scopedSlots:{customRender:"share_setting"}},{title:"参与人数",dataIndex:"part_num",key:"part_num",width:"8%"},{title:"引流成员",dataIndex:"user",key:"user",width:"10%",scopedSlots:{customRender:"user"}},{title:"状态",dataIndex:"status_str",key:"status_str",scopedSlots:{customRender:"status_str"}},{title:"操作",dataIndex:"action",key:"action",width:"18%",scopedSlots:{customRender:"action"}}],l={name:"raffleList",components:{},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpLen:JSON.parse(localStorage.getItem("corpArr")).length,corpInfo:[],suite_id:1,corpId:t,status:[],title:"",activityTime:null,date:null,activityList:[],isLoading:!0,columns:c,total:0,quickJumper:!1,page:1,page_size:15,pageSize:15,raffleNum:Number(this.$store.state.packageDetail.raffleNum)}},methods:{handleChange:function(t){this.corpId=t},rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},changeTime:function(t,e){0==t.length?this.date=null:this.date=e},find:function(){this.getActivityList()},clear:function(){this.status=[],this.title="",this.activityTime=null,this.date=null,this.page=1,this.pageSize=15,this.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",this.getActivityList()},addWelcomeText:function(){this.$router.push("/raffle/add")},getActivityList:function(){var t=Object(s["a"])(regeneratorRuntime.mark((function t(){var e,a,i,n,s,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,a=o.length>1&&void 0!==o[1]?o[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("awards-activity/list",{uid:localStorage.getItem("uid"),corp_id:this.corpId,status:this.status,title:this.title,date:this.date,page:e,pageSize:a});case 5:i=t.sent,n=i.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):(this.activityList=n.data.info,this.isLoading=!1,this.total=parseInt(n.data.count),this.page=e,this.pageSize=a,this.quickJumper=this.total>this.pageSize,s=this,s.$nextTick((function(t){s.activityList.map((function(t,e){document.getElementById("qrcode"+e).innerHTML=""})),s.activityList.map((function(t,e){s.qrcode(t.qr_code,e)}))})));case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),qrcode:function(t,e){new r.a("qrcode"+e,{width:100,height:100,text:t,colorDark:"#000",colorLight:"#FFF"})},failure:function(t,e){var a=this;a.$confirm({title:"确定结束该活动？",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){a.changeStatus(t,2)}})},changeStatus:function(){var t=Object(s["a"])(regeneratorRuntime.mark((function t(e,a){var i,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.post("awards-activity/update-award-status",{id:e,status:a});case 3:i=t.sent,n=i.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):this.getActivityList(this.page,this.pageSize);case 6:case"end":return t.stop()}}),t,this)})));function e(e,a){return t.apply(this,arguments)}return e}(),releaseActivity:function(t){var e=this;e.$confirm({title:"确定发布该活动？",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.changeStatus(t,1)}})},deleteActivity:function(t){var e=this;e.$confirm({title:"确定删除该活动？",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.delete(t,0)}})},delete:function(){var t=Object(s["a"])(regeneratorRuntime.mark((function t(e){var a,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.post("awards-activity/delete",{id:e});case 3:a=t.sent,i=a.data,0!=i.error?(this.isLoading=!1,this.$message.error(i.error_msg)):1==this.activityList.length&&this.page>1?this.getActivityList(this.page-1,this.pageSize):this.getActivityList(this.page,this.pageSize);case 6:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),edit:function(t){this.$router.push("/raffle/add?id="+t)},participants:function(t,e){var a=encodeURIComponent(t),i=encodeURIComponent(e.replace(/%/g,"%25"));this.$store.commit("changeRaffleParticipantsTabKey","1"),this.$router.push("/raffle/participants?fid="+a+"&title="+i)},downLoadWay:function(t){var e=this,a=new Image;a.setAttribute("crossOrigin","anonymous"),a.src=document.getElementById(t.target.dataset.id).getElementsByTagName("img")[0].getAttribute("src"),a.onload=function(){var i=document.createElement("canvas");i.width=200,i.height=200;var n=i.getContext("2d");n.drawImage(a,0,0,200,200),i.toBlob((function(a){var i=URL.createObjectURL(a);e.download(i,t.target.dataset.name),URL.revokeObjectURL(i)}))}},download:function(t,e){var a=document.createElement("a");a.download=e,a.href=t,a.click(),a.remove()},changePage:function(t,e){this.getActivityList(t,e),this.$nextTick((function(){document.getElementsByClassName("scroll")[0].scrollTo(0,230)}))},showSizeChange:function(t,e){this.getActivityList(1,e)}},mounted:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.getActivityList()}))},beforeRouteEnter:function(t,e,a){"/raffle/add"==e.path&&"undefined"!=typeof t.query.isRefresh&&"1"==t.query.isRefresh?a((function(t){t.getActivityList(t.page,t.pageSize)})):("/raffle/add"==e.path&&"undefined"==typeof t.query.isRefresh||"/raffle/participants"!=e.path)&&a((function(t){t.status=[],t.title="",t.activityTime=null,t.date=null,t.page=1,t.pageSize=15,t.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",t.getActivityList()})),a()}},p=l,d=(a("6001"),a("2877")),u=Object(d["a"])(p,i,n,!1,null,"7511e24e",null);e["default"]=u.exports},"7ff0":function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"#components-layout-demo-basic[data-v-7511e24e]{height:100%}#components-layout-demo-basic .ant-layout-header[data-v-7511e24e]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px}[data-v-7511e24e] .ant-layout-header{padding:0 20px;font-size:16px;text-align:left}#components-layout-demo-basic .ant-layout-sider[data-v-7511e24e]{background:#fff;-webkit-box-flex:0!important;-ms-flex:0 0 250px!important;flex:0 0 250px!important;max-width:250px!important;min-width:250px!important;width:250px!important;border-right:1px solid #e2e2e2}#components-layout-demo-basic .ant-layout-content[data-v-7511e24e]{margin:0 20px 20px;min-width:885px;width:100%;padding-right:40px}.content-hd[data-v-7511e24e]{width:100%;min-width:885px}.content-msg[data-v-7511e24e]{width:100%;border:1px solid #ffdda6;background:#fff2db;padding:10px;margin-top:12px}.content-bd[data-v-7511e24e]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;width:100%}#components-layout-demo-basic>.ant-layout[data-v-7511e24e]{margin-bottom:48px}#components-layout-demo-basic>.ant-layout[data-v-7511e24e]:last-child{margin:0}.ant-layout.ant-layout-has-sider[data-v-7511e24e],.list[data-v-7511e24e]{height:100%}.btn-primary[data-v-7511e24e]{margin-left:20px}[data-v-7511e24e] .dark-row{background:#fafafa}[data-v-7511e24e] .light-row{background:#fff}[data-v-7511e24e] .ant-radio-button-wrapper{width:90px;margin:0;text-align:center}",""])}}]);