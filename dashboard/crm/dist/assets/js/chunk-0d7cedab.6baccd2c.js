(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0d7cedab"],{4609:function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticStyle:{position:"absolute",left:"0",top:"0",right:"0",background:"#fff"}},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("div",{staticClass:"view-title"},[t._v(t._s(t.title)+"\n\t\t"),a("router-link",{staticStyle:{"font-size":"16px",float:"right","margin-right":"15px"},attrs:{to:"/scene/list"}},[a("a-button",{attrs:{type:"primary",icon:"rollback"}},[t._v("返回列表")])],1)],1),a("div",{staticStyle:{padding:"20px"}},[a("ul",{staticClass:"statistics-ul"},[a("li",{staticStyle:{background:"#40A9FF"}},[a("p",[t._v("今日扫码总次数\n\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"right"}},[a("template",{slot:"title"},[a("span",[t._v("扫过此渠道码的次数（可能同一人扫多次，每扫一次，扫码总次数+1）")])]),a("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1),a("p",{staticClass:"number"},[t._v(t._s(t.scan_times.day))]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.scan_times.sum))])]),a("li",{staticStyle:{background:"#FFC71B"}},[a("p",[t._v("今日扫码人数\n\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"right"}},[a("template",{slot:"title"},[a("span",[t._v("扫过此渠道码的人数（可能同一人扫多次，累计值只计入1人）")])]),a("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1),a("p",{staticClass:"number"},[t._v(t._s(t.scan_num.day))]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.scan_num.sum))])]),a("li",{staticStyle:{background:"#8E8AFF"}},[a("p",[t._v("今日关注人数\n\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"right"}},[a("template",{slot:"title"},[a("span",[t._v("通过扫该渠道码后关注的人数。（可能同一人扫该码，再取关，再扫该码，累计值只计入1人）")])]),a("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1),a("p",{staticClass:"number"},[t._v(t._s(t.subscribe.day))]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.subscribe.sum))])]),a("li",{staticStyle:{background:"#FF688E"}},[a("p",[t._v("今日取关人数\n\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"right"}},[a("template",{slot:"title"},[a("span",[t._v("通过扫渠道码关注后又取关的人数。（可能同一人扫该码关注，再取关，再扫该码，累计值只计入1人）")])]),a("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1),a("p",{staticClass:"number"},[t._v(t._s(t.unsubscribe.day))]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.unsubscribe.sum))])]),a("li",{staticStyle:{background:"#5DDCB2"}},[a("p",[t._v("今日净增粉丝人数\n\t\t\t\t\t"),a("a-tooltip",{attrs:{placement:"right"}},[a("template",{slot:"title"},[a("span",[t._v("扫该渠道码的新粉丝人数-取关粉丝人数。（数据可能存在一定的误差，因存在某粉丝在同一天内既存在多次关注也存在多次取消的特殊情况）")])]),a("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1),a("p",{staticClass:"number"},[t._v(t._s(t.net_increase.day))]),a("p",{staticClass:"count"},[t._v("累计："+t._s(t.net_increase.sum))])])]),a("div",{staticClass:"all-card-calendar"},[a("div",{staticClass:"card-caledar"},[a("div",{staticClass:"hour-picker-contain picker-contain"},[a("a-button",{class:1==t.time_type?"caledar-btn-selected":"",on:{click:function(e){return t.changeFansTimeType(1)}}},[t._v("\n\t\t\t\t\t\t今日按时\n\t\t\t\t\t")])],1),a("div",{staticClass:"day-picker-contain picker-contain"},[a("a-button",{class:2==t.time_type?"caledar-btn-selected":"",on:{click:function(e){return t.changeFansTimeType(2)}}},[t._v("\n\t\t\t\t\t\t按指定日\n\t\t\t\t\t")]),2==t.time_type&&1==t.is_long?a("a-range-picker",{attrs:{disabledDate:t.disabledDate,defaultValue:[t.moment(t.moment().subtract(30,"days").calendar(),"YYYY-MM-DD"),t.moment(new Date,"YYYY-MM-DD")],format:"YYYY-MM-DD"},on:{change:t.changeFansTime}}):t._e(),2==t.time_type&&0==t.is_long?a("a-range-picker",{attrs:{disabledDate:t.disabledDate2,defaultValue:[t.moment(t.startDate,t.dateFormat),t.moment(t.endDate,t.dateFormat)],format:"YYYY-MM-DD"},on:{change:t.changeFansTime}}):t._e()],1),a("div",{directives:[{name:"show",rawName:"v-show",value:1==t.is_long,expression:"is_long == 1"}],staticClass:"month-picker-contain picker-contain"},[a("a-button",{class:3==t.time_type?"caledar-btn-selected":"",on:{click:function(e){return t.changeFansTimeType(3)}}},[t._v("\n\t\t\t\t\t\t近12个月\n\t\t\t\t\t")])],1)])]),a("LineCharts",{staticStyle:{margin:"20px 0"},attrs:{options:t.fansInteractive}}),a("div",{staticStyle:{overflow:"hidden","margin-bottom":"20px"}},[a("div",{staticStyle:{float:"left"}},[t._v("详细数据")]),a("a-button",{staticStyle:{float:"right"},attrs:{type:"primary",loading:t.btnLoading},on:{click:t.exportData}},[a("icon-font",{directives:[{name:"show",rawName:"v-show",value:!t.btnLoading,expression:"!btnLoading"}],attrs:{type:"icon-tuichu"}}),t._v("\n\t\t\t\t导出数据\n\t\t\t")],1)],1),a("a-table",{attrs:{columns:t.columns,dataSource:t.timeData,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"scan_times",fn:function(e,s,i){return a("span",{},[t._v("\n\t\t     "+t._s(s.scan_times)+"次\n\t\t   ")])}},{key:"scan_num",fn:function(e,s,i){return a("span",{},[t._v("\n\t\t     "+t._s(s.scan_num)+"人\n\t\t   ")])}},{key:"net_increase",fn:function(e,s,i){return a("span",{},[t._v("\n\t\t     "+t._s(s.net_increase)+"人\n\t\t   ")])}},{key:"unsubscribe",fn:function(e,s,i){return a("span",{},[t._v("\n\t\t     "+t._s(s.unsubscribe)+"人\n\t\t   ")])}},{key:"subscribe",fn:function(e,s,i){return a("span",{},[t._v("\n\t\t     "+t._s(s.subscribe)+"人\n\t\t   ")])}}])})],1)])],1)},i=[],n=(a("7f7f"),a("96cf"),a("3b8d")),r=a("7304"),c=a("c1df"),o=a.n(c),d=a("0c63"),l=d["a"].createFromIconfontCN({scriptUrl:"//at.alicdn.com/t/font_8d5l8fzk5b87iudi.js"}),u=[{title:"时间",dataIndex:"perdate",key:"perdate"},{title:"扫码次数",dataIndex:"scan_times",scopedSlots:{customRender:"scan_times"},key:"scan_times"},{title:"扫码人数",dataIndex:"scan_num",scopedSlots:{customRender:"scan_num"},key:"scan_num"},{title:"关注人数",dataIndex:"subscribe",scopedSlots:{customRender:"subscribe"},key:"subscribe"},{title:"取关人数",dataIndex:"unsubscribe",scopedSlots:{customRender:"unsubscribe"},key:"unsubscribe"},{title:"净增粉丝人数",dataIndex:"net_increase",key:"net_increase",scopedSlots:{customRender:"net_increase"}}],p={components:{LineCharts:r["a"],IconFont:l},data:function(){return{urlId:"",scan_times:"",unsubscribe:"",net_increase:"",scan_num:"",subscribe:"",statisticsList:[],time_type:1,start_date:"",end_date:"",is_export:0,fansInteractive:{legendData:[],xAxisData:[],seriesData:[]},title:"",columns:u,timeData:[],btnLoading:!1,is_long:0,startDate:"",endDate:"",dateFormat:"YYYY-MM-DD",isLoading:!0}},methods:{moment:o.a,rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},getStatisticsSum:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("scene/sum",{scene_id:this.urlId});case 2:e=t.sent,a=e.data,0!=a.error?(this.$message.error(a.error_msg),this.isLoading=!1):(this.scan_times=a.data.scan_times,this.unsubscribe=a.data.unsubscribe,this.net_increase=a.data.net_increase,this.scan_num=a.data.scan_num,this.subscribe=a.data.subscribe,this.isLoading=!1);case 5:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getStatisticsList:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var e,a,s,i=this;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return 2==this.time_type&&""==this.start_date&&(this.start_date=o()(new Date(new Date-2592e6)).format("YYYY-MM-DD")),2==this.time_type&&""==this.end_date&&(this.end_date=o()(new Date).format("YYYY-MM-DD")),t.next=4,this.axios.post("scene/sum-list",{scene_id:this.urlId,time_type:this.time_type,start_date:this.start_date,end_date:this.end_date,is_export:this.is_export});case 4:if(e=t.sent,a=e.data,0!=a.error)this.$message.error(a.error_msg);else if(0==this.is_export){for(this.isLoading=!1,this.title=a.data.title,this.fansInteractive.xAxisData=a.data.perDate,s=0;s<a.data.seriesData.length;s++)this.fansInteractive.seriesData.push({name:"",type:"line",smooth:!0,data:[]}),this.fansInteractive.seriesData[s].name=a.data.seriesData[s].name,this.fansInteractive.seriesData[s].data=a.data.seriesData[s].data,this.fansInteractive.legendData.push(a.data.seriesData[s].name);this.timeData=a.data.timeData,this.is_long=a.data.is_long,this.startDate=a.data.startDate,this.endDate=a.data.endDate,this.start_date=this.startDate,this.end_date=this.endDate}else setTimeout((function(){window.open(a.data.url),i.is_export=0,i.btnLoading=!1,i.isLoading=!1}),2e3);case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),disabledDate:function(t){return(new Date).getTime()<t.valueOf()||t.valueOf()<new Date(new Date-2592e6).getTime()-864e5},disabledDate2:function(t){return new o.a(this.endDate).valueOf()<t.valueOf()||t.valueOf()<o()(this.startDate).valueOf()},changeFansTimeType:function(t){this.time_type=t,this.isLoading=!0,this.getStatisticsList()},changeFansTime:function(t,e){this.start_date=e[0],this.end_date=e[1],this.isLoading=!0,this.getStatisticsList()},exportData:function(){this.is_export=1,this.btnLoading=!0,this.getStatisticsList()}},created:function(){this.urlId=this.$route.query.id,"undefined"!=typeof this.urlId&&(this.getStatisticsSum(),this.getStatisticsList())}},m=p,b=(a("6874"),a("2877")),_=Object(b["a"])(m,s,i,!1,null,"34d15eb8",null);e["default"]=_.exports},"4e74":function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".statistics-ul[data-v-34d15eb8]{overflow:hidden;padding:0;margin-bottom:0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between}.statistics-ul li[data-v-34d15eb8]{width:19%;height:100px;color:#fff;padding:10px}.statistics-ul li p[data-v-34d15eb8]{margin-bottom:0}.statistics-ul li .number[data-v-34d15eb8]{font-size:30px}.statistics-ul li .count[data-v-34d15eb8]{float:right}.card-caledar[data-v-34d15eb8]{margin:12px 0;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-ms-flex-wrap:wrap;flex-wrap:wrap;color:#686868}.caledar-btn-selected[data-v-34d15eb8]{boder:0;border-right:1px solid #3b74ff;background:#3b74ff;color:#fff}.ant-btn[data-v-34d15eb8],[data-v-34d15eb8] .ant-input{border-radius:0}.picker-contain[data-v-34d15eb8]{margin-right:10px}[data-v-34d15eb8] .ant-table-wrapper{background:#fff}[data-v-34d15eb8] .dark-row{background:#fafafa}.view-title[data-v-34d15eb8],[data-v-34d15eb8] .light-row{background:#fff}.view-title[data-v-34d15eb8]{border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px;font-size:16px;padding:0 20px;text-align:left}",""])},6874:function(t,e,a){"use strict";var s=a("8941"),i=a.n(s);i.a},8941:function(t,e,a){var s=a("4e74");"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var i=a("499e").default;i("e15eddec",s,!0,{sourceMap:!1,shadowMode:!1})}}]);