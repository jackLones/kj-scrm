(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-5a3540d7"],{"168b":function(t,e,i){"use strict";i.r(e);var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"guide"},[i("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[i("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",left:"0",top:"0",bottom:"0",right:"0","overflow-x":"hidden","overflow-y":"auto"}},[i("a-layout-header",[t._v("导购管理")]),i("a-layout-content",[i("div",{staticStyle:{padding:"15px 20px",background:"#FFF"}},[i("div",{staticClass:"content-msg"},[i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t1、导购是本企业的已实名员工，需要是已激活未删除状态，且拥有外部联系人权限，当前未离职。\n\t\t\t\t\t\t")]),i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t2、建议先前往"),i("router-link",{attrs:{to:"/store/list"}},[t._v("【门店管理】")]),t._v("页面设置门店和员工，会显示员工和所属门店，如果一个员工同时在两个门店是作为两个导购处理。\n\t\t\t\t\t\t")],1),i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t3、顾客关联导购之后，导购可以在企微侧边栏菜单对顾客进行相应的操作。企微顾客可以根据相关规则自动关联，点击"),i("router-link",{attrs:{to:"/shopCustom/guideSet"}},[t._v("【导购设置】")]),t._v("。\n\t\t\t\t\t\t")],1),i("p",{staticStyle:{"margin-bottom":"2px"}},[t._v("\n\t\t\t\t\t\t\t4、一个顾客可能会关联多个导购人员。\n\t\t\t\t\t\t")])]),i("div",{staticClass:"content-hd"},[i("span",{staticClass:"select-option"},[0==t.storeIds.length?i("a-button",{staticStyle:{width:"210px","margin-right":"5px"},on:{click:t.showStoreModal}},[i("span",[t._v("选择门店")])]):t._e(),t.storeIds.length>0?[i("a-popover",{attrs:{placement:"right"}},[i("span",{attrs:{slot:"content"},slot:"content"},[i("div",{staticStyle:{"max-width":"375px"}},t._l(t.storeDetail,(function(e,a){return i("a-tag",{key:a,staticStyle:{display:"inline-block",margin:"0 10px 5px 0"},attrs:{color:"orange"}},[t._v("\n\t\t\t\t\t\t\t\t\t\t\t\t"+t._s(e.title)+"\n\t\t\t\t\t\t\t\t\t\t\t")])})),1)]),i("a-button",{staticStyle:{width:"210px","margin-right":"5px"},on:{click:t.showStoreModal}},[t._v("\n\t\t\t\t\t\t\t\t\t\t已选择"+t._s(t.storeIds.length)+"个门店\n\t\t\t\t\t\t\t\t\t")])],1)]:t._e()],2),i("span",{staticClass:"select-option"},[i("label",[t._v("快速搜索：")]),i("a-input",{attrs:{placeholder:"员工姓名/昵称/手机号"},on:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.searchStaff(e)}},model:{value:t.keyWord,callback:function(e){t.keyWord=e},expression:"keyWord"}})],1),t.roleArr.length>0?i("span",{staticClass:"select-option"},[i("label",[t._v("角色选择：")]),i("a-select",{attrs:{showSearch:"",optionFilterProp:"children",placeholder:"角色选择"},model:{value:t.roleVal,callback:function(e){t.roleVal=e},expression:"roleVal"}},[i("a-select-option",{attrs:{value:-1}},[t._v("角色选择")]),t._l(t.roleArr,(function(e,a){return i("a-select-option",{key:a,attrs:{value:a}},[t._v(t._s(e))])}))],2)],1):t._e(),i("span",{staticClass:"select-option"},[i("a-button",{staticStyle:{"margin-right":"5px"},attrs:{type:"primary"},on:{click:t.searchStaff}},[t._v("查找")]),i("a-button",{staticStyle:{"margin-right":"10px"},on:{click:t.reset}},[t._v("清空")])],1)]),i("div",{staticClass:"content-bd"},[i("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading}},[i("a-table",{attrs:{rowSelection:t.rowSelection,columns:t.columns,dataSource:t.customList,pagination:!1},scopedSlots:t._u([{key:"custom_amount",fn:function(e,a){return i("div",{},[i("router-link",{attrs:{to:"/shopCustom/CustomManage?guide_id="+a.id+"&store_id="+a.store_id}},[t._v(t._s(e))])],1)}},{key:"handle",fn:function(e,a){return[a.qc_url?i("a-button",{on:{click:function(e){return t.downCode(a.qc_url,1)}}},[t._v("下载码")]):i("a-button",[t._v("暂无下载码")])]}}])}),i("div",{staticStyle:{padding:"0 15px"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticStyle:{margin:"20px 0px","line-height":"32px"}},[i("a-checkbox",{on:{click:t.batchTypeChange},model:{value:t.batchTypeValue,callback:function(e){t.batchTypeValue=e},expression:"batchTypeValue"}}),t._v("\n\t\t\t\t\t\t\t\t\t当前页\n\t\t\t\t\t\t\t\t\t"),i("a-button",{staticStyle:{"margin-right":"5px"},attrs:{type:"primary",disabled:!(this.selectedRowKeys.length>0)},on:{click:function(e){return t.downCode(t.codeId)}}},[t._v("下载码\n\t\t\t\t\t\t\t\t\t")])],1),i("div",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticClass:"pagination",staticStyle:{margin:"20px 0px",overflow:"hidden"}},[i("div",{staticClass:"pagination",staticStyle:{display:"inline-block",height:"32px",float:"right"}},[i("a-pagination",{attrs:{total:t.total,showSizeChanger:"",showQuickJumper:t.quickJumper,current:t.page,pageSize:t.pageSize,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage,showSizeChange:t.showSizeChange}})],1)])])],1)],1)]),i("chooseDepartment",{ref:"user",attrs:{id:t.corpId,show:t.showModalDepartment,chooseNum:t.chooseNum,callback:t.modalVisibleChange,is_special:1}}),i("store-list",{attrs:{groupVisible:t.storeVisible,storeIds:JSON.parse(JSON.stringify(t.storeIds)),storeDetail:JSON.parse(JSON.stringify(t.storeDetail))},on:{setGroupId:t.setGroupId}})],1)],1)],1)],1)},s=[],o=(i("28a5"),i("6762"),i("2fdb"),i("6b54"),i("96cf"),i("3b8d")),n=i("c75b"),r=i("7528"),c=[{title:"姓名",dataIndex:"name",key:"name"},{title:"类型",dataIndex:"type",key:"type"},{title:"所属门店",dataIndex:"group_name",key:"group_name",ellipsis:!0},{title:"顾客总数",dataIndex:"num",key:"num",scopedSlots:{customRender:"custom_amount"}},{title:"本月关联顾客",dataIndex:"month_num",key:"month_num"},{title:"操作",scopedSlots:{customRender:"handle"}}],l={name:"guideSet",components:{chooseDepartment:n["a"],storeList:r["a"]},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpId:t,showModalDepartment:!1,guideList:[],guideListId:[],chooseNum:0,chooseUserNum:0,chooseDepartmentNum:0,keyWord:"",roleVal:-1,roleArr:["销售","店长","副店长"],isLoading:!1,columns:c,customList:[],page:1,pageSize:15,total:1,quickJumper:!1,batchTypeValue:!1,selectedRowKeys:[],checkArr:[],checkList:{},codeId:[],storeVisible:!1,storeIds:[],storeDetail:[]}},methods:{showDepartmentList:function(){this.$refs.user.rightIdList=JSON.parse(JSON.stringify(this.guideListId)),this.$refs.user.rightList=JSON.parse(JSON.stringify(this.guideList)),this.showModalDepartment=!0},modalVisibleChange:function(t,e,i,a,s){"ok"==t&&(this.guideList=s,this.guideListId=e,this.chooseNum=parseInt(i)+parseInt(a),this.chooseUserNum=i,this.chooseDepartmentNum=a),this.showModalDepartment=!1},searchStaff:function(){this.getCustomList()},reset:function(){var t=this;t.guideList=[],t.guideListId=[],t.chooseNum=0,t.chooseUserNum=0,t.chooseDepartmentNum=0,t.keyWord="",t.roleVal=-1,t.storeIds=[],t.storeDetail=[],t.getCustomList()},getCustomList:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(){var e,i,a,s,o,n,r=this,c=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=c.length>0&&void 0!==c[0]?c[0]:1,i=c.length>1&&void 0!==c[1]?c[1]:this.pageSize,this.isLoading=!0,a=this,s={corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",page:e,page_size:i,guide_keyword:a.keyWord,operator:1,store_id:a.storeIds.toString()},t.next=7,this.axios.post("shop-customer-guide/list",s);case 7:o=t.sent,n=o.data,0!=n.error?(this.isLoading=!1,this.$message.error(n.error_msg)):(this.isLoading=!1,this.roleArr=[],this.customList=n.data.result,this.total=parseInt(n.data.count),this.page=e,this.pageSize=i,this.quickJumper=this.total>this.pageSize,this.checkArr=[],this.customList.map((function(t){r.checkArr.push(t.key)})),this.batchTypeValue=!1,this.selectedRowKeys.length>0&&(this.batchTypeValue=!0,this.checkArr.map((function(t){-1==r.selectedRowKeys.indexOf(t)&&(r.batchTypeValue=!1)}))));case 10:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changePage:function(t,e){this.getCustomList(t,e),this.$nextTick((function(){document.getElementsByClassName("scroll")[0].scrollTo(0,40)}))},showSizeChange:function(t,e){this.getCustomList(1,e)},onSelectChange:function(t,e){var i=this;this.selectedRowKeys=t,this.batchTypeValue=this.checkArr.every((function(e){return t.includes(e)})),this.checkList=e,this.codeId=[],this.checkList.map((function(t){i.codeId.push(t.code_id)}))},batchTypeChange:function(){var t=this;this.batchTypeValue?(this.selectedRowKeys=[],this.checkList=[],this.codeId=[]):(this.selectedRowKeys=this.checkArr,this.checkList=this.customList,this.checkList.map((function(e){t.codeId.push(e.code_id)})))},downCode:function(){var t=Object(o["a"])(regeneratorRuntime.mark((function t(e,i){var a,s,o,n,r,c,l,d,h;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:if(a=this,a.loading=!0,1!=i){t.next=13;break}s=this.$store.state.commonUrl+e,o=new Image,n=s.split("/"),r="qc.png",n.length>0&&(r=n[n.length-1]),o.setAttribute("crossOrigin","anonymous"),o.src=s,o.onload=function(){var t=document.createElement("canvas");t.width=200,t.height=200;var e=t.getContext("2d");e.drawImage(o,0,0,200,200),t.toBlob((function(t){var e=URL.createObjectURL(t);a.download(e,r),URL.revokeObjectURL(e)}))},t.next=20;break;case 13:return c={corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",code_id:e.toString()},l="shop-customer-guide/download-code",t.next=17,a.axios.post(l,c);case 17:d=t.sent,h=d.data,0!=h.error?(a.loading=!1,a.$message.error(h.error_msg)):(a.loading=!1,window.open(h.data.url));case 20:case"end":return t.stop()}}),t,this)})));function e(e,i){return t.apply(this,arguments)}return e}(),download:function(t,e){var i=document.createElement("a");i.download=e,i.href=t,i.click(),i.remove()},showStoreModal:function(){this.storeVisible=!0},setGroupId:function(t,e,i){"ok"==t&&(this.storeIds=JSON.parse(JSON.stringify(e)),this.storeDetail=JSON.parse(JSON.stringify(i))),this.storeVisible=!1}},computed:{rowSelection:function(){var t=this.selectedRowKeys,e=this;return{selectedRowKeys:t,onChange:this.onSelectChange,hideDefaultSelections:!0,onSelection:e.onSelection}}},mounted:function(){var t=this;this.$store.dispatch("getCorp",(function(e){t.getCustomList()}))}},d=l,h=(i("47a2"),i("2877")),p=Object(h["a"])(d,a,s,!1,null,"8700080c",null);e["default"]=p.exports},"47a2":function(t,e,i){"use strict";var a=i("a808"),s=i.n(a);s.a},4926:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".tab[data-v-637129e4]{width:50%;display:inline-block;text-align:center;height:40px;line-height:40px}.active[data-v-637129e4]{background-color:#1890ff;color:#fff}.team-add[data-v-637129e4]{width:100%;height:50px;line-height:50px}.team-add-icon[data-v-637129e4],.team-add-title[data-v-637129e4]{display:inline-block;margin-left:10px}.team-add-icon[data-v-637129e4]{color:#1e90ff;cursor:pointer;vertical-align:initial}[data-v-637129e4] .ant-tree-node-content-wrapper{display:inline-block;width:calc(100% - 25px)}[data-v-637129e4] .ant-tree-switcher-icon{vertical-align:initial}[data-v-637129e4] .ant-tree-node-content-wrapper.ant-tree-node-selected{background-color:#fff!important}[data-v-637129e4] .ant-popover-inner .ant-popover-inner-content .ant-popover-buttons{display:none!important}.more-operation[data-v-637129e4]:hover{color:#1890ff}[data-v-637129e4] .ant-tree li .ant-tree-node-content-wrapper:hover{background-color:#fff}.ant-tree li span[draggable=true][data-v-637129e4],[data-v-637129e4] .ant-tree li span[draggable]{line-height:25px;height:30px}[data-v-637129e4] .ant-tree li span.ant-tree-switcher{vertical-align:sub}.operation[data-v-637129e4]{margin-bottom:8px;cursor:pointer}.operation2[data-v-637129e4]{margin-bottom:0;cursor:pointer}.operation1[data-v-637129e4]{margin-bottom:0;color:#e2e2e2}.operation[data-v-637129e4]:hover{color:#1890ff}[data-v-637129e4] li.ant-tree-treenode-disabled>.ant-tree-node-content-wrapper span{color:rgba(0,0,0,.65)!important}.store-list[data-v-637129e4]{max-width:350px;margin-top:10px}.store-item[data-v-637129e4]{margin:5px 0;cursor:pointer}.store-spin[data-v-637129e4]{position:fixed;margin-top:140px;margin-left:160px;z-index:9999}.spin-zhezhao[data-v-637129e4]{position:fixed;z-index:999;width:417px;height:350px;margin-top:-10px;background-color:rgba(0,0,0,.04)}",""])},"68a1":function(t,e,i){var a=i("4926");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var s=i("499e").default;s("261dc6ec",a,!0,{sourceMap:!1,shadowMode:!1})},7528:function(t,e,i){"use strict";var a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[t.visible?i("a-modal",{attrs:{width:"888px!important",title:"选择门店"},on:{cancel:t.cancelGroup},model:{value:t.visible,callback:function(e){t.visible=e},expression:"visible"}},[i("template",{slot:"footer"},[i("a-button",{key:"back",on:{click:t.cancelGroup}},[t._v("取消")]),i("a-button",{key:"submit",attrs:{type:"primary"},on:{click:t.updateGroup}},[t._v("确定\n\t\t\t")])],1),i("div",{staticStyle:{display:"inline-block","border-right":"1px solid #E2E2E2",width:"50%"}},[i("a-input",{staticStyle:{width:"230px",display:"block"},attrs:{placeholder:"请输入门店名称或地址进行查找"},on:{input:t.selectStore},model:{value:t.searchName,callback:function(e){t.searchName=e},expression:"searchName"}}),i("div",{staticClass:"group-tree2",staticStyle:{height:"350px",overflow:"auto"},on:{scroll:t.handleScroll}},[i("div",{directives:[{name:"show",rawName:"v-show",value:""!=t.searchName,expression:"searchName != ''"}],staticClass:"store-list"},[i("a-empty",{directives:[{name:"show",rawName:"v-show",value:0==t.storeList.length&&!t.isLoading,expression:"storeList.length == 0 && !isLoading"}]}),i("a-spin",{staticClass:"store-spin",attrs:{tip:"Loading...",spinning:t.isLoading}}),t.isLoading?i("div",{staticClass:"spin-zhezhao"}):t._e(),t._l(t.storeList,(function(e){return i("div",{staticClass:"store-item",on:{click:function(i){return t.selectId(e.id+"-s",e.shop_name,e.status)}}},[t._v("\n\t\t\t\t\t\t"+t._s(e.shop_name)),0==e.status?[t._v("（门店已关闭）")]:t._e(),i("span",{staticStyle:{color:"rgba(0, 0, 0, 0.3)"}},[t._v("（"+t._s(e.name)+"）")]),t.ids.includes(e.id+"-s")?i("a-icon",{staticStyle:{color:"#1890FF","margin-left":"10px"},attrs:{type:"check"}}):t._e()],2)}))],2),""==t.searchName?i("a-tree",{staticClass:"draggable-tree",attrs:{treeData:t.gData,"load-data":t.onLoadData},on:{select:t.onselect},scopedSlots:t._u([{key:"custom",fn:function(e){var a=e.title,s=e.count,o=e.key,n=e.store;return[t._v("\n\t\t\t\t\t\t"+t._s(a)),n?t._e():[t._v("（"+t._s(s)+"）")],t.ids.includes(o)?i("a-icon",{staticStyle:{color:"#1890FF","margin-left":"10px"},attrs:{type:"check"}}):t._e()]}}],null,!1,3951647589)}):t._e()],1)],1),i("div",{staticStyle:{display:"inline-block",width:"calc(50% - 5px)",height:"390px",overflow:"auto",padding:"4px 20px 10px 20px"}},[t._v("\n\t\t\t已选择的门店\n\t\t\t"),t._l(t.stores,(function(e,a){return i("div",{staticStyle:{margin:"10px 0"}},[t._v("\n\t\t\t\t"+t._s(e.title)+"\n\t\t\t\t"),i("a-icon",{staticStyle:{cursor:"pointer","margin-left":"5px",color:"rgba(0, 0, 0, 0.65)",float:"right"},attrs:{type:"close"},on:{click:function(e){return t.deleteStore(a)}}})],1)}))],2)],2):t._e()],1)},s=[],o=(i("20d6"),i("75fc")),n=(i("ac6a"),i("5df3"),i("96cf"),i("3b8d")),r={name:"Team",props:{groupVisible:{type:Boolean,default:!1},storeIds:{type:Array,default:function(){return[]}},storeDetail:{type:Array,default:function(){return[]}}},watch:{storeIds:function(t,e){this.ids=t},storeDetail:function(t,e){this.stores=t},groupVisible:function(t,e){this.visible=t,this.visible&&this.getGroupList()}},data:function(){var t=localStorage.getItem("corpId")?localStorage.getItem("corpId"):"";return{corpId:t,searchName:"",ids:"",stores:[],gData:[],groupData:[],visible:!1,timeInter:0,isLoading:!1,storeList:[],page:1,count:0}},mounted:function(){this.ids=this.storeIds,this.stores=this.storeDetail,this.visible=this.groupVisible,this.getGroupList()},methods:{handleScroll:function(){if(""!=this.searchName&&!this.isLoading){var t=document.getElementsByClassName("group-tree2")[0],e=t.scrollTop,i=t.clientHeight,a=t.scrollHeight;e+i==a&&this.count>this.storeList.length&&(this.isLoading=!0,this.selectStoreList(this.page+1))}},selectStore:function(t){""==t.val&&(this.storeList=[],this.page=1),clearTimeout(this.timeInter),this.timeInter=setTimeout(this.selectStoreList,500)},selectStoreList:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(){var e,i,a,s=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=s.length>0&&void 0!==s[0]?s[0]:1,this.isLoading=!0,t.next=4,this.axios.post("auth-store/get-store-list",{corp_id:this.corpId,group_id:"",uid:localStorage.getItem("uid"),status:"",search_name:this.searchName,page:e,page_size:20});case 4:i=t.sent,a=i.data,0!=a.error?(this.isLoading=!1,this.$message.destroy(),this.$message.error(a.error_msg)):(1==e?(this.storeList=a.data.data,this.count=parseInt(a.data.count)):this.storeList=JSON.parse(JSON.stringify(this.storeList.concat(a.data.data))),this.page=e,this.isLoading=!1);case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),cancelGroup:function(){this.searchName="",this.page=1,this.count=0,this.storeList=[],this.$emit("setGroupId","cancle")},updateGroup:function(){this.searchName="",this.page=1,this.count=0,this.storeList=[],this.$emit("setGroupId","ok",this.ids,this.stores)},onLoadData:function(t){var e=this;return new Promise((function(i){Promise.all([e.getGroupList(t.dataRef.key)]).then((function(a){t.dataRef.children=e.groupData;e.gData;e.gData=Object(o["a"])(e.gData),i()})).catch((function(t){}))}))},loopObjByKey:function(t,e,i){var a=this;t.forEach((function(t){return t.key==e?i(t):t.children&&t.children.length>0&&!t.isLeaf?a.loopObjByKey(t.children,e,i):void 0}))},getGroupList:function(){var t=Object(n["a"])(regeneratorRuntime.mark((function t(e){var i,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("auth-store/get-group-list",{uid:localStorage.getItem("uid"),corp_id:this.corpId,parent_id:e||"",store:2,choose:1,status:1});case 2:i=t.sent,a=i.data,0!=a.error?(this.isLoading=!1,this.$message.destroy(),this.$message.error(a.error_msg)):(e?this.groupData=a.data.data:this.gData=a.data.data,this.isLoading=!1);case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),selectId:function(t,e,i){if(0==i)return!1;var a=this.ids.findIndex((function(e){return e==t}));if(a>-1)this.ids.splice(a,1),this.stores.splice(a,1);else{this.ids.push(t);var s={id:t,title:e};this.stores.push(s)}},onselect:function(t,e){if(t.length>0&&this.id!=t[0]){var i=this.ids.findIndex((function(e){return e==t[0]}));if(i>-1)this.ids.splice(i,1),this.stores.splice(i,1);else{this.ids.push(t[0]);var a=this,s={id:t[0],title:e.node.dataRef.title};a.stores.push(s)}}},deleteStore:function(t){this.ids.splice(t,1),this.stores.splice(t,1)}}},c=r,l=(i("7a21"),i("2877")),d=Object(l["a"])(c,a,s,!1,null,"637129e4",null);e["a"]=d.exports},"7a21":function(t,e,i){"use strict";var a=i("68a1"),s=i.n(a);s.a},a808:function(t,e,i){var a=i("c793");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var s=i("499e").default;s("68f20204",a,!0,{sourceMap:!1,shadowMode:!1})},c793:function(t,e,i){e=t.exports=i("2350")(!1),e.push([t.i,".guide[data-v-8700080c]{width:100%;height:100%}.ant-layout-header[data-v-8700080c]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px;padding:0 20px;font-size:16px;text-align:left}.ant-layout-content[data-v-8700080c]{margin:20px;min-width:885px;background-color:#fff}.content-msg[data-v-8700080c]{border:1px solid #ffdda6;background:#fff2db;padding:10px;text-align:left;margin-bottom:20px}.content-hd[data-v-8700080c]{margin-top:20px;width:100%;min-width:885px}.content-hd .select-option[data-v-8700080c]{display:inline-block;margin-right:10px;margin-bottom:10px}.content-hd .select-option label[data-v-8700080c]{margin-right:5px;display:inline-block;text-align:right;width:100px}.content-hd .select-option .ant-input[data-v-8700080c],.content-hd .select-option .ant-select[data-v-8700080c]{margin-right:5px;width:210px}.content-bd[data-v-8700080c]{background:#fff;min-height:120px;border:1px solid #e2e2e2;min-width:885px;width:100%}",""])}}]);