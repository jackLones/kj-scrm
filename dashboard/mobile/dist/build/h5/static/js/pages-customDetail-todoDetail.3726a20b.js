(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-customDetail-todoDetail"],{"021e":function(t,e,i){"use strict";i.d(e,"b",(function(){return r})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return a}));var a={uniNavBar:i("f31d").default,uniSteps:i("947b").default,dragButton:i("d7d3").default,uniPopup:i("1c89").default,uniIcons:i("2ba4").default},r=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:t.isReady,expression:"isReady"}],staticClass:"todoDetailbox",class:t.wosScroll},[a("uni-nav-bar",{staticStyle:{position:"fixed",top:"0","z-index":"999"},attrs:{"left-icon":"back",title:"待办详情","background-color":"rgba(82, 132, 236, 1)",color:"#fff"},on:{clickLeft:function(e){arguments[0]=e=t.$handleEvent(e),t.goBack.apply(void 0,arguments)}}}),a("v-uni-view",{staticClass:"introduce"},[a("v-uni-view",{staticClass:"introduce-title"},[t._v(t._s(t.detailRecord.title))]),a("v-uni-view",{staticClass:"introduce-choose",staticStyle:{"margin-bottom":"0.533rem  /* 10/18.75 */"}},[a("v-uni-text",{staticClass:"status-tag",style:{color:t.detailRecord.status_color,"border-color":t.detailRecord.status_color}},[t._v(t._s(t.detailRecord.status_title))]),0!=t.detailRecord.is_finish?a("v-uni-text",{staticStyle:{"margin-left":"0.533rem  /* 10/18.75 */"}},[t._v(t._s(t.detailRecord.finish_time)),0==t.detailRecord.is_finish?a("v-uni-text",[t._v("（还有"+t._s(t.detailRecord.last_days)+"天）")]):t._e(),2==t.detailRecord.is_finish?a("v-uni-text",[t._v("（超时"+t._s(t.detailRecord.delay_days)+"天）")]):t._e(),3==t.detailRecord.is_finish?a("v-uni-text",[t._v("（提前"+t._s(t.detailRecord.pre_days)+"天）")]):t._e()],1):t._e()],1),a("v-uni-view",{staticClass:"introduce-choose"},[a("v-uni-text",{staticClass:"introduce-choose-left"},[t._v("项目处理人")]),a("v-uni-text",{staticClass:"introduce-choose-right"},[t._v(t._s(t.detailRecord.leader))])],1),a("v-uni-view",{staticClass:"introduce-choose"},[a("v-uni-text",{staticClass:"introduce-choose-left"},[t._v("项目优先级")]),a("v-uni-text",{staticClass:"introduce-choose-right",style:{color:t.detailRecord.level_color}},[t._v(t._s(t.detailRecord.level))])],1),a("v-uni-view",{staticClass:"introduce-choose"},[a("v-uni-text",{staticClass:"introduce-choose-left"},[t._v("预计截止时间")]),""!=t.detailRecord.end_time?a("v-uni-text",{staticClass:"introduce-choose-right"},[t._v(t._s(t.detailRecord.end_time))]):a("v-uni-text",{staticClass:"introduce-choose-right"},[t._v("--")])],1),a("v-uni-view",{staticClass:"introduce-choose"},[a("v-uni-text",{staticClass:"introduce-choose-left"},[t._v("项目开始时间")]),a("v-uni-text",{staticClass:"introduce-choose-right"},[t._v(t._s(t.detailRecord.start_time))])],1),a("v-uni-view",{staticClass:"introduce-choose"},[a("v-uni-text",{staticClass:"introduce-choose-left"},[t._v("待办项目描述")]),""!=t.detailRecord.desc?a("v-uni-text",{staticClass:"introduce-choose-right",staticStyle:{"line-height":"0.853rem  /* 16/18.75 */"}},[t._v(t._s(t.detailRecord.desc))]):a("v-uni-text",{staticClass:"introduce-choose-right"},[t._v("--")])],1)],1),a("v-uni-view",{staticClass:"process"},[a("v-uni-view",{staticClass:"process-title"},[t._v("项目进程")]),a("v-uni-view",{staticClass:"process-box"},[a("v-uni-view",{staticClass:"process-empty-box"},[0==t.processList.length?a("v-uni-image",{staticClass:"process-empty",attrs:{src:i("e446")}}):t._e()],1),t.processList.length>0?a("uni-steps",{attrs:{options:t.processList,direction:"column",type:3}}):t._e()],1)],1),t.processList.length>0?a("v-uni-view",{staticClass:"process-loading"},[t._v(t._s(t.loadingText))]):t._e(),0==t.processList.length?a("Footer",{directives:[{name:"show",rawName:"v-show",value:1==t.is_show_copyright,expression:"is_show_copyright == 1"}],staticStyle:{position:"absolute",bottom:"0",left:"0",right:"0"}}):a("Footer",{directives:[{name:"show",rawName:"v-show",value:1==t.is_show_copyright,expression:"is_show_copyright == 1"}]}),1==t.detailRecord.can_edit&&0==t.detailRecord.is_finish?a("drag-button",{attrs:{isDock:!0,existTabBar:!0,content:t.addBtnImg},on:{btnClick:function(e){arguments[0]=e=t.$handleEvent(e),t.getprojectList("addVisible",t.detailRecord.status_id,t.detailRecord.per)}}}):t._e(),a("uni-popup",{ref:"addVisible",staticClass:"add-project",attrs:{type:"bottom","mask-click":!1}},[a("v-uni-view",{staticClass:"uni-tip"},[a("v-uni-text",{staticStyle:{color:"#333","font-size":"0.96rem  /* 18/18.75 */","font-weight":"700","line-height":"2.667rem  /* 50/18.75 */","border-bottom":"0.053rem  /* 1/18.75 */ solid #F2F2F2"}},[t._v("添加项目跟进")]),a("v-uni-scroll-view",{staticClass:"scroll-Y",staticStyle:{"font-size":"0.747rem  /* 14/18.75 */",color:"#333","text-align":"left",height:"20rem  /* 375/18.75 */","overflow-y":"auto"},attrs:{"scroll-y":"true"}},[a("v-uni-view",{staticStyle:{"line-height":"3.733rem  /* 70/18.75 */",color:"#333333","font-size":"0.853rem  /* 16/18.75 */","font-weight":"700","text-align":"left","white-space":"nowrap","text-overflow":"ellipsis",overflow:"hidden","word-break":"break-all"}},[a("v-uni-text",{staticStyle:{width:"0.16rem  /* 3/18.75 */",height:"0.72rem  /* 13.5/18.75 */",display:"inline-block",background:"#5283EC","margin-right":"0.427rem  /* 8/18.75 */"}}),t._v(t._s(t.detailRecord.title))],1),a("v-uni-view",{staticStyle:{"font-size":"0.747rem  /* 14/18.75 */",color:"#333","text-align":"left"}},[a("v-uni-view",[a("v-uni-text",{staticStyle:{"font-weight":"700"}},[t._v("预计结束时间：")]),a("v-uni-text",{staticClass:"introduce-choose-right"},[t._v(t._s(t.detailRecord.end_time)),0==t.detailRecord.is_finish?a("v-uni-text",[t._v("（还有"+t._s(t.detailRecord.last_days)+"天）")]):t._e(),2==t.detailRecord.is_finish?a("v-uni-text",[t._v("（超时"+t._s(t.detailRecord.delay_days)+"天）")]):t._e(),3==t.detailRecord.is_finish?a("v-uni-text",[t._v("（提前"+t._s(t.detailRecord.pre_days)+"天）")]):t._e()],1)],1),t.projectList.length>0?a("v-uni-view",{staticStyle:{"margin-top":"0.8rem  /* 15/18.75 */"}},[a("v-uni-text",{staticStyle:{color:"red"}},[t._v("*")]),a("v-uni-text",{staticStyle:{"font-weight":"700"}},[t._v("项目状态：")]),a("v-uni-picker",{staticStyle:{width:"calc(100% - 4.667rem  /* 87.5/18.75 */)",display:"inline-block"},attrs:{value:t.projectIndex,range:t.projectList,"range-key":"title"},on:{change:function(e){arguments[0]=e=t.$handleEvent(e),t.changeStatus.apply(void 0,arguments)}}},[a("v-uni-view",{staticClass:"uni-input",staticStyle:{"font-size":"0.64rem  /* 12/18.75 */"}},[t._v(t._s(t.projectList[t.projectIndex].title)),a("uni-icons",{staticStyle:{float:"right",color:"#C5C5C5","font-size":"0.8rem  /* 15/18.75 */"},attrs:{type:"arrowdown",size:"20"}})],1)],1)],1):t._e(),a("v-uni-view",{staticStyle:{"margin-top":"0.8rem  /* 15/18.75 */"}},[a("v-uni-text",{staticStyle:{color:"red","vertical-align":"sub"}},[t._v("*")]),a("v-uni-text",{staticStyle:{"font-weight":"700","vertical-align":"sub"}},[t._v("项目进度：")]),a("v-uni-input",{staticClass:"uni-input",staticStyle:{width:"5.333rem  /* 100/18.75 */",display:"inline-block","vertical-align":"middle","margin-right":"0.267rem  /* 5/18.75 */","font-size":"0.64rem  /* 12/18.75 */"},attrs:{type:"number",placeholder:"请输入进度"},model:{value:t.close_rate,callback:function(e){t.close_rate=e},expression:"close_rate"}}),a("span",{staticStyle:{"vertical-align":"sub"}},[t._v("%")]),a("span",{staticStyle:{"vertical-align":"sub",color:"#FF2438","margin-left":"0.533rem  /* 10/18.75 */"}},[t._v("上次进度在"+t._s(t.lastPer)+"%")])],1),a("v-uni-view",{staticStyle:{"margin-top":"0.8rem  /* 15/18.75 */"}},[a("v-uni-text",{staticStyle:{color:"red"}},[t._v("*")]),a("v-uni-text",{staticStyle:{"font-weight":"700"}},[t._v("进度说明：")]),a("v-uni-textarea",{staticClass:"add-project-textareea",attrs:{placeholder:"请输入进度说明，200字以内",maxlength:"200"},model:{value:t.msg,callback:function(e){t.msg=e},expression:"msg"}})],1)],1)],1),a("v-uni-view",{staticClass:"uni-tip-group-button"},[a("v-uni-text",{staticClass:"add-project-cancal",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.cancel("addVisible")}}},[t._v("取消")]),a("v-uni-text",{staticClass:"add-project-ok",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.addBtn.apply(void 0,arguments)}}},[t._v("确定")])],1)],1)],1)],1)},o=[]},"0f0b":function(t,e,i){var a=i("1f94");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var r=i("4f06").default;r("1129a830",a,!0,{sourceMap:!1,shadowMode:!1})},"1bdb6":function(t,e,i){"use strict";i.r(e);var a=i("4318"),r=i.n(a);for(var o in a)["default"].indexOf(o)<0&&function(t){i.d(e,t,(function(){return a[t]}))}(o);e["default"]=r.a},"1f94":function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,"uni-page-body[data-v-48f3a1f6]{position:absolute;width:100%;height:100%;background:#f6f6f6}.todoDetailbox[data-v-48f3a1f6]{background:#f6f6f6;text-align:left;font-size:.693rem ;overflow-y:auto;height:100%}[data-v-48f3a1f6] .uni-navbar--border{border:0}.introduce[data-v-48f3a1f6]{padding:.533rem .853rem ;background:#fff;border-radius:.16rem ;color:grey;margin:54px .533rem 0}.status-tag[data-v-48f3a1f6]{border:.053rem solid;padding:.16rem .267rem ;border-radius:.32rem ;font-size:.64rem\n}.introduce-title[data-v-48f3a1f6]{color:#333;font-weight:700;font-size:.907rem ;margin-bottom:.533rem\n}.introduce-choose[data-v-48f3a1f6]{line-height:1.44rem /* 27/18.75 */}.introduce-choose-left[data-v-48f3a1f6]{width:6.667rem /* 125/18.75 */;display:inline-block;vertical-align:top;color:#999}.introduce-choose-right[data-v-48f3a1f6]{width:calc(100% - 6.667rem);display:inline-block;word-break:break-all;word-wrap:break-word!important;color:#111}.process[data-v-48f3a1f6]{margin-bottom:.533rem\n}.process-title[data-v-48f3a1f6]{padding:.64rem .853rem ;background:#f2f2f3;border-radius:.16rem .16rem 0 0 ;font-size:.8rem ;margin:.533rem .533rem 0}.process-box[data-v-48f3a1f6]{padding:0 .853rem ;background:#fff;margin:0 .533rem ;overflow:hidden}.process-empty-box[data-v-48f3a1f6]{text-align:center}.process-empty[data-v-48f3a1f6]{width:6.507rem /* 122/18.75 */;height:4.053rem /* 76/18.75 */;margin:2.667rem auto}.process-loading[data-v-48f3a1f6]{text-align:center;line-height:1.6rem;font-size:.693rem;color:#ccc}.uni-textarea[data-v-48f3a1f6]{border:1px solid #ccc;border-radius:4px;margin:.8rem 0;font-size:.8rem;text-align:left;padding:.427rem;box-sizing:border-box}uni-textarea[data-v-48f3a1f6]{width:auto}\n\n/* 提示窗口 */.uni-tip[data-v-48f3a1f6]{\ndisplay:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;\npadding:15px;width:16rem /* 300/18.75 */;background-color:#fff;border-radius:4px;\n\t/*left: 12.5%;*/\n\t/*position: absolute;*/\n\t/*top: 15%;*/box-sizing:border-box}.add-project[data-v-48f3a1f6] .uni-tip{width:100%;border-radius:5px 5px 0 0}.uni-input[data-v-48f3a1f6]{height:28px;line-height:28px;font-size:15px;text-align:left;border:1px solid #ccc;border-radius:4px;padding:0 8px;margin:0 5px 0 0}[data-v-48f3a1f6] .uni-input{margin:0 5px 0 0;border-color:#f6f6f6}[data-v-48f3a1f6] .uni-input-placeholder{color:#ccc}.add-project-cancal[data-v-48f3a1f6]{width:6.4rem /* 120/18.75 */;line-height:2.24rem /* 42/18.75 */;border:.053rem solid #eee;border-radius:.267rem ;text-align:center;margin-right:.48rem ;font-size:.8rem ;color:#333}.uni-tip-group-button[data-v-48f3a1f6]{\ndisplay:-webkit-box;display:-webkit-flex;display:flex;\n-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;margin-top:20px}.add-project-ok[data-v-48f3a1f6]{width:calc(100% - 6.88rem);line-height:2.24rem /* 42/18.75 */;border-radius:.267rem ;text-align:center;background:#5283ec;font-size:.8rem ;color:#fff}.add-project-textareea[data-v-48f3a1f6]{border:.053rem solid #eee;margin-top:.8rem ;padding:.533rem ;box-sizing:border-box;font-size:.693rem ;width:100%}.add-project-textareea[data-v-48f3a1f6] .uni-textarea-placeholder{font-size:.693rem\n}body.?%PAGE?%[data-v-48f3a1f6]{background:#f6f6f6}",""]),t.exports=e},4318:function(t,e,i){"use strict";var a=i("ee27");i("99af"),i("a9e3"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("96cf");var r=a(i("c964")),o=a(i("2c4b")),s={name:"todoDetail",inject:["initPage","getParameter"],components:{Footer:o.default},data:function(){return{wosScroll:"wos-scroll-yes",isReady:!1,processList:[],loadingText:"加载中...",page:1,count:"",detailRecord:{},addBtnImg:"../../static/customDetail/addProject.png",msg:"",status:1,close_rate:"",projectIndex:0,projectList:[],lastPer:0,is_show_copyright:localStorage.getItem("is_show_copyright")}},onReachBottom:function(){this.processList.length<this.count?(this.page+=1,this.getProcessList(this.page)):this.loadingText="已加载全部"},onLoad:function(){var t=this;uni.setNavigationBarTitle({title:"待办详情"});var e=localStorage.getItem("corpid");null==e&&(e=this.getParameter("corpid")),null!=e&&localStorage.setItem("corpid",e),null!=localStorage.getItem("uid")?this.init():this.$store.dispatch("setWx",(function(){t.initPage(t.init)}))},methods:{init:function(){this.is_show_copyright=localStorage.getItem("is_show_copyright"),this.getProcessDetail()},getProcessDetail:function(){var t=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,a;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,t.axios.post("wait-project/wait-detail",{uid:localStorage.getItem("uid"),id:null!=t.getParameter("id")?t.getParameter("id"):"",corp_id:localStorage.getItem("corpid"),userid:localStorage.getItem("user_id")});case 2:i=e.sent,a=i.data,0!=a.error?uni.showToast({title:a.error_msg,image:"/static/fail.png",duration:2e3}):(t.detailRecord=a.data,t.getProcessList());case 5:case"end":return e.stop()}}),e)})))()},getProcessList:function(){var t=arguments,e=this;return(0,r.default)(regeneratorRuntime.mark((function i(){var a,r,o;return regeneratorRuntime.wrap((function(i){while(1)switch(i.prev=i.next){case 0:return a=t.length>0&&void 0!==t[0]?t[0]:1,uni.showLoading({title:"加载中...",duration:2e3}),i.next=4,e.axios.post("wait-project/wait-info",{id:null!=e.getParameter("id")?e.getParameter("id"):"",task_id:e.detailRecord.task_id,external_userid:e.detailRecord.external_userid,sea_id:e.detailRecord.sea_id,page:a,page_size:15});case 4:r=i.sent,o=r.data,0!=o.error?(uni.hideLoading(),uni.showToast({title:o.error_msg,image:"/static/fail.png",duration:2e3})):(uni.hideLoading(),e.count=o.data.count,e.page=a,e.isReady=!0,e.processList=1==a?o.data.info:e.processList.concat(o.data.info),e.processList.length<15&&(e.loadingText="已加载全部"));case 7:case"end":return i.stop()}}),i)})))()},goBack:function(){uni.navigateBack()},getprojectList:function(t,e,i){var a=this;return(0,r.default)(regeneratorRuntime.mark((function r(){var o,s,n,c;return regeneratorRuntime.wrap((function(r){while(1)switch(r.prev=r.next){case 0:return r.next=2,a.axios.post("wait-project/common-detail",{uid:localStorage.getItem("uid")});case 2:if(o=r.sent,s=o.data,0==s.error){r.next=8;break}uni.showToast({title:s.error_msg,image:"/static/fail.png",duration:2e3}),r.next=23;break;case 8:for(a.projectList=[],n=1;n<s.data.project_status.length;n++)a.projectList.push(s.data.project_status[n]);c=0;case 11:if(!(c<a.projectList.length)){r.next=19;break}if(a.projectList[c].id!=e){r.next=16;break}return a.projectIndex=c,a.status=e,r.abrupt("break",19);case 16:c++,r.next=11;break;case 19:""==a.status&&a.projectList.length>0&&(a.projectIndex=0,a.status=Number(a.projectList[0].id)),a.lastPer=null!=i?i:0,a.wosScroll="wos-scroll-no",a.$refs[t].open();case 23:case"end":return r.stop()}}),r)})))()},cancel:function(t){this.wosScroll="wos-scroll-yes",this.$refs[t].close(),this.status="",this.close_rate="",this.lastPer=0,this.msg=""},changeStatus:function(t){this.projectIndex=t.target.value,this.status=this.projectList[t.target.value].id},addBtn:function(){var t=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,a,r;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(""!=t.status){e.next=3;break}return uni.showToast({title:"请选择项目状态！",image:"/static/fail.png",duration:2e3}),e.abrupt("return",!1);case 3:if(""!=t.close_rate){e.next=6;break}return uni.showToast({title:"请填写项目进度！",image:"/static/fail.png",duration:2e3}),e.abrupt("return",!1);case 6:if(i=/^(?:0|[1-9][0-9]?|100)$/,null!=t.close_rate&&""!=t.close_rate){e.next=10;break}return uni.showToast({title:"请填写项目进度！",image:"/static/fail.png",duration:2e3}),e.abrupt("return",!1);case 10:if(null==t.close_rate||""==t.close_rate||!(t.close_rate<0||t.close_rate>100)&&i.test(t.close_rate)){e.next=13;break}return uni.showToast({title:"项目进度必须为0-100正整数！",image:"/static/fail.png",duration:2e3}),e.abrupt("return",!1);case 13:if(""!=t.msg){e.next=16;break}return uni.showToast({title:"进度说明不能为空！",image:"/static/fail.png",duration:2e3}),e.abrupt("return",!1);case 16:return e.next=18,t.axios.post("wait-project/add-project-status",{uid:localStorage.getItem("uid"),id:null!=t.getParameter("id")?t.getParameter("id"):"",per_desc:t.msg,status:t.status,per:t.close_rate,task_id:t.detailRecord.task_id,external_userid:t.detailRecord.external_userid,sea_id:t.detailRecord.sea_id});case 18:a=e.sent,r=a.data,0!=r.error?uni.showToast({title:r.error_msg,image:"/static/fail.png",duration:2e3}):(t.cancel("addVisible"),t.getProcessDetail(),t.getProcessList());case 21:case"end":return e.stop()}}),e)})))()}}};e.default=s},a1db:function(t,e,i){"use strict";var a=i("0f0b"),r=i.n(a);r.a},bc15:function(t,e,i){"use strict";i.r(e);var a=i("021e"),r=i("1bdb6");for(var o in r)["default"].indexOf(o)<0&&function(t){i.d(e,t,(function(){return r[t]}))}(o);i("a1db");var s,n=i("f0c5"),c=Object(n["a"])(r["default"],a["b"],a["c"],!1,null,"48f3a1f6",null,!1,a["a"],s);e["default"]=c.exports},e446:function(t,e,i){t.exports=i.p+"static/img/empty.fd12ec00.png"}}]);