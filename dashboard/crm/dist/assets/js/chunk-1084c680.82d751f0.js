(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1084c680"],{"7b34":function(t,e,a){"use strict";a.r(e);var o=function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"mate-set"},[o("a-layout",{staticStyle:{position:"relative",height:"100%",overflow:"hidden"}},[o("a-layout",{staticClass:"scroll",staticStyle:{position:"absolute",left:"0",top:"0",bottom:"0",right:"0","overflow-x":"hidden","overflow-y":"auto"}},[o("a-layout-header",[t._v("电商素材")]),o("a-layout-content",[o("div",{staticStyle:{padding:"15px 20px",background:"#FFF"}},[o("div",{staticClass:"content-msg"},[o("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t1、需要有自建应用才可以使用电商素材功能\n\t\t\t\t\t\t")]),o("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t2、默认已经有对接的电商系统，欢迎咨询管理员了解。若需要对接第三方电商系统，也可与我们联系。\n\t\t\t\t\t\t")]),o("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t3、可实现电商素材转发给客户，并进行溯源和转化统计的功能。\n\t\t\t\t\t\t")]),o("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t4、可转发小程序或H5页面，小程序需要提前绑定企业微信。\n\t\t\t\t\t\t")]),o("p",{staticStyle:{"margin-bottom":"0px"}},[t._v("\n\t\t\t\t\t\t\t5、复制下面页面地址，可以去企业微信后台添加聊天侧边栏。\n\t\t\t\t\t\t")])]),o("div",{staticClass:"content-hd"},[o("div",{staticClass:"content-box"},[o("div",{staticClass:"guide-role"},[o("div",{staticClass:"mate-title"},[o("span",[t._v("电商素材配置")])]),o("div",{staticClass:"mate-statis-list"},[o("a-form",[o("a-form-item",{attrs:{label:"页面标签:","label-col":{span:2},"wrapper-col":{span:22}}},[o("a-checkbox-group",{attrs:{"default-value":t.tipId},on:{change:t.onChangeTip},model:{value:t.tipId,callback:function(e){t.tipId=e},expression:"tipId"}},t._l(t.tipArr,(function(e,a){return o("a-checkbox",{key:a,attrs:{value:e.id}},[t._v(t._s(e.name))])})),1),o("a-tooltip",{attrs:{placement:"top"}},[o("template",{slot:"title"},[o("div",[t._v("勾选已与商城对接的标签，至少选一项")])]),o("a-icon",{staticStyle:{"margin-left":"5px",color:"#000000"},attrs:{type:"question-circle",theme:"filled"}})],2)],1),o("a-form-item",{attrs:{label:"小程序appid:","label-col":{span:2},"wrapper-col":{span:22}}},[o("a-input",{attrs:{placeholder:"请输入"},model:{value:t.appid,callback:function(e){t.appid=e},expression:"appid"}}),o("a-tooltip",{attrs:{placement:"top"}},[o("template",{slot:"title"},[o("div",[t._v("需要先将小程序关联到企业微信后台")])]),o("a-icon",{staticStyle:{"margin-left":"5px",color:"#000000"},attrs:{type:"question-circle",theme:"filled"}})],2)],1),o("a-form-item",{attrs:{label:"H5商城:","label-col":{span:2},"wrapper-col":{span:22}}},[o("a-switch",{attrs:{"default-checked":""},on:{change:function(e){return t.onShop(e)}},model:{value:t.openShop,callback:function(e){t.openShop=e},expression:"openShop"}})],1),o("a-form-item",{attrs:{label:"页面地址:","label-col":{span:2},"wrapper-col":{span:22}}},[o("a-input",{attrs:{placeholder:"请输入"},model:{value:t.pageUrl,callback:function(e){t.pageUrl=e},expression:"pageUrl"}}),o("a",{staticStyle:{"margin-left":"10px","vertical-align":"middle"},on:{click:function(e){return t.copyText(t.pageUrl)}}},[t._v("复制")])],1)],1)],1)])]),o("div",{staticClass:"content-box"},[o("div",{staticClass:"guide-set"},[o("div",{staticClass:"mate-title"},[o("span",[t._v("发送效果展示")])]),o("div",{staticClass:"mate-show-list"},[o("div",{staticClass:"list-item"},[o("img",{attrs:{src:a("080d"),alt:"加载失败"}}),o("p",[t._v("小程序卡片")])]),o("div",{staticClass:"list-item"},[o("img",{attrs:{src:a("cd5b"),alt:"加载失败"}}),o("p",[t._v("文字+H5链接")])])])])])]),o("div",{staticClass:"content-bd"},[o("a-button",{attrs:{type:"primary"},on:{click:t.saveMate}},[t._v("保存")])],1)])])],1)],1)],1)},n=[],i=(a("6b54"),a("96cf"),a("3b8d")),s={name:"shopMaterial",components:{},data:function(){return{loading:!1,tipId:[1,3],tipArr:[{id:1,name:"商品",checked:1},{id:2,name:"页面",checked:0},{id:3,name:"优惠券",checked:1}],appid:"",openShop:!1,pageUrl:"http://m.fastwhale.com.cn/h5/pages/scrm/shopcontent?agent_id=1120&corpid=ww25707817d75831a6"}},methods:{mateSet:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,o;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=this,e.isLoading=!0,t.next=4,e.axios.post("shop-customer-guide/get-config",{corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):""});case 4:a=t.sent,o=a.data,0!=o.error?(e.isLoading=!1,e.$message.error(o.error_msg)):(e.isLoading=!1,e.tipArr=o.data.role,e.tipId=o.data.config.role);case 7:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}(),onChangeTip:function(t){this.tipId=t},onShop:function(t){this.openShop=t},copyText:function(t){var e=this,a=document.createElement("input");document.body.appendChild(a),a.setAttribute("value",t),a.select(),document.execCommand("copy")&&(document.execCommand("copy"),e.$message.success("复制成功！")),document.body.removeChild(a)},saveMate:function(){var t=Object(i["a"])(regeneratorRuntime.mark((function t(){var e,a,o,n;return regeneratorRuntime.wrap((function(t){while(1)switch(t.prev=t.next){case 0:return e=this,e.loading=!0,a={corp_id:localStorage.getItem("corpId")?localStorage.getItem("corpId"):"",role:e.tipId.toString()},t.next=5,e.axios.post("shop-customer-guide/update-attribution",a);case 5:o=t.sent,n=o.data,0!=n.error?(e.loading=!1,e.$message.error(n.error_msg)):(e.loading=!1,e.$message.success("保存成功"));case 8:case"end":return t.stop()}}),t,this)})));function e(){return t.apply(this,arguments)}return e}()},mounted:function(){this.$store.dispatch("getCorp",(function(t){}))}},c=s,r=(a("f79b"),a("2877")),l=Object(r["a"])(c,o,n,!1,null,"122627c8",null);e["default"]=l.exports},"83ad":function(t,e,a){var o=a("c238");"string"===typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);var n=a("499e").default;n("6a0e96a0",o,!0,{sourceMap:!1,shadowMode:!1})},c238:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,".mate-set[data-v-122627c8]{width:100%;height:100%}.ant-layout-header[data-v-122627c8]{background:#fff;border-bottom:1px solid #e2e2e2;height:50px;min-width:885px;width:100%;line-height:50px;padding:0 20px;font-size:16px;text-align:left}.ant-layout-content[data-v-122627c8]{margin:20px;min-width:885px;background-color:#fff}.content-msg[data-v-122627c8]{border:1px solid #ffdda6;background:#fff2db;padding:10px;text-align:left;margin-bottom:20px}.content-hd[data-v-122627c8]{margin-top:20px}.content-bd[data-v-122627c8],.content-hd[data-v-122627c8]{width:100%;min-width:885px}.content-bd[data-v-122627c8]{background:#fff;min-height:120px;text-align:center}.content-bd .ant-btn[data-v-122627c8]{margin-top:50px}.content-box[data-v-122627c8]{background:#f5f5f5;border:1px solid #e2e2e2;margin-bottom:20px;padding:10px}.content-box[data-v-122627c8]:last-child{margin-bottom:0}.content-box .mate-title[data-v-122627c8]{font-size:16px;font-weight:600;margin:10px}.content-box .mate-statis-list .ant-checkbox-group[data-v-122627c8]{width:auto}.content-box .mate-statis-list .ant-input[data-v-122627c8]{width:210px}.content-box .mate-show-list[data-v-122627c8]{display:-webkit-box;display:-ms-flexbox;display:flex}.content-box .mate-show-list .list-item[data-v-122627c8]{margin-left:50px;margin-top:20px;text-align:center;font-size:16px;font-weight:600}.content-box .mate-show-list .list-item img[data-v-122627c8]{width:200px}.content-box .mate-show-list .list-item p[data-v-122627c8]{margin-top:12px}",""])},cd5b:function(t,e,a){t.exports=a.p+"assets/img/guide.0b1cdfa5.png"},f79b:function(t,e,a){"use strict";var o=a("83ad"),n=a.n(o);n.a}}]);