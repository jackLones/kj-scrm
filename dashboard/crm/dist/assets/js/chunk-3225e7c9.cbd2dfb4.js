(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-3225e7c9"],{"20d6":function(t,e,a){"use strict";var i=a("5ca1"),s=a("0a49")(6),n="findIndex",o=!0;n in[]&&Array(1)[n]((function(){o=!1})),i(i.P+i.F*o,"Array",{findIndex:function(t){return s(this,t,arguments.length>1?arguments[1]:void 0)}}),a("9c6c")(n)},"816f":function(t,e,a){"use strict";a.r(e);var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticStyle:{width:"100%","max-height":"100%",position:"absolute","overflow-y":"auto",padding:"20px 0"}},[a("div",{staticStyle:{padding:"0 20px"}},[a("span",{directives:[{name:"has",rawName:"v-has",value:t.hasHignAttribute,expression:"hasHignAttribute"}],staticClass:"tabBtn",class:{activeBtn:1==t.tabKey},on:{click:function(e){return t.changeTab("1")}}},[t._v("高级属性")]),a("span",{directives:[{name:"has",rawName:"v-has",value:t.hasFollowStatus,expression:"hasFollowStatus"}],staticClass:"tabBtn",class:{activeBtn:2==t.tabKey},on:{click:function(e){return t.changeTab("2")}}},[t._v("跟进状态")])]),a("div",{directives:[{name:"show",rawName:"v-show",value:1==t.tabKey,expression:"tabKey == 1"}],staticStyle:{padding:"15px 0",margin:"0px 20px",background:"#FFF"}},[t._m(0),a("a-row",{staticStyle:{"margin-bottom":"20px",padding:"0 20px"}},[a("a-col",{staticStyle:{float:"left"}},[a("a-select",{staticStyle:{width:"120px"},attrs:{defaultValue:"2"},on:{change:t.selectStatus}},[a("a-select-option",{attrs:{value:"2"}},[t._v("全部状态")]),a("a-select-option",{attrs:{value:"1"}},[t._v("开启")]),a("a-select-option",{attrs:{value:"0"}},[t._v("关闭")])],1)],1),a("a-col",{staticStyle:{float:"right"}},[t.allVisible?a("a-button",{directives:[{name:"has",rawName:"v-has",value:t.hasAddName,expression:"hasAddName"}],staticStyle:{"margin-right":"15px"},on:{click:t.addModel}},[t._v("新增属性\n\t\t\t\t\t")]):t._e(),t.allVisible?a("a-button",{directives:[{name:"has",rawName:"v-has",value:t.hasEditName,expression:"hasEditName"}],attrs:{type:"primary"},on:{click:t.editAll}},[t._v("批量修改\n\t\t\t\t\t")]):t._e(),t.allVisible?t._e():a("a-button",{staticStyle:{"margin-right":"15px"},attrs:{type:"primary",disabled:t.submitDisabled},on:{click:t.submit}},[t._v("提交\n\t\t\t\t\t")]),t.allVisible?t._e():a("a-button",{on:{click:t.cancelEditAll}},[t._v("取消\n\t\t\t\t\t")])],1)],1),a("div",{staticClass:"content-bd"},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[a("div",{staticClass:"spin-content"},[a("a-table",{directives:[{name:"has",rawName:"v-has",value:t.hasListName,expression:"hasListName"}],key:t.tableKey,attrs:{columns:t.columns,dataSource:t.managentList,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([t._l(["status","chat_status","titleMsg","type","optionVal","sort","action"],(function(e){return{key:e,fn:function(i,s,n){return[a("div",{key:e},["status"==e?[s.flag||"sex"==s.key?[1==i?a("span",[t._v("开启")]):t._e(),0==i?a("span",[t._v("关闭")]):t._e()]:a("a-switch",{attrs:{defaultChecked:0!=i},on:{click:function(e){return t.changeStatus(e,n)}}})]:t._e(),"chat_status"==e?[s.flag||"sex"==s.key?[1==i?a("span",[t._v("开启")]):t._e(),0==i?a("span",[t._v("关闭")]):t._e()]:a("a-switch",{attrs:{defaultChecked:0!=i},on:{click:function(e){return t.changeChatStatus(e,n)}}})]:t._e(),"titleMsg"==e?[s.flag?[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(i)+"\n\t\t\t\t\t\t\t\t\t\t")]:[0==s.is_define?a("span",[t._v(t._s(i))]):t._e(),1==s.is_define?a("a-input",{attrs:{value:i,placeholder:"请输入字段名称",maxLength:8},on:{change:function(e){return t.handleChange(e.target.value,s.key)}}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[a("span",[t._v(t._s(t.managentList[n].title.length))]),t._v("/8\n                  ")])]):t._e()]]:t._e(),"type"==e?[s.flag?[1==i?a("span",[t._v("文本")]):t._e(),2==i?a("span",[t._v("单选")]):t._e(),3==i?a("span",[t._v("多选")]):t._e(),4==i?a("span",[t._v("日期")]):t._e(),5==i?a("span",[t._v("手机号")]):t._e(),6==i?a("span",[t._v("邮箱")]):t._e(),7==i?a("span",[t._v("区域")]):t._e(),8==i?a("span",[t._v("图片")]):t._e()]:[s.addFlag?t._e():a("span",[1==i?a("span",[t._v("文本")]):t._e(),2==i?a("span",[t._v("单选")]):t._e(),3==i?a("span",[t._v("多选")]):t._e(),4==i?a("span",[t._v("日期")]):t._e(),5==i?a("span",[t._v("手机号")]):t._e(),6==i?a("span",[t._v("邮箱")]):t._e(),7==i?a("span",[t._v("区域")]):t._e(),8==i?a("span",[t._v("图片")]):t._e()]),s.addFlag&&1==s.is_define?a("a-select",{staticStyle:{width:"120px"},attrs:{defaultValue:i},on:{change:function(a){return t.handleChange2(a,s.key,e)}}},[a("a-select-option",{attrs:{value:"1"}},[t._v("文本")]),a("a-select-option",{attrs:{value:"2"}},[t._v("单选")]),a("a-select-option",{attrs:{value:"3"}},[t._v("多选")]),a("a-select-option",{attrs:{value:"4"}},[t._v("日期")]),a("a-select-option",{attrs:{value:"5"}},[t._v("手机号")]),a("a-select-option",{attrs:{value:"6"}},[t._v("邮箱")]),a("a-select-option",{attrs:{value:"7"}},[t._v("区域")]),a("a-select-option",{attrs:{value:"8"}},[t._v("图片")])],1):t._e()]]:t._e(),"optionVal"==e?[s.flag?t._e():[0==s.is_define?a("span",[a("span",{directives:[{name:"show",rawName:"v-show",value:""==i||null==i,expression:"text == '' || text == null"}]},[t._v("--")]),a("a-tooltip",[a("template",{slot:"title"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(i)+"\n\t\t\t\t\t\t\t\t\t\t\t\t")]),a("span",{directives:[{name:"show",rawName:"v-show",value:""!=i&&null!=i,expression:"text != '' && text != null"}],staticClass:"optionVal-text"},[t._v(t._s(i))])],2)],1):t._e(),1==s.is_define?a("span",[2==s.type||3==s.type?a("span",[t._l(s.optionVal2,(function(e){return a("a-tag",{staticStyle:{"margin-bottom":"5px"},attrs:{closable:""},on:{close:function(a){return t.delTag(a,e,s.key)}}},[t._v(t._s(e))])})),a("a-textarea",{attrs:{rows:2,placeholder:"请输入选项内容"},on:{change:function(a){return t.handleChange3(a.target.value,s.key,e)}}}),a("div",[t._v("用“,”新增选项内容，每个选项内容不超过12个字")])],2):a("span",[t._v("--")])]):t._e()],s.flag?[""==i||null==i?a("span",[t._v("--")]):t._e(),a("a-tooltip",[a("template",{slot:"title"},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(i)+"\n\t\t\t\t\t\t\t\t\t\t\t\t")]),""!=i&&null!=i?a("span",{staticClass:"optionVal-text"},[t._v(t._s(i))]):t._e()],2)]:t._e()]:t._e(),"sort"==e?[s.flag?[t._v("\n\t\t\t\t\t\t\t\t\t\t\t"+t._s(i)+"\n\t\t\t\t\t\t\t\t\t\t")]:[a("a-input",{attrs:{value:i,placeholder:"请输入排序展示"},on:{change:function(e){return t.handleChange4(e.target.value,s.key)}}}),a("span",{staticStyle:{color:"red"}},[t._v("数值越大，在手机端展示越靠前")])]]:t._e(),"action"==e?[s.flag?[a("a-popconfirm",{attrs:{title:"确定开启吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.submitOne(s,1,0)}}},[0==s.status&&"sex"!=s.key?a("a-button",{staticStyle:{margin:"0 0px 5px 5px"}},[t._v("客户开启\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1),a("a-popconfirm",{attrs:{title:"确定关闭吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.submitOne(s,0,0)}}},[1==s.status&&"sex"!=s.key?a("a-button",{staticStyle:{margin:"0 0px 5px 5px"}},[t._v("客户关闭\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1),a("a-popconfirm",{attrs:{title:"确定开启吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.submitOne(s,1,1)}}},[0==s.chat_status&&"sex"!=s.key?a("a-button",{staticStyle:{margin:"0 0px 5px 5px"}},[t._v("群开启\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1),a("a-popconfirm",{attrs:{title:"确定关闭吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.submitOne(s,0,1)}}},[1==s.chat_status&&"sex"!=s.key?a("a-button",{staticStyle:{margin:"0 0px 5px 5px"}},[t._v("群关闭\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1),a("a-button",{directives:[{name:"has",rawName:"v-has",value:t.hasEditName,expression:"hasEditName"}],staticStyle:{margin:"0 0px 5px 5px"},on:{click:function(e){return t.edit(s,n)}}},[t._v("编辑\n\t\t\t\t\t\t\t\t\t\t\t")]),a("a-popconfirm",{attrs:{title:"确定删除吗?",okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.submitOne(s,2)}}},[1==s.is_define?a("a-button",{directives:[{name:"has",rawName:"v-has",value:t.hasDeleteName,expression:"hasDeleteName"}],staticStyle:{margin:"0 0px 5px 5px"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t删除\n\t\t\t\t\t\t\t\t\t\t\t\t")]):t._e()],1)]:[t.allVisible||1!=s.is_define?t._e():[a("a-button",{directives:[{name:"has",rawName:"v-has",value:t.hasDeleteName,expression:"hasDeleteName"}],staticStyle:{"margin-left":"5px"},on:{click:function(e){return t.deleteOne(n)}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t\t删除\n\t\t\t\t\t\t\t\t\t\t\t\t")])]]]:t._e()],2)]}}}))],null,!0)}),t.managentList.length>0?a("div",{staticStyle:{"margin-top":"20px"}},[t.allVisible?t._e():a("a-button",{directives:[{name:"has",rawName:"v-has",value:t.hasAddName,expression:"hasAddName"}],attrs:{type:"link"},on:{click:t.addLine}},[t._v("+ 增加自定义注册项\n\t\t\t\t\t\t\t")])],1):t._e(),t.managentList.length>0?a("div",{staticStyle:{"text-align":"center"}},[t.allVisible?t._e():a("a-button",{staticStyle:{width:"100px",margin:"20px 20px 20px 0","z-index":"999"},on:{click:t.cancelEditAll}},[t._v("取消\n\t\t\t\t\t\t\t")]),t.allVisible?t._e():a("a-button",{staticStyle:{width:"100px",margin:"20px","z-index":"999"},attrs:{type:"primary",disabled:t.submitDisabled},on:{click:t.submit}},[t._v("提交\n\t\t\t\t\t\t\t")])],1):t._e()],1)])],1),a("a-modal",{attrs:{title:t.modelTitle,width:"565px"},on:{ok:t.addHandleOk,cancel:t.addHandleCancel},model:{value:t.addVisible,callback:function(e){t.addVisible=e},expression:"addVisible"}},[a("a-form-model",{attrs:{model:t.form,"label-col":{span:6},"wrapper-col":{span:14}}},[a("a-form-model-item",{attrs:{label:"字段名称"}},[1==t.form.is_define?a("a-input",{attrs:{placeholder:"请输入字段名称",maxLength:8},model:{value:t.form.title,callback:function(e){t.$set(t.form,"title",e)},expression:"form.title"}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[a("span",[t._v(t._s(t.form.title.length))]),t._v("/8\n\t\t\t\t\t\t\t ")])]):t._e(),0==t.form.is_define?a("span",[t._v(t._s(t.form.title))]):t._e()],1),a("a-form-model-item",{attrs:{label:"字段类型"}},[t.form.addFlag||1!=t.form.is_define?t._e():a("a-select",{model:{value:t.form.type,callback:function(e){t.$set(t.form,"type",e)},expression:"form.type"}},[a("a-select-option",{attrs:{value:"1"}},[t._v("文本")]),a("a-select-option",{attrs:{value:"2"}},[t._v("单选")]),a("a-select-option",{attrs:{value:"3"}},[t._v("多选")]),a("a-select-option",{attrs:{value:"4"}},[t._v("日期")]),a("a-select-option",{attrs:{value:"5"}},[t._v("手机号")]),a("a-select-option",{attrs:{value:"6"}},[t._v("邮箱")]),a("a-select-option",{attrs:{value:"7"}},[t._v("区域")]),a("a-select-option",{attrs:{value:"8"}},[t._v("图片")])],1),t.form.addFlag||0==t.form.is_define?[1==t.form.type?a("span",[t._v("文本")]):t._e(),2==t.form.type?a("span",[t._v("单选")]):t._e(),3==t.form.type?a("span",[t._v("多选")]):t._e(),4==t.form.type?a("span",[t._v("日期")]):t._e(),5==t.form.type?a("span",[t._v("手机号")]):t._e(),6==t.form.type?a("span",[t._v("邮箱")]):t._e(),7==t.form.type?a("span",[t._v("区域")]):t._e(),8==t.form.type?a("span",[t._v("图片")]):t._e()]:t._e()],2),2==t.form.type||3==t.form.type?a("a-form-model-item",{attrs:{label:"选项内容"}},[t._l(t.form.optionVal2,(function(e){return 1==t.form.is_define?a("a-tag",{staticStyle:{"margin-bottom":"5px"},attrs:{closable:""},on:{close:function(a){return t.delTag(a,e)}}},[t._v(t._s(e)+"\n\t\t\t\t\t\t")]):t._e()})),t._l(t.form.optionVal2,(function(e){return 0==t.form.is_define?a("a-tag",{staticStyle:{"margin-bottom":"5px"}},[t._v(t._s(e)+"\n\t\t\t\t\t\t")]):t._e()})),1==t.form.is_define?a("a-input",{staticStyle:{"margin-top":"3px"},attrs:{type:"textarea",placeholder:"请输入选项内容"},model:{value:t.form.optionVal,callback:function(e){t.$set(t.form,"optionVal",e)},expression:"form.optionVal"}}):t._e(),1==t.form.is_define?a("div",[t._v("用“,”新增选项内容，每个选项内容不超过12个字")]):t._e()],2):t._e(),a("a-form-model-item",{attrs:{label:"排序展示"}},[a("a-input",{staticStyle:{"margin-top":"3px"},attrs:{placeholder:"请输入排序展示"},model:{value:t.form.sort,callback:function(e){t.$set(t.form,"sort",e)},expression:"form.sort"}}),a("span",{staticStyle:{color:"red"}},[t._v("数值越大，在手机端展示越靠前")])],1),a("a-form-model-item",{attrs:{label:"客户画像显示"}},["sex"!=t.form.key?a("a-radio-group",{model:{value:t.form.status,callback:function(e){t.$set(t.form,"status",e)},expression:"form.status"}},[a("a-radio",{attrs:{value:"1"}},[t._v("开启")]),a("a-radio",{attrs:{value:"0"}},[t._v("关闭")])],1):t._e(),"sex"==t.form.key?a("span",[t._v("开启")]):t._e()],1),a("a-form-model-item",{attrs:{label:"群画像显示"}},["sex"!=t.form.key?a("a-radio-group",{model:{value:t.form.chat_status,callback:function(e){t.$set(t.form,"chat_status",e)},expression:"form.chat_status"}},[a("a-radio",{attrs:{value:"1"}},[t._v("开启")]),a("a-radio",{attrs:{value:"0"}},[t._v("关闭")])],1):t._e(),"sex"==t.form.key?a("span",[t._v("关闭")]):t._e()],1)],1)],1)],1),a("div",{directives:[{name:"show",rawName:"v-show",value:2==t.tabKey,expression:"tabKey == 2"}],staticStyle:{background:"#FFF",padding:"20px 0",margin:"0 20px"}},[a("a-row",{staticStyle:{"margin-top":"10px","margin-left":"4.16%"}},[t._l(t.follows,(function(e,i){return[a("a-col",{style:{float:i%16>7?"right":"left",marginRight:i%16==8?"4.17%":""},attrs:{span:2}},[a("a-button",{staticStyle:{border:"1px solid #1890FF",color:"#1890FF",width:"100%",overflow:"hidden","text-overflow":"ellipsis"}},[t._v("\n\t\t\t\t\t\t\t"+t._s(e.title)+"\n\t\t\t\t\t\t")])],1),i%8!=7&&i!=t.follows.length-1?a("a-col",{staticStyle:{padding:"2px 0","text-align":"center"},style:{float:i%16>7?"right":"left"},attrs:{span:1}},[i%16>7?a("div",{staticClass:"arrow-left"}):t._e(),a("div",{staticClass:"line"}),i%16<7?a("div",{staticClass:"arrow-right"}):t._e()]):t._e(),i%16==7&&i!=t.follows.length-1?a("a-col",{staticStyle:{"margin-top":"10px"},attrs:{span:23}},[a("div",{staticClass:"line-right"})]):t._e(),i%16==7&&i!=t.follows.length-1?a("a-col",{staticStyle:{"margin-bottom":"5px"},attrs:{span:23}},[a("div",{staticClass:"arrow-bottom arrow-bottom-right"})]):t._e(),i%16==15&&i!=t.follows.length-1?a("a-col",{staticStyle:{"margin-top":"10px"},attrs:{span:23}},[a("div",{staticClass:"line-left"})]):t._e(),i%16==15&&i!=t.follows.length-1?a("a-col",{staticStyle:{"margin-bottom":"5px"},attrs:{span:23}},[a("div",{staticClass:"arrow-bottom arrow-bottom-left"})]):t._e()]}))],2),a("div",{staticStyle:{height:"40px","line-height":"40px",margin:"20px 0 10px 0"}},[a("span",{staticStyle:{color:"#FF562D","margin-left":"20px"}},[t._v("鼠标移动面板更改状态排序")]),a("a-button",{staticStyle:{"margin-right":"20px","vertical-align":"middle",float:"right"},attrs:{type:"primary",icon:"plus"},on:{click:t.addFollow}},[t._v("新增\n\t\t\t\t")])],1),a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading1}},[a("a-table",{staticClass:"follow-table",staticStyle:{margin:"20px"},attrs:{columns:t.columns1,dataSource:t.follows,pagination:!1,rowClassName:t.rowClassName},scopedSlots:t._u([{key:"sort",fn:function(e,i,s){return a("span",{},[t._v("\n\t\t\t\t     "+t._s(s+1)+"\n\t\t\t\t   ")])}},{key:"describe",fn:function(e,i,s){return a("span",{},[t._v("\n\t\t\t\t       "+t._s(i.describe||"--")+"\n\t\t\t\t    ")])}},{key:"action",fn:function(e,i,s){return a("span",{},[a("a-button",{staticStyle:{"margin-right":"5px"},on:{click:function(e){return t.editFollow(i.id,i.title,i.describe)}}},[t._v("\n\t\t\t\t\t\t\t编辑\n\t\t\t\t\t\t")]),a("a-button",{on:{click:function(e){return t.delFollow(i.id)}}},[t._v("\n\t\t\t\t\t\t\t删除\n\t\t\t\t\t\t")])],1)}}])})],1)],1),a("a-modal",{attrs:{visible:t.followVisible,title:t.addOrEditTitle,width:"720px"},on:{cancel:t.handleCancelFollow}},[a("template",{slot:"footer"},[a("a-button",{on:{click:t.handleCancelFollow}},[t._v("取消")]),a("a-button",{attrs:{loading:t.isLoading2,type:"primary"},on:{click:t.handleFollow}},[t._v("确定")])],1),a("a-form",[a("a-form-item",{attrs:{label:"状态名称","label-col":{span:3},"wrapper-col":{span:21}}},[a("a-input",{attrs:{maxLength:8,placeholder:"请输入状态名称，8个字以内"},model:{value:t.title,callback:function(e){t.title=e},expression:"title"}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[a("span",[t._v(t._s(t.title.length))]),t._v("/8\n\t\t\t\t\t\t")])])],1),a("a-form-item",{attrs:{label:"状态描述","label-col":{span:3},"wrapper-col":{span:21}}},[a("a-input",{attrs:{maxLength:50,placeholder:"请输入状态描述，50个字以内"},model:{value:t.desc,callback:function(e){t.desc=e},expression:"desc"}},[a("span",{attrs:{slot:"suffix"},slot:"suffix"},[a("span",[t._v(t._s(t.desc.length))]),t._v("/50\n\t\t\t\t\t\t")])])],1)],1)],2)],1)},s=[function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"content-msg"},[a("p",{staticStyle:{"margin-bottom":"0"}},[t._v("\n\t\t\t\t\t本系统提供手机号、姓名、公司、年龄、性别、行业、爱好、生日及所在区域等15个通用属性字段，属性类型包括文本、单选、多选、日期等。系统通用字段只可修改使用状态和排序顺序（数值越大，在手机端展示越靠前）。")])])}],n=(a("8e6e"),a("456d"),a("ac4d"),a("8a81"),a("ac6a"),a("20d6"),a("a481"),a("55dd"),a("bd86")),o=(a("28a5"),a("75fc")),r=(a("96cf"),a("3b8d")),l=a("c758");function c(t,e){var a=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),a.push.apply(a,i)}return a}function d(t){for(var e=1;e<arguments.length;e++){var a=null!=arguments[e]?arguments[e]:{};e%2?c(a,!0).forEach((function(e){Object(n["a"])(t,e,a[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(a)):c(a).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(a,e))}))}return t}var p=[{title:"字段名称",dataIndex:"title",key:"title",width:"15%",scopedSlots:{customRender:"titleMsg"}},{title:"填写格式",dataIndex:"type",key:"type",width:"10%",scopedSlots:{customRender:"type"}},{title:"选项内容",dataIndex:"optionVal",key:"optionVal",width:"25%",scopedSlots:{customRender:"optionVal"}},{title:"排序展示",dataIndex:"sort",key:"sort",width:"15%",scopedSlots:{customRender:"sort"}},{title:"客户画像显示",dataIndex:"status",key:"status",width:"10%",scopedSlots:{customRender:"status"}},{title:"群画像显示",dataIndex:"chat_status",key:"chat_status",width:"10%",scopedSlots:{customRender:"chat_status"}},{title:"操作",dataIndex:"action",key:"action",scopedSlots:{customRender:"action"}}],u=[{title:"排序",dataIndex:"sort",key:"sort",scopedSlots:{customRender:"sort"}},{title:"客户阶段",dataIndex:"title",key:"title"},{title:"阶段描述",dataIndex:"describe",key:"describe",scopedSlots:{customRender:"describe"}},{title:"操作",dataIndex:"action",key:"action",scopedSlots:{customRender:"action"}}],f={components:{TemplateList:l["default"]},data:function(){return{isLoading:!1,isLoading1:!1,commonUrl:this.$store.state.commonUrl,allVisible:!0,tableKey:0,managentList:[],cacheData:[],arr:[],editingKey:"",columns:p,columns1:u,addVisible:!1,form:{title:"",type:"1",optionVal:"",status:"1",chat_status:"1",sort:0,is_define:0,addFlag:!1},modelTitle:"新增属性",id:"",key:0,submitDisabled:!1,visible:!0,status:"2",follows:[],startId:"",endId:"",tabKey:"1",hasHignAttribute:"",hasFollowStatus:"",hasAddName:"",hasEditName:"",hasListName:"",hasDeleteName:"",followId:"",title:"",desc:"",followVisible:!1,isLoading2:!1,addOrEditTitle:""}},methods:{rowClassName:function(t,e){var a="dark-row";return e%2===0&&(a="light-row"),a},selectStatus:function(t){this.status=t,this.getList()},getList:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading=!0,t.next=3,this.axios.post("custom-field/field-list",{uid:localStorage.getItem("uid"),status:this.status});case 3:e=t.sent,a=e.data,0!=a.error?(this.isLoading=!1,this.$message.error(a.error_msg)):(this.isLoading=!1,this.allVisible=!0,this.managentList=a.data.field,i=Object(o["a"])(this.managentList),i.map((function(t){2!=t.type&&3!=t.type||(t.optionVal2=t.optionVal.split(",")),t.flag=!0})),this.managentList=i,this.cacheData=this.managentList.map((function(t){return d({},t)})),this.arr=this.managentList.map((function(t){return d({},t)})),this.$forceUpdate());case 6:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),addModel:function(){this.modelTitle="新增属性",this.form={title:"",type:"1",optionVal:"",status:"1",chat_status:"1",sort:0,is_define:1,addFlag:!1},this.addVisible=!0},addHandleOk:function(){if(""==this.form.title.trim())return this.$message.error("请填写字段名称"),!1;if(!this.form.addFlag&&(2==this.form.type||3==this.form.type)&&""==this.form.optionVal)return this.$message.error("请填写选项内容"),!1;var t=/^(?:0|[1-9][0-9]?|100)$/;if(!t.test(this.form.sort))return this.$message.warning("排序展示必须为0-100正整数"),!1;this.add()},addHandleCancel:function(){this.addVisible=!1,this.id="",this.form={title:"",type:"1",optionVal:"",status:"1",sort:0,is_define:0,addFlag:!1}},add:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(e="",2!=this.form.type&&3!=this.form.type){t.next=12;break}this.form.optionVal2&&0!=this.form.optionVal2.length?(this.form.optionVal=this.form.optionVal.replace(/，/g,","),e=this.form.optionVal2.join(",")+","+this.form.optionVal):e=this.form.optionVal.replace(/，/g,","),a=e.split(","),i=0;case 5:if(!(i<a.length)){t.next=12;break}if(!(a[i].length>12)){t.next=9;break}return this.$message.error("每个选项内容不超过12个字"),t.abrupt("return",!1);case 9:i++,t.next=5;break;case 12:return t.next=14,this.axios.post("custom-field/set-field",{uid:localStorage.getItem("uid"),id:this.id,title:this.form.title,type:this.form.type,optionVal:e,sort:this.form.sort,status:this.form.status,chat_status:this.form.chat_status});case 14:s=t.sent,n=s.data,0!=n.error?this.$message.error(n.error_msg):(this.addVisible=!1,this.id="",this.form={title:"",type:"1",optionVal:"",status:"1",sort:0,is_define:0},this.getList());case 17:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),editAll:function(){this.allVisible=!1,this.cacheData=this.arr.map((function(t){return d({},t)})),this.managentList.map((function(t){t.flag=!1,t.addFlag=!1,2!=t.type&&3!=t.type||(t.optionVal2=t.optionVal.split(","),t.optionVal="")}))},cancelEditAll:function(){this.allVisible=!0,this.managentList=Object(o["a"])(this.cacheData),this.managentList.map((function(t){t.flag=!0,t.addFlag=!0})),this.key=0,this.tableKey++},changeStatus:function(t,e){var a=Object(o["a"])(this.managentList);a[e].status=t?"1":"0",this.managentList=a},changeChatStatus:function(t,e){var a=Object(o["a"])(this.managentList);a[e].chat_status=t?"1":"0",this.managentList=a},edit:function(t,e){this.editingKey=e,this.modelTitle="编辑属性",this.form.title=t.title,this.form.type=t.type,this.form.is_define=t.is_define,2!=t.type&&3!=t.type||(this.form.optionVal2=t.optionVal.split(",")),this.form.status=t.status,this.form.chat_status=t.chat_status,this.form.sort=t.sort,this.id=t.id,this.addVisible=!0,this.form.addFlag=!0,this.form.key=t.key},deleteOne:function(t){this.managentList.splice(t,1)},submitOne:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e,a,i){var s,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("custom-field/set-field-status",{uid:localStorage.getItem("uid"),id:e.id,is_define:e.is_define,status:a,type:i});case 2:s=t.sent,n=s.data,0!=n.error?this.$message.error(n.error_msg):this.getList();case 5:case"end":return t.stop()}}),t,this)})));function e(e,a,i){return t.apply(this,arguments)}return e}(),handleChange:function(t,e){var a=Object(o["a"])(this.managentList),i=a.filter((function(t){return e===t.key}))[0];i&&(i["title"]=t,this.managentList=a)},handleChange2:function(t,e,a){var i=Object(o["a"])(this.managentList),s=i.filter((function(t){return e===t.key}))[0];s&&(s[a]=t,this.managentList=i)},handleChange3:function(t,e,a){var i=Object(o["a"])(this.managentList),s=i.filter((function(t){return e===t.key}))[0];s&&(s[a]=t,this.managentList=i)},handleChange4:function(t,e){var a=Object(o["a"])(this.managentList),i=a.filter((function(t){return e===t.key}))[0];i&&(i["sort"]=t,this.managentList=a)},submit:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a,i,s,n,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:e=/^(?:0|[1-9][0-9]?|100)$/,this.managentList.map((function(t){2!=t.type&&3!=t.type||(t.optionVal2&&0!=t.optionVal2.length?""==t.optionVal?t.optionVal=t.optionVal2.join(","):t.optionVal=t.optionVal2.join(",")+","+t.optionVal.replace(/，/g,","):t.optionVal=t.optionVal.replace(/，/g,","))})),a=Object(o["a"])(this.managentList),i=0;case 4:if(!(i<a.length)){t.next=23;break}if(1!=a[i].is_define){t.next=17;break}if(2!=a[i].type&&3!=a[i].type){t.next=17;break}if(""==a[i].optionVal||null==a[i].optionVal.split(",")||null==a[i].optionVal){t.next=17;break}a[i].optionVal3=a[i].optionVal.split(","),s=0;case 10:if(!(s<a[i].optionVal3.length)){t.next=17;break}if(!(a[i].optionVal3[s].length>12)){t.next=14;break}return this.$message.error("每个选项内容不超过12个字"),t.abrupt("return",!1);case 14:s++,t.next=10;break;case 17:if(e.test(a[i].sort)){t.next=20;break}return this.$message.warning("排序展示必须为0-100正整数"),t.abrupt("return",!1);case 20:i++,t.next=4;break;case 23:return t.next=25,this.axios.post("custom-field/set-field-batch",{uid:localStorage.getItem("uid"),msgData:a});case 25:n=t.sent,r=n.data,0!=r.error?(this.$message.error(r.error_msg),this.submitDisabled=!1):(this.submitDisabled=!1,this.getList());case 28:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),delTag:function(t,e,a){t.preventDefault(),this.addVisible?-1!=this.form.optionVal2.indexOf(e)&&this.form.optionVal2.splice(this.form.optionVal2.indexOf(e),1):this.managentList.map((function(t){t.key==a&&t.optionVal2.splice(t.optionVal2.indexOf(e),1)})),this.$forceUpdate()},addLine:function(){var t=Object(o["a"])(this.managentList);t.push({key:this.key,title:"",type:"1",is_define:"1",status:"1",optionVal:"",flag:!1,addFlag:!0}),this.key++,this.managentList=t},changeTab:function(t){this.tabKey=t},delFollow:function(t){var e=this;e.$confirm({title:"确定删除该状态?",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){e.isLoading=!0,e.delF(t)}})},delF:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var a,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("custom-field/del-follow",{uid:localStorage.getItem("uid"),id:e});case 2:a=t.sent,i=a.data,0!=i.error?(this.isLoading1=!1,this.$message.error(i.error_msg)):this.getFollowStatus();case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),editFollow:function(t,e,a){this.followId=t,this.title=e,this.desc=a,this.addOrEditTitle="编辑状态",this.followVisible=!0},addFollow:function(){this.followId="",this.title="",this.desc="",this.addOrEditTitle="新建状态",this.followVisible=!0},handleFollow:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(this.isLoading2=!0,""!=this.title){t.next=6;break}return this.isLoading2=!1,this.$message.destroy(),this.$message.warning("请填写状态名称"),t.abrupt("return",!1);case 6:return t.next=8,this.axios.post("custom-field/add-follow",{uid:localStorage.getItem("uid"),id:this.followId,title:this.title,describe:this.desc});case 8:e=t.sent,a=e.data,0!=a.error?(this.isLoading2=!1,this.$message.error(a.error_msg)):(this.followVisible=!1,this.isLoading2=!1,this.getFollowStatus());case 11:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),handleCancelFollow:function(){this.followId="",this.title="",this.desc="",this.addOrEditTitle="新建状态",this.followVisible=!1},getFollowStatus:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,a,i;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return this.isLoading1=!0,t.next=3,this.axios.post("custom-field/follow",{uid:localStorage.getItem("uid"),status:1});case 3:e=t.sent,a=e.data,0!=a.error?(this.isLoading1=!1,this.$message.error(a.error_msg)):(this.follows=a.data.follow,i=this,this.$nextTick((function(){for(var t=document.getElementsByClassName("follow-table")[0].getElementsByTagName("tr"),e=1;e<t.length;e++)t[e].setAttribute("draggable",!0),t[e].setAttribute("data-id",i.follows[e-1].id),t[e].addEventListener("dragstart",i.dragStart),t[e].addEventListener("drop",i.drop),t[e].addEventListener("dragover",i.dragOver)})),this.isLoading1=!1);case 6:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),dragOver:function(t){t.preventDefault()},dragStart:function(t){this.startId=t.currentTarget.attributes["data-id"].value},drop:function(t){var e=this;this.endId=t.currentTarget.attributes["data-id"].value;var a=this.follows.findIndex((function(t){return t.id==e.startId})),i=this.follows.findIndex((function(t){return t.id==e.endId}));if(i<a){for(var s=JSON.parse(JSON.stringify(this.follows[a])),n=a;n>i;n--)this.follows[n]=JSON.parse(JSON.stringify(this.follows[n-1]));this.follows[i]=JSON.parse(JSON.stringify(s))}else{for(var o=JSON.parse(JSON.stringify(this.follows[a])),r=a;r<i;r++)this.follows[r]=JSON.parse(JSON.stringify(this.follows[r+1]));this.follows[i]=JSON.parse(JSON.stringify(o))}var l=[],c=!0,d=!1,p=void 0;try{for(var u,f=this.follows[Symbol.iterator]();!(c=(u=f.next()).done);c=!0){var h=u.value;l.push(h.id)}}catch(m){d=!0,p=m}finally{try{c||null==f.return||f.return()}finally{if(d)throw p}}this.followSort(l)},followSort:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var a,i,s,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("custom-field/follow-sort",{uid:localStorage.getItem("uid"),ids:e});case 2:if(a=t.sent,i=a.data,0!=i.error)this.$message.error(i.error_msg);else{for(s=document.getElementsByClassName("follow-table")[0].getElementsByTagName("tr"),n=1;n<s.length;n++)s[n].removeEventListener("dragstart",this.dragStart),s[n].removeEventListener("drop",this.drop),s[n].removeEventListener("dragover",this.dragOver);this.getFollowStatus()}case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}()},created:function(){if("/corpCustomerAttribute/management"==this.$route.path)if(this.hasHignAttribute="hignAttribute",this.hasFollowStatus="followStatus",this.hasAddName="customerAttribute-add",this.hasEditName="customerAttribute-edit",this.hasListName="customerAttribute-list",this.hasDeleteName="customerAttribute-del",2==localStorage.getItem("isMasterAccount")){var t=localStorage.getItem("permissionButton").split(","),e=this;-1!=t.indexOf("hignAttribute")?e.tabKey="1":e.tabKey="2",e.$forceUpdate()}else this.tabKey="1";else if("/fansCustomerAttribute/management"==this.$route.path)if(this.hasHignAttribute="hignAttributeFans",this.hasFollowStatus="followStatusFans",this.hasAddName="customerFansAttribute-add",this.hasEditName="customerFansAttribute-edit",this.hasListName="customerFansAttribute-list",this.hasDeleteName="customerFansAttribute-del",2==localStorage.getItem("isMasterAccount")){var a=localStorage.getItem("permissionButton").split(","),i=this;-1!=a.indexOf("hignAttributeFans")?i.tabKey="1":i.tabKey="2",i.$forceUpdate()}else this.tabKey="1";this.getList(),this.getFollowStatus()}},h=f,m=(a("82a9"),a("2877")),g=Object(m["a"])(h,i,s,!1,null,"2fbac99c",null);e["default"]=g.exports},"82a9":function(t,e,a){"use strict";var i=a("aca3"),s=a.n(i);s.a},a7ac:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".tpl-title[data-v-2fbac99c]{float:left;font-size:16px;vertical-align:top}.content-bd[data-v-2fbac99c]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;margin:0 20px}[data-v-2fbac99c] .dark-row{background:#fafafa}[data-v-2fbac99c] .light-row{background:#fff}.content-msg[data-v-2fbac99c]{border:1px solid #ffdda6;background:#fff2db;padding:10px;text-align:left;margin:0 20px 20px}.optionVal-text[data-v-2fbac99c]{white-space:nowrap;text-overflow:ellipsis;overflow:hidden;width:200px;display:inline-block}.status-index[data-v-2fbac99c]{display:inline-block;width:40px;height:40px;line-height:38px;border-radius:20px;border:1px solid #1890ff;font-size:16px;color:#1890ff}.name-input[data-v-2fbac99c]{width:60%;margin-left:40px;margin-top:-11px}.add-btn[data-v-2fbac99c]{width:60%;margin-left:100px;text-align:center}.save-btn[data-v-2fbac99c]{margin:0 auto;text-align:center;margin-top:40px}.delete-btn[data-v-2fbac99c]{margin-left:20px;margin-top:-11px}.tabBtn[data-v-2fbac99c]{margin:0;margin-right:2px;padding:10px 16px;line-height:38px;background:#fafafa;border:1px solid #e8e8e8;border-bottom:0;border-radius:4px 4px 0 0;cursor:pointer}.activeBtn[data-v-2fbac99c]{color:#1890ff;background:#fff}.arrow-left[data-v-2fbac99c]{border:5px solid;border-color:transparent #1890ff transparent transparent}.arrow-left[data-v-2fbac99c],.arrow-right[data-v-2fbac99c]{vertical-align:middle;display:inline-block;width:0;height:0}.arrow-right[data-v-2fbac99c]{border:5px solid;border-color:transparent transparent transparent #1890ff}.line[data-v-2fbac99c]{display:inline-block;height:0;width:calc(100% - 10px);border-top:2px solid #1890ff;vertical-align:middle}.line-right[data-v-2fbac99c]{margin-right:4.17%;float:right}.line-left[data-v-2fbac99c],.line-right[data-v-2fbac99c]{height:20px;width:0;border-right:2px solid #1890ff}.line-left[data-v-2fbac99c]{margin-left:4.17%;float:left}.arrow-bottom[data-v-2fbac99c]{display:inline-block;color:#c3c3c3;-webkit-transform:rotate(90deg);transform:rotate(90deg);width:0;height:0;border:5px solid;border-color:transparent transparent transparent #1890ff}.arrow-bottom-right[data-v-2fbac99c]{line-height:10px;float:right;margin-right:calc(4.17% - 4px)}.arrow-bottom-left[data-v-2fbac99c]{line-height:9px;float:left;margin-left:calc(4.17% - 4px)}[data-v-2fbac99c] .ant-input-affix-wrapper .ant-input:not(:last-child){padding-right:50px}",""])},aca3:function(t,e,a){var i=a("a7ac");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=a("499e").default;s("726fb317",i,!0,{sourceMap:!1,shadowMode:!1})}}]);