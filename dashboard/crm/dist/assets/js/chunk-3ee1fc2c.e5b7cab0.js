(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-3ee1fc2c"],{"0b45":function(t,e,i){"use strict";var s=i("7116"),a=i.n(s);a.a},2979:function(t,e,i){"use strict";i.r(e);var s=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticStyle:{width:"100%",height:"100%"}},[s("div",{staticStyle:{height:"100%"}},[s("a-card",{staticStyle:{"margin-bottom":"20px",padding:"10px 20px"}},[s("label",{staticClass:"tpl-title"},[t._v("待办看板")])]),s("div",{staticStyle:{height:"calc(100% - 84px)"}},[s("a-row",{ref:"searchArea",staticStyle:{margin:"0 20px 20px",padding:"20px 20px 5px",background:"#FFF","line-height":"40px"}},[s("span",{staticClass:"select-option"},[s("label",{staticStyle:{"margin-right":"5px"}},[t._v("关键词：")]),s("a-input",{staticStyle:{width:"210px","margin-right":"5px"},attrs:{placeholder:"客户姓名/昵称/备注/公司名称"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.find(e)}},model:{value:t.name,callback:function(e){t.name=e},expression:"name"}})],1),s("span",{staticClass:"select-option"},[s("label",{staticStyle:{"margin-right":"5px"}},[t._v("手机号：")]),s("a-input",{staticStyle:{width:"210px","margin-right":"5px"},attrs:{placeholder:"请输入手机号码"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.find(e)}},model:{value:t.phone,callback:function(e){t.phone=e},expression:"phone"}})],1),t.projectList.length>1?s("span",{staticClass:"select-option"},[s("label",{staticStyle:{"margin-right":"5px"}},[t._v("选择项目：")]),s("a-select",{staticStyle:{width:"210px","margin-right":"5px"},attrs:{showSearch:"",optionFilterProp:"children"},model:{value:t.projectId,callback:function(e){t.projectId=e},expression:"projectId"}},[s("a-select-option",{attrs:{value:""}},[t._v("全部")]),t._l(t.projectList,(function(e){return[s("a-select-option",{attrs:{value:e.id}},[t._v("\n\t\t\t\t\t\t\t\t"+t._s(e.title)+"\n\t\t\t\t\t\t\t")])]}))],2)],1):t._e(),s("span",{staticClass:"select-option"},[s("label",{staticStyle:{"margin-right":"5px"}},[t._v("项目处理人：")]),s("a-button",{staticStyle:{width:"210px",margin:"0 10px 10px 0"},on:{click:t.showDepartmentList}},[t.chooseNum>0?s("span",[t._v("已选择"+t._s(t.chooseUserNum)+"名成员，"+t._s(t.chooseDepartmentNum)+"个部门")]):s("span",[t._v("选择成员")])])],1),s("span",{staticClass:"select-option"},[s("label",{staticStyle:{"margin-right":"5px"}},[t._v("完成时间：")]),s("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00:00","HH:mm:ss"),t.moment("23:59:59","HH:mm:ss")]},format:"YYYY-MM-DD HH:mm:ss",allowClear:"","disabled-date":t.disabledDate},on:{change:t.changeTime},model:{value:t.joinTime,callback:function(e){t.joinTime=e},expression:"joinTime"}})],1),s("a-button",{staticStyle:{margin:"0 10px 10px 0"},attrs:{type:"primary"},on:{click:t.find}},[t._v("查找")]),s("a-button",{on:{click:t.clear}},[t._v("清空")])],1),s("div",{ref:"scroll",staticClass:"content-bd"},[s("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[s("a-empty",{directives:[{name:"show",rawName:"v-show",value:0==t.list.length,expression:"list.length == 0"}],staticStyle:{margin:"100px auto 0"}}),t._l(t.list,(function(e,a){return s("div",{directives:[{name:"show",rawName:"v-show",value:t.list.length>0,expression:"list.length > 0"}],staticClass:"part",attrs:{id:"part"+e.id}},[s("a-spin",{attrs:{tip:"Loading...",spinning:t.isSpinning[a]}},[s("div",{staticClass:"part-title"},[s("span",{staticClass:"part-title-left"},[t._v(t._s(e.title)+"（"+t._s(e.count)+"）")])]),s("a-row",{directives:[{name:"perfect-scroll-bar",rawName:"v-perfect-scroll-bar",value:t.perfectScrollBarOptions,expression:"perfectScrollBarOptions"}],ref:"part_body_"+e.id,refInFor:!0,staticClass:"part-body",attrs:{id:"partBody"+e.id},on:{"ps-scroll-down":function(i){return t.handleScroll(e.id)},"ps-scroll-y":function(i){return t.changeScrollPosition(i,e.id)}}},[0==e.members.length?s("a-empty",{staticStyle:{"margin-top":"100px"}}):t._e(),t._l(e.members,(function(a){return e.members.length>0?s("div",{ref:"part_body_card_"+a.id,refInFor:!0,staticClass:"part-body-card",attrs:{id:a.id}},[s("div",{staticStyle:{padding:"10px 10px 0","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden","word-break":"break-all","font-weight":"700"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.title)+"\n\t\t\t\t\t\t\t\t\t\t")]),s("div",{staticStyle:{overflow:"hidden",padding:"10px 5px 10px 10px"},on:{click:function(e){1!=a.status&&t.customDetail(a.id,a.end_time,a.task_id,a.external_userid,a.sea_id)}}},[a.avatar?s("img",{staticStyle:{float:"left",width:"47px","margin-right":"5px",height:"47px","border-radius":"5px"},attrs:{src:a.avatar,alt:""}}):t._e(),a.avatar?t._e():s("img",{staticStyle:{float:"left",width:"47px","margin-right":"5px",height:"47px","border-radius":"5px"},attrs:{src:i("4bef"),alt:""}}),s("div",{staticStyle:{float:"left",width:"calc(100% - 52px)","font-size":"12px"}},[s("div",{staticStyle:{"word-wrap":"break-word","word-break":"break-all","white-space":"normal"}},[s("a-popover",{attrs:{placement:"top"}},[s("template",{slot:"content"},[s("p",[t._v(t._s(a.name))])]),""!=a.name?[s("span",[t._v(t._s(a.nickname))]),s("span",{class:null!=a.corp_name?"corp-name":"wx-name"},[0==a.s_type?[null!=a.corp_name?[t._v("@"+t._s(a.corp_name))]:[t._v("@微信")]]:t._e()],2)]:t._e()],2),""==a.name?[s("span",[t._v(t._s(a.nickname))]),s("span",{class:null!=a.corp_name?"corp-name":"wx-name"},[0==a.s_type?[null!=a.corp_name?[t._v("@"+t._s(a.corp_name))]:[t._v("@微信")]]:t._e()],2)]:t._e()],2),s("p",{staticStyle:{"margin-top":"5px"}},[s("span",{staticStyle:{overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis","max-width":"89px",display:"inline-block",border:"1px solid","border-radius":"4px",padding:"0 5px"},style:{"border-color":a.level_color,color:a.level_color}},[t._v(t._s(a.level))]),s("a-progress",{staticStyle:{"font-size":"12px",width:"100px","vertical-align":"top",float:"right"},attrs:{percent:Number(a.per),size:"small",strokeColor:"#1890ff"}})],1)])]),s("div",{staticStyle:{padding:"0 10px","margin-bottom":"10px"},on:{click:function(e){1!=a.status&&t.customDetail(a.id,a.end_time,a.task_id,a.external_userid,a.sea_id)}}},[3==a.status?s("p",{staticStyle:{overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t项目实际完成："+t._s(a.finish_time)+"\n\t\t\t\t\t\t\t\t\t\t\t\t"),1==a.is_finish?s("span",[t._v("（按时完成）")]):t._e(),2==a.is_finish?s("span",{staticStyle:{color:"red"}},[t._v("（超时"+t._s(a.delay_days)+"天）")]):t._e(),3==a.is_finish?s("span",[t._v("（提前"+t._s(a.pre_days)+"天）")]):t._e()]):t._e(),2==a.status||3==a.status?s("p",{staticStyle:{overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t项目开始时间："+t._s(a.start_time))]):t._e(),2==a.status||3==a.status?s("p",{staticStyle:{overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t预计截止时间："+t._s(a.end_time))]):t._e(),1==a.status?s("p",{staticStyle:{"white-space":"normal"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t项目规则："),1==a.type?s("span",[t._v("手动启动，")]):t._e(),2==a.type?s("span",[t._v("立即启动，")]):t._e(),3==a.type?s("span",[t._v("在"+t._s(a.start_days)+"天后启动，")]):t._e(),t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t需要"+t._s(a.days)+"天内完成\n\t\t\t\t\t\t\t\t\t\t\t")]):t._e()]),s("div",{staticStyle:{background:"#F3F3F3",padding:"7px 10px",overflow:"hidden","font-size":"12px"}},[s("div",{staticStyle:{float:"left","line-height":"24px"}},[t._v("处理人："),s("span",{staticStyle:{color:"#1890FF","margin-right":"5px"}},[t._v(t._s(a.user_name))])]),1==a.status&&1==a.can_edit?s("a-button",{staticStyle:{float:"right",height:"24px"},attrs:{type:"primary"},on:{click:function(e){return t.startRecord(a.id)}}},[t._v("开始\n\t\t\t\t\t\t\t\t\t\t\t")]):t._e(),2==a.status&&1==a.can_edit?s("a-button",{staticStyle:{float:"right",height:"24px"},attrs:{type:"primary"},on:{click:function(i){return t.addRecord(a.id,e.id,a.per,a.nickname,a.end_time,a.task_id,a.external_userid,a.sea_id)}}},[t._v("项目跟进\n\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1)]):t._e()}))],2)],1)],1)}))],2)],1)],1),s("a-drawer",{attrs:{title:t.drawTitle,placement:"right",closable:!1,visible:t.recordVisible,width:"460px!important"},on:{close:t.onClose}},[s("div",{staticStyle:{height:"calc(100% - 51px)",overflow:"auto"}},[s("div",{staticStyle:{padding:"20px 20px 0"}},[t._v("\n\t\t\t\t\t\t预计截止时间："),s("span",[t._v(t._s(t.finishTime))])]),s("div",{staticStyle:{padding:"20px 20px 0"}},[s("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("项目状态：\n\t\t\t\t\t\t"),s("a-select",{staticStyle:{width:"180px"},model:{value:t.status,callback:function(e){t.status=e},expression:"status"}},t._l(t.follows,(function(e){return s("a-select-option",{attrs:{value:e.id}},[t._v("\n\t\t\t\t\t\t\t\t"+t._s(e.title)+"\n\t\t\t\t\t\t\t")])})),1)],1),s("div",{staticStyle:{padding:"20px"}},[s("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("项目进度：\n\t\t\t\t\t\t"),s("a-input-number",{staticStyle:{width:"165px","margin-right":"5px"},attrs:{placeholder:"请输入项目进度"},model:{value:t.close_rate,callback:function(e){t.close_rate=e},expression:"close_rate"}}),t._v("\n\t\t\t\t\t\t%\n\t\t\t\t\t")],1),s("div",{staticClass:"textArea"},[s("div",{staticStyle:{"margin-bottom":"10px"}},[s("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("进度说明：\n\t\t\t\t\t\t")]),t.showTextArea?s("a-textarea",{attrs:{placeholder:"请输入进度说明，200字以内","auto-size":{minRows:5,maxRows:20},maxLength:200},on:{change:t.changeText}}):t._e(),s("div",{staticStyle:{"text-align":"right","margin-top":"10px"}},[t._v(t._s(t.followMsg.length)+"/200\n\t\t\t\t\t\t")])],1)]),s("div",{staticClass:"footer"},[s("a-button",{staticStyle:{marginRight:"8px"},on:{click:t.onClose}},[t._v("\n\t\t\t\t\t\t取消\n\t\t\t\t\t")]),s("a-button",{attrs:{type:"primary"},on:{click:t.onSure}},[t._v("\n\t\t\t\t\t\t确定\n\t\t\t\t\t")])],1)]),s("a-drawer",{attrs:{title:t.detalTitle,placement:"right",closable:!1,visible:t.detailVisible,width:"460px!important"},on:{close:t.detailClose}},[s("div",{staticStyle:{height:"calc(100% - 51px)",overflow:"auto"}},[t.detailData.length>0?s("div",{staticStyle:{padding:"20px 20px 0"}},[t._v("\n\t\t\t\t\t\t预计截止时间："),s("span",[t._v(t._s(t.finishTime))])]):t._e(),t.detailFinishTime.finish_time&&""!=t.detailFinishTime.finish_time?s("div",{staticStyle:{padding:"20px 20px 0"}},[t._v("\n\t\t\t\t\t\t实际完成："+t._s(t.detailFinishTime.finish_time)+"\n\t\t\t\t\t\t"),1==t.detailFinishTime.is_finish?s("span",[t._v("【按时完成】")]):t._e(),2==t.detailFinishTime.is_finish?s("span",{staticStyle:{color:"red"}},[t._v("【超时"+t._s(t.detailFinishTime.delay_days)+"天】")]):t._e(),3==t.detailFinishTime.is_finish?s("span",[t._v("【提前"+t._s(t.detailFinishTime.pre_days)+"天】")]):t._e()]):t._e(),s("div",{staticStyle:{padding:"20px 20px 0"}},[t._l(t.detailData,(function(e){return s("div",[s("p",{staticStyle:{"font-size":"16px","font-weight":"700"}},[t._v(t._s(e.date))]),s("a-steps",{attrs:{"progress-dot":"",direction:"vertical",current:e.data.length-1}},t._l(e.data,(function(e){return s("a-step",{attrs:{description:e.per_desc}},[s("template",{slot:"title"},[s("span",{staticStyle:{"font-weight":"700"}},[t._v(t._s(e.time))]),s("span",{staticStyle:{"margin-left":"10px"},style:{color:e.status_color}},[t._v(t._s(e.status_title))]),s("a-tag",{staticStyle:{float:"right",margin:"0"},attrs:{color:"green"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.per)+"%\n\t\t\t\t\t\t\t\t\t\t")])],1)],2)})),1)],1)})),0==t.detailData.length?s("a-empty",{staticStyle:{"margin-top":"100px"}}):t._e(),s("div",{directives:[{name:"show",rawName:"v-show",value:t.total2>0,expression:"total2 > 0"}],staticClass:"pagination",staticStyle:{margin:"20px 0","box-sizing":"border-box",overflow:"hidden"}},[s("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t"),s("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total2))]),t._v("条\n\t\t\t\t\t\t\t")]),s("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[s("a-pagination",{attrs:{total:t.total2,showSizeChanger:"",showQuickJumper:t.quickJumper2,current:t.page2,pageSize:t.pageSize2,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])],2)]),s("div",{staticClass:"footer"},[s("a-button",{staticStyle:{marginRight:"8px"},on:{click:t.detailClose}},[t._v("\n\t\t\t\t\t\t关闭\n\t\t\t\t\t")])],1)])],1),s("chooseDepartment",{ref:"user",attrs:{id:t.corpId1,show:t.showModalDepartment,chooseNum:t.chooseNum,callback:t.modalVisibleChange,is_special:1}})],1)},a=[],o=(i("c5f6"),i("96cf"),i("3b8d")),n=(i("ac6a"),i("7f7f"),i("c75b")),r=i("487a"),c=i.n(r),l=i("c1df"),p=i.n(l);var d={directives:{infiniteScroll:c.a},name:"todoManagementKanban",components:{chooseDepartment:n["a"]},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{moment:p.a,corpInfo:[],corpId:t,corpId1:"",corpLen:JSON.parse(localStorage.getItem("corpArr")).length,isLoading:!1,name:"",phone:"",joinTime:null,pages:[],projectList:[],projectId:"",showModalDepartment:!1,chooseNum:0,chooseUserNum:0,chooseDepartmentNum:0,checkedList:[],user:[],startX:0,scrollX:0,recordVisible:!1,showTextArea:!1,status:[],follows:[],followMsg:"",followMsgTimeout:"",close_rate:"",task_id:"",external_userid:"",sea_id:"",list:[],timeout:"",page:1,id:"",count:"",cid:"",drawTitle:"",isMasterAccount:localStorage.getItem("isMasterAccount")?localStorage.getItem("isMasterAccount"):"",customItem:{corpId:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",name:"",phone:"",projectId:"",checkedList:[],user:[],chooseNum:0,chooseUserNum:0,chooseDepartmentNum:0,joinTime:null},perfectScrollBarOptions:{suppressScrollX:!0,minScrollbarLength:15},isSpinning:[],scrollData:[],detalTitle:"",detailData:[],detailFinishTime:{},detailVisible:!1,finishTime:"",total2:1,quickJumper2:!1,page2:1,pageSize2:15}},methods:{filter:function(t,e){return e.some((function(e){return e.label.toLowerCase().indexOf(t.toLowerCase())>-1}))},scroLineDown:function(t){this.startX=t.x},scroLineUp:function(t){var e=t.x;this.scrollX=this.scrollX+e-this.startX,console.log(this.scrollX,"scrollX"),this.$refs.scroll.style.right=this.scrollX+"px",this.$forceUpdate()},changeWx:function(t){this.corpId=t,this.checkedList=[],this.chooseNum=0,this.chooseUserNum=0,this.chooseDepartmentNum=0,this.user=[]},showDepartmentList:function(){this.corpId1=this.corpId,this.showModalDepartment=!0},modalVisibleChange:function(t,e,i,s,a){"ok"==t&&(this.checkedList=e,this.chooseNum=parseInt(i)+parseInt(s),this.chooseUserNum=i,this.chooseDepartmentNum=s,this.chooseNum>0?this.user=a:this.user=[]),this.showModalDepartment=!1},disabledDate:function(t){return t&&t>p()().endOf("day")},changeTime:function(t,e){this.joinTime=t},find:function(){var t=this;this.id="",this.customItem.corpId=this.corpId,this.customItem.name=this.name,this.customItem.phone=this.phone,this.customItem.projectId=this.projectId,this.customItem.checkedList=this.checkedList,this.customItem.user=this.user,this.customItem.chooseNum=this.chooseNum,this.customItem.chooseUserNum=this.chooseUserNum,this.customItem.chooseDepartmentNum=this.chooseDepartmentNum,this.customItem.joinTime=this.joinTime,this.pages=[],this.scrollData=[],this.cid="",this.getAccount(),this.$nextTick((function(){t.list.map((function(e){t.changeScrollTop(!1,0,e.id)}))}));var e=document.getElementsByClassName("part-body-card");Array.prototype.forEach.call(e,(function(t){t.style.border=""}))},clear:function(){var t=this;this.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",this.name="",this.id="",this.cid="",this.phone="",this.projectId="",this.checkedList=[],this.user=[],this.chooseNum=0,this.chooseUserNum=0,this.chooseDepartmentNum=0,this.joinTime=null,this.customItem.corpId=this.corpId,this.customItem.name=this.name,this.customItem.phone=this.phone,this.customItem.projectId=this.projectId,this.customItem.checkedList=this.checkedList,this.customItem.user=this.user,this.customItem.chooseNum=this.chooseNum,this.customItem.chooseUserNum=this.chooseUserNum,this.customItem.chooseDepartmentNum=this.chooseDepartmentNum,this.customItem.joinTime=this.joinTime,this.pages=[],this.scrollData=[],this.getAccount(),this.$nextTick((function(){t.list.map((function(e){t.changeScrollTop(!1,0,e.id)}))}));var e=document.getElementsByClassName("part-body-card");Array.prototype.forEach.call(e,(function(t){t.style.border=""}))},groupSend:function(t){this.customItem.id=t,this.$router.push({path:"/massMessage/add",query:{item:this.customItem}})},addRecord:function(t,e,i,s,a,o,n,r){this.list.map((function(e,i){e.members.map((function(e){e.cid==t&&(e.popVisible=!1)}))})),this.cid=t,this.getFollowStatus(e,i),this.drawTitle=s,this.followMsg="",this.finishTime=a,this.task_id=o,this.external_userid=n,this.sea_id=r;var c=document.getElementsByClassName("part-body-card");Array.prototype.forEach.call(c,(function(t){t.style.border=""}))},changeText:function(t){var e=this;clearTimeout(this.followMsgTimeout),this.followMsgTimeout=setTimeout((function(){e.followMsg=t.target.value}),3)},onSure:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i,s,a=this;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(""!=this.followMsg.trim()){t.next=3;break}return this.$message.error("进度说明不能为空！"),t.abrupt("return",!1);case 3:if(""!=this.status){t.next=6;break}return this.$message.warning("请选择项目状态！"),t.abrupt("return",!1);case 6:if(e=/^(?:0|[1-9][0-9]?|100)$/,null!=this.close_rate&&""!=this.close_rate){t.next=10;break}return this.$message.warning("请填写项目进度！"),t.abrupt("return",!1);case 10:if(null==this.close_rate||""==this.close_rate||!(this.close_rate<0||this.close_rate>100)&&e.test(this.close_rate)){t.next=13;break}return this.$message.warning("项目进度必须为0-100正整数！"),t.abrupt("return",!1);case 13:return t.next=15,this.axios.post("wait-project/add-project-status",{uid:localStorage.getItem("uid"),id:this.cid,per_desc:this.followMsg,status:this.status,per:this.close_rate,task_id:this.task_id,external_userid:this.external_userid,sea_id:this.sea_id});case 15:i=t.sent,s=i.data,0!=s.error?this.$message.error(s.error_msg):(this.recordVisible=!1,this.status=1,this.followMsg="",this.close_rate="",this.showTextArea=!1,this.id="",0==this.pages.length&&this.list.length>0&&this.list.map((function(t){a.pages.push(t.page)})),this.getAccount());case 18:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),onClose:function(){this.recordVisible=!1,this.status=1,this.followMsg="",this.close_rate="",this.finishTime="",this.showTextArea=!1},customDetail:function(t,e,i,s,a){this.list.map((function(e,i){e.members.map((function(e){e.cid==t&&(e.popVisible=!1)}))})),this.$forceUpdate(),this.finishTime=e,this.task_id=i,this.external_userid=s,this.sea_id=a,this.cid=t;var o=document.getElementsByClassName("part-body-card");Array.prototype.forEach.call(o,(function(t){t.style.border=""})),this.getDetail()},getDetail:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i,s,a,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,i=o.length>1&&void 0!==o[1]?o[1]:this.pageSize2,t.next=4,this.axios.post("wait-project/wait-info",{id:this.cid,task_id:this.task_id,external_userid:this.external_userid,sea_id:this.sea_id,page:e,page_size:i});case 4:s=t.sent,a=s.data,0!=a.error?this.$message.error(a.error_msg):(this.detalTitle=a.data.title,this.detailData=a.data.info,this.detailFinishTime=a.data.time,this.total2=parseInt(a.data.count),this.page2=e,this.pageSize2=i,this.quickJumper2=this.total2>this.pageSize2,this.detailVisible=!0);case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changePage:function(t,e){this.getDetail(t,e)},showSizeChange:function(t,e){this.getDetail(1,e)},detailClose:function(){this.detalTitle="",this.finishTime="",this.detailData=[],this.detailVisible=!1},getAccount:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i,s,a,o,n,r,c,l=this;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(""==this.id)this.isLoading=!0;else if(this.list.length>0)for(e=0;e<this.list.length;e++)this.list[e].id==this.id&&(this.isSpinning[e]=!0);return this.$forceUpdate(),i="",""!=this.id&&this.list.length>0?this.list.map((function(t){t.id==l.id&&(i=t.page)})):i=1,t.next=6,this.axios.post("wait-project/wait-project-board",{uid:localStorage.getItem("uid"),isMasterAccount:this.isMasterAccount,sub_id:localStorage.getItem("sub_id"),corp_id:this.corpId,name:this.name,phone:this.phone,user_ids:this.checkedList,page:i,pages:this.pages,id:this.id,project_id:this.projectId,start_time:this.joinTime&&this.joinTime.length>1?this.joinTime[0].format("YYYY-MM-DD HH:mm:ss"):"",end_time:this.joinTime&&this.joinTime.length>1?this.joinTime[1].format("YYYY-MM-DD HH:mm:ss"):""});case 6:if(s=t.sent,a=s.data,0!=a.error){if(""==this.id)this.isLoading=!1;else for(o=0;o<this.list.length;o++)this.list[o].id==this.id&&(this.isSpinning[o]=!1);this.$message.error(a.message)}else{for(n=this,0==n.pages.length?(1==i&&(n.list=a.data.info,n.list.map((function(t,e){t.page=1,n.scrollData[e]={position:null}}))),i>1&&n.list.map((function(t,e){t.id&&t.id==n.id&&t.members.length<t.count&&(t.members=t.members.concat(a.data.info[e].members))}))):n.list=a.data.info,n.list.map((function(t,e){n.isSpinning[e]=!1})),n.isLoading=!1,r=0;r<n.list.length;r++)for(n.list[r].id==n.id&&(n.isSpinning[r]=!1),c=0;c<n.list[r].members;c++)n.list[r].members[c].popVisible=!1;n.$forceUpdate(),n.$nextTick((function(){var t=n.$refs.searchArea.$el.clientHeight;n.$refs.scroll.style.height="calc(100% - "+t+"px)"})),""!=n.cid&&n.$nextTick((function(){var t=n.$refs["part_body_card_"+n.cid][0];t.style.boxShadow="0 2px 8px 0 rgba(0, 0, 0, .12)",t.style.border="2px solid #1890ff"}))}case 9:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getprojectList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("wait-project/project",{corp_id:this.corpId});case 2:e=t.sent,i=e.data,0!=i.error?this.$message.error(i.error_msg):this.projectList=i.data;case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),mouseIn:function(t){clearTimeout(this.timeout),this.list.map((function(e,i){e.members.map((function(e){e.cid==t&&(e.popVisible=!0)}))})),this.$forceUpdate()},mouseOut:function(t){var e=this;e.list.map((function(i,s){i.members.map((function(i){i.cid==t&&(e.timeout=setTimeout((function(){i.popVisible=!1,e.$forceUpdate()}),10))}))}))},handleScroll:function(t){var e=this,i=this.$refs["part_body_"+t][0].$el._ps_.reach.y;t!=this.id&&(this.id=t,this.list.map((function(t){t.id==e.id&&(e.page=t.page)}))),this.list.map((function(t){t.id==e.id&&(e.count=t.count)})),"end"===i&&this.count/15>this.page&&(++this.page,this.list.map((function(t){t.id==e.id&&(t.page=e.page)})),this.pages=[],this.cid="",this.corpId=this.customItem.corpId,this.name=this.customItem.name,this.phone=this.customItem.phone,this.projectId=this.customItem.projectId,this.checkedList=this.customItem.checkedList,this.user=this.customItem.user,this.chooseNum=this.customItem.chooseNum,this.chooseUserNum=this.customItem.chooseUserNum,this.chooseDepartmentNum=this.customItem.chooseDepartmentNum,this.joinTime=this.customItem.joinTime,this.getAccount())},getFollowStatus:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e,i){var s,a,o,n,r,c;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("wait-project/common-detail",{uid:localStorage.getItem("uid")});case 2:if(s=t.sent,a=s.data,0==a.error){t.next=8;break}this.$message.error(a.error_msg),t.next=24;break;case 8:for(this.follows=[],o=1;o<a.data.project_status.length;o++)this.follows.push(a.data.project_status[o]);n=0;case 11:if(!(n<this.follows.length)){t.next=18;break}if(this.follows[n].id!=e){t.next=15;break}return this.status=Number(e),t.abrupt("break",18);case 15:n++,t.next=11;break;case 18:for(""==this.status&&(this.status=Number(this.follows[0].id)),this.close_rate=i,this.recordVisible=!0,this.showTextArea=!0,r=document.getElementsByClassName("ant-popover"),c=0;c<r.length;c++)r[c].style.display="none";case 24:case"end":return t.stop()}}),t,this)})));function e(e,i){return t.apply(this,arguments)}return e}(),changeScrollPosition:function(t,e){var i=this;i.list.map((function(s,a){s.id==e&&i.scrollData.length>0&&"undefined"!=typeof i.scrollData[a].position&&(i.scrollData[a].position=t.srcElement.scrollTop)}))},changeScrollTop:function(){var t=this,e=(arguments.length>0&&void 0!==arguments[0]&&arguments[0],arguments.length>1&&void 0!==arguments[1]?arguments[1]:null),i=arguments.length>2?arguments[2]:void 0;this.$nextTick((function(){t.$refs["part_body_"+i][0].$el.scrollTop=e}))},startRecord:function(t){var e=document.getElementsByClassName("part-body-card");Array.prototype.forEach.call(e,(function(t){t.style.border=""}));var i=this;i.cid=t,i.$confirm({title:"确定开始吗？",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){i.startRecord2()}})},startRecord2:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e){var i,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("wait-project/start",{uid:localStorage.getItem("uid"),id:this.cid});case 2:i=t.sent,s=i.data,0!=s.error?this.$message.error(s.error_msg):this.getAccount();case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}()},created:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.pages=[],t.cid="",t.getAccount(),t.getprojectList()}))},beforeRouteEnter:function(t,e,i){"/customManage/detail"==e.path?i((function(t){t.id="",t.cid="",t.corpId=t.customItem.corpId,t.name=t.customItem.name,t.phone=t.customItem.phone,t.projectId=t.customItem.projectId,t.checkedList=t.customItem.checkedList,t.user=t.customItem.user,t.chooseNum=t.customItem.chooseNum,t.chooseUserNum=t.customItem.chooseUserNum,t.chooseDepartmentNum=t.customItem.chooseDepartmentNum,t.joinTime=t.customItem.joinTime,0==t.pages.length&&t.list.length>0&&t.list.map((function(e){t.pages.push(e.page)})),t.getAccount(),t.list.map((function(e,i){"undefined"!==typeof t.$refs["part_body_"+e.id][0]&&"undefined"!==typeof t.scrollData[i]&&null!==t.scrollData[i].position&&t.changeScrollTop(!1,t.scrollData[i].position,e.id)}))})):i((function(t){t.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",t.id="",t.cid="",t.name="",t.phone="",t.projectId="",t.checkedList=[],t.user=[],t.chooseNum=0,t.chooseUserNum=0,t.chooseDepartmentNum=0,t.joinTime=null,t.pages=[]})),i()}},h=d,u=(i("0b45"),i("2877")),m=Object(u["a"])(h,s,a,!1,null,"792e4018",null);e["default"]=m.exports},"487a":function(t,e,i){(function(e,i){t.exports=i()})(0,(function(){"use strict";var t="@@InfiniteScroll",e=function(t,e){var i,s,a,o,n,r=function(){t.apply(o,n),s=i};return function(){if(o=this,n=arguments,i=Date.now(),a&&(clearTimeout(a),a=null),s){var t=e-(i-s);t<0?r():a=setTimeout((function(){r()}),t)}else r()}},i=function(t){return t===window?Math.max(window.pageYOffset||0,document.documentElement.scrollTop):t.scrollTop},s=document.defaultView.getComputedStyle,a=function(t){var e=t;while(e&&"HTML"!==e.tagName&&"BODY"!==e.tagName&&1===e.nodeType){var i=s(e).overflowY;if("scroll"===i||"auto"===i)return e;e=e.parentNode}return window},o=function(t){return t===window?document.documentElement.clientHeight:t.clientHeight},n=function(t){return t===window?i(window):t.getBoundingClientRect().top+i(window)},r=function(t){var e=t.parentNode;while(e){if("HTML"===e.tagName)return!0;if(11===e.nodeType)return!1;e=e.parentNode}return!1},c=function(){if(!this.binded){this.binded=!0;var t=this,i=t.el,s=i.getAttribute("infinite-scroll-throttle-delay"),o=200;s&&(o=Number(t.vm[s]||s),(isNaN(o)||o<0)&&(o=200)),t.throttleDelay=o,t.scrollEventTarget=a(i),t.scrollListener=e(l.bind(t),t.throttleDelay),t.scrollEventTarget.addEventListener("scroll",t.scrollListener),this.vm.$on("hook:beforeDestroy",(function(){t.scrollEventTarget.removeEventListener("scroll",t.scrollListener)}));var n=i.getAttribute("infinite-scroll-disabled"),r=!1;n&&(this.vm.$watch(n,(function(e){t.disabled=e,!e&&t.immediateCheck&&l.call(t)})),r=Boolean(t.vm[n])),t.disabled=r;var c=i.getAttribute("infinite-scroll-distance"),p=0;c&&(p=Number(t.vm[c]||c),isNaN(p)&&(p=0)),t.distance=p;var d=i.getAttribute("infinite-scroll-immediate-check"),h=!0;d&&(h=Boolean(t.vm[d])),t.immediateCheck=h,h&&l.call(t);var u=i.getAttribute("infinite-scroll-listen-for-event");u&&t.vm.$on(u,(function(){l.call(t)}))}},l=function(t){var e=this.scrollEventTarget,s=this.el,a=this.distance;if(!0===t||!this.disabled){var r=i(e),c=r+o(e),l=!1;if(e===s)l=e.scrollHeight-c<=a;else{var p=n(s)-n(e)+s.offsetHeight+r;l=c+a>=p}l&&this.expression&&this.expression()}},p={bind:function(e,i,s){e[t]={el:e,vm:s.context,expression:i.value};var a=arguments;e[t].vm.$on("hook:mounted",(function(){e[t].vm.$nextTick((function(){r(e)&&c.call(e[t],a),e[t].bindTryCount=0;var i=function i(){e[t].bindTryCount>10||(e[t].bindTryCount++,r(e)?c.call(e[t],a):setTimeout(i,50))};i()}))}))},unbind:function(e){e&&e[t]&&e[t].scrollEventTarget&&e[t].scrollEventTarget.removeEventListener("scroll",e[t].scrollListener)}},d=function(t){t.directive("InfiniteScroll",p)};return window.Vue&&(window.infiniteScroll=p,Vue.use(d)),p.install=d,p}))},"4bef":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAABAElEQVR42s3VIY7CUBAG4LkEliC4xKJ6BA6AognXQBLCAVBYNBK3pim7zP8XBAJHSLhAZVURi+jC2+681xD4R89nZiYjIhojQRlQicYiolFQ8600EqZNAKbSpB0lyqcCBeYccIA5ihDgtO3JLdseTr7Ap9wFRx+g2HTuge8PH+AsjuBiBzZO4MsOHJzAwQ7kTiD3mcLsoX3muQcaV9s1DtnENUfssssR1i+6hRcD3GPFBaeccoEV9z7AUodoP4yxrUMs/wd26EtN0MeuBuBYDOH4LyDPWhYga1WXugpMxBhMnIBGVqD6S34B9nqvTUwaAUnNpZue689/DnuwTDUSuQJ1pL7YQ6lKkQAAAABJRU5ErkJggg=="},7116:function(t,e,i){var s=i("7fa1");"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var a=i("499e").default;a("7b445f8e",s,!0,{sourceMap:!1,shadowMode:!1})},"7fa1":function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,"[data-v-792e4018] .ant-card-bordered{border:0}.content-bd[data-v-792e4018]::-webkit-scrollbar{width:4px;height:10px}.content-bd[data-v-792e4018]::-webkit-scrollbar-thumb{border-radius:10px;-webkit-box-shadow:inset 0 0 5px rgba(0,0,0,.2);background:#535353}p[data-v-792e4018]{margin-bottom:0}.select-option[data-v-792e4018]{display:inline-block;margin-right:10px;margin-bottom:10px}.select-option label[data-v-792e4018]{display:inline-block;text-align:right;width:100px}.tpl-title[data-v-792e4018]{float:left;font-size:16px;vertical-align:top}.content-bd[data-v-792e4018]{min-width:885px;margin:0 20px;overflow-y:hidden;overflow-x:auto;white-space:nowrap;padding-bottom:20px}a[data-v-792e4018]:active,a[data-v-792e4018]:hover,a[data-v-792e4018]:link,a[data-v-792e4018]:visited{text-decoration:none}.part[data-v-792e4018]{width:300px;height:100%;display:inline-block;background:#f7f7f7;margin-right:15px;-webkit-box-shadow:0 2px 8px rgba(0,0,0,.2);box-shadow:0 2px 8px rgba(0,0,0,.2);position:relative;cursor:-webkit-grabbing;cursor:grabbing}.part[data-v-792e4018]:hover{background:#f9f9f9}.part-title[data-v-792e4018]{padding:15px;overflow:hidden;height:56px}.part-body[data-v-792e4018]{padding:0 16px;-webkit-box-sizing:border-box;box-sizing:border-box;overflow:auto;position:absolute;top:57px;bottom:0;width:300px}.part-title-left[data-v-792e4018]{float:left;color:rgba(0,0,0,.6);font-size:16px;font-weight:700}.part-title-right[data-v-792e4018]{float:right;margin-top:4.5px;font-size:18px;cursor:pointer}.part-body-card[data-v-792e4018],.part-body-card2[data-v-792e4018]{background:#fff;margin-bottom:15px;-webkit-box-shadow:0 1px 4px 0 rgba(0,0,0,.08);box-shadow:0 1px 4px 0 rgba(0,0,0,.08)}.part-body-card2[data-v-792e4018]:hover,.part-body-card[data-v-792e4018]:hover{-webkit-box-shadow:0 2px 8px 0 rgba(0,0,0,.12);box-shadow:0 2px 8px 0 rgba(0,0,0,.12)}.url-btn[data-v-792e4018]{cursor:pointer}.part-title-right[data-v-792e4018]:hover,.url-btn[data-v-792e4018]:hover{color:#1890ff}.textArea[data-v-792e4018]{padding:0 20px}.textArea-btn[data-v-792e4018]{padding:20px;overflow:hidden}.upload-file[data-v-792e4018] .ant-upload.ant-upload-select-picture-card{background-color:#fff;border:0;height:32px}[data-v-792e4018] .ant-upload.ant-upload-select-picture-card>.ant-upload{padding:0}.footer[data-v-792e4018]{position:absolute;bottom:63px;width:100%;border-top:1px solid #e8e8e8;padding:10px 16px;text-align:right;background:#fff}[data-v-792e4018] .ant-spin-spinning:first-child{width:100%;margin-top:100px}[data-v-792e4018] .ant-spin-container,[data-v-792e4018] .ant-spin-nested-loading:first-child{height:100%}[data-v-792e4018] .ant-input-number-handler-wrap{display:none}[data-v-792e4018] .ant-popover-inner-content{padding:8px 16px}.content-bd[data-v-792e4018] .ant-spin-container:first-child{display:-webkit-box;display:-ms-flexbox;display:flex}.part[data-v-792e4018] .ant-spin-container{width:300px}[data-v-792e4018] .ant-steps-item-subtitle{margin:0}.ant-steps-dot.ant-steps-small .ant-steps-item-content[data-v-792e4018],[data-v-792e4018] .ant-steps-dot .ant-steps-item-content{width:calc(100% - 26px)}[data-v-792e4018] .ant-steps-item-title{width:100%}.ant-steps-dot.ant-steps-small .ant-steps-item-process .ant-steps-item-icon[data-v-792e4018],[data-v-792e4018] .ant-steps-dot .ant-steps-item-process .ant-steps-item-icon{width:8px;height:8px}[data-v-792e4018] .ant-progress-status-success .ant-progress-text{color:#1890ff}",""])}}]);