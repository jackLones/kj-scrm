(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-65c22d84"],{"01f0":function(t,e,a){"use strict";var i=a("9edc"),s=a.n(i);s.a},"679a":function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticStyle:{position:"absolute",left:"0",top:"0",right:"0",background:"#FFF"}},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("div",{staticClass:"view-title"},[t._v(t._s(t.title)+"\n\t\t\t"),a("a-button",{staticStyle:{float:"right",margin:"9px 20px 0 0"},attrs:{type:"primary",icon:"rollback"},on:{click:t.rollback}},[t._v("\n\t\t\t\t返回列表\n\t\t\t")])],1),a("div",{staticStyle:{padding:"20px"}},[a("ul",{staticClass:"statistics-ul"},[a("li",[a("p",[t._v("今日新增拉新人数")]),a("p",{staticClass:"number",staticStyle:{color:"#3B74FF"}},[t._v(t._s(t.newMemberToday)+"人")]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.newMemberAll)+"人")])]),a("li",[a("p",[t._v("今日领取金额")]),a("p",{staticClass:"number",staticStyle:{color:"#FF562D"}},[t._v("￥"+t._s(t.receiveTodaySum))]),a("p",{staticClass:"count"},[t._v("累计：￥"+t._s(t.receiveAllSum))])]),a("li",[a("p",[t._v("今日领取人数")]),a("p",{staticClass:"number",staticStyle:{color:"#3B74FF"}},[t._v(t._s(t.receiveTodayNum)+"人")]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.receiveAllNum)+"人")])]),a("li",[a("p",[t._v("今日未领取金额")]),a("p",{staticClass:"number",staticStyle:{color:"#FF562D"}},[t._v("￥"+t._s(t.notReceiveTodaySum))]),a("p",{staticClass:"count"},[t._v("累计：￥"+t._s(t.notReceiveAllSum))])]),a("li",[a("p",[t._v("今日未领取人数")]),a("p",{staticClass:"number",staticStyle:{color:"#3B74FF"}},[t._v(t._s(t.notReceiveTodayNum)+"人")]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.notReceiveAllNum)+"人")])]),a("li",[a("p",[t._v("当前剩余可发放总金额\n\t\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"right"}},[a("template",{slot:"title"},[a("span",[t._v("当前剩余可发放总金额 = 投放总金额 - 已领取 - 未领取 - 已过期")])]),a("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1),a("p",{staticClass:"number",staticStyle:{color:"#FF562D"}},[t._v("￥"+t._s(t.leftAmount))]),a("p",{staticClass:"count"},[t._v("投放总金额：￥"+t._s(t.redpacketAmount))])])]),a("div",{staticClass:"all-card-calendar"},[a("div",{staticClass:"card-caledar"},[a("div",{staticClass:"day-picker-contain picker-contain"},[a("a-button",{class:"1"==t.timeType?"caledar-btn-selected":"",on:{click:function(e){return t.changeFansTimeType1(1)}}},[t._v("\n\t\t\t\t\t\t\t按日\n\t\t\t\t\t\t")]),"1"==t.timeType?a("a-range-picker",{attrs:{allowClear:!1,disabledDate:t.disabledDateDay,defaultValue:[t.moment(t.moment().subtract(30,"days").calendar(),"YYYY-MM-DD"),t.moment(t.moment().subtract(1,"days"),"YYYY-MM-DD")],format:"YYYY-MM-DD"},on:{change:t.changeFansTime1}}):t._e()],1),a("div",{staticClass:"week-picker-contain picker-contain"},[a("a-button",{class:"2"==t.timeType?"caledar-btn-selected":"",on:{click:function(e){return t.changeFansTimeType1(2)}}},[t._v("\n\t\t\t\t\t\t\t按周\n\t\t\t\t\t\t")]),"2"==t.timeType?a("a-week-picker",{attrs:{allowClear:!1,value:t.weekStart,disabledDate:t.disabledDateWeek},on:{change:t.changeFansStartWeek1}}):t._e(),"2"==t.timeType?a("a-week-picker",{attrs:{allowClear:!1,value:t.weekEnd,disabledDate:t.disabledDateWeek},on:{change:t.changeFansEndWeek1}}):t._e()],1),a("div",{staticClass:"month-picker-contain picker-contain"},[a("a-button",{class:"3"==t.timeType?"caledar-btn-selected":"",on:{click:function(e){return t.changeFansTimeType1(3)}}},[t._v("\n\t\t\t\t\t\t\t按月\n\t\t\t\t\t\t")])],1)]),a("div",{staticClass:"sec-card-caledar down-text",staticStyle:{"border-top":"0px"}},[a("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t这段时间里，共新增拉新人数\n\t\t\t\t\t\t"),a("span",[t._v(t._s(t.newMember))]),t._v("\n\t\t\t\t\t\t人、领取金额\n\t\t\t\t\t\t"),a("span",[t._v(t._s(t.receiveSum))]),t._v("\n\t\t\t\t\t\t元、领取人数\n\t\t\t\t\t\t"),a("span",[t._v(t._s(t.receiveNum))]),t._v("\n\t\t\t\t\t\t人、未领取金额\n\t\t\t\t\t\t"),a("span",[t._v(t._s(t.notReceiveSum))]),t._v("\n\t\t\t\t\t\t元、未领取人数\n\t\t\t\t\t\t"),a("span",[t._v(t._s(t.notReceiveNum))]),t._v("\n\t\t\t\t\t\t人\n\t\t\t\t\t")])])]),a("LineCharts",{staticStyle:{margin:"20px 0"},attrs:{options:t.fansInteractive}}),a("div",{staticStyle:{overflow:"hidden","margin-bottom":"20px"}},[a("div",{staticStyle:{float:"left"}},[t._v("详细数据")]),a("a-button",{staticStyle:{float:"right"},attrs:{type:"primary",loading:t.btnLoading},on:{click:t.exportData}},[a("icon-font",{directives:[{name:"show",rawName:"v-show",value:!t.btnLoading,expression:"!btnLoading"}],attrs:{type:"icon-tuichu"}}),t._v("\n\t\t\t\t\t导出数据\n\t\t\t\t")],1)],1),a("a-table",{attrs:{columns:t.columns,dataSource:t.timeData,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"newMember",fn:function(e,i){return a("span",{},[t._v("\n\t\t\t\t\t"+t._s(i.new_member)+"人\n\t\t\t\t")])}},{key:"notReceive",fn:function(e,i){return a("span",{},[t._v("\n\t\t\t\t\t￥"+t._s(i.not_receive_sum)+"（"+t._s(i.not_receive_num)+"人）\n\t\t\t\t")])}},{key:"receiveInfo",fn:function(e,i){return a("span",{},[t._v("\n\t\t\t\t\t￥"+t._s(i.receive_sum)+"（"+t._s(i.receive_num)+"人）\n\t\t\t\t")])}}])},[a("span",{attrs:{slot:"new_member"},slot:"new_member"},[t._v("\n\t\t\t        拉新人数\n\t\t\t\t    ")]),a("span",{attrs:{slot:"not_receive"},slot:"not_receive"},[t._v("\n\t\t\t        未领取金额/人数\n\t\t\t\t\t")]),a("span",{attrs:{slot:"receive"},slot:"receive"},[t._v("\n\t\t\t        已领取金额/人数\n\t\t\t\t\t")])]),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticStyle:{width:"100%"}},[a("div",{staticStyle:{height:"32px",display:"inline-block","margin-top":"25px"}},[t._v("\n\t\t\t\t\t共"),a("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("条\n\t\t\t\t")]),a("div",{staticClass:"pagination",staticStyle:{"margin-top":"20px",float:"right"}},[a("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["10","20","30","60"]},on:{change:t.changeFansPage,showSizeChange:t.showFansSizeChange}})],1)])],1)])],1)},s=[],n=(a("28a5"),a("7f7f"),a("96cf"),a("3b8d")),r=a("7304"),o=a("c1df"),c=a.n(o),d=a("0c63"),l=d["a"].createFromIconfontCN({scriptUrl:"//at.alicdn.com/t/font_8d5l8fzk5b87iudi.js"}),m=[{title:"时间",dataIndex:"time",key:"time"},{dataIndex:"new_member",key:"new_member",slots:{title:"new_member"},scopedSlots:{customRender:"newMember"}},{dataIndex:"receive_num",key:"receive_num",slots:{title:"receive"},scopedSlots:{customRender:"receiveInfo"}},{dataIndex:"not_receive_num",key:"not_receive_num",slots:{title:"not_receive"},scopedSlots:{customRender:"notReceive"}}],p={components:{LineCharts:r["a"],IconFont:l},data:function(){return{urlId:"",corpId:"",newMember:0,receiveSum:0,receiveNum:0,notReceiveSum:0,notReceiveNum:0,newMemberToday:"0",newMemberAll:"0",receiveTodaySum:"0",receiveTodayNum:"0",receiveAllSum:"0",receiveAllNum:"0",notReceiveTodaySum:"0",notReceiveTodayNum:"0",notReceiveAllSum:"0",notReceiveAllNum:"0",expiredTodaySum:"0",expiredTodayNum:"0",expiredAllSum:"0",expiredAllNum:"0",redpacketAmount:"0",leftAmount:"0",statisticsList:[],timeType:"1",ds_date:"",de_date:"",weekStart:c()().subtract(7,"days"),weekEnd:c()().subtract(7,"days"),ws_date:c()().subtract(7,"days").weekday(0).format("YYYY-MM-DD"),we_date:c()().subtract(7,"days").weekday(6).format("YYYY-MM-DD"),ws_week:c()(new Date).week()-1||52,is_export:0,fansInteractive:{legendData:[],xAxisData:[],seriesData:[]},title:"",columns:m,timeDataList:[],timeData:[],total:0,quickJumper:!1,page:1,pageSize:10,btnLoading:!1,dateFormat:"YYYY-MM-DD",isLoading:!0}},methods:{moment:c.a,rollback:function(){this.$router.push("/redForNew/list?isRefresh=1")},rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},changeFansPage:function(t){var e=this;e.page=t;var a=e.getPageData(e.timeDataList,e.page,e.pageSize);e.timeData=a.list,e.page=a.page,this.$nextTick((function(){document.getElementsByClassName("ant-layout").length>1&&document.getElementsByClassName("ant-layout")[2].scrollTo(0,700)}))},showFansSizeChange:function(t,e){var a=this;a.pageSize=e;var i=a.getPageData(a.timeDataList,a.page,a.pageSize);a.timeData=i.list,a.page=i.page},getPageData:function(t,e,a){var i=e*a,s=(e-1)*a,n={page:e,list:[]};if(0==t.length)n.list=[];else{t.length<i&&(i=t.length),s>t.length-1&&(t.length%a!=0?(s=parseInt(t.length/a)*a,n.page=parseInt(t.length/a)+1):(s=(parseInt(t.length/a)-1)*a,n.page=parseInt(t.length/a)));for(var r=s;r<i;r++)n.list.push(t[r])}return n},getStatisticsSum:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("work-contact-way-redpacket/redpacket-send-today",{id:this.urlId,corp_id:this.corpId});case 2:e=t.sent,a=e.data,0!=a.error?(this.$message.error(a.error_msg),this.isLoading=!1):(this.newMemberToday=a.data.newMemberToday,this.newMemberAll=a.data.newMemberAll,this.receiveTodaySum=a.data.receiveTodaySum,this.receiveTodayNum=a.data.receiveTodayNum,this.receiveAllSum=a.data.receiveAllSum,this.receiveAllNum=a.data.receiveAllNum,this.notReceiveTodaySum=a.data.notReceiveTodaySum,this.notReceiveTodayNum=a.data.notReceiveTodayNum,this.notReceiveAllSum=a.data.notReceiveAllSum,this.notReceiveAllNum=a.data.notReceiveAllNum,this.expiredTodaySum=a.data.expiredTodaySum,this.expiredTodayNum=a.data.expiredTodayNum,this.expiredAllSum=a.data.expiredAllSum,this.expiredAllNum=a.data.expiredAllNum,this.redpacketAmount=a.data.redpacketAmount,this.leftAmount=a.data.leftAmount,this.isLoading=!1);case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getStatisticsList:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,n,r,o=this;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e="",a="",1==this.timeType?(e=this.ds_date||c()(new Date(new Date-2592e6)).format("YYYY-MM-DD"),a=this.de_date||c()(new Date-864e5).format("YYYY-MM-DD")):2==this.timeType?(c()().subtract(7,"days").weekday(6).format("YYYY-MM-DD")>c()().format("YYYY-MM-DD")&&(this.we_date=c()().format("YYYY-MM-DD")),e=this.ws_date,a=this.we_date):3==this.timeType&&(e=c()().startOf("month").format("YYYY-MM-DD"),a=c()(new Date).format("YYYY-MM-DD")),t.next=5,this.axios.post("work-contact-way-redpacket/redpacket-send-statistic",{corp_id:this.corpId,id:this.urlId,type:this.timeType,s_date:e,e_date:a,s_week:this.ws_week,is_export:this.is_export});case 5:if(i=t.sent,s=i.data,0!=s.error)this.is_export=0,this.$message.error(s.error_msg);else if(0==this.is_export){for(this.isLoading=!1,this.newMember=s.data.newMember,this.receiveSum=s.data.receiveSum,this.receiveNum=s.data.receiveNum,this.notReceiveSum=s.data.notReceiveSum,this.notReceiveNum=s.data.notReceiveNum,this.fansInteractive.xAxisData=s.data.xData,n=0;n<s.data.seriesData.length;n++)this.fansInteractive.seriesData.push({name:"",type:"line",smooth:!0,data:[]}),this.fansInteractive.seriesData[n].name=s.data.seriesData[n].name,this.fansInteractive.seriesData[n].data=s.data.seriesData[n].data,this.fansInteractive.legendData.push(s.data.seriesData[n].name);this.timeDataList=s.data.chatData.reverse(),this.total=s.data.chatData.length,r=this.getPageData(this.timeDataList,this.page,this.pageSize),this.timeData=r.list,this.page=r.page}else setTimeout((function(){window.open(s.data.url),o.is_export=0,o.btnLoading=!1,o.isLoading=!1}),2e3);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changeFansTimeType1:function(t){this.timeType!=t&&(1==t&&(this.ds_date=c()(new Date(new Date-2592e6)).format("YYYY-MM-DD"),this.de_date=c()(new Date-864e5).format("YYYY-MM-DD")),this.page=1,this.isLoading=!0,this.timeType=t,this.getStatisticsList())},changeFansTime1:function(t,e){this.isLoading=!0,1==this.timeType&&(this.ds_date=e[0],this.de_date=e[1]),this.getStatisticsList()},changeFansStartWeek1:function(t,e){if(this.isLoading=!0,t>this.weekEnd){this.weekStart=c()(JSON.parse(JSON.stringify(this.weekEnd))),this.weekEnd=c()(JSON.parse(JSON.stringify(t)));var a=c()(this.weekStart).format("E");this.fansAddTime.ws_date=c()(this.weekStart).subtract(a-1,"days").format("YYYY-MM-DD"),a=c()(this.weekEnd).format("E"),this.we_date=c()(this.weekEnd).add(7-a,"days").format("YYYY-MM-DD"),this.we_date>c()().format("YYYY-MM-DD")&&(this.fansAddTime.we_date=c()().format("YYYY-MM-DD")),this.ws_week=c()(this.ws_date,"YYYY-MM-DD").week()}else{this.weekStart=t;var i=e.split("-")[1],s=i.substring(0,i.length-1),n=c()(this.weekStart).format("E");this.ws_date=c()(this.weekStart).subtract(n-1,"days").format("YYYY-MM-DD"),this.ws_week=s}this.getStatisticsList()},changeFansEndWeek1:function(t,e){if(this.isLoading=!0,t<this.weekStart){this.weekEnd=c()(JSON.parse(JSON.stringify(this.weekStart))),this.weekStart=c()(JSON.parse(JSON.stringify(t)));var a=c()(this.weekStart).format("E");this.ws_date=c()(this.weekStart).subtract(a-1,"days").format("YYYY-MM-DD"),a=c()(this.weekEnd).format("E"),this.we_date=c()(this.weekEnd).add(7-a,"days").format("YYYY-MM-DD"),this.we_date>c()().format("YYYY-MM-DD")&&(this.we_date=c()().format("YYYY-MM-DD")),this.ws_week=c()(this.ws_date,"YYYY-MM-DD").week()}else{this.weekEnd=t;var i=e.split("-")[1],s=i.substring(0,i.length-1),n=c()(this.weekEnd).format("E");this.we_date=c()(this.weekEnd).add(7-n,"days").format("YYYY-MM-DD"),this.we_week=s}this.getStatisticsList()},disabledDateDay:function(t){return t.valueOf()>(new Date).getTime()-864e5||t<c()().subtract(31,"days")},disabledDateWeek:function(t){var e=c()(new Date).format("E");return t.valueOf()>c()(new Date).subtract(e,"days")||t<c()().subtract(365,"days")},exportData:function(){this.is_export=1,this.btnLoading=!0,this.getStatisticsList()}},created:function(){this.urlId=this.$route.query.id,this.title=decodeURIComponent(this.$route.query.title),this.corpId=decodeURIComponent(this.$route.query.id1),"undefined"!=typeof this.urlId&&(this.getStatisticsSum(),this.getStatisticsList())}},u=p,h=(a("01f0"),a("2877")),v=Object(h["a"])(u,i,s,!1,null,"651c6bd6",null);e["default"]=v.exports},"9edc":function(t,e,a){var i=a("f7a4");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=a("499e").default;s("71ef9909",i,!0,{sourceMap:!1,shadowMode:!1})},f7a4:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".statistics-ul[data-v-651c6bd6]{overflow:hidden;padding:0;margin-bottom:0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.statistics-ul li[data-v-651c6bd6]{width:16.67%;height:105px;color:#1a1a1a;padding:10px;background:#f9f9f9;border-right:1px solid #e9e9e9;border-top:1px solid #e9e9e9;border-bottom:1px solid #e9e9e9}.statistics-ul li p[data-v-651c6bd6]{margin-bottom:0}.statistics-ul li .number[data-v-651c6bd6]{font-size:18px;height:45px;line-height:45px}.statistics-ul li .count[data-v-651c6bd6]{float:right}.statistics-ul li[data-v-651c6bd6]:first-child{border-left:1px solid #e9e9e9}.card-caledar[data-v-651c6bd6]{margin:12px 0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-ms-flex-wrap:wrap;flex-wrap:wrap;color:#686868}.caledar-btn-selected[data-v-651c6bd6]{boder:0;border-right:1px solid #3b74ff;background:#3b74ff;color:#fff}.ant-btn[data-v-651c6bd6],[data-v-651c6bd6] .ant-input{border-radius:0}.picker-contain[data-v-651c6bd6]{margin-right:10px}[data-v-651c6bd6] .ant-table-wrapper{background:#fff}[data-v-651c6bd6] .dark-row{background:#fafafa}.view-title[data-v-651c6bd6],[data-v-651c6bd6] .light-row{background:#fff}.view-title[data-v-651c6bd6]{border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px;font-size:16px;padding:0 20px;text-align:left}.picker-contain[data-v-651c6bd6]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.picker-contain .caledar-btn-selected[data-v-651c6bd6]{boder:0;border-right:1px solid #3b74ff;background:#3b74ff;border-radius:0;color:#fff}.picker-contain button[data-v-651c6bd6]{margin:0 0 0 5px}.sec-card-caledar[data-v-651c6bd6]{height:auto;padding:5px;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-align:center;-ms-flex-align:center;align-items:center;background:#f7f7f7}.sec-card-caledar.down-text[data-v-651c6bd6]{background:#fff}.sec-card-caledar.down-text p[data-v-651c6bd6]{color:#1a1a1a!important}.sec-card-caledar.down-text span[data-v-651c6bd6]{color:#ff562d!important}",""])}}]);