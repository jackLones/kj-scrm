(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1b5dfd44"],{"1c4c":function(t,e,s){"use strict";var a=s("9b43"),i=s("5ca1"),n=s("4bf8"),r=s("1fa8"),o=s("33a4"),c=s("9def"),l=s("f1ae"),u=s("27ee");i(i.S+i.F*!s("5cc5")((function(t){Array.from(t)})),"Array",{from:function(t){var e,s,i,h,p=n(t),d="function"==typeof this?this:Array,f=arguments.length,g=f>1?arguments[1]:void 0,y=void 0!==g,v=0,b=u(p);if(y&&(g=a(g,f>2?arguments[2]:void 0,2)),void 0==b||d==Array&&o(b))for(e=c(p.length),s=new d(e);e>v;v++)l(s,v,y?g(p[v],v):p[v]);else for(h=b.call(p),s=new d;!(i=h.next()).done;v++)l(s,v,y?r(h,g,[i.value,v],!0):i.value);return s.length=v,s}})},3846:function(t,e,s){s("9e1e")&&"g"!=/./g.flags&&s("86cc").f(RegExp.prototype,"flags",{configurable:!0,get:s("0bfb")})},"42db":function(t,e,s){e=t.exports=s("2350")(!1),e.push([t.i,".participant-name[data-v-0f267ef1]{float:left;margin-left:10px;line-height:42px;max-width:100px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}[data-v-0f267ef1] .ant-table-thead>tr>th:first-child .ant-table-header-column{display:none}",""])},"4bef":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAABAElEQVR42s3VIY7CUBAG4LkEliC4xKJ6BA6AognXQBLCAVBYNBK3pim7zP8XBAJHSLhAZVURi+jC2+681xD4R89nZiYjIhojQRlQicYiolFQ8600EqZNAKbSpB0lyqcCBeYccIA5ihDgtO3JLdseTr7Ap9wFRx+g2HTuge8PH+AsjuBiBzZO4MsOHJzAwQ7kTiD3mcLsoX3muQcaV9s1DtnENUfssssR1i+6hRcD3GPFBaeccoEV9z7AUodoP4yxrUMs/wd26EtN0MeuBuBYDOH4LyDPWhYga1WXugpMxBhMnIBGVqD6S34B9nqvTUwaAUnNpZue689/DnuwTDUSuQJ1pL7YQ6lKkQAAAABJRU5ErkJggg=="},"4f7f":function(t,e,s){"use strict";var a=s("c26b"),i=s("b39a"),n="Set";t.exports=s("e0b8")(n,(function(t){return function(){return t(this,arguments.length>0?arguments[0]:void 0)}}),{add:function(t){return a.def(i(this,n),t=0===t?0:t,t)}},a)},5325:function(t,e,s){var a=s("42db");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var i=s("499e").default;i("28c8b1ea",a,!0,{sourceMap:!1,shadowMode:!1})},"6b54":function(t,e,s){"use strict";s("3846");var a=s("cb7c"),i=s("0bfb"),n=s("9e1e"),r="toString",o=/./[r],c=function(t){s("2aba")(RegExp.prototype,r,t,!0)};s("79e5")((function(){return"/a/b"!=o.call({source:"a",flags:"b"})}))?c((function(){var t=a(this);return"/".concat(t.source,"/","flags"in t?t.flags:!n&&t instanceof RegExp?i.call(t):void 0)})):o.name!=r&&c((function(){return o.call(this)}))},"70ae":function(t,e,s){"use strict";s.r(e);var a=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"scroll",staticStyle:{width:"100%",height:"100%",position:"absolute","overflow-y":"auto"}},[a("div",{staticStyle:{height:"50px","line-height":"50px","background-color":"#FFFFFF"}},[a("label",{staticStyle:{"margin-left":"20px"}},[t._v(t._s(t.name))]),a("a-button",{staticStyle:{float:"right",margin:"9px 20px 0"},attrs:{type:"primary",icon:"rollback"},on:{click:t.rollback}},[t._v("返回列表\n\t\t\t")])],1),a("div",{staticStyle:{margin:"10px 20px","background-color":"#FFFFFF"}},[a("a-spin",{attrs:{tip:"Loading...",size:"large",spinning:t.isLoading2}},[a("a-table",{attrs:{columns:t.columns3,dataSource:t.hlepRecordList,pagination:!1,rowClassName:t.rowClassName,rowSelection:t.rowSelection},scopedSlots:t._u([{key:"user",fn:function(e,i){return a("span",{},[i.avatar?a("a-avatar",{staticStyle:{float:"left"},attrs:{shape:"square",size:42,src:i.avatar}}):t._e(),i.avatar?t._e():a("img",{staticStyle:{width:"42px",height:"42px",float:"left"},attrs:{src:s("4bef")}}),a("div",{staticClass:"participant-name"},[t._v("\n\t\t\t\t\t\t\t\t\t"+t._s(i.name)+"\n\t\t\t\t\t\t\t")]),0==i.status?a("a-tag",{staticStyle:{margin:"10px 0 10px 5px"},attrs:{color:"orange"}},[t._v("\n        无效\n      ")]):t._e(),1==i.status?a("a-tag",{staticStyle:{margin:"10px 0 10px 5px"},attrs:{color:"blue"}},[t._v("\n        有效\n      ")]):t._e()],1)}},{key:"send_status",fn:function(e,s){return a("span",{},[a("a-tooltip",[a("template",{slot:"title"},[t._v("\n                                        余额不足，无法发放\n                                    ")]),0==s.send_status?a("span",[t._v("未发放")]):t._e()],2),1==s.send_status?a("span",[t._v("已发放")]):t._e()],1)}},{key:"action",fn:function(e,s){return a("span",{},[a("a-popconfirm",{attrs:{okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.manualRelease(s.id)}}},[a("template",{slot:"title"},[a("div",[t._v("确定发放给该客户吗？")])]),1==s.status&&0==s.send_status?a("a-button",{staticStyle:{margin:"0 5px 5px 0"}},[t._v("手动发放")]):t._e()],2),a("a-popconfirm",{attrs:{okText:"确定",cancelText:"取消"},on:{confirm:function(e){return t.signRelease(s.id)}}},[a("template",{slot:"title"},[a("div",[t._v("确定发放给该客户吗？")])]),1==s.status&&0==s.send_status?a("a-button",{staticStyle:{margin:"0 5px 5px 0"}},[t._v("标记发放")]):t._e()],2),1!=s.status||0!=s.send_status?[t._v("--")]:t._e()],2)}}])}),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total3>0&&t.userKeys.length>0,expression:"total3 > 0 && userKeys.length > 0"}],staticStyle:{margin:"20px 20px 0","padding-bottom":"20px"}},[a("a-checkbox",{on:{click:t.batchTypeChange},model:{value:t.batchTypeValue,callback:function(e){t.batchTypeValue=e},expression:"batchTypeValue"}}),a("a-select",{staticStyle:{width:"150px",margin:"0 5px"},attrs:{optionFilterProp:"children"},on:{change:t.changeBatchType},model:{value:t.batchType,callback:function(e){t.batchType=e},expression:"batchType"}},[a("a-select-option",{attrs:{value:"0"}},[t._v("选择当前页")]),a("a-select-option",{attrs:{value:"1"}},[t._v("选择所有")])],1),a("a-popconfirm",{attrs:{okText:"确定",cancelText:"取消",disabled:!(this.selectedRowKeys.length>0)},on:{confirm:function(e){return t.bulkChangeLimit(t.selectedRowKeys)}}},[a("template",{slot:"title"},[a("div",[t._v("确定批量手动发放吗？")])]),a("a-button",{attrs:{type:"primary",disabled:!(this.selectedRowKeys.length>0)}},[t._v("批量手动发放\n\t\t\t\t\t\t")])],2)],1),a("div",{directives:[{name:"show",rawName:"v-show",value:t.total3>0,expression:"total3 > 0"}],staticClass:"pagination",staticStyle:{width:"100%",position:"absolute",margin:"20px 0px"}},[a("div",{staticStyle:{height:"32px",float:"left","line-height":"32px"}},[t._v("\n\t\t\t\t\t\t共\n\t\t\t\t\t\t"),a("span",{staticStyle:{color:"blue"}},[t._v(t._s(t.total3))]),t._v("条\n\t\t\t\t\t")]),a("div",{staticClass:"pagination",staticStyle:{height:"32px",float:"right"}},[a("a-pagination",{attrs:{total:t.total3,showSizeChanger:"",showQuickJumper:t.quickJumper3,current:t.page3,pageSize:t.pageSize3,pageSizeOptions:["15","30","50","100"]},on:{change:t.changePage3,showSizeChange:t.showSizeChange3}})],1)])],1)],1)])},i=[],n=(s("7f7f"),s("75fc")),r=(s("4f7f"),s("5df3"),s("1c4c"),s("55dd"),s("6b54"),s("6762"),s("2fdb"),s("ac6a"),s("96cf"),s("3b8d")),o=[{title:"好友拆领",dataIndex:"user",key:"user",scopedSlots:{customRender:"user"}},{title:"获得红包（元）",dataIndex:"amount",key:"amount"},{title:"拆领时间",dataIndex:"help_time",key:"help_time"},{title:"好友发放状态",dataIndex:"send_status",key:"send_status",scopedSlots:{customRender:"send_status"}},{title:"操作",dataIndex:"action",key:"action",scopedSlots:{customRender:"action"}}],c={name:"HelpRecord",data:function(){return{columns3:o,name:"",jid:0,isLoading2:!1,hlepRecordVisible:!1,hlepRecordList:[],total3:0,page3:1,pageSize3:15,quickJumper3:!1,is_end:0,selectedRowKeys:[],userKeys:[],batchType:"1",batchTypeValue:!1,checkArr:[]}},methods:{rowClassName:function(t,e){var s="dark-row";return e%2===0&&(s="light-row"),s},rollback:function(){this.$router.go(-1)},helpRecord:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(){var e,s,a,i,n,r=this,o=arguments;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=o.length>0&&void 0!==o[0]?o[0]:1,s=o.length>1&&void 0!==o[1]?o[1]:this.pageSize3,this.isLoading2=!0,t.next=5,this.axios.post("red-pack/help-list",{uid:localStorage.getItem("uid"),jid:this.jid,page:e,pageSize:s});case 5:a=t.sent,i=a.data,0!=i.error?(this.isLoading2=!1,this.$message.error(i.error_msg)):(this.hlepRecordList=i.data.helpList,this.page3=e,this.pageSize3=s,this.total3=parseInt(i.data.count),this.isLoading2=!1,this.is_end=i.data.is_end,this.userKeys=i.data.keys,n=this.selectedRowKeys,this.checkArr=[],this.hlepRecordList.map((function(t){0==t.send_status&&r.checkArr.push(t.key)})),"0"==this.batchType?this.checkArr.length>0?this.batchTypeValue=this.checkArr.every((function(t){return n.includes(t)})):this.batchTypeDisabled=!0:"1"==this.batchType&&(this.userKeys.length>0?this.batchTypeValue=n.sort().toString()==this.userKeys.sort().toString():this.batchTypeDisabled=!0));case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),changePage3:function(t,e){this.helpRecord(t,this.pageSize3),document.getElementsByClassName("scroll")[0].scrollTo(0,0)},showSizeChange3:function(t,e){this.helpRecord(1,e)},manualRelease:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var s,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("red-pack/help-hand-send",{uid:localStorage.getItem("uid"),jid:this.jid,hid:e});case 2:s=t.sent,a=s.data,0!=a.error?this.$message.error(a.error_msg):(this.$message.success(a.data.textHtml),this.helpRecord());case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),signRelease:function(){var t=Object(r["a"])(regeneratorRuntime.mark((function t(e){var s,a;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,this.axios.post("red-pack/help-status",{uid:localStorage.getItem("uid"),hid:e});case 2:s=t.sent,a=s.data,0!=a.error?this.$message.error(a.error_msg):(this.$message.success("已成功标记发放"),this.helpRecord());case 5:case"end":return t.stop()}}),t,this)})));function e(e){return t.apply(this,arguments)}return e}(),onSelectChange:function(t){this.selectedRowKeys=t,"0"==this.batchType?this.batchTypeValue=this.checkArr.every((function(e){return t.includes(e)})):"1"==this.batchType&&(this.batchTypeValue=t.sort().toString()==this.userKeys.sort().toString())},batchTypeChange:function(){var t=this;this.batchTypeValue?this.selectedRowKeys=[]:"0"==this.batchType?(this.hlepRecordList.map((function(e){0==e.send_status&&2==e.status&&t.selectedRowKeys.push(e.key)})),this.selectedRowKeys=Array.from(new Set(this.selectedRowKeys))):"1"==this.batchType&&(this.selectedRowKeys=this.userKeys)},changeBatchType:function(){var t=this;this.batchTypeDisabled||(this.batchTypeValue?"0"==this.batchType?(this.selectedRowKeys=[],this.hlepRecordList.map((function(e){0==e.send_status&&2==e.status&&t.selectedRowKeys.push(e.key)})),this.selectedRowKeys=Array.from(new Set(this.selectedRowKeys))):"1"==this.batchType&&(this.selectedRowKeys=Object(n["a"])(this.userKeys.valueOf())):"0"==this.batchType?this.batchTypeValue=this.checkArr.every((function(e){return t.selectedRowKeys.includes(e)})):"1"==this.batchType&&(this.batchTypeValue=this.selectedRowKeys.sort().toString()==this.userKeys.sort().toString()))},bulkChangeLimit:function(t){this.manualRelease(t)}},created:function(){this.name=this.$route.query.name,this.jid=this.$route.query.jid,this.helpRecord()},computed:{rowSelection:function(){var t=this.selectedRowKeys;return{selectedRowKeys:t,onChange:this.onSelectChange,hideDefaultSelections:!0,getCheckboxProps:function(t){return{props:{disabled:0!=t.send_status||1!=t.status}}}}}}},l=c,u=(s("e831"),s("2877")),h=Object(u["a"])(l,a,i,!1,null,"0f267ef1",null);e["default"]=h.exports},b39a:function(t,e,s){var a=s("d3f4");t.exports=function(t,e){if(!a(t)||t._t!==e)throw TypeError("Incompatible receiver, "+e+" required!");return t}},c26b:function(t,e,s){"use strict";var a=s("86cc").f,i=s("2aeb"),n=s("dcbc"),r=s("9b43"),o=s("f605"),c=s("4a59"),l=s("01f9"),u=s("d53b"),h=s("7a56"),p=s("9e1e"),d=s("67ab").fastKey,f=s("b39a"),g=p?"_s":"size",y=function(t,e){var s,a=d(e);if("F"!==a)return t._i[a];for(s=t._f;s;s=s.n)if(s.k==e)return s};t.exports={getConstructor:function(t,e,s,l){var u=t((function(t,a){o(t,u,e,"_i"),t._t=e,t._i=i(null),t._f=void 0,t._l=void 0,t[g]=0,void 0!=a&&c(a,s,t[l],t)}));return n(u.prototype,{clear:function(){for(var t=f(this,e),s=t._i,a=t._f;a;a=a.n)a.r=!0,a.p&&(a.p=a.p.n=void 0),delete s[a.i];t._f=t._l=void 0,t[g]=0},delete:function(t){var s=f(this,e),a=y(s,t);if(a){var i=a.n,n=a.p;delete s._i[a.i],a.r=!0,n&&(n.n=i),i&&(i.p=n),s._f==a&&(s._f=i),s._l==a&&(s._l=n),s[g]--}return!!a},forEach:function(t){f(this,e);var s,a=r(t,arguments.length>1?arguments[1]:void 0,3);while(s=s?s.n:this._f){a(s.v,s.k,this);while(s&&s.r)s=s.p}},has:function(t){return!!y(f(this,e),t)}}),p&&a(u.prototype,"size",{get:function(){return f(this,e)[g]}}),u},def:function(t,e,s){var a,i,n=y(t,e);return n?n.v=s:(t._l=n={i:i=d(e,!0),k:e,v:s,p:a=t._l,n:void 0,r:!1},t._f||(t._f=n),a&&(a.n=n),t[g]++,"F"!==i&&(t._i[i]=n)),t},getEntry:y,setStrong:function(t,e,s){l(t,e,(function(t,s){this._t=f(t,e),this._k=s,this._l=void 0}),(function(){var t=this,e=t._k,s=t._l;while(s&&s.r)s=s.p;return t._t&&(t._l=s=s?s.n:t._t._f)?u(0,"keys"==e?s.k:"values"==e?s.v:[s.k,s.v]):(t._t=void 0,u(1))}),s?"entries":"values",!s,!0),h(e)}}},e0b8:function(t,e,s){"use strict";var a=s("7726"),i=s("5ca1"),n=s("2aba"),r=s("dcbc"),o=s("67ab"),c=s("4a59"),l=s("f605"),u=s("d3f4"),h=s("79e5"),p=s("5cc5"),d=s("7f20"),f=s("5dbc");t.exports=function(t,e,s,g,y,v){var b=a[t],m=b,_=y?"set":"add",w=m&&m.prototype,x={},S=function(t){var e=w[t];n(w,t,"delete"==t?function(t){return!(v&&!u(t))&&e.call(this,0===t?0:t)}:"has"==t?function(t){return!(v&&!u(t))&&e.call(this,0===t?0:t)}:"get"==t?function(t){return v&&!u(t)?void 0:e.call(this,0===t?0:t)}:"add"==t?function(t){return e.call(this,0===t?0:t),this}:function(t,s){return e.call(this,0===t?0:t,s),this})};if("function"==typeof m&&(v||w.forEach&&!h((function(){(new m).entries().next()})))){var k=new m,R=k[_](v?{}:-0,1)!=k,A=h((function(){k.has(1)})),T=p((function(t){new m(t)})),K=!v&&h((function(){var t=new m,e=5;while(e--)t[_](e,e);return!t.has(-0)}));T||(m=e((function(e,s){l(e,m,t);var a=f(new b,e,m);return void 0!=s&&c(s,y,a[_],a),a})),m.prototype=w,w.constructor=m),(A||K)&&(S("delete"),S("has"),y&&S("get")),(K||R)&&S(_),v&&w.clear&&delete w.clear}else m=g.getConstructor(e,t,y,_),r(m.prototype,s),o.NEED=!0;return d(m,t),x[t]=m,i(i.G+i.W+i.F*(m!=b),x),v||g.setStrong(m,t,y),m}},e831:function(t,e,s){"use strict";var a=s("5325"),i=s.n(a);i.a}}]);