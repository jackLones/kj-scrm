(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-bbfb2a94"],{"0e85":function(t,a,e){"use strict";var i=e("c241"),s=e.n(i);s.a},64283:function(t,a,e){"use strict";e.r(a);var i=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("div",{staticStyle:{width:"100%","max-height":"100%",position:"absolute","overflow-y":"auto","padding-bottom":"30px"}},[i("div",[i("a-card",{staticStyle:{"margin-bottom":"20px",padding:"10px 20px"}},[i("label",{staticClass:"tpl-title"},[t._v("客户详情")]),i("a-button",{staticStyle:{"font-size":"14px",float:"right"},attrs:{type:"primary",icon:"rollback"},on:{click:t.goBack}},[t._v("返回列表\n\t\t\t\t")])],1),i("div",{staticClass:"content-bd"},[i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[i("div",{staticClass:"custom-info"},[i("a-avatar",{staticStyle:{"vertical-align":"top"},attrs:{shape:"square",size:132,src:t.avatar}}),i("div",{staticClass:"custom-info-text"},[i("div",{staticClass:"col"},[i("span",{staticStyle:{"font-size":"20px","font-weight":"700"}},[t._v(t._s(t.name)+"\n\t\t\t\t\t\t\t\t\t"),""!=t.corp_name?i("span",{class:null!=t.corp_name?"corp-name":"wx-name"},[null!=t.corp_name?[t._v("@"+t._s(t.corp_name))]:[t._v("@微信")]],2):t._e(),t.remarkVisible||""==t.nickname?t._e():i("span",[t._v("（"+t._s(t.nickname)+"）")]),"男性"==t.gender?i("a-icon",{staticStyle:{color:"#427EBA"},attrs:{slot:"prefix",type:"man"},slot:"prefix"}):t._e(),"女性"==t.gender?i("a-icon",{staticStyle:{color:"#ED4997"},attrs:{slot:"prefix",type:"woman"},slot:"prefix"}):t._e()],1),t.followTitle?i("a-tag",{staticStyle:{float:"right","margin-right":"0",height:"25px","line-height":"25px"},attrs:{color:"#67c23a"}},[t._v(t._s(t.followTitle)+"\n\t\t\t\t\t\t\t\t")]):t._e()],1),1!=t.is_hide_phone?i("div",{staticClass:"col"},[i("label",[t._v("\n\t\t\t\t\t\t\t\t\t手机："),t.phone&&""!=t.phone?i("span",[t._v(t._s(t.phone))]):i("span",[t._v("暂无")])])]):t._e(),i("div",{staticClass:"col"},[i("label",[t._v("\n\t\t\t\t\t\t\t\t\t所在地："),t.area&&""!=t.area?i("span",[t._v(t._s(t.area))]):i("span",[t._v("暂无")])])]),i("div",{staticClass:"col"},[i("span",[i("label",[t._v("标签：")]),i("span",{staticStyle:{display:"inline-block",width:"calc(100% - 45px)","vertical-align":"text-top"}},[t.hasTagsValue&&t.hasTagsValue.length>5?i("span",[i("a-popover",[i("span",{attrs:{slot:"content"},slot:"content"},[i("div",{staticClass:"over-width"},t._l(t.hasTagsValue,(function(a){return i("a-tag",{staticStyle:{"margin-bottom":"5px"},attrs:{color:"blue"}},[t._v("\n                                          "+t._s(a)+"\n                                        ")])})),1)]),t._l(t.hasTagsValue,(function(a,e){return i("span",[e<5?i("a-tag",{staticStyle:{"margin-bottom":"5px"},attrs:{color:"blue"}},[t._v("\n                                          "+t._s(a)+"\n                                        ")]):t._e()],1)})),i("span",[t._v("等"+t._s(t.hasTagsValue.length)+"个标签")])],2)],1):t._e(),t.hasTagsValue&&t.hasTagsValue.length<5?i("span",t._l(t.hasTagsValue,(function(a){return i("a-tag",{staticStyle:{"margin-bottom":"5px"},attrs:{color:"blue"}},[t._v("\n                                          "+t._s(a)+"\n                                        ")])})),1):t._e(),0==t.hasTagsValue.length?i("span",[t._v("暂无")]):t._e()])])]),i("div",{staticClass:"col"},[i("span",[i("label",[t._v("描述：")]),t.des&&""!=t.des?i("span",[t._v(t._s(t.des))]):t._e(),""==t.des?i("span",[t._v("暂无")]):t._e(),t.file3?i("img",{staticStyle:{"max-width":"100px","max-height":"100px",margin:"0 0 10px 10px"},attrs:{src:this.commonUrl+t.file3,alt:"avatar"}}):t._e()])])]),i("div",{staticClass:"custom-info-box"},[i("div",{staticClass:"box"},[i("p",{staticClass:"box-first"},[t._v("预计成交率")]),i("p",{staticClass:"box-second"},[t.closeRateVisible||null===t.close_rate?t._e():i("span",[t._v(t._s(t.close_rate)+" %")]),t.closeRateVisible||null!==t.close_rate?t._e():i("span",[t._v("暂无")])])]),i("div",{staticClass:"box"},[i("p",{staticClass:"box-first"},[t._v("所在群"),t.chatName.length>2?i("span",[t._v("（"+t._s(t.chatName.length)+"个）")]):t._e()]),t._l(t.chatName,(function(a){return t.chatName&&t.chatName.length>0?i("p",{staticClass:"box-second"},[i("a-popover",{attrs:{placement:"top"}},[i("template",{slot:"content"},[i("div",{staticStyle:{"max-width":"300px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.name)+"（"+t._s(a.join_time)+"）\n\t\t\t\t\t\t\t\t\t\t\t")])]),i("span",{staticStyle:{cursor:"pointer",display:"inline-block","max-width":"180px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden"}},[t._v(t._s(a.name)+"（"+t._s(a.join_time)+"）")])],2)],1):t._e()})),t.chatName&&0!=t.chatName.length?t._e():i("p",{staticClass:"box-second"},[t._v("暂无")])],2),i("div",{staticClass:"box"},[i("p",{staticClass:"box-first"},[t._v("上次联系")]),i("p",{staticClass:"box-second"},[t.follow_time&&""!=t.follow_time?i("span",[t._v(t._s(t.follow_time))]):i("span",[t._v("暂无")])])]),i("div",{staticClass:"box"},[i("p",{staticClass:"box-first"},[t._v("联系次数")]),i("p",{staticClass:"box-second"},[i("a-popover",{attrs:{placement:"top"}},[i("template",{slot:"content"},[i("div",{staticStyle:{"max-height":"300px","overflow-y":"auto"}},t._l(t.follow_times,(function(a){return i("div",[t._v(t._s(a.name)+"："+t._s(a.follow_num)+"次\n\t\t\t\t\t\t\t\t\t\t\t\t")])})),0)]),t.follow_num&&""!=t.follow_num?i("span",{staticStyle:{cursor:"pointer"}},[t._v(t._s(t.follow_num))]):t._e()],2),""==t.follow_num||null==t.follow_num?i("span",[t._v("暂无")]):t._e()],1)]),i("div",{staticClass:"box"},[i("p",{staticClass:"box-first"},[t._v("归属企业成员")]),t._l(t.memberInfo,(function(a){return t.memberInfo&&t.memberInfo.length>0?i("p",{staticClass:"box-second"},[i("a-popover",{attrs:{placement:"top"}},[i("template",{slot:"content"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.member)+"("+t._s(a.create_time)+")\n\t\t\t\t\t\t\t\t\t\t")]),i("span",{staticStyle:{display:"inline-block","max-width":"250px","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden",cursor:"pointer"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.member)+"("+t._s(a.create_time)+")\n\t\t\t\t\t\t\t\t\t\t")])],2)],1):i("p",[t._v("暂无")])}))],2)]),t.projectList.length>0?i("div",{staticClass:"custom-info-project single-accout-card"},[i("a-steps",{attrs:{"progress-dot":"",current:t.projectList.length-1}},t._l(t.projectList,(function(a){return i("a-step",[i("template",{slot:"title"},[t._v("\n\t\t\t\t\t\t\t\t\t\t"+t._s(a.project_name)+"\n\t\t\t\t\t\t\t\t\t")]),i("div",{attrs:{slot:"description"},slot:"description"},[3==a.status?i("div",[t._v("项目实际完成："+t._s(a.finish_time)+"\n\t\t\t\t\t\t\t\t\t\t\t"),1==a.is_finish?i("span",[t._v("（按时完成）")]):t._e(),2==a.is_finish?i("span",{staticStyle:{color:"red"}},[t._v("（逾期"+t._s(a.delay_days)+"天完成）")]):t._e(),3==a.is_finish?i("span",[t._v("（提前"+t._s(a.pre_days)+"天完成）")]):t._e()]):t._e(),1!=a.status?i("div",[t._v("项目开始时间："+t._s(a.start_time))]):t._e(),1!=a.status?i("div",[t._v("预计截止时间："+t._s(a.end_time))]):t._e(),1==a.status?i("div",[t._v("项目规则：接到任务起，需要在"+t._s(a.days)+"天内完成")]):t._e(),i("div",[t._v("项目处理人："+t._s(a.name))])])],2)})),1)],1):t._e(),i("div",{staticClass:"custom-info-tab"},[i("a-tabs",{attrs:{type:"card"},on:{change:t.changeTab},model:{value:t.tabKey,callback:function(a){t.tabKey=a},expression:"tabKey"}},[i("a-tab-pane",{key:"1",attrs:{tab:"跟进记录"}},[i("div",{staticClass:"time"},[i("a-timeline",{staticClass:"time-line"},t._l(t.followRecord,(function(a){return i("a-timeline-item",[i("div",{staticClass:"time-line-time"},[t._v(t._s(a.time))]),i("img",{staticStyle:{width:"18px"},attrs:{slot:"dot",src:e("ff27")},slot:"dot"}),i("div",{staticStyle:{margin:"10px 0"}},[i("span",{staticStyle:{color:"#1989FA"}},[t._v(t._s(a.name))]),t._v(" 发表：\n\t\t\t\t\t\t\t\t\t\t\t\t\t"),""!=a.follow_status?i("span",{staticStyle:{color:"red"}},[t._v("【"+t._s(a.follow_status)+"】")]):t._e()]),i("div",{staticClass:"time-line-title"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.record)+"\n\t\t\t\t\t\t\t\t\t\t\t\t\t"),a.file.length>0?i("div",{staticStyle:{"margin-top":"5px",overflow:"hidden"}},t._l(a.file,(function(a){return i("div",{staticClass:"preview-box"},[i("div",{staticClass:"preview-cover"},[i("img",{attrs:{src:t.commonUrl+a,alt:""}})]),i("span",{staticClass:"preview-cover-icon",on:{click:function(e){return t.previewHasImg(t.commonUrl+a)}}},[i("a-icon",{attrs:{type:"eye"}})],1)])})),0):t._e()])])})),1),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticClass:"pagination",staticStyle:{margin:"20px 0","box-sizing":"border-box",overflow:"hidden"}},[i("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t\t\t\t\t"),i("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("条\n\t\t\t\t\t\t\t\t\t\t\t")]),i("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])],1)]),i("a-tab-pane",{key:"2",attrs:{tab:"互动轨迹"}},[i("a-timeline",{staticClass:"time-line",staticStyle:{margin:"20px"}},t._l(t.interactList,(function(a){return i("a-timeline-item",[1==a.icon?i("img",{staticStyle:{width:"22px"},attrs:{slot:"dot",src:e("cf8c")},slot:"dot"}):t._e(),2==a.icon?i("img",{staticStyle:{width:"22px"},attrs:{slot:"dot",src:e("c943")},slot:"dot"}):t._e(),3==a.icon?i("img",{staticStyle:{width:"22px"},attrs:{slot:"dot",src:e("cc91")},slot:"dot"}):t._e(),4==a.icon?i("img",{staticStyle:{width:"26px"},attrs:{slot:"dot",src:e("4553")},slot:"dot"}):t._e(),5==a.icon?i("img",{staticStyle:{width:"26px"},attrs:{slot:"dot",src:e("8f93")},slot:"dot"}):t._e(),6==a.icon?i("img",{staticStyle:{width:"18px"},attrs:{slot:"dot",src:e("eacd")},slot:"dot"}):t._e(),8==a.icon?i("img",{staticStyle:{width:"18px"},attrs:{slot:"dot",src:e("ff27")},slot:"dot"}):t._e(),10==a.icon?i("img",{staticStyle:{width:"18px"},attrs:{slot:"dot",src:e("f180")},slot:"dot"}):t._e(),11==a.icon?i("img",{staticStyle:{width:"18px"},attrs:{slot:"dot",src:e("38a9")},slot:"dot"}):t._e(),12==a.icon?i("img",{staticStyle:{width:"16px"},attrs:{slot:"dot",src:e("fee6")},slot:"dot"}):t._e(),13==a.icon?i("a-icon",{attrs:{slot:"dot",type:"pay-circle"},slot:"dot"}):t._e(),i("div",{staticStyle:{"min-height":"40px"}},[i("div",{staticClass:"time-line-time"},[t._v(t._s(a.event_time))]),i("div",{staticClass:"time-line-title"},[t._v(t._s(a.content))])])],1)})),1),i("div",{staticStyle:{"text-align":"center"}},[1==t.btnFlag?i("a-button",{attrs:{type:"primary"},on:{click:t.loadMore}},[t._v("加载更多")]):t._e(),2==t.btnFlag?i("span",{attrs:{type:"primary"}},[t._v("没有更多数据了")]):t._e()],1)],1),i("a-tab-pane",{key:"3",attrs:{tab:"用户画像"}},[i("div",{staticClass:"col2"},t._l(t.field_list,(function(a,e){return"phone"==a.key&&1!=t.is_hide_phone||"phone"!=a.key?i("div",{staticClass:"half"},[i("a-tag",{attrs:{color:"#1890FF"}},[t._v(t._s(a.title))]),t.badicInfoVisible||""==a.value||8==a.type||"phone"==a.key||"company"==a.key?t._e():i("span",{staticClass:"half-text"},[t._v(t._s(a.value))]),t.badicInfoVisible||"phone"!=a.key?t._e():i("span",{staticClass:"half-text"},[""!=t.phone?i("span",[t._v(t._s(t.phone))]):t._e(),""==t.phone?i("span",[t._v("暂无")]):t._e()]),t.badicInfoVisible||"company"!=a.key?t._e():i("span",{staticClass:"half-text"},[""!=t.company?i("span",[t._v(t._s(t.company))]):t._e(),""==t.company?i("span",[t._v("暂无")]):t._e()]),t.badicInfoVisible||0==a.value.length||8!=a.type?t._e():i("span",{staticClass:"half-text"},t._l(a.value,(function(a){return i("img",{staticStyle:{"max-width":"70px","max-height":"70px",margin:"2px","line-height":"0px",cursor:"pointer"},attrs:{src:t.commonUrl+a.url},on:{click:function(e){return t.preview(t.commonUrl+a.url)}}})})),0),t.badicInfoVisible||""!=a.value&&0!=a.value.length||"phone"==a.key||"company"==a.key?t._e():i("span",{staticClass:"half-text"},[t._v("暂无")])],1):t._e()})),0)])],1)],1)],1)])],1)],1),i("a-modal",{attrs:{visible:t.previewVisible,footer:null},on:{cancel:t.handleCancel}},[i("img",{staticStyle:{width:"100%"},attrs:{alt:"example",src:t.previewImage}})])],1)},s=[],o=(e("28a5"),e("7f7f"),e("96cf"),e("3b8d")),n=e("0f2e"),l=e("c1df"),r=e.n(l);e("5c3a");r.a.locale("zh-cn");var c={components:{},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpId:t,is_hide_phone:0,isLoading:!1,commonUrl:this.$store.state.commonUrl,remarkVisible:!1,confirmLoading2:!1,id:"",avatar:"",name:"",corp_name:"",nickname:"",gender:"",followTitle:"",is_follow_del:"",phone:"",phone2:"",company:"",company2:"",area:"",des:"",visible:!1,hasTags:[],hasTagsValue:[],tagCheckValue:[],tagCheckValue2:[],disabled:[],tagsList:[],tagValue:[],addTagVisible:!1,confirmLoading3:!1,inputValue:"",editGroupId:"",groupList:[],groupId:"-1",tagLoading:!1,desVisible:!1,imageUrl3:"",file3:"",desImgLoading:!1,desText:"",follow_id:"",followStatus:"",follows:[],closeRateVisible:!1,close_rate:"",follow_time:"",follow_times:[],follow_num:"",memberInfo:"",chatName:[],field_list:[],fieldData:[],badicInfoVisible:!1,projectList:[],cityData:n["a"],moment:r.a,followRecord:[],imageUrl:[],file:[],imgNum:0,msg:"",previewVisible:!1,previewImage:"",isImg:!0,submitDisabled:!0,record_id:"",editVisible:!1,imageUrl2:[],file2:[],imgNum2:0,msg2:"",previewVisible2:!1,previewImage2:"",isImg2:!0,submitDisabled2:!0,total:1,quickJumper:!1,page:1,pageSize:15,status:[],interactList:[],picFile:{},isImg3:!0,page2:1,btnFlag:1,tabKey:"1",isMasterAccount:localStorage.getItem("isMasterAccount"),canEdit:1}},methods:{goBack:function(){this.$router.go(-1)},getInfo:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var a,e,i=this;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.post("work-external-contact-follow-user/custom-detail",{uid:localStorage.getItem("uid"),cid:this.id,isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id")});case 3:a=t.sent,e=a.data,0!=e.error?(this.isLoading=!1,this.$message.error(e.message)):(this.isLoading=!1,this.avatar=e.data.avatar,this.name=e.data.name,this.corp_name=e.data.corp_name,this.nickname=e.data.nickname,this.gender=e.data.gender,this.phone=e.data.phone,this.company=e.data.company,this.followTitle=e.data.follow_title,this.is_follow_del=e.data.is_follow_del,this.area=e.data.area,this.des=e.data.des,this.file3=e.data.path,this.hasTags=[],this.tagCheckValue=[],this.hasTagsValue=[],e.data.tag_name.map((function(t){i.hasTags.push(t.tid),i.tagCheckValue.push(t.tid),i.hasTagsValue.push(t.tname)})),this.follow_id=JSON.parse(JSON.stringify(e.data.follow_id)),""==this.follow_id&&(this.follow_id=this.follows[0]&&this.follows[0].id?this.follows[0].id:""),this.status=this.follow_id,this.followStatus=this.follow_id,this.close_rate=e.data.close_rate,this.follow_time=e.data.follow_time,this.follow_times=e.data.follow_times,this.follow_num=e.data.follow_num,this.memberInfo=e.data.memberInfo,this.chatName=e.data.chat_name,this.is_hide_phone=e.data.is_hide_phone,this.getFollowStatus(),this.field_list=e.data.field_list,this.field_list.map((function(t){2==t.type||3==t.type?(t.optionVal2=t.optionVal.split(","),2==t.type?""==t.value?t.hasOption=t.value.split(""):t.hasOption=t.value:3==t.type&&""!=t.value&&(t.hasOption=t.value.split(","))):7==t.type?""==t.value?t.provice=[]:t.provice=t.value.split("-"):4==t.type&&(t.data=t.value)})),this.fieldData=e.data.field_list,this.projectList=e.data.project);case 6:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),disabledStartDate:function(t){return t.valueOf()>(new Date).getTime()},changeTab:function(t){2==t?(this.btnFlag=1,this.getInteract(1)):1==t&&this.getFollowRecord()},getFollowRecord:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var a,e,i,s,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=o.length>0&&void 0!==o[0]?o[0]:1,e=o.length>1&&void 0!==o[1]?o[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("work-external-contact-follow-user/custom-follow-record",{isMasterAccount:localStorage.getItem("isMasterAccount"),uid:localStorage.getItem("uid"),corp_id:this.corpId,cid:this.id,sub_id:localStorage.getItem("sub_id"),page:a,page_size:e});case 5:i=t.sent,s=i.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.error_msg)):(this.isLoading=!1,this.total=parseInt(s.data.count),this.page=a,this.pageSize=e,this.quickJumper=this.total>this.pageSize,this.followRecord=s.data.followRecord);case 8:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),changePage:function(t,a){this.getFollowRecord(t,a)},showSizeChange:function(t,a){this.getFollowRecord(1,a)},handleCancel:function(){this.previewVisible=!1},preview:function(t){this.previewImage=t,this.previewVisible=!0},previewHasImg:function(t){this.previewImage=t,this.previewVisible=!0},getInteract:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var a,e,i,s=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=s.length>0&&void 0!==s[0]?s[0]:1,this.isLoading=!0,t.next=4,this.axios.post("work-external-contact-follow-user/custom-track",{isMasterAccount:localStorage.getItem("isMasterAccount"),uid:localStorage.getItem("uid"),corp_id:this.corpId,cid:this.id,sub_id:localStorage.getItem("sub_id"),page:a,page_size:15});case 4:e=t.sent,i=e.data,0!=i.error?(this.isLoading=!1,this.$message.error(i.error_msg)):(this.isLoading=!1,this.page2=a,i.data.length<15?this.btnFlag=2:this.btnFlag=1,this.interactList=1==a?i.data:this.interactList.concat(i.data));case 7:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),loadMore:function(){this.page2++,this.getInteract(this.page2)},getFollowStatus:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var a,e,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("custom-field/follow",{uid:localStorage.getItem("uid"),status:1});case 2:a=t.sent,e=a.data,0!=e.error?this.$message.error(e.error_msg):(this.follows=e.data.follow,1==this.is_follow_del&&(i={id:this.follow_id,title:this.followTitle,status:0},this.follows.unshift(i)));case 5:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}()},created:function(){this.id=this.$route.query.id,this.getInfo(),this.getFollowRecord()}},p=c,d=(e("0e85"),e("2877")),h=Object(d["a"])(p,i,s,!1,null,"f0595352",null);a["default"]=h.exports},c241:function(t,a,e){var i=e("ccc9");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=e("499e").default;s("f793e2fa",i,!0,{sourceMap:!1,shadowMode:!1})},ccc9:function(t,a,e){a=t.exports=e("2350")(!1),a.push([t.i,'[data-v-f0595352] .ant-card-bordered{border:0}.tpl-title[data-v-f0595352]{float:left;font-size:16px;vertical-align:top}.content-bd[data-v-f0595352]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;margin:0 20px}.custom-info[data-v-f0595352]{padding:20px}.custom-info-text[data-v-f0595352]{width:calc(100% - 132px);display:inline-block;height:132px;padding:0 15px}.col[data-v-f0595352]{margin-bottom:5px}.custom-info-box[data-v-f0595352]{margin-top:20px;overflow:hidden;text-align:center}.box[data-v-f0595352]{padding:20px 20px 0;height:115px;width:20%;border:1px solid #e9e9e9;background-color:#f9f9f9;float:left;overflow:auto}.box[data-v-f0595352]:first-child,.box[data-v-f0595352]:nth-child(2),.box[data-v-f0595352]:nth-child(3){border-right:0}.box-first[data-v-f0595352]{font-size:16px}.box-second[data-v-f0595352]{padding:0;margin:0;overflow:hidden}.custom-info-tab[data-v-f0595352]{margin-top:20px}.col2[data-v-f0595352]{overflow:hidden}.half[data-v-f0595352]{width:50%;float:left;line-height:40px;padding:0 20px;border-top:1px solid #e9e9e9}.half-text[data-v-f0595352]{float:right;word-break:break-all;max-width:calc(100% - 120px)}[data-v-f0595352] .ant-tabs-top-content{padding-bottom:0}.time[data-v-f0595352]{border:1px solid #e9e9e9;padding:20px 20px 0}.textArea-btn[data-v-f0595352]{padding:20px 0 10px;overflow:hidden}.upload-file[data-v-f0595352] .ant-upload-picture-card-wrapper{display:inline}.upload-file[data-v-f0595352] .ant-upload.ant-upload-select-picture-card{background-color:#fff;border:0;height:32px}[data-v-f0595352] .ant-upload.ant-upload-select-picture-card>.ant-upload{padding:0}.time-line-title[data-v-f0595352]{background:#f9f9f9;padding:10px;margin-top:5px}.preview-box[data-v-f0595352]{width:104px;height:104px;margin:0 8px 8px 0;padding:8px;border:1px solid #d9d9d9;border-radius:4px;position:relative;float:left}.preview-cover[data-v-f0595352],.preview-cover img[data-v-f0595352]{display:block;width:100%;height:100%;-o-object-fit:cover;object-fit:cover;position:relative}.preview-cover[data-v-f0595352]:before{z-index:1;width:100%;height:100%;background-color:rgba(0,0,0,.5);content:" "}.preview-cover-icon[data-v-f0595352],.preview-cover[data-v-f0595352]:before{position:absolute;opacity:0;-webkit-transition:all .3s;transition:all .3s}.preview-cover-icon[data-v-f0595352]{top:50%;left:50%;z-index:10;white-space:nowrap;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);cursor:pointer;color:#fff;font-size:18px}.preview-box:hover .preview-cover-icon[data-v-f0595352],.preview-box:hover .preview-cover[data-v-f0595352]:before{opacity:1}[data-v-f0595352] .ant-input-affix-wrapper .ant-input:not(:last-child){padding-right:58px}.upload-pic[data-v-f0595352] .ant-upload-list-picture-card-container,.upload-pic[data-v-f0595352] .ant-upload-list-picture-card .ant-upload-list-item,.upload-pic[data-v-f0595352] .ant-upload.ant-upload-select-picture-card{width:70px;height:70px}.upload-pic[data-v-f0595352] .ant-upload-select-picture-card i{font-size:32px;color:#999}.upload-pic[data-v-f0595352] .ant-upload-select-picture-card .ant-upload-text{margin-top:8px;color:#666}[data-v-f0595352] .ant-input-group-addon{padding:0}.custom-info-project[data-v-f0595352]{border:1px solid #e9e9e9;background-color:#f9f9f9;margin-top:20px;padding:20px;overflow-x:auto}.ant-steps-dot.ant-steps-small .ant-steps-item-process .ant-steps-item-icon[data-v-f0595352],[data-v-f0595352] .ant-steps-dot .ant-steps-item-process .ant-steps-item-icon{width:8px;height:8px}.ant-steps-dot.ant-steps-small .ant-steps-item-content[data-v-f0595352],[data-v-f0595352] .ant-steps-dot .ant-steps-item-content{width:200px}.ant-steps-dot.ant-steps-small .ant-steps-item-icon[data-v-f0595352],[data-v-f0595352] .ant-steps-dot .ant-steps-item-icon{margin-left:97px}.ant-steps-dot.ant-steps-small .ant-steps-item-tail[data-v-f0595352],[data-v-f0595352] .ant-steps-dot .ant-steps-item-tail{margin:0 0 0 100px}.single-accout-card[data-v-f0595352]::-webkit-scrollbar{width:10px;height:10px}[data-v-f0595352] .ant-steps-item-finish>.ant-steps-item-container>.ant-steps-item-content>.ant-steps-item-title{color:rgba(0,0,0,.85)}[data-v-f0595352] .ant-steps-item-finish>.ant-steps-item-container>.ant-steps-item-content>.ant-steps-item-description{color:rgba(0,0,0,.65)}.over-width[data-v-f0595352]{width:500px!important;max-height:400px!important;overflow-y:auto!important}',""])}}]);