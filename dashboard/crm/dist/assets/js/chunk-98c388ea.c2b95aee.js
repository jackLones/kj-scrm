(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-98c388ea"],{1877:function(t,a,e){"use strict";e.r(a);var s=function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("div",{staticClass:"custom-statis"},[s("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[s("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",left:"0",top:"0",bottom:"0",right:"0","overflow-x":"hidden","overflow-y":"auto"}},[s("a-layout-content",[s("div",{staticStyle:{padding:"15px 20px",background:"#FFF"}},[t.isPreview?[s("a-alert",{attrs:{type:"error",closable:""},on:{close:t.onCloseAlert}},[s("p",{staticStyle:{"margin-bottom":"0"},attrs:{slot:"message"},slot:"message"},[t._v("\n\t\t\t\t\t\t\t\t当前消费数据未开启，点击"),s("router-link",{attrs:{to:"/shopCustom/CustomSet"}},[t._v("【顾客设置】")]),t._v("将前往开启，无数据情况下将展示演示数据。\n\t\t\t\t\t\t\t")],1)])]:t._e(),s("div",{staticClass:"content content-hd"},[s("a-row",[s("a-col",{attrs:{span:6}},[s("div",{staticClass:"panel-item"},[s("div",{staticClass:"panel-t"},[s("span",{staticClass:"panel-name"},[t._v("总销售额")]),s("a-tooltip",{attrs:{placement:"top"}},[s("template",{slot:"title"},[s("div",[t._v("企业累计总销售额（只包含导入的多渠道订单）")])]),s("a-icon",{attrs:{type:"info-circle"}})],2)],1),s("div",{staticClass:"panel-m"},[s("div",{staticClass:"panel-num"},[t._v(t._s(t.baseData.monetary))]),s("div",{staticClass:"panel-echart"},[s("LineCharts",{staticStyle:{width:"100%"},attrs:{options:t.fansAdd0}})],1)]),s("a-divider"),s("div",{staticClass:"panel-b"},[s("div",[s("span",[t._v("昨日销售额")]),t._v("¥"+t._s(t.baseData.yesterday_monetary))]),s("div",[0==t.baseData.monetary_rate_type?s("img",{attrs:{src:e("e7e4")}}):t._e(),1==t.baseData.monetary_rate_type?s("img",{attrs:{src:e("d85a")}}):t._e(),s("span",[t._v(t._s(t.baseData.day_monetary_rate)+"%")])])])],1)]),s("a-col",{attrs:{span:6}},[s("div",{staticClass:"panel-item"},[s("div",{staticClass:"panel-t"},[s("span",{staticClass:"panel-name"},[t._v("顾客总量")]),s("a-tooltip",{attrs:{placement:"top"}},[s("template",{slot:"title"},[s("div",[t._v("当前的顾客总量（企微加多个好友视为一个，非企微用户需要有手机号）")])]),s("a-icon",{attrs:{type:"info-circle"}})],2)],1),s("div",{staticClass:"panel-m"},[s("div",{staticClass:"panel-num"},[t._v(t._s(t.baseData.all_customer_number))]),s("div",{staticClass:"panel-echart"},[s("LineCharts",{staticStyle:{width:"100%"},attrs:{options:t.fansAdd}})],1)]),s("a-divider"),s("div",{staticClass:"panel-b"},[s("div",[s("span",[t._v("昨日增量")]),t._v(t._s(t.baseData.yesterday_add_user_number))])])],1)]),s("a-col",{attrs:{span:6}},[s("div",{staticClass:"panel-item"},[s("div",{staticClass:"panel-t"},[s("span",{staticClass:"panel-name"},[t._v("消费顾客数")]),s("a-tooltip",{attrs:{placement:"top"}},[s("template",{slot:"title"},[s("div",[t._v("有下单的顾客数")])]),s("a-icon",{attrs:{type:"info-circle"}})],2)],1),s("div",{staticClass:"panel-m"},[s("div",{staticClass:"panel-num"},[t._v(t._s(t.baseData.buy_customer_number))]),s("div",{staticClass:"panel-echart"},[s("BarCharts",{staticStyle:{width:"100%"},attrs:{options:t.fansAdd1}})],1)]),s("a-divider"),s("div",{staticClass:"panel-b"},[s("div",[s("span",[t._v("比例")]),t._v(t._s(t.baseData.monetary_rate)+"%")])])],1)]),s("a-col",{attrs:{span:6}},[s("div",{staticClass:"panel-item"},[s("div",{staticClass:"panel-t"},[s("span",{staticClass:"panel-name"},[t._v("互动顾客数")]),s("a-tooltip",{attrs:{placement:"top"}},[s("template",{slot:"title"},[s("div",[t._v("有互动的顾客数")])]),s("a-icon",{attrs:{type:"info-circle"}})],2)],1),s("div",{staticClass:"panel-m"},[s("div",{staticClass:"panel-num"},[t._v(t._s(t.baseData.interaction_number))]),s("div",{staticClass:"panel-echart"},[s("BarCharts",{staticStyle:{width:"100%"},attrs:{options:t.fansAdd2}})],1)]),s("a-divider"),s("div",{staticClass:"panel-b"},[s("div",[s("span",[t._v("比例")]),t._v(t._s(t.baseData.interaction_rate)+"%")])])],1)])],1)],1),s("div",{staticClass:"content content-td"},[[s("a-tabs",{on:{change:t.saleBarType}},[s("div",{attrs:{slot:"tabBarExtraContent"},slot:"tabBarExtraContent"},[s("a-radio-group",{attrs:{"button-style":"solid"},on:{change:t.changeCycleType},model:{value:t.cycleType,callback:function(a){t.cycleType=a},expression:"cycleType"}},[s("a-space",{attrs:{size:0}},[s("a-radio-button",{attrs:{value:"1"}},[t._v("本周")]),s("a-radio-button",{attrs:{value:"2"}},[t._v("本月")]),s("a-radio-button",{attrs:{value:"3"}},[t._v("全年")])],1)],1),s("a-range-picker",{staticStyle:{width:"320px","margin-left":"50px"},attrs:{format:"YYYY-MM-DD",allowClear:"","disabled-date":t.disabledDateDay},on:{change:function(a){return t.changeTime(a)}},model:{value:t.searchTime,callback:function(a){t.searchTime=a},expression:"searchTime"}},[s("a-icon",{attrs:{slot:"suffixIcon",type:"calendar"},slot:"suffixIcon"})],1)],1),s("a-tab-pane",{key:"1",attrs:{tab:"销售额"}},[s("BarCharts",{staticStyle:{width:"100%"},attrs:{options:t.saleBar}})],1),s("a-tab-pane",{key:"2",attrs:{tab:"拉新量"}},[s("BarCharts",{staticStyle:{width:"100%"},attrs:{options:t.saleBar}})],1)],1)]],2),s("div",{staticClass:"content content-md"},[s("a-row",[s("a-col",{attrs:{span:12}},[[s("a-tabs",{attrs:{type:"card"},on:{change:t.guideShopping}},[s("div",{attrs:{slot:"tabBarExtraContent"},slot:"tabBarExtraContent"},[t._v("导购拉新排行榜")]),t._e(),t._e(),s("a-tab-pane",{key:"3",attrs:{tab:"导购"}},[s("a-table",{attrs:{columns:t.columns,dataSource:t.customList,pagination:!1},scopedSlots:t._u([{key:"rank",fn:function(a,e,r){return[s("span",[t._v(t._s(r+1))])]}},{key:"name",fn:function(a){return[s("a",[t._v(t._s(a))])]}},{key:"shop",fn:function(a){return[s("a",[t._v(t._s(a))])]}}])})],1)],1)]],2),s("a-col",{staticStyle:{float:"right"},attrs:{span:8}},[[s("a-tabs",{attrs:{type:"card"},on:{change:t.guideSale}},[s("div",{attrs:{slot:"tabBarExtraContent"},slot:"tabBarExtraContent"},[t._v("导购销售额排名")]),t._e(),t._e(),s("a-tab-pane",{key:"3",attrs:{tab:"导购"}},[t.saleList.length>0?s("ul",{staticStyle:{"padding-left":"0"}},t._l(t.saleList,(function(a,e){return s("li",{key:e,staticClass:"sale-list"},[s("a-row",[s("a-col",{attrs:{span:3}},[s("span",{staticClass:"sort",class:{active:e<3}},[t._v(t._s(e+1))])]),s("a-col",{attrs:{span:12}},[s("span",[t._v(t._s(a.guide_name))])]),s("a-col",{attrs:{span:9}},[s("span",[t._v(t._s(a.monetary))])])],1)],1)})),0):s("ul",[s("li",{staticStyle:{"text-align":"center","margin-top":"30%"}},[t._v("暂无数据")])])])],1)]],2)],1)],1),s("div",{staticClass:"content content-bd"},[t.tabData.length>0?s("a-tabs",{on:{change:t.guideProgress}},[s("div",{attrs:{slot:"tabBarExtraContent"},slot:"tabBarExtraContent"},[s("a-month-picker",{attrs:{format:"YYYY/MM","default-value":t.moment(t.now,"YYYY/MM"),"disabled-date":t.disabledDate},on:{change:function(a){return t.changeMonth(a)}}})],1),s("a-tab-pane",{key:"0",attrs:{tab:"一级分组"}}),t._l(t.tabData,(function(t){return s("a-tab-pane",{key:t.group_id,attrs:{tab:t.grade_name}})}))],2):s("a-tabs",{on:{change:t.guideProgress}},[s("div",{attrs:{slot:"tabBarExtraContent"},slot:"tabBarExtraContent"},[s("a-month-picker",{attrs:{format:"YYYY/MM","default-value":t.moment(t.now,"YYYY/MM"),"disabled-date":t.disabledDate},on:{change:function(a){return t.changeMonth(a)}}})],1),s("a-tab-pane",{key:"0",attrs:{tab:"所有分组"}})],1),s("div",{staticClass:"progress-area"},[s("div",{staticClass:"go-left",on:{click:function(a){return t.goScroll("left")}}},[s("a-icon",{attrs:{type:"left"}})],1),s("div",{ref:"progressW",staticClass:"progress-content"},[s("div",{staticClass:"progress-warper",style:{left:t.progressLeft+"px"}},t._l(t.ereaList,(function(a,e){return s("div",{key:e,staticClass:"progress-item",class:{active:t.ereaIndex==e},on:{click:function(s){return t.areaTab(e,a)}}},[s("div",{staticClass:"progress-chiled"},[s("div",{staticClass:"progress-l"},[s("div",{staticClass:"progress-name"},[t._v(t._s(a.group_name))]),s("span",[t._v("拉新完成度")]),s("div",{staticClass:"progress-num"},[t._v(t._s(a.customer_rate)+"%")])]),s("div",{staticClass:"progress-r"},[s("svg",{attrs:{width:"80",height:"80",viewBox:"0 0 80 80"}},[s("circle",{staticClass:"circle-grey",attrs:{cx:"40",cy:"40",r:"30"}}),s("circle",{staticClass:"circle-color",attrs:{cx:"40",cy:"40",r:"30","stroke-dasharray":"190","stroke-dashoffset":190*(100-a.customer_rate)/100}})])])]),s("div",{staticClass:"progress-chiled progress-chiled-sale"},[s("div",{staticClass:"progress-l"},[s("span",[t._v("销售完成度")]),s("div",{staticClass:"progress-num"},[t._v(t._s(a.monetary_rate)+"%")])]),s("div",{staticClass:"progress-r"},[s("svg",{attrs:{width:"80",height:"80",viewBox:"0 0 80 80"}},[s("circle",{staticClass:"circle-grey",attrs:{cx:"40",cy:"40",r:"30"}}),s("circle",{staticClass:"circle-color",attrs:{cx:"40",cy:"40",r:"30","stroke-dasharray":"190","stroke-dashoffset":190*(100-a.monetary_rate)/100}})])])])])})),0)]),s("div",{staticClass:"go-right",on:{click:function(a){return t.goScroll("right")}}},[s("a-icon",{attrs:{type:"right"}})],1)]),s("div",{staticClass:"echarts-area"},[s("div",{staticClass:"echarts-area"},[s("LineCharts",{staticStyle:{width:"100%"},attrs:{options:t.fansAdd3}})],1)])],1),s("div",{staticClass:"page-tip",on:{click:t.tipOpen}},[s("a-icon",{attrs:{type:"question-circle"}})],1)],2),s("a-drawer",{attrs:{title:"使用帮助",placement:"right",destroyOnClose:!0,closable:!0,visible:t.tipVisible},on:{close:t.tipClose}},[s("div",{staticClass:"content-msg"},[s("p",{staticStyle:{"margin-bottom":"8px"}},[t._v("\n\t\t\t\t\t\t\t1、当前页面是统计企业和门店的拉新和销售情况，若未开始消费数据功能，将没有销售业绩统计，前往开启"),s("router-link",{attrs:{to:"/shopCustom/CustomSet"}},[t._v("【顾客设置】")])],1),s("p",{staticStyle:{"margin-bottom":"8px"}},[t._v("\n\t\t\t\t\t\t\t2、 所有数据是截止到昨天的数据，用户删除、订单退款等不会影响历史数据\n\t\t\t\t\t\t")]),s("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t3、数据定义：销售额指的订单在时间范围内，企业的多渠道订单总的消费额。拉新指的是在时间范围内，首次消费的顾客。导购关联的销售额需要订单关联本人，门店关联的销售额需要订单能归属此门店，拉新的归属同样。\n\t\t\t\t\t\t")]),s("p",{staticStyle:{"margin-bottom":"8px"}},[t._v("\n\t\t\t\t\t\t\t数据来源：前往"),s("router-link",{attrs:{to:"/appCenter/list"}},[t._v("【应用中心】")]),t._v("开启电商系统的订单导入功能，具体可以联系客服了解。\n\t\t\t\t\t\t")],1),s("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t4、顶部迷你卡片区域：\n\t\t\t\t\t\t")]),s("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t销售额是企业累计总销售额（只包含导入的多渠道订单），今天看到的是昨天日销售额环比前天的，小图表显示前15天的日销售额。\n\t\t\t\t\t\t")]),s("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t顾客总量当前的顾客总量（企微加多个好友视为一个，非企微用户需要有手机号），单独显示昨天增加了多少个，小图表显示前15天的天顾客增量。\n\t\t\t\t\t\t")]),s("p",{staticStyle:{"margin-bottom":"8px"}},[t._v("\n\t\t\t\t\t\t\t消费顾客数指的有下单的顾客数，比例为占比总顾客比例，小图表显示前15天的天消费顾客增量。会话顾客数是有互动的顾客数，占比总顾客比例，小图表显示前15天的天会话顾客增量。需要开启"),s("router-link",{attrs:{to:"/archive/message"}},[t._v("【会话存档】")]),t._v("功能，为保证数据齐全，需要开通所有对外沟通人的权限。\n\t\t\t\t\t\t")],1),s("p",{staticStyle:{"margin-bottom":"8px"}},[t._v("\n\t\t\t\t\t\t\t5、销售额拉新量柱状图、导购拉新销售排行榜，可以进行时间筛选，只能选择有数据起始的那天到昨天。跨度大于31天柱状图按照月显示。\n\t\t\t\t\t\t")]),s("p",{staticStyle:{"margin-bottom":"8px"}},[t._v("\n\t\t\t\t\t\t\t6、门店及分组数据统计区域，固定按照自然月展示数据。可以按照分组从高到低逐级查看拉新和销售数据，完成度比例是环比上月数据。无分组建议前往"),s("router-link",{attrs:{to:"/store/list"}},[t._v("【门店管理】")]),t._v("设置。\n\t\t\t\t\t\t")],1)])])],1)],1)],1)],1)},r=[],n=(e("ac6a"),e("456d"),e("96cf"),e("3b8d")),i=e("c1df"),o=e.n(i),c=e("20fa"),l=e("7304"),d=[{title:"排名",width:"60px",scopedSlots:{customRender:"rank"}},{title:"员工姓名",dataIndex:"guide_name",key:"guide_name",scopedSlots:{customRender:"name"},ellipsis:!0},{title:"总顾客数",dataIndex:"all_customer_number",key:"all_customer_number"},{title:"拉新数",dataIndex:"add_user_number",key:"add_user_number"}],p=new Date,g=p.getDay(),u=p.getDate(),m=p.getMonth(),h=p.getYear();function b(t,a){var e=new Date(t),s=e.getFullYear(),r=e.getMonth()+1;r=r<10?"0"+r:r;var n=e.getDate();n=n<10?"0"+n:n;var i=e.getHours();i=i<10?"0"+i:i;var o=e.getMinutes(),c=e.getSeconds();return o=o<10?"0"+o:o,c=c<10?"0"+c:c,a?s+"-"+r+"-"+n+" "+i+":"+o+":"+c:s+"-"+r+"-"+n}function f(){0==g&&(g=7);var t=new Date(h,m,u-g+1);return b(t)}function v(){var t=new Date(h,m,u)-864e5;return b(t)}function x(){var t=new Date(h,m,1);return b(t)}function _(){var t=new Date(h,m,u)-864e5;return b(t)}function y(){var t=new Date(h,0,1);return b(t)}h+=h<2e3?1900:0;var w={name:"CustomStatis",components:{BarCharts:c["a"],LineCharts:l["a"]},data:function(){return{moment:o.a,now:p,isPreview:0,baseData:{},fansAdd0:{title:"总销售额",data_Type:1,legendData:[{show:!1}],xAxisData:[],seriesData:[{name:"总销售额",type:"line",smooth:!0,showSymbol:!1,areaStyle:{opacity:.3,color:"#3398DB",shadowColor:"#3398DB",shadowOffsetY:1},emphasis:{focus:"series"},data:[]}]},fansAdd:{title:"客户总量",data_Type:1,legendData:[{show:!1}],xAxisData:[],seriesData:[{name:"客户总量",type:"line",smooth:!0,showSymbol:!1,areaStyle:{opacity:.3,color:"#3398DB",shadowColor:"#3398DB",shadowOffsetY:1},emphasis:{focus:"series"},data:[]}]},fansAdd1:{title:"顾客数",xAxisData:[],name:"顾客数",data_Type:5,seriesData:[]},fansAdd2:{title:"顾客数",xAxisData:[],name:"顾客数",data_Type:5,seriesData:[]},saleBarTab:1,cycleType:"1",startValue:"",endValue:"",searchTime:null,minDay:"",saleBar:{title:"销售额趋势",xAxisData:[],name:"销售额",seriesData:[]},saleBarData:{},columns:d,customList:[],saleList:[],ereaList:[],ereaIndex:0,progressLeft:0,fansAdd3:{title:"客户增长",legendData:["拉新量","销售额"],xAxisData:[],yAxisData:[{type:"value",name:"拉新量",splitLine:{show:!0},axisLine:{show:!0},axisLabel:{formatter:"{value}"}},{type:"value",name:"销售额",splitLine:{show:!0,lineStyle:{type:"dashed"}},axisLabel:{formatter:"{value}"}}],seriesData:[{name:"拉新量",type:"line",smooth:!1,data:[]},{name:"销售额",type:"line",yAxisIndex:1,smooth:!1,data:[]}]},searchMonth:o()(p,"YYYY/MM").format("YYYY-MM"),pid:0,tabData:[],isChild:!1,store_str:"",tipVisible:!1}},methods:{onCloseAlert:function(){this.isPreview=0},disabledDateDay:function(t){return t.valueOf()>(new Date).getTime()-864e5||t<o()(this.minDay)},disabledDate:function(t){return t&&t>o()().endOf("day")},changeCycleType:function(){1==this.cycleType?(this.startValue=f(),this.endValue=v()):2==this.cycleType?(this.startValue=x(),this.endValue=_()):3==this.cycleType&&(this.startValue=y(),this.endValue=v()),this.searchTime=null,this.barFun(),this.shopSale()},changeTime:function(t){var a=this;a.cycleType=0,a.searchTime=t,a.startValue=t[0].format("YYYY-MM-DD"),a.endValue=t[1].format("YYYY-MM-DD"),a.barFun(),a.shopSale()},saleBarType:function(t){this.saleBarTab=t,1==t?(this.saleBar.title="销售额趋势",this.saleBar.name="销售额",this.saleBar.seriesData=this.saleBarData.monetary):2==t&&(this.saleBar.title="拉新量趋势",this.saleBar.name="拉新量",this.saleBar.seriesData=this.saleBarData.add_user_number)},guideShopping:function(t){1==t?console.log("城市"):2==t?console.log("门店"):3==t&&console.log("导购")},guideSale:function(t){1==t?console.log("城市"):2==t?console.log("门店"):3==t&&console.log("导购")},guideProgress:function(t){this.progressLeft=0,this.ereaIndex=0,this.gardeFun(t)},areaTab:function(t,a){var e=this;if(e.ereaIndex!=t){if(e.ereaIndex=t,1==a.have_child){e.tabData=e.tabData.concat(a);for(var s=0;s<e.tabData.length;s++)e.tabData[s].group_id==a.group_id&&s>0&&(e.isChild?e.tabData.splice(s-1,1):e.tabData.splice(s,1))}else{e.tabData=e.tabData.concat(a);for(var r=0;r<e.tabData.length;r++)e.tabData[r].group_id==a.group_id&&r>0&&e.tabData.splice(r,1)}this.store_str=a.store_str,this.lineFun(a.store_str)}},goScroll:function(t){var a=this;"left"==t?a.progressLeft<0&&(a.progressLeft=a.progressLeft+220):"right"==t&&a.$refs.progressW.clientWidth-220*a.ereaList.length<a.progressLeft&&(a.progressLeft=a.progressLeft-220)},changeMonth:function(t){var a=this;a.searchMonth=t.format("YYYY-MM"),a.lineFun(a.store_str),a.gardeFun1(a.pid)},baseFun:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var a,e,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=this,a.isLoading=!0,t.next=4,a.axios.post("shop-customer/all-data",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):""});case 4:e=t.sent,s=e.data,0!=s.error?(a.isLoading=!1,a.$message.error(s.error_msg)):(a.isLoading=!1,a.isPreview=s.data.is_preview,setTimeout((function(){a.isPreview=0}),1e4),a.baseData=s.data.result,a.minDay=s.data.result.min_day,Object.keys(s.data.result.old_data_list).length>0&&(a.fansAdd0.xAxisData=s.data.result.old_data_list.add_day,a.fansAdd.xAxisData=s.data.result.old_data_list.add_day,a.fansAdd1.xAxisData=s.data.result.old_data_list.add_day,a.fansAdd2.xAxisData=s.data.result.old_data_list.add_day,a.fansAdd0.seriesData[0].data=s.data.result.old_data_list.day_monetary,a.fansAdd.seriesData[0].data=s.data.result.old_data_list.day_add_user_number,a.fansAdd1.seriesData=s.data.result.old_data_list.day_consumption_number,a.fansAdd2.seriesData=s.data.result.old_data_list.day_interaction_number));case 7:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),barFun:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var a,e,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=this,a.isLoading=!0,t.next=4,a.axios.post("shop-customer/all-columnar",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",start_date:a.startValue,end_date:a.endValue});case 4:e=t.sent,s=e.data,0!=s.error?(a.isLoading=!1,a.$message.error(s.error_msg)):(a.isLoading=!1,a.saleBarData=s.data.result,a.saleBar.xAxisData=s.data.result.day,1==a.saleBarTab?a.saleBar.seriesData=s.data.result.monetary:2==a.saleBarTab&&(a.saleBar.seriesData=s.data.result.add_user_number));case 7:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),shopSale:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var a,e,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return a=this,a.isLoading=!0,t.next=4,a.axios.post("shop-customer/guide-rank",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",start_date:a.startValue,end_date:a.endValue});case 4:e=t.sent,s=e.data,0!=s.error?(a.isLoading=!1,a.$message.error(s.error_msg)):(a.isLoading=!1,a.customList=s.data.add_user_list,a.saleList=s.data.monetary_list);case 7:case"end":return t.stop()}}),t,this)})));function a(){return t.apply(this,arguments)}return a}(),gardeFun:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(a){var e,s,r,n,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=this,e.isLoading=!0,t.next=4,e.axios.post("shop-customer/group-line",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",pid:a||e.pid,month:e.searchMonth});case 4:if(s=t.sent,r=s.data,0!=r.error)e.isLoading=!1,e.$message.error(r.error_msg);else{if(e.isLoading=!1,e.ereaList=r.data,e.pid=a||e.pid,n=a||e.pid,1==r.data[0].have_child)if(0==n)e.tabData=[r.data[0]],e.isChild=!0;else for(e.tabData=e.tabData.concat(r.data[0]),i=0;i<e.tabData.length;i++)e.tabData[i].group_id==n&&(e.tabData.splice(i+1,e.tabData.length,r.data[0]),e.isChild=!0);else 0==n&&(e.tabData=[]),e.isChild=!1;e.store_str=r.data[0].store_str,this.lineFun(r.data[0].store_str)}case 7:case"end":return t.stop()}}),t,this)})));function a(a){return t.apply(this,arguments)}return a}(),gardeFun1:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(a){var e,s,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=this,e.isLoading=!0,t.next=4,e.axios.post("shop-customer/group-line",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",pid:a||e.pid,month:e.searchMonth});case 4:s=t.sent,r=s.data,0!=r.error?(e.isLoading=!1,e.$message.error(r.error_msg)):(e.isLoading=!1,e.ereaList=r.data);case 7:case"end":return t.stop()}}),t,this)})));function a(a){return t.apply(this,arguments)}return a}(),lineFun:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(a){var e,s,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=this,e.isLoading=!0,t.next=4,e.axios.post("shop-customer/group-month-data",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",store_str:a,month:e.searchMonth});case 4:s=t.sent,r=s.data,0!=r.error?(e.isLoading=!1,e.$message.error(r.error_msg)):(e.isLoading=!1,e.fansAdd3.xAxisData=r.data.add_day,e.fansAdd3.seriesData[0].data=r.data.add_user_number,e.fansAdd3.seriesData[1].data=r.data.monetary);case 7:case"end":return t.stop()}}),t,this)})));function a(a){return t.apply(this,arguments)}return a}(),tipOpen:function(){this.tipVisible=!0},tipClose:function(){this.tipVisible=!1}},mounted:function(){var t=this;this.$store.dispatch("getCorp",(function(a){t.corpInfo=a,t.startValue=f(),t.endValue=v(),t.dateValue=[o()(t.startValue,"YYYY-MM-DD"),o()(t.endValue,"YYYY-MM-DD")],t.baseFun(),t.barFun(),t.shopSale(),t.gardeFun()}))}},D=w,k=(e("5172"),e("2877")),C=Object(k["a"])(D,s,r,!1,null,"9ca0c234",null);a["default"]=C.exports},5172:function(t,a,e){"use strict";var s=e("7245"),r=e.n(s);r.a},7245:function(t,a,e){var s=e("d75f");"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var r=e("499e").default;r("4fd06f1a",s,!0,{sourceMap:!1,shadowMode:!1})},d75f:function(t,a,e){a=t.exports=e("2350")(!1),a.push([t.i,".custom-statis[data-v-9ca0c234]{width:100%;height:100%}.ant-layout-content[data-v-9ca0c234]{margin:20px;min-width:885px}.content-msg[data-v-9ca0c234]{border:1px solid #ffdda6;background:#fff2db;padding:10px;text-align:left;margin-bottom:20px}.content[data-v-9ca0c234]{margin-top:20px;width:100%;min-width:885px;background:#fff}.content-hd .ant-col:first-child .panel-item[data-v-9ca0c234]{margin-left:0}.content-hd .ant-col:last-child .panel-item[data-v-9ca0c234]{margin-right:0}.content-hd .panel-item[data-v-9ca0c234]{border:1px solid #e2e2e2;padding:20px;margin:0 10px}.content-hd .panel-item .panel-t[data-v-9ca0c234]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.content-hd .panel-item .panel-m .panel-num[data-v-9ca0c234]{font-size:25px;font-weight:600}.content-hd .panel-item .panel-m .panel-echart[data-v-9ca0c234]{width:100%;height:50px}.content-hd .panel-item .panel-m .panel-echart .echarts[data-v-9ca0c234]{min-height:130px;height:130px}.content-hd .panel-item .panel-m .panel-echart .echarts[data-v-9ca0c234] canvas{margin-top:-45px!important;margin-left:-20px!important}.content-hd .panel-item .panel-b[data-v-9ca0c234]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;-webkit-box-align:center;-ms-flex-align:center;align-items:center}.content-hd .panel-item .panel-b span[data-v-9ca0c234]{margin-right:10px}.content-hd .panel-item .panel-b img[data-v-9ca0c234]{margin:0 5px 3px;width:9px}.content-td[data-v-9ca0c234] .ant-tabs-bar{background-color:#fff;border-bottom:1px solid #e8e8e8}.content-td .ant-radio-button-wrapper[data-v-9ca0c234]{text-align:center;width:60px}.content-md[data-v-9ca0c234] .ant-tabs-bar{background-color:#fff;border-bottom:1px solid #e8e8e8;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;height:40px}.content-md[data-v-9ca0c234] .ant-tabs-extra-content{font-size:15px;font-weight:600}.content-md[data-v-9ca0c234] .ant-table-tbody>tr>td,.content-md[data-v-9ca0c234] .ant-table-thead>tr>th{padding:8px 16px}.content-md .sale-list[data-v-9ca0c234]{margin-bottom:22px}.content-md .sale-list[data-v-9ca0c234]:last-child{margin-bottom:0}.content-md .sale-list .sort[data-v-9ca0c234]{width:20px;height:20px;text-align:center;line-height:20px;display:block;background-color:rgba(0,0,0,.1);border-radius:50%}.content-md .sale-list .sort.active[data-v-9ca0c234]{background-color:#000;color:#fff}.content-bd[data-v-9ca0c234] .ant-tabs-bar{background-color:#fff;border-bottom:1px solid #e8e8e8}.content-bd[data-v-9ca0c234] .ant-tabs-content{display:none}.content-bd .progress-area[data-v-9ca0c234]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;-webkit-box-align:center;-ms-flex-align:center;align-items:center;height:auto}.content-bd .progress-area .go-left[data-v-9ca0c234],.content-bd .progress-area .go-right[data-v-9ca0c234]{-ms-flex-negative:0;flex-shrink:0;width:30px;height:100px;text-align:center;line-height:100px;border-right:1px solid #eaeaea;color:#999;background-color:#fff}.content-bd .progress-area .go-right[data-v-9ca0c234]{border-left:1px solid #eaeaea;border-right:none}.content-bd .progress-area .progress-content[data-v-9ca0c234]{width:calc(100% - 60px);min-height:220px;overflow:hidden;position:relative}.content-bd .progress-area .progress-content .progress-warper[data-v-9ca0c234]{position:absolute;left:0;top:0;z-index:0;width:100%;height:100%;display:-webkit-box;display:-ms-flexbox;display:flex}.content-bd .progress-area .progress-content .progress-item[data-v-9ca0c234]{width:200px;-ms-flex-negative:0;flex-shrink:0;-webkit-box-sizing:border-box;box-sizing:border-box;padding:20px 10px 20px 20px;border-top:2px solid #fff;margin-right:20px}.content-bd .progress-area .progress-content .progress-item .progress-chiled[data-v-9ca0c234]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;-webkit-box-align:end;-ms-flex-align:end;align-items:flex-end}.content-bd .progress-area .progress-content .progress-item .progress-chiled .progress-name[data-v-9ca0c234]{max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.content-bd .progress-area .progress-content .progress-item .progress-chiled-sale[data-v-9ca0c234]{margin-top:12px}.content-bd .progress-area .progress-content .progress-item.active[data-v-9ca0c234]{border-top:2px solid #1890ff}.content-bd .progress-area .progress-content .progress-item.active .progress-r .circle-color[data-v-9ca0c234]{stroke:#1890ff}.content-bd .progress-area .progress-content .progress-item[data-v-9ca0c234]:last-child{margin-right:0}.content-bd .progress-area .progress-content .progress-item .progress-l[data-v-9ca0c234]{font-size:12px;color:#aaa}.content-bd .progress-area .progress-content .progress-item .progress-l .progress-name[data-v-9ca0c234]{font-size:16px;color:#666;margin-bottom:10px}.content-bd .progress-area .progress-content .progress-item .progress-l .progress-num[data-v-9ca0c234]{font-size:22px;color:#333}.content-bd .progress-area .progress-content .progress-item .progress-r svg[data-v-9ca0c234]{-webkit-transform:rotate(-90deg);transform:rotate(-90deg);-webkit-transform-origin:50% 50%;transform-origin:50% 50%}.content-bd .progress-area .progress-content .progress-item .progress-r .circle-grey[data-v-9ca0c234]{stroke:#dfdfdf;stroke-width:12px;fill:none}.content-bd .progress-area .progress-content .progress-item .progress-r .circle-color[data-v-9ca0c234]{stroke:#99d5fd;stroke-width:12px;fill:none;-webkit-transition:stroke-dashoffset 1s linear;transition:stroke-dashoffset 1s linear}.content-bd .echarts-area[data-v-9ca0c234]{margin-top:12px}.page-tip[data-v-9ca0c234]{position:fixed;right:10px;bottom:18%;z-index:10000;width:50px;height:50px;border-radius:50%;display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center;-webkit-box-align:center;-ms-flex-align:center;align-items:center;background-color:#fff;-webkit-box-shadow:2px 2px 5px #d3d6da;box-shadow:2px 2px 5px #d3d6da}.page-tip .anticon[data-v-9ca0c234]{font-size:30px}",""])}}]);