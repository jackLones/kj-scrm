(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-69519de6"],{"050e":function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".custom[data-v-75a137b0]{width:100%;height:100%}.ant-layout-header[data-v-75a137b0]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px;padding:0 20px;font-size:16px;text-align:left}.ant-layout-content[data-v-75a137b0]{margin:20px;min-width:885px;background-color:#fff}.content-msg[data-v-75a137b0]{border:1px solid #ffdda6;background:#fff2db;padding:10px;text-align:left;margin-bottom:20px}.content-hd[data-v-75a137b0]{margin-top:20px;width:100%;min-width:885px}.content-hd-box[data-v-75a137b0]{line-height:40px}.select-option[data-v-75a137b0]{display:inline-block;margin-right:10px;margin-bottom:10px}.select-option label[data-v-75a137b0]{margin-right:5px;display:inline-block;text-align:right;width:100px}.select-option .ant-select[data-v-75a137b0],.select-option input[data-v-75a137b0]{margin-right:5px;width:210px}.content-hd-list[data-v-75a137b0]{display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-top:12px}.content-hd-list .ant-tag[data-v-75a137b0]{margin-bottom:8px}.content-hd-list .ant-tag .anticon[data-v-75a137b0]{margin-left:8px}.table-num[data-v-75a137b0]{height:32px;line-height:32rpx;margin-top:6px}.table-num span[data-v-75a137b0]{color:#1890ff;margin:0 3px}.content-bd[data-v-75a137b0]{min-height:120px;border:1px solid #e2e2e2;min-width:885px;width:100%}.content-bd[data-v-75a137b0],[data-v-75a137b0] .light-row{background:#fff}[data-v-75a137b0] .ant-drawer-content-wrapper{width:470px!important}.frm-item[data-v-75a137b0]{margin-top:24px;padding-left:24px;font-size:18px}.frm-title[data-v-75a137b0]{font-weight:600;margin-left:10px}.frm-title2[data-v-75a137b0]{margin-bottom:12px;font-weight:600}.guide-modal label[data-v-75a137b0]{width:80px;text-align:right;display:block}.guide-list[data-v-75a137b0]{-ms-flex-align:start;margin-top:20px}.guide-item[data-v-75a137b0],.guide-list[data-v-75a137b0]{display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:start;align-items:flex-start}.guide-item[data-v-75a137b0]{border:1px solid #d9d9d9;border-radius:5px;padding:10px;-webkit-box-flex:1;-ms-flex:1;flex:1;-ms-flex-wrap:wrap;flex-wrap:wrap;-ms-flex-align:start;min-height:80px}.guide-item .ant-tag[data-v-75a137b0]{margin-bottom:5px}.guide-select[data-v-75a137b0],.guide-user[data-v-75a137b0]{display:-webkit-box;display:-ms-flexbox;display:flex}.guide-select[data-v-75a137b0]{-webkit-box-align:center;-ms-flex-align:center;align-items:center;margin-top:20px}.guide-select .ant-radio-group[data-v-75a137b0]{-webkit-box-flex:1;-ms-flex:1;flex:1;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap}.guide-select .ant-radio-group .ant-radio-wrapper[data-v-75a137b0]{margin-bottom:5px}.guide-des[data-v-75a137b0]{margin-top:5px;margin-left:80px}",""])},9122:function(t,e,i){"use strict";i.r(e);var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"custom"},[i("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[i("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",top:"0",bottom:"0",left:"0",right:"0","overflow-x":"hidden","overflow-y":"auto"}},[i("a-layout-header",[t._v("顾客管理")]),i("a-layout-content",[i("div",{staticStyle:{padding:"15px 20px",background:"#FFF"}},[i("div",{staticClass:"content-msg"},[i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t1、顾客包括企微用户和非企微用户，多个企微好友关系只会算为一个顾客，非企微用户必须有手机号，与企微用户如果手机号相同会合并。\n\t\t\t\t\t\t")]),i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t2、为便于顾客管理，建议先设置客户等级和RFM评级的"),i("router-link",{attrs:{to:"/shopCustom/CustomSet"}},[t._v("相关设置。")])],1),i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t3、入库时间是指用户首次进入系统的时间，可能有两种情况，一是添加了企微好友，一是手机号作为非企微用户生成顾客。\n\t\t\t\t\t\t")])]),i("div",{staticClass:"content-hd"},[i("div",{staticClass:"content-hd-box"},[i("span",{staticClass:"select-option"},[i("label",[t._v("快速搜索：")]),i("a-input",{attrs:{placeholder:"顾客姓名/昵称/手机号"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.searchStaff(e)}},model:{value:t.keyWord,callback:function(e){t.keyWord=e},expression:"keyWord"}})],1),i("span",{staticClass:"select-option"},[i("label",[t._v("导购姓名：")]),i("a-input",{attrs:{placeholder:"导购姓名/手机号"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.searchStaff(e)}},model:{value:t.customName,callback:function(e){t.customName=e},expression:"customName"}})],1),i("span",{staticClass:"select-option"},[i("label",[t._v("等级：")]),i("a-select",{attrs:{showSearch:"",optionFilterProp:"children",placeholder:"等级筛选"},model:{value:t.gradeVal,callback:function(e){t.gradeVal=e},expression:"gradeVal"}},[i("a-select-option",{attrs:{value:-1}},[t._v("等级筛选")]),t._l(t.gradeArr,(function(e,a){return i("a-select-option",{key:a},[t._v(t._s(e.title))])}))],2)],1),i("span",{staticClass:"select-option"},[i("label",[t._v("RFM评级：")]),i("a-select",{attrs:{showSearch:"",optionFilterProp:"children"},model:{value:t.rfmVal,callback:function(e){t.rfmVal=e},expression:"rfmVal"}},[i("a-select-option",{attrs:{value:-1}},[t._v("等级筛选")]),t._l(t.rfmArr,(function(e,a){return i("a-select-option",{key:a},[t._v(t._s(e.default_name))])}))],2)],1),i("span",{staticClass:"select-option"},[i("label",[t._v("添加时间：")]),i("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00","HH:mm"),t.moment("23:59","HH:mm")],format:"HH:mm"},format:"YYYY-MM-DD HH:mm",allowClear:"","disabled-date":t.disabledDate},on:{change:function(e){return t.changeTime(e,"joinTime")}},model:{value:t.joinTime,callback:function(e){t.joinTime=e},expression:"joinTime"}})],1),i("span",{staticClass:"select-option"},[i("label",[t._v("最后消费：")]),i("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00","HH:mm"),t.moment("23:59","HH:mm")],format:"HH:mm"},format:"YYYY-MM-DD HH:mm",allowClear:"","disabled-date":t.disabledDate},on:{change:function(e){return t.changeTime(e,"moneyTime")}},model:{value:t.moneyTime,callback:function(e){t.moneyTime=e},expression:"moneyTime"}})],1),i("span",{staticClass:"select-option"},[i("label",[t._v("最后互动：")]),i("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00","HH:mm"),t.moment("23:59","HH:mm")],format:"HH:mm"},format:"YYYY-MM-DD HH:mm",allowClear:"","disabled-date":t.disabledDate},on:{change:function(e){return t.changeTime(e,"interaTime")}},model:{value:t.interaTime,callback:function(e){t.interaTime=e},expression:"interaTime"}})],1),i("span",{staticClass:"select-option"},[i("label",[t._v("消费区间：")]),i("a-input",{attrs:{placeholder:"金额下限"},model:{value:t.moneyDownLimit,callback:function(e){t.moneyDownLimit=e},expression:"moneyDownLimit"}}),i("a-input",{attrs:{placeholder:"金额上限"},model:{value:t.moneyUpLimit,callback:function(e){t.moneyUpLimit=e},expression:"moneyUpLimit"}})],1),i("span",{staticClass:"select-option"},[i("label",[t._v("互动区间：")]),i("a-input",{attrs:{placeholder:"次数下限"},model:{value:t.rateDownLimit,callback:function(e){t.rateDownLimit=e},expression:"rateDownLimit"}}),i("a-input",{attrs:{placeholder:"次数上限"},model:{value:t.rateUpLimit,callback:function(e){t.rateUpLimit=e},expression:"rateUpLimit"}})],1),i("span",{staticClass:"select-option"},[i("a-button",{staticStyle:{"margin-right":"5px"},attrs:{type:"primary"},on:{click:t.searchStaff}},[t._v("查找")]),i("a-button",{staticStyle:{"margin-right":"10px"},on:{click:t.reset}},[t._v("清空")])],1)]),i("div",{staticClass:"content-hd-list"},[t.keyWord?i("a-tag",{attrs:{color:"pink"}},[i("span",[t._v("顾客："+t._s(t.keyWord))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("keyWord")}}})],1):t._e(),t.customName?i("a-tag",{attrs:{color:"red"}},[i("span",[t._v("导购："+t._s(t.customName))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("customName")}}})],1):t._e(),t.gradeVal>-1?i("a-tag",{attrs:{color:"orange"}},[i("span",[t._v("等级："+t._s(t.gradeArr[t.gradeVal].title))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("gradeVal")}}})],1):t._e(),t.rfmVal>-1?i("a-tag",{attrs:{color:"green"}},[i("span",[t._v("RFM评级："+t._s(t.rfmArr[t.rfmVal].default_name))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("rfmVal")}}})],1):t._e(),t.joinTime?i("a-tag",{attrs:{color:"cyan"}},[i("span",[t._v("添加时间："+t._s(t.moment(this.joinTime[0]).format("YYYY-MM-DD"))+"至"+t._s(t.moment(this.joinTime[1]).format("YYYY-MM-DD")))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("joinTime")}}})],1):t._e(),t.moneyTime?i("a-tag",{attrs:{color:"blue"}},[i("span",[t._v("最后消费："+t._s(t.moment(this.moneyTime[0]).format("YYYY-MM-DD"))+"至"+t._s(t.moment(this.moneyTime[1]).format("YYYY-MM-DD")))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("moneyTime")}}})],1):t._e(),t.interaTime?i("a-tag",{attrs:{color:"purple"}},[i("span",[t._v("最后互动："+t._s(t.moment(this.interaTime[0]).format("YYYY-MM-DD"))+"至"+t._s(t.moment(this.interaTime[1]).format("YYYY-MM-DD")))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("interaTime")}}})],1):t._e(),t.moneyDownLimit||t.moneyUpLimit?i("a-tag",{attrs:{color:"red"}},[i("span",[t._v("消费区间："+t._s(t.moneyDownLimit)),t.moneyDownLimit&&t.moneyUpLimit?i("span",[t._v("至")]):t._e(),t._v(t._s(t.moneyUpLimit))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("moneyLimit")}}})],1):t._e(),t.rateDownLimit||t.rateUpLimit?i("a-tag",{attrs:{color:"green"}},[i("span",[t._v("互动区间："+t._s(t.rateDownLimit)),t.rateDownLimit&&t.rateUpLimit?i("span",[t._v("至")]):t._e(),t._v(t._s(t.rateUpLimit))]),i("a-icon",{attrs:{type:"close-circle"},on:{click:function(e){return t.deleteTag("rateLimit")}}})],1):t._e()],1),i("div",{staticClass:"table-num"},[t._v("共"),i("span",[t._v(t._s(t.total))]),t._v("位顾客")])]),i("div",{staticClass:"content-bd"},[i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[i("a-table",{directives:[{name:"has",rawName:"v-has",value:"shopCustomManage-list",expression:"'shopCustomManage-list'"}],attrs:{rowSelection:t.rowSelection,columns:t.columns,dataSource:t.customList,pagination:!1},scopedSlots:t._u([{key:"name",fn:function(e){return i("div",{},[t._v(t._s(e))])}},{key:"type",fn:function(e){return i("div",{},[t._v(t._s(e))])}},{key:"add_time",fn:function(e){return i("div",{},[t._v(t._s(e))])}},{key:"guide",fn:function(e,a){return i("div",{},[i("span",[0==e.length?i("span",[t._v("--")]):1==e.length?i("span",[t._v(t._s(e[0].guide_name))]):i("span",[t._v("更多导购")]),i("a-icon",{directives:[{name:"has",rawName:"v-has",value:"shopCustomManage-set",expression:"'shopCustomManage-set'"}],staticStyle:{"margin-left":"5px",cursor:"pointer"},attrs:{type:"form"},on:{click:function(e){return t.setGuide("list",a)}}})],1)])}},{key:"amount",fn:function(e,a){return i("div",{},[i("router-link",{attrs:{to:"/shopCustom/CustomOrder?cus_id="+a.id}},[t._v(t._s(e))])],1)}},{key:"level_name",fn:function(e,a){return i("div",{style:{color:a.level_color}},[t._v(t._s(e))])}},{key:"rfm_name",fn:function(e,a){return i("div",{},[i("a-button",{on:{click:function(i){"暂无"!=e&&t.lookRfm(a.id,e)}}},[t._v(t._s(e))])],1)}}])},[i("div",{attrs:{slot:"amount2"},slot:"amount2"},[t._v("\n\t\t\t\t\t\t\t\t\t消费额\n\t\t\t\t\t\t\t\t\t"),i("a-tooltip",{attrs:{placement:"bottom"}},[i("template",{slot:"title"},[i("div",[t._v("指的是这个顾客全渠道的订单合计金额，无需有关联导购")])]),i("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1)]),i("div",{staticStyle:{padding:"0 15px"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticStyle:{margin:"20px 0px","line-height":"32px"}},[i("a-checkbox",{on:{click:t.batchTypeChange},model:{value:t.batchTypeValue,callback:function(e){t.batchTypeValue=e},expression:"batchTypeValue"}}),t._v("\n\t\t\t\t\t\t\t\t\t当前页\n\t\t\t\t\t\t\t\t\t"),i("a-button",{directives:[{name:"has",rawName:"v-has",value:"shopCustomManage-grade",expression:"'shopCustomManage-grade'"}],staticStyle:{"margin-right":"5px"},attrs:{type:"primary",disabled:!(this.selectedRowKeys.length>0)},on:{click:function(e){return t.setGuide("setGarde")}}},[t._v("等级修改")]),i("a-button",{directives:[{name:"has",rawName:"v-has",value:"shopCustomManage-set",expression:"'shopCustomManage-set'"}],staticStyle:{"margin-right":"5px"},attrs:{type:"primary",disabled:!(this.selectedRowKeys.length>0)},on:{click:function(e){return t.setGuide("all")}}},[t._v("设置导购\n\t\t\t\t\t\t\t\t\t")])],1),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"shopCustomManage-list",expression:"'shopCustomManage-list'"}],staticClass:"pagination",staticStyle:{margin:"20px 0px",overflow:"hidden"}},[i("div",{staticClass:"pagination",staticStyle:{display:"inline-block",height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])])],1)],1)]),i("a-drawer",{attrs:{title:"RFM评级",placement:"right",destroyOnClose:!0,closable:!0,visible:t.rfmVisible},on:{close:t.rfmClose}},[i("div",[i("div",{staticClass:"frm-item"},[i("span",[t._v("当前评级")]),i("span",{staticClass:"frm-title"},[t._v("\n\t\t\t\t\t\t\t\t"+t._s(t.rfmName)+"\n\t\t\t\t\t\t\t\t"),i("a-tooltip",{attrs:{placement:"right"}},[i("template",{slot:"title"},[i("span",[t._v("是截止昨天的数据最新的评级，下面是三个维度的是最新指数")])]),i("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"question-circle"}})],2)],1)]),i("div",[t.hasCorp?i("RadarCharts",{attrs:{options:t.rfmRadar}}):t._e(),i("a-empty",{directives:[{name:"show",rawName:"v-show",value:!t.hasCorp,expression:"!hasCorp"}],staticStyle:{"margin-top":"50px"}},[i("span",{staticStyle:{color:"#999"},attrs:{slot:"description"},slot:"description"},[t._v("暂无数据")])])],1),t.rfmLog.length>0?i("div",{staticClass:"frm-item"},[i("div",{staticClass:"frm-title2"},[t._v("变化情况")]),i("a-timeline",{staticStyle:{"margin-top":"30px"}},t._l(t.rfmLog,(function(e,a){return i("a-timeline-item",{key:a},[t._v(t._s(e.rfm_name)+" "+t._s(e.add_time))])})),1),t.rfmTotal>t.rfmPage?i("a-button",{staticStyle:{"margin-bottom":"20px"},on:{click:t.lookRfmLog}},[t._v("点击加载更多")]):t._e()],1):t._e()]),i("div",{staticStyle:{height:"117px"}},[i("div",{style:{position:"absolute",right:0,bottom:"64px",width:"100%",borderTop:"1px solid #e9e9e9",padding:"10px 16px",background:"#fff",textAlign:"right",zIndex:1}},[i("a-button",{attrs:{type:"primary"},on:{click:t.rfmClose}},[t._v("关闭")])],1)])]),i("a-modal",{staticClass:"guide-modal",attrs:{width:"600px",title:t.guideTitle},model:{value:t.bindGuideVisible,callback:function(e){t.bindGuideVisible=e},expression:"bindGuideVisible"}},[i("template",{slot:"footer"},[i("a-button",{key:"back",on:{click:t.handleCancelBind}},[t._v("取消")]),i("a-button",{key:"submit",attrs:{type:"primary",disabled:!1,loading:t.loading},on:{click:function(e){return t.handleBind(t.guideType)}}},[t._v("确定绑定")])],1),"all"==t.guideType?i("div",{staticClass:"content-msg"},[t._v("当前是批量设置导购，请谨慎选择")]):t._e(),"setGarde"==t.guideType?i("div",{staticClass:"content-msg"},[t._v("当前是批量修改等级，请谨慎选择")]):t._e(),i("div",{staticClass:"guide-user"},[i("label",[t._v("顾客：")]),("all"==t.guideType||"setGarde"==t.guideType)&&t.guideMes1.length>0?i("span",[t._v(t._s(t.guideMes1[0].name)),t.guideMes1.length>1?i("span",[t._v("等"+t._s(t.guideMes1.length)+"人")]):t._e()]):t._e(),"list"==t.guideType?i("span",[t._v(t._s(t.guideMes.name))]):t._e()]),"list"==t.guideType&&t.guideMes.guide&&t.guideMes.guide.length>0?i("div",{staticClass:"guide-list"},[i("label",[t._v("现有导购：")]),i("div",{staticClass:"guide-item"},t._l(t.guideMes.guide,(function(e,a){return i("a-tag",{key:a,attrs:{color:"blue"}},[i("span",[t._v(t._s(e.guide_name))]),i("a-icon",{staticStyle:{"margin-left":"5px"},attrs:{type:"close-circle"},on:{click:function(e){return t.deleteGuide(a)}}})],1)})),1)]):t._e(),"setGarde"!=t.guideType?i("div",{staticClass:"guide-select"},[i("label",[i("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("选择导购：")]),i("chooseStaffSelect",{ref:"staff",staticStyle:{flex:"1"},attrs:{index:0,index2:0,type:2,callback:t.selectStaffCallback}})],1):t._e(),"setGarde"==t.guideType?i("div",{staticClass:"guide-select"},[i("label",[i("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("选择等级：")]),t.gradeArr.length>0?[i("a-radio-group",{attrs:{name:"radioGroup","default-value":t.gradeArr[0].id},model:{value:t.levelId,callback:function(e){t.levelId=e},expression:"levelId"}},t._l(t.gradeArr,(function(e,a){return i("a-radio",{key:a,attrs:{value:e.id}},[t._v(t._s(e.title))])})),1)]:[t._v("暂无等级可选")]],2):t._e()],2)],1)],1)],1)],1)},s=[],n=(i("6b54"),i("75fc")),o=(i("6762"),i("2fdb"),i("96cf"),i("3b8d")),r=i("c1df"),l=i.n(r),d=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-chart",{ref:"radarCharts",attrs:{options:t.option}})},c=[],m=(i("7f7f"),i("9ca8")),u=(i("627c"),i("d28f"),i("007d"),i("b11c"),i("2f73"),i("8deb"),{name:"RadarCharts",props:{options:Object},watch:{options:{handler:function(t,e){this.options&&(t?(this.option=this.getOptions(t),this.$refs.radarCharts.resize()):(this.option=this.getOptions(e),this.$refs.radarCharts.resize()))},deep:!0}},components:{"v-chart":m["a"]},data:function(){return{option:{title:{text:"多雷达图",left:"left",show:!1,textStyle:{fontFamily:"PingFangSC-Medium",color:"#1A1A1A",fontSize:"18px"}},toolbox:{show:!1,feature:{saveAsImage:{}}},color:["#ee6666"],tooltip:{trigger:"axis",axisPointer:{type:"shadow"}},legend:{left:"center",data:["某软件"],show:!1},radar:[{indicator:[{text:"额度",max:100},{text:"进度",max:100},{text:"频率",max:100}],center:["35%","50%"],radius:120}],series:[{type:"radar",tooltip:{trigger:"item"},areaStyle:{},data:[{value:[30,73,150],name:"某软件"}]}]}}},mounted:function(){this.option.radar[0].indicator=this.options.indicator,this.option.series[0].data[0].value=this.options.seriesData,this.option.series[0].data[0].name=this.options.name,this.$refs.radarCharts.resize(),window.addEventListener("resize",this.resizeHandle)},beforeDestroy:function(){window.removeEventListener("resize",this.resizeHandle)},methods:{resizeHandle:function(){this.$refs.radarCharts.resize()}}}),p=u,h=i("2877"),g=Object(h["a"])(p,d,c,!1,null,null,null),f=g.exports,v=i("95204"),y=[{title:"客户信息",dataIndex:"name",scopedSlots:{customRender:"name"},key:"name",ellipsis:!0},{title:"类型",dataIndex:"type",key:"type",scopedSlots:{customRender:"type"}},{title:"入库时间",dataIndex:"add_time",key:"add_time",scopedSlots:{customRender:"add_time"}},{title:"导购",dataIndex:"guide",key:"guide",scopedSlots:{customRender:"guide"}},{dataIndex:"amount",key:"amount",slots:{title:"amount2"},scopedSlots:{customRender:"amount"}},{title:"客户等级",dataIndex:"level_name",key:"level_name",width:90,scopedSlots:{customRender:"level_name"}},{title:"RFM评级",dataIndex:"rfm_name",key:"rfm_name",width:140,scopedSlots:{customRender:"rfm_name"}}],_={name:"shopCustomManage",components:{RadarCharts:f,chooseStaffSelect:v["a"]},data:function(){return{moment:l.a,keyWord:"",customName:"",gradeArr:[],gradeVal:-1,rfmArr:["等级筛选","重要价值","重要发展","重要保持","已经流失"],rfmVal:-1,joinTime:null,moneyTime:null,interaTime:null,moneyUpLimit:"",moneyDownLimit:"",rateUpLimit:"",rateDownLimit:"",isLoading:!0,columns:y,customList:[],page:1,pageSize:15,total:1,batchTypeValue:!1,selectedRowKeys:[],quickJumper:!1,checkArr:[],rfmVisible:!1,rfmName:"",hasCorp:!1,rfmRadar:{indicator:[],seriesData:[],name:"RFM评级"},rfmLog:[],rfmPage:1,rfmTotal:0,customerId:"",guide_id:"",store_id:"",guideArr:["导购一","导购二","导购三","导购四","导购五"],bindGuideVisible:!1,loading:!1,userName:"",guideName:[],guideType:"list",guideTitle:"",guideMes:{},guideId:[],guideId2:[],storeId:[],storeId2:[],customerId2:[],levelId:"",guideMes1:{}}},methods:{disabledDate:function(t){return t&&t>l()().endOf("day")},changeTime:function(t,e){var i=this;"joinTime"==e?i.joinTime=t:"moneyTime"==e?i.moneyTime=t:"interaTime"==e&&(i.interaTime=t)},searchStaff:function(){this.getCustomList()},reset:function(){var t=this;t.keyWord="",t.customName="",t.gradeVal=-1,t.rfmVal=-1,t.joinTime=null,t.moneyTime=null,t.interaTime=null,t.moneyUpLimit="",t.moneyDownLimit="",t.rateUpLimit="",t.rateDownLimit="",t.guide_id="",t.store_id="",this.getCustomList()},deleteTag:function(t){var e=this;"keyWord"==t?e.keyWord="":"customName"==t?e.customName="":"gradeVal"==t?e.gradeVal=-1:"rfmVal"==t?e.rfmVal=-1:"joinTime"==t?e.joinTime=null:"moneyTime"==t?e.moneyTime=null:"interaTime"==t?e.interaTime=null:"moneyLimit"==t?(e.moneyUpLimit="",e.moneyDownLimit=""):"rateLimit"==t&&(e.rateUpLimit="",e.rateDownLimit="")},rfmLevel:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.post("shop-customer/level-rfm",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):""});case 3:e=t.sent,i=e.data,0!=i.error?(this.isLoading=!1,this.$message.error(i.error_msg)):(this.isLoading=!1,this.gradeArr=i.data.level,this.rfmArr=i.data.rfm);case 6:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),getCustomList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i,a,s,n=this,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,i=o.length>1&&void 0!==o[1]?o[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("shop-customer/customer-list",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",page:e,page_size:i,cus_keyword:this.keyWord,guide_keyword:this.customName,level_id:this.gradeVal>-1?this.gradeArr[this.gradeVal].id:"",rfm_id:this.rfmVal>-1?this.rfmArr[this.rfmVal].id:"",add_time_start:this.joinTime&&this.joinTime.length>1?this.joinTime[0].format("YYYY-MM-DD HH:mm"):"",add_time_end:this.joinTime&&this.joinTime.length>1?this.joinTime[1].format("YYYY-MM-DD HH:mm"):"",last_consumption_time_start:this.moneyTime&&this.moneyTime.length>1?this.moneyTime[0].format("YYYY-MM-DD HH:mm"):"",last_consumption_time_end:this.moneyTime&&this.moneyTime.length>1?this.moneyTime[1].format("YYYY-MM-DD HH:mm"):"",last_interactive_time_start:this.interaTime&&this.interaTime.length>1?this.interaTime[0].format("YYYY-MM-DD HH:mm"):"",last_interactive_time_end:this.interaTime&&this.interaTime.length>1?this.interaTime[1].format("YYYY-MM-DD HH:mm"):"",amount_min:this.moneyDownLimit,amount_max:this.moneyUpLimit,interactive_count_min:this.rateDownLimit,interactive_count_max:this.rateUpLimit,guide_id:this.guide_id,store_id:this.store_id});case 5:a=t.sent,s=a.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.error_msg)):(this.isLoading=!1,this.customList=s.data.customer,this.total=parseInt(s.data.count),this.page=e,this.pageSize=i,this.quickJumper=this.total>this.pageSize,this.checkArr=[],this.customList.map((function(t){n.checkArr.push(t.key)})),this.batchTypeValue=!1,this.selectedRowKeys=[]);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),onSelectChange:function(t,e){this.selectedRowKeys=t,this.batchTypeValue=this.checkArr.every((function(e){return t.includes(e)})),this.guideMes1=e},batchTypeChange:function(){this.batchTypeValue?(this.selectedRowKeys=[],this.guideMes1=[]):(this.selectedRowKeys=this.checkArr,this.guideMes1=this.customList)},changePage:function(t,e){this.getCustomList(t,e),this.$nextTick((function(){document.getElementsByClassName("scroll")[0].scrollTo(0,40)}))},showSizeChange:function(t,e){this.getCustomList(1,e)},lookRfm:function(t,e){this.customerId=t,this.rfmPage=1,this.rfmName=e,this.rfmRadarFun(t)},rfmClose:function(){this.rfmName="",this.rfmVisible=!1},rfmRadarFun:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e){var i,a,s,o,r=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return i=r.length>1&&void 0!==r[1]?r[1]:1,a=r.length>2&&void 0!==r[2]?r[2]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("shop-customer/sidebar-msg",{page:i,page_size:a,corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",customer_id:e});case 5:s=t.sent,o=s.data,0!=o.error?(this.isLoading=!1,this.$message.error(o.error_msg)):(this.isLoading=!1,1==i?(this.rfmVisible=!0,this.hasCorp=!0,this.rfmRadar.indicator[0]={text:"额度",max:o.data.rfm.monetary_value},this.rfmRadar.indicator[1]={text:"近度",max:o.data.rfm.recency_value},this.rfmRadar.indicator[2]={text:"频率",max:o.data.rfm.frequency_value},this.rfmRadar.seriesData[0]=o.data.rfm.monetary,this.rfmRadar.seriesData[1]=o.data.rfm.recency,this.rfmRadar.seriesData[2]=o.data.rfm.frequency,this.rfmLog=o.data.rfm_log,this.rfmTotal=Math.ceil(o.data.count/a)):this.rfmLog=[].concat(Object(n["a"])(this.rfmLog),Object(n["a"])(o.data.rfm_log)));case 8:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),lookRfmLog:function(){this.rfmRadarFun(this.customerId,++this.rfmPage,this.pageSize)},setGuide:function(t,e){var i=this;i.guideType=t,i.bindGuideVisible=!0,i.guideName=[],i.guideId=[],i.guideId2=[],i.storeId=[],i.storeId2=[],i.customerId2=[],"setGarde"==t?(i.guideTitle="修改等级",i.customerId2=i.selectedRowKeys):(i.guideTitle="设置导购",i.guideMes=e,"list"==t?(i.customerId2=[e.id],i.guideMes.guide.length>0&&i.guideMes.guide.map((function(t){i.guideId2.push(1*t.guide_id),i.guideName.push(t.guide_name),i.storeId2.push(1*t.store_id)}))):"all"==t&&(i.customerId2=i.selectedRowKeys))},handleCancelBind:function(){var t=this;t.bindGuideVisible=!1,t.loading=!1,t.$refs.staff&&(t.$refs.staff.userId=[])},handleBind:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e){var i,a,s,o,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(i=this,"setGarde"!=e){t.next=5;break}console.log("设置等级"),t.next=11;break;case 5:if(0!=i.guideId.length||"all"!=e){t.next=8;break}return i.$message.error("请先选择导购"),t.abrupt("return",!1);case 8:i.guideId=[].concat(Object(n["a"])(i.guideId2),Object(n["a"])(i.guideId)),i.guideName=i.guideName.concat(i.userName),i.storeId=i.storeId2.concat(i.storeId);case 11:return i.loading=!0,a={corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):""},"setGarde"==e?(a.level_id=i.levelId,a.customer_ids=i.customerId2.toString(),s="shop-customer/change-level"):(a.guide_id=i.guideId.toString(),a.customer_id=i.customerId2.toString(),a.store_guide_name=i.guideName.toString(),a.store_id=i.storeId.toString(),s="shop-customer/save-guide-msg"),t.next=16,i.axios.post(s,a);case 16:o=t.sent,r=o.data,0!=r.error?(i.loading=!1,i.$message.error(r.error_msg)):(i.loading=!1,i.$refs.staff&&(i.$refs.staff.userId=[]),i.handleCancelBind(),i.getCustomList());case 19:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),selectStaffCallback:function(t,e,i,a,s){var n=this;"ok"==t&&(this.guideId=[e],this.storeId=s.store_id,this.$nextTick((function(){n.userName=n.$refs.staff.$el.innerText})))},deleteGuide:function(t){var e=this;e.guideMes.guide.splice(t,1),e.guideId2.splice(t,1),e.guideName.splice(t,1),e.storeId2.splice(t,1)}},computed:{rowSelection:function(){var t=this.selectedRowKeys,e=this;return{selectedRowKeys:t,onChange:this.onSelectChange,hideDefaultSelections:!0,onSelection:e.onSelection}}},created:function(){},mounted:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.$route.query.guide_id&&(t.guide_id=t.$route.query.guide_id,t.store_id=t.$route.query.store_id),t.rfmLevel(),t.getCustomList()}))}},b=_,x=(i("fd9c"),Object(h["a"])(b,a,s,!1,null,"75a137b0",null));e["default"]=x.exports},95204:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("a-select",{staticStyle:{width:"100%"},attrs:{showSearch:"",optionFilterProp:"children",filterOption:!1,placeholder:2==t.type?"请选择导购":"请选择企业成员",disabled:t.disabled},on:{focus:t.focusSelect,select:t.changeSelect,popupScroll:t.popScroll,search:t.searchName},scopedSlots:t._u([{key:"dropdownRender",fn:function(e){return i("div",{},[i("v-nodes",{attrs:{vnodes:e}}),i("a-spin",{staticStyle:{position:"absolute",bottom:"50%",left:"0",right:"0"},attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}})],1)}}]),model:{value:t.userId,callback:function(e){t.userId=e},expression:"userId"}},t._l(t.userList,(function(e,a){return i("a-select-option",{key:2==t.type?a:e.id},[t._v("\n\t\t\t"+t._s(e.name)),2==t.type&&e.group_name?i("span",[t._v("("+t._s(e.group_name)+")")]):t._e()])})),1)],1)},s=[],n=(i("96cf"),i("3b8d")),o=(i("c5f6"),{props:{callback:{type:Function,default:null},index:{type:Number,default:-1},index2:{type:Number,default:-1},disabled:{type:Boolean,default:!1},getFisrstStaff:{type:Boolean,default:!1},type:{type:Number,default:0},ignore_dialout:{type:Number,default:0}},components:{VNodes:{functional:!0,render:function(t,e){return e.props.vnodes}}},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpId:t,userList:[],userId:[],userName:"",page:1,count:0,isLoading:!1}},created:function(){var t=this;this.$nextTick((function(){t.getAllStaffList()}))},mounted:function(){},methods:{getAllStaffList:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var e,i,a,s,n,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,this.isLoading=!0,2==this.type?(i={corp_id:this.corpId,guide_keyword:this.userName,operator:0,page:e,page_size:10},a="shop-customer-guide/list"):(i={corp_id:this.corpId,name:this.userName,user_id:this.userId,is_all:0,page:e,ignore_dialout:this.ignore_dialout},1==this.type&&(i.sub_id=localStorage.getItem("sub_id"),i.isMasterAccount=localStorage.getItem("isMasterAccount")),a="work-user/get-all-user"),t.next=5,this.axios.post(a,i);case 5:s=t.sent,n=s.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):(this.isLoading=!1,this.count=n.data.count,this.getFisrstStaff&&(this.userId=n.data.info[0].id,null!==this.callback&&"function"===typeof this.callback&&(-1===this.index&&-1===this.index2?this.callback("ok",n.data.info[0].id):this.callback("ok",n.data.info[0].id,this.index,this.index2,n.data.info[0]))),1==e?2==this.type?this.userList=n.data.result:this.userList=n.data.info:2==this.type?this.userList=this.userList.concat(n.data.result):this.userList=this.userList.concat(n.data.info),this.page=e);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changeSelect:function(t){null!==this.callback&&"function"===typeof this.callback&&(2==this.type?-1===this.index&&-1===this.index2?this.callback("ok",this.userList[t].id):this.callback("ok",this.userList[t].id,this.index,this.index2,this.userList[t]):-1===this.index&&-1===this.index2?this.callback("ok",t):this.callback("ok",t,this.index,this.index2))},focusSelect:function(){""!=this.userName&&(this.page=1,this.userList=[],this.userName="",this.getAllStaffList())},searchName:function(t){this.userName=t,this.getAllStaffList()},popScroll:function(t){var e=t.target;e.scrollTop+e.offsetHeight===e.scrollHeight&&this.userList.length<this.count&&(this.page++,this.getAllStaffList(this.page))}},watch:{userId:{handler:function(t){this.getAllStaffList()},deep:!0},getFisrstStaff:{handler:function(t){this.getAllStaffList()},deep:!0}}}),r=o,l=i("2877"),d=Object(l["a"])(r,a,s,!1,null,"2fbfc039",null);e["a"]=d.exports},ac06:function(t,e,i){var a=i("050e");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var s=i("499e").default;s("ac2d22de",a,!0,{sourceMap:!1,shadowMode:!1})},fd9c:function(t,e,i){"use strict";var a=i("ac06"),s=i.n(a);s.a}}]);