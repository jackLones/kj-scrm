(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-509703d2"],{"117b":function(t,e,i){var s=i("8699");"string"===typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);var a=i("499e").default;a("517f9f6b",s,!0,{sourceMap:!1,shadowMode:!1})},"20d6":function(t,e,i){"use strict";var s=i("5ca1"),a=i("0a49")(6),r="findIndex",o=!0;r in[]&&Array(1)[r]((function(){o=!1})),s(s.P+s.F*o,"Array",{findIndex:function(t){return a(this,t,arguments.length>1?arguments[1]:void 0)}}),i("9c6c")(r)},"69f5":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAQAAABuvaSwAAAAY0lEQVR42r2SQQ1AIQxDkYIEJCFlacMdR+CIOeAnYKAkH7brO7x1DeHplFSSiLJycrJq8Nyrwb5gF43R0WXrW4OMgYF8cCDG/2kg0+mixs0asbGJTzlL46hItmATrS1afJvTB40bQb8Be38ZAAAAAElFTkSuQmCC"},8699:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".sider-content[data-v-7df8ec7e]{float:left;width:100%;width:calc(100% - 3px);width:-webkit-calc(100% - 3px);height:100%;max-height:100%;overflow:hidden}.scroll-content[data-v-7df8ec7e]{width:calc(100% - 40px);height:calc(100% - 162px);overflow:hidden;position:absolute;top:162px}.scro-right[data-v-7df8ec7e]{position:relative;float:right;height:100%;background-color:#fff}.scro-line[data-v-7df8ec7e],.scro-right[data-v-7df8ec7e]{width:3px;overflow:hidden}.scro-line[data-v-7df8ec7e]{position:absolute;z-index:1;top:0;right:0;border-radius:3px;background-color:#d3d3d3}.sider-one[data-v-7df8ec7e]{padding:0 20px;height:100%;overflow-y:hidden}.sider-one-txt[data-v-7df8ec7e]{height:60px;line-height:60px;text-align:left;overflow-y:hidden}.team[data-v-7df8ec7e]{width:100%;padding:0 10px}.team[data-v-7df8ec7e],.team-type[data-v-7df8ec7e]{height:50px;line-height:50px}.team-type[data-v-7df8ec7e]{display:inline-block;margin-right:20px;min-width:60px;text-align:center}.team-add[data-v-7df8ec7e]{width:calc(100% - 3px);height:50px;line-height:50px;background-color:#f5f5f5}.team-name[data-v-7df8ec7e]{display:inline-block;width:160px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}.team-add-title[data-v-7df8ec7e]{display:inline-block;margin-left:76px}.team-add-icon[data-v-7df8ec7e]{float:right;color:#1e90ff;margin-top:18px;margin-right:20px;cursor:pointer}.team-operation[data-v-7df8ec7e]{margin-top:4px;cursor:pointer;margin-bottom:8px!important}.team-operation1[data-v-7df8ec7e]{margin-top:4px;cursor:pointer;margin-bottom:4px!important}.team-operation[data-v-7df8ec7e]:hover{color:#1890ff}.active-key[data-v-7df8ec7e]{border-bottom:2px solid #1e90ff;color:#1e90ff}.active[data-v-7df8ec7e]{background:#1e90ff;color:#fff}[data-v-7df8ec7e] .ant-popover-inner .ant-popover-inner-content .ant-popover-buttons,[data-v-7df8ec7e] .ant-tooltip-placement-bottom .ant-tooltip-arrow{display:none!important}",""])},a426:function(t,e,i){"use strict";var s=i("117b"),a=i.n(s);a.a},f467:function(t,e,i){"use strict";var s=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"sider-one"},[s("div",{staticClass:"sider-one-txt"},[t._v("选择企业微信")]),s("a-select",{staticStyle:{width:"200px","margin-bottom":"20px"},attrs:{showSearch:"",optionFilterProp:"children"},on:{change:t.handleChange},model:{value:t.corpId,callback:function(e){t.corpId=e},expression:"corpId"}},[t._l(t.corpInfo,(function(e){return[s("a-select-option",{attrs:{value:e.corpid}},[t._v(t._s(e.corp_full_name||e.corp_name))])]}))],2),s("div",{staticClass:"team-add"},[s("span",{staticClass:"team-add-title"},[t._v("分组管理")]),s("a-icon",{directives:[{name:"has",rawName:"v-has",value:t.hasAddName,expression:"hasAddName"}],staticClass:"team-add-icon",attrs:{type:"plus"},on:{click:t.addTeamModal}})],1),s("div",{directives:[{name:"has",rawName:"v-has",value:t.hasGroupName,expression:"hasGroupName"}],ref:"scroll",staticClass:"sider-content scroll-content",on:{mousewheel:t.scrollWheel,mouseover:t.scrollOver,mouseout:t.scrollOut,mousemove:t.scroLineMove,mouseup:t.scroLineUp}},[s("div",{ref:"scroLeft",staticClass:"sider-content"},[s("div",{staticStyle:{width:"100%"}},[2==t.type?s("div",{staticClass:"team",class:{active:0==t.teamId},on:{click:function(e){return t.selectTeam(0,"所有分组")}}},[s("label",{staticClass:"team-name"},[t._v("所有分组")])]):t._e(),s("vuedraggable",{on:{end:t.changeGroupSort},model:{value:t.teamsList1,callback:function(e){t.teamsList1=e},expression:"teamsList1"}},[t._l(t.teamsList1,(function(e,a){return[s("div",{staticClass:"team",class:{active:t.teamId==e.id},on:{click:function(i){return t.selectTeam(e.id,e.group_name)},mouseleave:t.hideTip,mouseover:function(i){return t.showOperation(e.id)}}},[s("label",{staticClass:"team-name"},[t._v(t._s(e.group_name))]),t.mouseOverId==e.id&&"未分组"!=e.group_name&&t.isShowPop?s("a-popover",{staticStyle:{display:"inline-block",float:"right",cursor:"pointer"},attrs:{placement:"right",trigger:"hover"}},[s("template",{slot:"content"},[s("div",{staticStyle:{width:"100%","text-align":"center"},on:{mouseover:t.showTip,mouseleave:t.hidePopover}},[s("p",{directives:[{name:"has",rawName:"v-has",value:t.hasEditName,expression:"hasEditName"}],staticClass:"team-operation",on:{click:function(e){return t.editTeamName(a)}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t编辑小组")]),s("p",{directives:[{name:"has",rawName:"v-has",value:t.hasDeleteName,expression:"hasDeleteName"}],staticClass:"team-operation1",on:{click:function(i){return t.removeTeam(e.id,e.group_name)}}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t删除小组")])])]),s("img",{staticStyle:{float:"right","vertical-align":"middle","margin-top":"14px"},attrs:{src:i("69f5")}})],2):t._e()],1)]}))],2)],1)]),s("div",{ref:"scroRight",staticClass:"scro-right",on:{mouseup:t.jumpScroll}},[s("div",{directives:[{name:"show",rawName:"v-show",value:t.scrollFlag&&t.scroRight,expression:"scrollFlag && scroRight"}],ref:"scroLine",staticClass:"scro-line",on:{mousedown:t.scroLineDown}})])]),s("a-modal",{attrs:{title:t.title,destroyOnClose:!0,visible:t.visible,confirmLoading:t.confirmLoading},on:{ok:t.handleOk,cancel:t.handleCancel}},[s("a-form-item",{staticClass:"current0",attrs:{"label-col":{span:4},"wrapper-col":{span:20}}},[s("template",{slot:"label"},[s("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("分组名称\n\t\t\t")]),s("a-input",{attrs:{placeholder:"请输入分组名(不得超过15个字符)",maxLength:15},model:{value:t.teamName,callback:function(e){t.teamName=e},expression:"teamName"}},[s("span",{attrs:{slot:"suffix"},slot:"suffix"},[s("span",[t._v(t._s(t.teamName.length))]),t._v("/15\n\t\t\t\t\t\t\t\t\t\t\t")])])],2),0!=t.type||t.isEdit?t._e():s("a-form-item",{staticClass:"current0",attrs:{"label-col":{span:4},"wrapper-col":{span:20}}},[s("template",{slot:"label"},[s("span",{staticStyle:{color:"red"}},[t._v("*")]),t._v("标签名称\n\t\t\t")]),s("p",{staticStyle:{margin:"0px","font-size":"13px",color:"#909399"}},[t._v("\n\t\t\t\t每个标签名称最多15个字。同时新建多个标签时，请用“空格”隔开\n\t\t\t")]),s("a-input",{attrs:{placeholder:"请输入标签（不得超过15个字符）"},model:{value:t.inputValue,callback:function(e){t.inputValue=e},expression:"inputValue"}})],2)],1)],1)},a=[],r=(i("28a5"),i("a481"),i("96cf"),i("3b8d")),o=i("b76a"),n=i.n(o),c={name:"team",components:{vuedraggable:n.a},props:{callback:{type:Function,default:null}},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{mouseEnterFlag:!1,startY:0,scrollFlag:!1,scroRight:!0,scrollOutFlag:!1,corpInfo:[],corpId:t,suite_id:1,key:1,visible:!1,editGroupId:"",title:"新建分组",teamsList:[],teamsList1:[],isEdit:!1,timeOut:"",teamId:"",mouseOverId:"",editFlag:!1,confirmLoading:!1,type:1,teamName:"",hasDeleteName:"",hasEditName:"",hasAddName:"",hasGroupName:"",isShowPop:!0,inputValue:"",newTagList:[]}},created:function(){"/label/list"==this.$route.path?(this.type=1,this.hasDeleteName="work-tag-group-delete",this.hasEditName="work-tag-group-edit",this.hasAddName="work-tag-group-add",this.hasGroupName="work-tag-group"):"/customTags/list"==this.$route.path?(this.type=0,this.hasDeleteName="client-tag-group-del",this.hasEditName="client-tag-group-edit",this.hasAddName="client-tag-group-add",this.hasGroupName="client-tag-group"):"/group/list"==this.$route.path&&(this.type=2,this.hasDeleteName="groupList-del",this.hasEditName="groupList-edit",this.hasAddName="groupList-add",this.hasGroupName="groupList-group"),""==this.hasDeleteName&&""==this.hasEditName&&(this.isShowPop=!1)},mounted:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.corpInfo=e,t.$emit("changeCorpId",t.corpId),t.getTeamsList();var i=new MutationObserver(t.setScroLineHeight);i.observe(t.$refs.scroLine,{attributes:!0,attributeFilter:["style"],attributeOldValue:!0}),t.setScroLineHeight(),t.initFirefoxScroll()}))},methods:{changeGroupSort:function(){this.sortGroup()},sortGroup:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,i,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=[],this.teamsList1.map((function(t){e.push(t.id)})),i="",2==this.type?i="work-chat/chat-group-sort":1!=this.type&&0!=this.type||(i="work-tag-group/group-sort"),t.next=6,this.axios.post(i,{ids:e,isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id")});case 6:s=t.sent,s.data,this.getTeamsList();case 9:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),showOperation:function(t){clearTimeout(this.timeOut),this.mouseOverId=t},showTip:function(){clearTimeout(this.timeOut)},hideTip:function(){var t=this;this.timeOut=setTimeout((function(){t.mouseOverId=""}),500)},hidePopover:function(){this.mouseOverId=""},addTeamModal:function(){this.title="新增分组",this.teamName="",this.isEdit=!1,this.visible=!0},handleOk:function(){if(String.prototype.Trim=function(){return this.replace(/(^\s*)|(\s*$)/g,"")},this.teamName=this.teamName.Trim(),!this.teamName)return this.$message.error("请输入分组名"),!1;this.editGroupId?this.sureRemark():this.addTeam()},addTeam:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,i,s,a,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(e="",2!=this.type){t.next=5;break}e="work-chat/chat-group-add",t.next=27;break;case 5:if(1!=this.type&&0!=this.type){t.next=27;break}if(e="work-tag-group/add",0!=this.type){t.next=27;break}if(""!=this.inputValue){t.next=13;break}return this.$message.warning("标签名称不能为空"),t.abrupt("return",!1);case 13:i=JSON.parse(JSON.stringify(this.inputValue)).split(" "),this.newTagList=[],s=0;case 16:if(!(s<i.length)){t.next=27;break}if(!(i[s].length>15)){t.next=23;break}return this.$message.error("每个标签最多15个字"),this.inputValue="",t.abrupt("return",!1);case 23:i[s].length>0&&this.newTagList.push(i[s]);case 24:s++,t.next=16;break;case 27:return t.next=29,this.axios.post(e,{corp_id:this.corpId,id:this.editGroupId,suite_id:this.suite_id,name:this.teamName,type:this.type,isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id"),tag_name:this.newTagList});case 29:a=t.sent,r=a.data,0!=r.error?this.$message.error(r.error_msg):(this.teamName="",this.newTagList=[],this.inputValue="",this.visible=!1,this.getTeamsList());case 32:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),handleCancel:function(){this.editGroupId="",this.newTagList=[],this.inputValue="",this.visible=!1},handleChange:function(t){var e=this;this.corpInfo.map((function(i){t==i.corpid&&e.$store.dispatch("changeCorp",i)})),this.isLoading=!0,this.teamId="",this.getTeamsList(),2==localStorage.getItem("isMasterAccount")&&this.$store.dispatch("getPermissionButton")},changetype:function(t){this.key=t},selectTeam:function(t,e){if(t==this.teamId)return!1;this.editFlag=!1,this.teamId=t,this.teamName=e,null!==this.callback&&"function"===typeof this.callback&&this.callback(this.corpId,this.suite_id,this.type,this.teamId,e)},sureRemark:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,i,s;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e="",2==this.type?e="work-chat/chat-group-update":1!=this.type&&0!=this.type||(e="work-tag-group/update"),t.next=4,this.axios.post(e,{id:this.editGroupId,name:this.teamName,type:this.type,isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id")});case 4:i=t.sent,s=i.data,0!=s.error?this.$message.error(s.error_msg):(this.editGroupId="",this.visible=!1,this.getTeamsList());case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),editTeamName:function(t){this.mouseOverId="",this.editGroupId=this.teamsList[t].id,this.title="编辑分组",this.teamName=this.teamsList[t].group_name,this.isEdit=!0,this.visible=!0},removeTeam:function(t,e){this.mouseOverId="";var i=this;i.$confirm({title:"【"+e+"】一旦删除，归属于该分组的"+(2==this.type?"群":"标签")+"都将被移至【未分组】，确定删除分组吗??",okText:"确定",okType:"primary",cancelText:"取消",onOk:function(){i.delTeam(t)}})},delTeam:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var i,s,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return i="",2==this.type?i="work-chat/chat-group-delete":1!=this.type&&0!=this.type||(i="work-tag-group/delete"),t.next=4,this.axios.post(i,{id:e,corp_id:this.corpId,isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id")});case 4:s=t.sent,a=s.data,0!=a.error?this.$message.error(a.error_msg):this.getTeamsList(e);case 7:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),getTeamsList:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var i,s,a,r;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return i={},s="",0==this.type||1==this.type?(i={corp_id:this.corpId,type:this.type,suite_id:this.suite_id,isMasterAccount:localStorage.getItem("isMasterAccount"),sub_id:localStorage.getItem("sub_id")},s="work-tag-group/list"):2==this.type&&(i={corp_id:this.corpId},s="work-chat/chat-group-list"),t.next=5,this.axios.post(s,i);case 5:a=t.sent,r=a.data,0!=r.error?(this.isLoading=!1,this.$message.error(r.error_msg)):(this.isLoading=!1,this.teamsList=r.data.info,this.teamsList1=JSON.parse(JSON.stringify(this.teamsList)),this.teamId&&this.teamId!=e||(2!=this.type?(this.teamId=this.teamsList[0].id,this.teamName=this.teamsList[0].group_name):(this.teamId=0,this.teamName="所有分组")),null!==this.callback&&"function"===typeof this.callback&&this.callback(this.corpId,this.suite_id,this.type,this.teamId,this.teamName));case 8:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),setScroLineHeight:function(){var t=this,e=0;e=t.$refs.scroLeft.clientHeight,e>=t.$refs.scroLeft.scrollHeight?t.scroRight=!1:(t.$refs.scroLine.style.height=(e-50)/t.$refs.scroLeft.scrollHeight*(t.$refs.scroRight.scrollHeight-50)+"px",t.scroRight=!0)},initFirefoxScroll:function(){var t=this;document.addEventListener&&document.addEventListener("DOMMouseScroll",(function(e){var i=window.event||e,s=t.$refs.scroLeft.scrollTop,a=t.$refs.scroLeft.scrollHeight-t.$refs.scroLeft.clientHeight;i.detail>=0?s+=80:s-=80,s<0&&(s=0),s>a-50&&(s=a),t.$refs.scroLeft.scrollTop=s;var r=s/a*this.$refs.scroRight.clientHeight-this.$refs.scroLine.clientHeight;s>0&&s<a-50&&(r=s/this.$refs.scroLeft.scrollHeight*this.$refs.scroRight.clientHeight),r>this.$refs.scroRight.clientHeight-this.$refs.scroLine.clientHeight&&(r=this.$refs.scroRight.clientHeight-this.$refs.scroLine.clientHeight),r<0&&(r=0),t.$refs.scroLine.style.top=r+"px"}),!1)},scrollWheel:function(){var t=t||(window.event?window.event:null),e=this.$refs.scroLeft.scrollTop,i=this.$refs.scroLeft.scrollHeight-this.$refs.scroLeft.clientHeight;t.wheelDelta,e-=.5*t.wheelDelta,e<0&&(e=0),e>i&&(e=i),this.$refs.scroLeft.scrollTop=e;var s=e/i*this.$refs.scroRight.clientHeight-this.$refs.scroLine.clientHeight;e>0&&e<i&&(s=e/this.$refs.scroLeft.scrollHeight*this.$refs.scroRight.clientHeight),s<0&&(s=0),this.$refs.scroLine.style.top=s+"px"},jumpScroll:function(){var t=this;if(!t.mouseEnterFlag){var e=e||(window.event?window.event:null),i=t.$refs.scroLeft.scrollTop,s=t.$refs.scroLeft.scrollHeight-t.$refs.scroLeft.clientHeight;e.y,t.startY,i+=(e.y-t.startY)/t.$refs.scroLeft.clientHeight*t.$refs.scroLeft.scrollHeight,t.$refs.scroLeft.scrollTop=i,i<0&&(i=0),i>s&&(i=s);var a=i/s*this.$refs.scroRight.clientHeight-this.$refs.scroLine.clientHeight;i>0&&i<s&&(a=i/this.$refs.scroLeft.scrollHeight*this.$refs.scroRight.clientHeight),a<0&&(a=0),t.$refs.scroLine.style.top=a+"px",t.startY=e.y}},scroLineDown:function(){this.mouseEnterFlag=!0,this.startY=event.y,window.addEventListener&&(window.addEventListener("mouseup",this.scroLineUp,!1),window.addEventListener("mousemove",this.scroLineMove,!1)),this.$emit("banUserSelect")},scroLineMove:function(){var t=this;if(t.mouseEnterFlag){var e=e||(window.event?window.event:null),i=t.$refs.scroLeft.scrollTop,s=t.$refs.scroLeft.scrollHeight-t.$refs.scroLeft.clientHeight,a=(e.y-t.startY)/t.$refs.scroLeft.clientHeight*t.$refs.scroLeft.scrollHeight;i+=a,t.$refs.scroLeft.scrollTop=i,i<0&&(i=0),i>s&&(i=s);var r=i/s*this.$refs.scroRight.clientHeight-this.$refs.scroLine.clientHeight;i>0&&i<s&&(r=i/this.$refs.scroLeft.scrollHeight*this.$refs.scroRight.clientHeight),r<0&&(r=0),t.$refs.scroLine.style.top=r+"px",t.startY=e.y}},scroLineUp:function(){if(1==this.mouseEnterFlag){var t=t||(window.event?window.event:null);this.mouseEnterFlag=!1,this.changeWidthSmallFlag&&(this.changeWidthSmall(),this.changeWidthSmallFlag=!1),this.scrollOutFlag&&(this.scrollOut(),this.scrollOutFlag=!1),window.removeEventListener&&(window.removeEventListener("mouseup",this.scroLineUp,!1),window.removeEventListener("mousemove",this.scroLineMove,!1)),this.$emit("userSelect")}},scrollOver:function(){this.scrollFlag=!0},scrollOut:function(){this.mouseEnterFlag||(this.scrollFlag=!1),this.scrollOutFlag=!0}}},l=c,h=(i("a426"),i("2877")),d=Object(h["a"])(l,s,a,!1,null,"7df8ec7e",null);e["a"]=d.exports}}]);