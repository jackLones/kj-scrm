(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-538a4dba"],{"0827":function(t,e,a){"use strict";var i=a("0efc"),s=a.n(i);s.a},"0efc":function(t,e,a){var i=a("b3dc4");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=a("499e").default;s("32b841b2",i,!0,{sourceMap:!1,shadowMode:!1})},"4bef":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAABAElEQVR42s3VIY7CUBAG4LkEliC4xKJ6BA6AognXQBLCAVBYNBK3pim7zP8XBAJHSLhAZVURi+jC2+681xD4R89nZiYjIhojQRlQicYiolFQ8600EqZNAKbSpB0lyqcCBeYccIA5ihDgtO3JLdseTr7Ap9wFRx+g2HTuge8PH+AsjuBiBzZO4MsOHJzAwQ7kTiD3mcLsoX3muQcaV9s1DtnENUfssssR1i+6hRcD3GPFBaeccoEV9z7AUodoP4yxrUMs/wd26EtN0MeuBuBYDOH4LyDPWhYga1WXugpMxBhMnIBGVqD6S34B9nqvTUwaAUnNpZue689/DnuwTDUSuQJ1pL7YQ6lKkQAAAABJRU5ErkJggg=="},"4ed2":function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"list"},[i("div",{attrs:{id:"components-layout-demo-basic"}},[i("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[i("a-layout",{staticClass:"fans-content scroll",staticStyle:{position:"absolute",left:"0",top:"0",bottom:"0",right:"0","overflow-x":"hidden","overflow-y":"auto"}},[i("a-layout-header",[t._v("离职继承")]),i("a-layout-content",[i("div",{staticClass:"content-msg"},[t._v("\n\t\t\t\t\t\t从通讯录将离职员工删除后，可以分配他的客户及客户群给其他员工继续跟进，"),i("span",{staticStyle:{color:"#FF562D"}},[t._v("且客户及客户群信息也一并给接替的员工。")])]),i("div",{staticClass:"content-hd"},[i("a-col",{staticStyle:{float:"left"}},[i("a-select",{staticStyle:{width:"210px","margin-right":"5px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleChangeCorp},model:{value:t.corpId,callback:function(e){t.corpId=e},expression:"corpId"}},[t._l(t.corpInfo,(function(e){return[i("a-select-option",{attrs:{value:e.corpid}},[t._v(t._s(e.corp_full_name||e.corp_name)+"\n\t\t\t\t\t\t\t\t\t")])]}))],2),i("a-button",{staticStyle:{width:"210px","margin-right":"5px"},on:{click:function(e){return t.showDepartmentList(1)}}},[t.chooseNum>0?i("span",[t._v("已选择"+t._s(t.chooseUserNum)+"名成员，"+t._s(t.chooseDepartmentNum)+"个部门")]):i("span",[t._v("选择成员")])]),i("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00","HH:mm"),t.moment("23:59","HH:mm")],format:"HH:mm"},format:"YYYY-MM-DD HH:mm",allowClear:"","disabled-date":t.disabledDate},on:{change:t.changeTime},model:{value:t.leaveTime,callback:function(e){t.leaveTime=e},expression:"leaveTime"}}),i("a-button",{staticStyle:{"margin-left":"10px"},attrs:{type:"primary"},on:{click:t.selectTitle}},[t._v("查找")]),i("a-button",{staticStyle:{"margin-left":"10px"},on:{click:t.clearTitle}},[t._v("清空")])],1)],1),i("div",{staticClass:"content-bd"},[i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[i("a-table",{directives:[{name:"has",rawName:"v-has",value:"staffDimission-list",expression:"'staffDimission-list'"}],attrs:{columns:t.columns,dataSource:t.dimissionList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"name2",fn:function(e,s,o){return i("span",{},[s.avatar?i("a-avatar",{staticStyle:{float:"left",width:"42px",height:"42px"},attrs:{shape:"square",src:s.avatar}}):t._e(),s.avatar?t._e():i("img",{staticStyle:{width:"42px",height:"42px",float:"left"},attrs:{src:a("4bef")}}),i("div",{staticStyle:{float:"left","max-width":"270px","word-wrap":"break-word",height:"21px"}},[i("a-popover",{attrs:{placement:"top"}},[i("span",{attrs:{slot:"content"},slot:"content"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(s.name)+"\n\t\t\t\t\t\t\t\t\t\t\t\t")]),i("div",{staticStyle:{display:"inline-block","margin-left":"10px","max-width":"140px",overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis","vertical-align":"-webkit-baseline-middle"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(s.name)+"\n\t\t\t\t\t\t\t\t\t\t\t\t")])])],1),i("span",{staticStyle:{"vertical-align":"-webkit-baseline-middle"}},[1==s.gender?i("a-icon",{staticStyle:{"margin-left":"10px",color:"#427EBA"},attrs:{slot:"prefix",type:"man"},slot:"prefix"}):t._e(),2==s.gender?i("a-icon",{staticStyle:{"margin-left":"10px",color:"#ED4997"},attrs:{slot:"prefix",type:"woman"},slot:"prefix"}):t._e()],1)],1)}},{key:"action",fn:function(e,a){return i("span",{},[i("a-button",{directives:[{name:"has",rawName:"v-has",value:"staffDimission-detail",expression:"'staffDimission-detail'"}],on:{click:function(e){return t.detailList(a.id,a.name,a.corp_id)}}},[t._v("明细")])],1)}}])}),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"},{name:"has",rawName:"v-has",value:"staffDimission-list",expression:"'staffDimission-list'"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[i("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t\t"),i("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total))]),t._v("条\n\t\t\t\t\t\t\t\t")]),i("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])],1)],1)])],1)],1)],1),i("a-drawer",{attrs:{placement:"right",closable:!1,visible:t.detailVisible,width:"1000px!important"},on:{close:t.detailDrawerClose}},[i("template",{slot:"title"},[t._v("\n\t\t\t"+t._s(t.drawerTitle)+"\n\t\t")]),i("div",{staticStyle:{padding:"20px"}},[i("a-tabs",{staticStyle:{margin:"0 0 20px"},attrs:{type:"card"},on:{change:t.changeTabKey},model:{value:t.tabKey,callback:function(e){t.tabKey=e},expression:"tabKey"}},[i("a-tab-pane",{key:1,attrs:{tab:"分配客户"}},[i("div",{staticStyle:{margin:"0 0 20px 0"}},[i("div",{staticStyle:{"background-color":"#FFFFFF"}},[i("div",{staticStyle:{height:"32px","line-height":"32px","margin-bottom":"10px",color:"#000"}},[t._v("\n\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t"),i("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total2))]),t._v("个客户\n\t\t\t\t\t\t\t")]),i("span",{staticClass:"select-option"},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("搜索客户：")]),i("a-input",{staticStyle:{"margin-right":"5px",width:"210px"},attrs:{placeholder:"请输入要搜索的客户"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.getCustomerList(1,t.pageSize2)}},model:{value:t.customName,callback:function(e){t.customName=e},expression:"customName"}})],1),i("span",{staticClass:"select-option",staticStyle:{"margin-left":"15px"}},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("分配成员：")]),i("a-button",{staticStyle:{width:"210px","margin-right":"5px"},on:{click:function(e){return t.showDepartmentList(2)}}},[t.chooseNum2>0?i("span",[t._v("已选择"+t._s(t.chooseUserNum2)+"名成员，"+t._s(t.chooseDepartmentNum2)+"个部门")]):i("span",[t._v("选择成员")])])],1),i("span",{staticClass:"select-option",staticStyle:{"margin-left":"15px"}},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("客户状态：")]),i("a-select",{staticStyle:{width:"210px","margin-right":"5px"},attrs:{showSearch:"",optionFilterProp:"children",placeholder:"请选择分配状态"},model:{value:t.customStatus,callback:function(e){t.customStatus=e},expression:"customStatus"}},[i("a-select-option",{attrs:{value:-1}},[t._v("全部")]),i("a-select-option",{attrs:{value:0}},[t._v("未分配")]),i("a-select-option",{attrs:{value:1}},[t._v("已分配")]),i("a-select-option",{attrs:{value:2}},[t._v("客户拒绝")]),i("a-select-option",{attrs:{value:3}},[t._v("接替成员客户达到上限")]),i("a-select-option",{attrs:{value:4}},[t._v("分配中")]),i("a-select-option",{attrs:{value:5}},[t._v("未知")])],1)],1),i("span",{staticClass:"select-option"},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("分配时间：")]),i("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00","HH:mm"),t.moment("23:59","HH:mm")],format:"HH:mm"},format:"YYYY-MM-DD HH:mm",allowClear:"","disabled-date":t.disabledDate},on:{change:t.changeTime2},model:{value:t.leaveTime2,callback:function(e){t.leaveTime2=e},expression:"leaveTime2"}})],1),i("a-button",{staticStyle:{"margin-right":"5px","margin-left":"15px"},attrs:{type:"primary"},on:{click:t.searchRecord}},[t._v("查找\n\t\t\t\t\t\t\t")]),i("a-button",{staticStyle:{"margin-right":"10px"},on:{click:t.resetRecord}},[t._v("清空")])],1)]),i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading2}},[i("a-table",{attrs:{columns:t.columns2,dataSource:t.customerList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"customerInfo",fn:function(e,s,o){return i("div",{},[i("div",{staticStyle:{"max-width":"400px",overflow:"hidden"}},[s.avatar?i("a-avatar",{staticStyle:{float:"left"},attrs:{shape:"square",size:42,src:s.avatar}}):t._e(),s.avatar?t._e():i("img",{staticStyle:{width:"42px",height:"42px",float:"left"},attrs:{src:a("4bef")}}),i("div",{staticStyle:{float:"left","margin-left":"10px",width:"calc(100% - 52px)"}},[i("div",[i("a-popover",{attrs:{placement:"top"}},[i("template",{slot:"content"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(s.name)+"\n\t\t\t\t\t\t\t\t\t\t\t\t\t"),i("span",{class:null!=s.corp_name?"corp-name":"wx-name"},[null!=s.corp_name?[t._v("@"+t._s(s.corp_name))]:[t._v("@微信")]],2)]),i("span",[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(s.name)+"\n\t\t\t\t\t\t\t\t\t\t\t\t\t"),i("span",{class:null!=s.corp_name?"corp-name":"wx-name"},[null!=s.corp_name?[t._v("@"+t._s(s.corp_name))]:[t._v("@微信")]],2)])],2),"男性"==s.gender?i("a-icon",{staticStyle:{color:"#427EBA"},attrs:{type:"man"}}):t._e(),"女性"==s.gender?i("a-icon",{staticStyle:{color:"#ED4997"},attrs:{type:"woman"}}):t._e()],1),i("div",[0==s.chat_name.length?i("div",{staticStyle:{width:"80px",color:"#999","font-size":"12px"}},[t._v("所在群：0个\n\t\t\t\t\t\t\t\t\t\t\t")]):t._e(),i("a-popover",{attrs:{placement:"left"}},[i("template",{slot:"content"},[i("div",{staticStyle:{"max-height":"500px","overflow-y":"auto"}},t._l(s.chat_name,(function(e,a){return i("div",{staticStyle:{"max-width":"500px",display:"block","margin-bottom":"5px"}},[i("span",{staticStyle:{display:"inline-block","font-weight":"700",width:"80px","text-align":"right","vertical-align":"top"}},[t._v("群"+t._s(a+1)+"：")]),i("span",{staticStyle:{width:"calc(100% - 110px)",display:"inline-block","margin-bottom":"3px","word-break":"break-all"}},[t._v(t._s(e.name)+"（"+t._s(e.join_time)+"）")])])})),0)]),s.chat_name.length>0?i("span",{staticStyle:{cursor:"pointer",width:"80px",color:"#999","font-size":"12px"}},[t._v("所在群："+t._s(s.chat_name.length)+"个\n\t\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],2)],1),s.wx_name&&""!=s.wx_name?i("div",{staticStyle:{color:"#999","font-size":"12px"}},[t._v("公众号："+t._s(s.wx_name)+"\n\t\t\t\t\t\t\t\t\t\t")]):t._e()])],1)])}},{key:"user_name",fn:function(e,a,s){return i("div",{},[""==a.user_name?i("span",[t._v("--")]):i("a-tag",{attrs:{color:"orange"}},[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(a.user_name)+"\n\t\t\t\t\t\t\t\t")])],1)}}])}),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total2>0,expression:"total2 > 0"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[i("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total2,showSizeChanger:"",showQuickJumper:t.quickJumper2,current:t.page2,pageSize:t.pageSize2,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage2,showSizeChange:t.showSizeChange2}})],1)])],1)],1),i("a-tab-pane",{key:2,attrs:{tab:"分配客户群"}},[i("div",{staticStyle:{margin:"0 0 20px 0"}},[i("div",{staticStyle:{"background-color":"#FFFFFF"}},[i("div",{staticStyle:{height:"32px","line-height":"32px","margin-bottom":"10px",color:"#000"}},[t._v("\n\t\t\t\t\t\t\t\t共\n\t\t\t\t\t\t\t\t"),i("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total3))]),t._v("个客户群\n\t\t\t\t\t\t\t")]),i("span",{staticClass:"select-option"},[i("label",{staticStyle:{"margin-right":"5px",width:"85px"}},[t._v("搜索客户群：")]),i("a-input",{staticStyle:{"margin-right":"5px",width:"210px"},attrs:{placeholder:"请输入要搜索的客户群"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.getCustomerChatList(1,t.pageSize3)}},model:{value:t.customChatName,callback:function(e){t.customChatName=e},expression:"customChatName"}})],1),i("span",{staticClass:"select-option",staticStyle:{"margin-left":"15px"}},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("分配成员：")]),i("a-button",{staticStyle:{width:"210px","margin-right":"5px"},on:{click:function(e){return t.showDepartmentList(3)}}},[t.chooseNum3>0?i("span",[t._v("已选择"+t._s(t.chooseUserNum3)+"名成员，"+t._s(t.chooseDepartmentNum3)+"个部门")]):i("span",[t._v("选择成员")])])],1),i("span",{staticClass:"select-option",staticStyle:{"margin-left":"15px"}},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("分配状态：")]),i("a-select",{staticStyle:{width:"210px","margin-right":"5px"},attrs:{showSearch:"",optionFilterProp:"children",placeholder:"请选择分配状态"},model:{value:t.customChatStatus,callback:function(e){t.customChatStatus=e},expression:"customChatStatus"}},[i("a-select-option",{attrs:{value:-1}},[t._v("全部")]),i("a-select-option",{attrs:{value:0}},[t._v("未分配")]),i("a-select-option",{attrs:{value:1}},[t._v("已分配")])],1)],1),i("span",{staticClass:"select-option",staticStyle:{"margin-left":"15px"}},[i("label",{staticStyle:{"margin-right":"5px"}},[t._v("分配时间：")]),i("a-range-picker",{staticStyle:{width:"320px","margin-right":"5px"},attrs:{"show-time":{defaultValue:[t.moment("00:00","HH:mm"),t.moment("23:59","HH:mm")],format:"HH:mm"},format:"YYYY-MM-DD HH:mm",allowClear:"","disabled-date":t.disabledDate},on:{change:t.changeTime3},model:{value:t.leaveTime3,callback:function(e){t.leaveTime3=e},expression:"leaveTime3"}})],1),i("a-button",{staticStyle:{"margin-right":"5px"},attrs:{type:"primary"},on:{click:t.searchStaff}},[t._v("查找\n\t\t\t\t\t\t\t")]),i("a-button",{staticStyle:{"margin-right":"10px"},on:{click:t.resetStaff}},[t._v("清空")])],1)]),i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading3}},[i("a-table",{attrs:{columns:t.columns3,dataSource:t.customChatList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"chat_name",fn:function(e,a,s){return i("div",{},[i("div",{staticStyle:{"max-width":"400px",overflow:"hidden"}},[0==a.avatarData.length?i("span",{staticStyle:{background:"#1890FF",width:"36px",height:"36px","margin-right":"5px","border-radius":"4px",float:"left"}},[i("img",{staticStyle:{width:"20px",margin:"8px",height:"20px"},attrs:{src:t.img}})]):1==a.avatarData.length?i("span",{staticStyle:{background:"#DFDFDF",width:"36px",height:"36px","margin-right":"5px","border-radius":"4px",float:"left"}},[""!=a.avatarData[0]?i("img",{staticStyle:{width:"36px",height:"36px"},attrs:{src:a.avatarData[0]}}):t._e(),""==a.avatarData[0]?i("img",{staticStyle:{width:"36px",height:"36px"},attrs:{src:t.img2}}):t._e()]):a.avatarData.length>1&&a.avatarData.length<=4?i("span",{staticStyle:{background:"#DFDFDF",width:"36px",height:"36px","margin-right":"5px",display:"flex","flex-wrap":"wrap","justify-content":"space-around","border-radius":"4px",float:"left","align-items":"center"}},[t._l(a.avatarData,(function(e){return[""!=e?i("img",{staticStyle:{width:"17px",height:"17px"},attrs:{src:e}}):t._e(),""==e?i("img",{staticStyle:{width:"17px",height:"17px"},attrs:{src:t.img2}}):t._e()]}))],2):a.avatarData.length>4?i("span",{staticStyle:{background:"#DFDFDF",width:"36px",height:"36px","margin-right":"5px",display:"flex","flex-wrap":"wrap","justify-content":"space-around","border-radius":"4px",float:"left","align-items":"center"}},[t._l(a.avatarData,(function(e){return[""!=e?i("img",{staticStyle:{width:"10px",height:"10px"},attrs:{src:e}}):t._e(),""==e?i("img",{staticStyle:{width:"10px",height:"10px"},attrs:{src:t.img2}}):t._e()]}))],2):t._e(),i("div",{staticStyle:{display:"inline-block",width:"calc(100% - 41px)","line-height":"36px"}},[a.chat_name?i("a-popover",{attrs:{trigger:"hover"}},[i("span",{staticStyle:{display:"inline-block","max-width":"500px","word-wrap":"break-word","word-break":"break-all"},attrs:{slot:"content"},slot:"content"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.chat_name)+"\n\t\t\t\t\t\t\t\t\t\t")]),i("span",{staticStyle:{display:"inline-block","max-width":"calc(100% - 36px)",overflow:"hidden","white-space":"nowrap","text-overflow":"ellipsis"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(a.chat_name)+"\n\t\t\t\t\t\t\t\t\t\t")])]):t._e(),a.chat_name?t._e():i("span",[t._v("--")]),i("span",{staticStyle:{"vertical-align":"top"}},[t._v("（"+t._s(a.count)+"）")])],1)])])}},{key:"user_name",fn:function(e,a,s){return i("div",{},[""==a.user_name?i("span",[t._v("--")]):i("a-tag",{attrs:{color:"orange"}},[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(a.user_name)+"\n\t\t\t\t\t\t\t\t")])],1)}}])}),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total3>0,expression:"total3 > 0"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[i("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total3,showSizeChanger:"",showQuickJumper:t.quickJumper3,current:t.page3,pageSize:t.pageSize3,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage3,showSizeChange:t.showSizeChange3}})],1)])],1)],1)],1)],1)],2),i("chooseDepartment",{ref:"user",attrs:{id:t.id,show:t.showModalDepartment,chooseNum:t.chooseStaffNum,callback:t.modalVisibleChange,noticeTitle:t.noticeTitle,is_del:t.is_del}})],1)},s=[],o=(a("96cf"),a("3b8d")),n=a("e306"),r=a.n(n),c=a("4bef"),l=a.n(c),h=a("c75b"),m=a("c1df"),p=a.n(m),d=[{title:"离职成员",dataIndex:"name",key:"name",scopedSlots:{customRender:"name2"}},{title:"持有客户数",dataIndex:"user_count",key:"user_count"},{title:"持有群聊数",dataIndex:"chat_count",key:"chat_count"},{title:"待分配客户数",dataIndex:"will_user_count",key:"will_user_count"},{title:"待分配群聊数",dataIndex:"will_chat_count",key:"will_chat_count"},{title:"离职时间",dataIndex:"time",key:"time",width:150},{title:"操作",dataIndex:"action",key:"action",width:180,scopedSlots:{customRender:"action"}}],u=[{title:"客户信息",dataIndex:"customerInfo",key:"customerInfo",scopedSlots:{customRender:"customerInfo"}},{title:"客户状态",dataIndex:"status",key:"status"},{title:"分配成员",dataIndex:"user_name",key:"user_name",scopedSlots:{customRender:"user_name"}},{title:"分配时间",dataIndex:"time",key:"time"}],g=[{title:"客户群信息",dataIndex:"chat_name",key:"chat_name",scopedSlots:{customRender:"chat_name"}},{title:"分配状态",dataIndex:"status",key:"status"},{title:"分配成员",dataIndex:"user_name",key:"user_name",scopedSlots:{customRender:"user_name"}},{title:"分配时间",dataIndex:"time",key:"time"}],f={name:"staffDimission",components:{chooseDepartment:h["a"]},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{img:r.a,img2:l.a,moment:p.a,id:"",commonUrl:this.$store.state.commonUrl,corpId:t,corpId1:"",corpInfo:[],columns:d,isLoading:!1,dimissionList:[],leaveTime:null,total:0,quickJumper:!1,page:1,pageSize:15,showModalDepartment:!1,noticeTitle:"",is_del:0,chooseStaffNum:0,chooseType:1,chooseNum:0,chooseUserNum:0,chooseDepartmentNum:0,chooseNum2:0,chooseUserNum2:0,chooseDepartmentNum2:0,chooseNum3:0,chooseUserNum3:0,chooseDepartmentNum3:0,checkedList:[],user:[],checkedList2:[],user2:[],checkedList3:[],user3:[],detailVisible:!1,detailId:"",drawerTitle:"",tabKey:1,columns2:u,isLoading2:!1,customName:"",customStatus:-1,customerList:[],leaveTime2:null,total2:0,quickJumper2:!1,page2:1,pageSize2:15,columns3:g,isLoading3:!1,customChatName:"",customChatStatus:-1,customChatList:[],leaveTime3:null,total3:0,quickJumper3:!1,page3:1,pageSize3:15}},methods:{rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},getList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,a=o.length>1&&void 0!==o[1]?o[1]:this.pageSize,this.isLoading=!0,t.next=5,this.axios.post("work-user/work-dismiss-users",{corp_id:this.corpId,user_id:this.checkedList,page:e,page_size:a,start_time:this.leaveTime&&this.leaveTime.length>1?this.leaveTime[0].format("YYYY-MM-DD HH:mm"):"",end_time:this.leaveTime&&this.leaveTime.length>1?this.leaveTime[1].format("YYYY-MM-DD HH:mm"):""});case 5:i=t.sent,s=i.data,0!=s.error?(this.isLoading=!1,this.$message.error(s.error_msg)):(this.dimissionList=s.data.info,this.isLoading=!1,this.total=parseInt(s.data.count),this.page=e,this.pageSize=a,this.quickJumper=this.total>this.pageSize);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),disabledDate:function(t){return t&&t>p()().endOf("day")},changeTime:function(t,e){this.leaveTime=t},selectTitle:function(){this.getList(1,this.pageSize)},clearTitle:function(){this.corpId=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",this.chooseNum=0,this.chooseUserNum=0,this.chooseDepartmentNum=0,this.checkedList=[],this.user=[],this.leaveTime=null,this.getList(this.page,this.pageSize)},changePage:function(t,e){this.getList(t,e),this.$nextTick((function(){document.getElementsByClassName("scroll")[0].scrollTo(0,90)}))},showSizeChange:function(t,e){this.getList(1,e)},changePage2:function(t,e){this.getCustomerList(t,e),document.getElementsByClassName("ant-drawer-wrapper-body")[0].scrollTo(0,0)},showSizeChange2:function(t,e){this.getCustomerList(1,e)},changePage3:function(t,e){this.getCustomerChatList(t,e),document.getElementsByClassName("ant-drawer-wrapper-body")[0].scrollTo(0,0)},showSizeChange3:function(t,e){this.getCustomerChatList(1,e)},detailList:function(t,e,a){this.detailId=t,this.corpId1=a,this.drawerTitle=e,this.tabKey=1,this.customName="",this.checkedList=[],this.chooseStaffNum=0,this.page2=1,this.pageSize2=15,this.getCustomerList()},getCustomerList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,a=o.length>1&&void 0!==o[1]?o[1]:this.pageSize2,this.isLoading2=!0,t.next=5,this.axios.post("work-user/work-dismiss-user-detail",{corp_id:this.corpId1,id:this.detailId,name:this.customName,user_id:this.checkedList2,start_time:this.leaveTime2&&this.leaveTime2.length>1?this.leaveTime2[0].format("YYYY-MM-DD HH:mm"):"",end_time:this.leaveTime2&&this.leaveTime2.length>1?this.leaveTime2[1].format("YYYY-MM-DD HH:mm"):"",status:this.customStatus,page:e,pageSize:a});case 5:i=t.sent,s=i.data,0!=s.error?this.$message.error(s.error_msg):(this.customerList=s.data.info,this.total2=parseInt(s.data.count),this.page2=e,this.pageSize2=a,this.quickJumper2=this.total2>this.pageSize2,this.detailVisible=!0,this.isLoading2=!1);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),detailDrawerClose:function(){this.drawerTitle="",this.customName="",this.chooseNum2=0,this.chooseUserNum2=0,this.chooseDepartmentNum2=0,this.checkedList2=[],this.user2=[],this.chooseType=1,this.customStatus=-1,this.leaveTime2=null,this.customerList=[],this.total2=0,this.quickJumper2=!1,this.page2=1,this.pageSize2=15,this.customChatName="",this.chooseNum3=0,this.chooseUserNum3=0,this.chooseDepartmentNum3=0,this.checkedList3=[],this.user3=[],this.customChatStatus=-1,this.leaveTime3=null,this.customChatList=[],this.total3=0,this.quickJumper3=!1,this.page3=1,this.pageSize3=15,this.detailVisible=!1},handleChangeCorp:function(){this.checkedList=[],this.user=[],this.chooseNum=0,this.chooseUserNum=0,this.chooseDepartmentNum=0},showDepartmentList:function(t){this.chooseType=t,1==this.chooseType?(this.id=this.corpId,this.is_del=1,this.noticeTitle="只展示已删除的成员",this.chooseStaffNum=this.chooseNum,this.$refs.user.rightIdList=JSON.parse(JSON.stringify(this.checkedList)),this.$refs.user.rightList=JSON.parse(JSON.stringify(this.user))):2==this.chooseType?(this.id=this.corpId1,this.is_del=0,this.noticeTitle="",this.chooseStaffNum=this.chooseNum2,this.$refs.user.rightIdList=JSON.parse(JSON.stringify(this.checkedList2)),this.$refs.user.rightList=JSON.parse(JSON.stringify(this.user2))):3==this.chooseType&&(this.id=this.corpId1,this.is_del=0,this.noticeTitle="",this.chooseStaffNum=this.chooseNum3,this.$refs.user.rightIdList=JSON.parse(JSON.stringify(this.checkedList3)),this.$refs.user.rightList=JSON.parse(JSON.stringify(this.user3))),this.showModalDepartment=!0},modalVisibleChange:function(t,e,a,i,s){"ok"==t&&(1==this.chooseType?(this.checkedList=e,this.chooseNum=parseInt(a)+parseInt(i),this.chooseUserNum=a,this.chooseDepartmentNum=i,this.user=s):2==this.chooseType?(this.checkedList2=e,this.chooseNum2=parseInt(a)+parseInt(i),this.chooseUserNum2=a,this.chooseDepartmentNum2=i,this.user2=s):3==this.chooseType&&(this.checkedList3=e,this.chooseNum3=parseInt(a)+parseInt(i),this.chooseUserNum3=a,this.chooseDepartmentNum3=i,this.user3=s)),this.showModalDepartment=!1},changeTime2:function(t,e){this.leaveTime2=t},searchRecord:function(){this.getCustomerList()},resetRecord:function(){this.customName="",this.chooseNum2=0,this.chooseUserNum2=0,this.chooseDepartmentNum2=0,this.checkedList2=[],this.user2=[],this.leaveTime2=null,this.customStatus=-1,this.getCustomerList()},changeTabKey:function(t){1==t?this.getCustomerList():2==t&&this.getCustomerChatList()},getCustomerChatList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,a=o.length>1&&void 0!==o[1]?o[1]:this.pageSize3,this.isLoading3=!0,t.next=5,this.axios.post("work-user/work-dismiss-chat-detail",{corp_id:this.corpId1,id:this.detailId,name:this.customChatName,status:this.customChatStatus,user_id:this.checkedList3,start_time:this.leaveTime3&&this.leaveTime3.length>1?this.leaveTime3[0].format("YYYY-MM-DD HH:mm"):"",end_time:this.leaveTime3&&this.leaveTime3.length>1?this.leaveTime3[1].format("YYYY-MM-DD HH:mm"):"",page:e,pageSize:a});case 5:i=t.sent,s=i.data,0!=s.error?this.$message.error(s.error_msg):(this.customChatList=s.data.info,this.total3=parseInt(s.data.count),this.page3=e,this.pageSize3=a,this.quickJumper3=this.total3>this.pageSize3,this.isLoading3=!1);case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changeTime3:function(t,e){this.leaveTime3=t},searchStaff:function(){this.getCustomerChatList()},resetStaff:function(){this.customChatName="",this.chooseNum3=0,this.chooseUserNum3=0,this.chooseDepartmentNum3=0,this.checkedList3=[],this.user3=[],this.leaveTime3=null,this.customChatStatus=-1,this.getCustomerChatList()}},created:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.getList()}))}},v=f,x=(a("0827"),a("2877")),y=Object(x["a"])(v,i,s,!1,null,"e7d8f9ec",null);e["default"]=y.exports},b3dc4:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,"#components-layout-demo-basic[data-v-e7d8f9ec]{height:100%}#components-layout-demo-basic .ant-layout-header[data-v-e7d8f9ec]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px}[data-v-e7d8f9ec] .ant-layout-header{padding:0 20px;font-size:16px;text-align:left}#components-layout-demo-basic .ant-layout-sider[data-v-e7d8f9ec]{background:#fff;-webkit-box-flex:0!important;-ms-flex:0 0 250px!important;flex:0 0 250px!important;max-width:250px!important;min-width:250px!important;width:250px!important;border-right:1px solid #e2e2e2}#components-layout-demo-basic .ant-layout-content[data-v-e7d8f9ec]{margin:0 20px 20px;min-width:885px;width:100%;padding-right:40px}.content-hd[data-v-e7d8f9ec]{height:60px;line-height:60px}.content-bd[data-v-e7d8f9ec],.content-hd[data-v-e7d8f9ec]{width:100%;min-width:885px}.content-bd[data-v-e7d8f9ec]{background:#fff;min-height:120px;border:1px solid #e2e2e2}#components-layout-demo-basic>.ant-layout[data-v-e7d8f9ec]{margin-bottom:48px}#components-layout-demo-basic>.ant-layout[data-v-e7d8f9ec]:last-child{margin:0}.ant-layout.ant-layout-has-sider[data-v-e7d8f9ec],.list[data-v-e7d8f9ec]{height:100%}[data-v-e7d8f9ec] .dark-row{background:#fafafa}[data-v-e7d8f9ec] .light-row{background:#fff}.actionBtn[data-v-e7d8f9ec],[data-v-e7d8f9ec] .ant-tag{margin-bottom:5px}.actionBtn[data-v-e7d8f9ec]{margin-right:5px}[data-v-e7d8f9ec] .ant-tree-switcher-icon{vertical-align:initial}[data-v-e7d8f9ec] .ant-tree-node-content-wrapper.ant-tree-node-selected{background-color:#fff!important}.content-msg[data-v-e7d8f9ec]{width:100%;border:1px solid #ffdda6;background:#fff2db;padding:10px;margin-top:12px;text-align:left}.single-accout-card[data-v-e7d8f9ec]{width:100%;background:#f9f9f9}.single-accout-card .single-accout-cardItem[data-v-e7d8f9ec]{display:inline-block;height:95px;background:#f9f9f9;border:1px solid #e9e9e9;border-left:0}.single-accout-card .single-accout-cardItem[data-v-e7d8f9ec]:first-child{border:1px solid #e9e9e9}.single-accout-cardItem-num[data-v-e7d8f9ec]{color:#000;font-weight:700;font-size:34px;line-height:60px}.single-accout-cardItem-num[data-v-e7d8f9ec],.single-accout-cardItem-title[data-v-e7d8f9ec]{text-align:center;margin-bottom:0}.select-option[data-v-e7d8f9ec]{display:inline-block;margin-right:10px;margin-bottom:10px}.select-option label[data-v-e7d8f9ec]{display:inline-block;text-align:left;width:70px}[data-v-e7d8f9ec] .ant-tabs-bar{background:none;border-bottom:2px solid #f5f5f5}",""])},e306:function(t,e,a){t.exports=a.p+"assets/img/chat.29abd713.png"}}]);