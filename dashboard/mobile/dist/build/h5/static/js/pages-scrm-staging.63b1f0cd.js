(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-scrm-staging"],{"28a3":function(t,e,i){"use strict";i.r(e);var a=i("63b6"),n=i.n(a);for(var r in a)["default"].indexOf(r)<0&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},"30b4":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAADXklEQVRYhc2YzU4UQRDHJ8DJYITn2Kib7PT/3xP20OoCJ40OzguIGu9+BbxsnHCRsMgzmHDFJ9BwMQKKQYmJN5UPDzKLASSuWV0P9mpnnJmd/VIrmUP3VFf9pru6q3osqwMC4BaAA5I3OmGvbQGwBqBG8vW/ZrEsy7KEEC8B1AC8+evOlVJ9QogCyWmSj0m+BVDVQBUAjwDck1IWPM/r7RpIJpPpJzkBYFM7b/iQ3AJwO5fLHekoDIDzALYjnB4AWAWwr9tfdV9Yb0NKebYTLD0AZgB8N4zvAihJKVlfEjOGPM/rBeCQvA/gU2jGpovFYk9LJMVisYfkA8NgFUApm80OhHUBPNM6L8z+fD4/SHIOwDcD6kFLUHpm6jABgNMJuqcBPBRCnIl6T3KEZNmcqWZhzpswjuMcb/J7omyeNKC+SynPpRqolOrXu6O+TLEz06yQHDGWbyOTyfQ3HARg0pidmU7BGFBzxtJNJCorpfoAfNADylEB3K7k8/lBY/dtJh6eAEa7OTuGn1Ldj5SykKRo7iy7W0CO48BYtntJQItacS/tWeH6wbjrB09cP3gyNhVcSjNGH54HAGpCiMdJQO810HIaw2NT5auuv1Mzn7RQOuXUALwLv7ip65l1I2sf6PaKlFLFAvnB0zCQ6wdP4/SFEGdILmnb9ZxX1e01ktctAJ8bZO2FOAeuv7PUDBCAhw18HVq6pHilKSv6xRfdXiU5EjtDdz9eiwAaj9OXUhaEEM9Dviq6vQ5gMvwFizry99MWV2N+cMX1d1ZcP1i+OBVcTjPGDGqSiUH9a9vbto00xlsRkjJVotW5pusHI8lZA2g4VjGUOna7nTpIbjUMjVByLXUaSFeSdfuTDQcopfrxu36uxhVdLcIMG+XHtlKqcflhWX8WaCRPtAsjhMiaVaOU0m3WwKwJlTRT+FnCLpA8FfVeSlkAUG4rFPRZMW8YqQIo5XK5YxFAkUV+Npsd0DFjFvnzLd88PM/rNbdoffcBmLFtG3HXINu2oc+0XXMsydmO3GYdx7lg1Nmmgz0AK/h9UazovnCO2gZwoW0QU4aGho7qIyHqBhv3fABwJ/VuakX04Tmql2QRwIYRIxXdV3IcZ1Qp1dc1kCT5p79jogT/2w8r/PyldwjgVru2fgBJR3ONng9/gwAAAABJRU5ErkJggg=="},"31e6":function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var a={uniNavBar:i("f31d").default,uniSearchBar:i("1c61").default,uniIcons:i("2ba4").default,uniIndexedList:i("8ba9").default},n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",{staticClass:"box content_head"},[a("uni-nav-bar",{attrs:{"left-icon":"back","left-text":"返回","background-color":"#5284ec",color:"#fff"},on:{clickLeft:function(e){arguments[0]=e=t.$handleEvent(e),t.back.apply(void 0,arguments)}}},[t.searchVisible?a("uni-search-bar",{ref:"searchBar",attrs:{radius:"30",placeholder:"请输入搜索内容",clearButton:"always",cancelButton:"none"},on:{input:function(e){arguments[0]=e=t.$handleEvent(e),t.search.apply(void 0,arguments)}}}):t._e(),a("v-uni-view",{attrs:{slot:"right"},slot:"right"},[a("uni-icons",{staticStyle:{"line-height":"46px"},attrs:{type:"search",size:"22",color:"#fff"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.showSearch.apply(void 0,arguments)}}})],1)],1),t.isReady?a("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:0==t.usersList.length,expression:"usersList.length == 0"}]},[a("img",{staticClass:"empty-img",attrs:{src:i("250a"),alt:""}})]):t._e(),t.isReady?a("uni-indexed-list",{directives:[{name:"show",rawName:"v-show",value:t.usersList.length>0,expression:"usersList.length > 0"}],ref:"uniList",staticStyle:{bottom:"0"},attrs:{options:t.usersList,showSelect:!0},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.bindClick.apply(void 0,arguments)},jump:function(e){arguments[0]=e=t.$handleEvent(e),t.jumpClick.apply(void 0,arguments)}}}):t._e(),a("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:t.usersList.length>0,expression:"usersList.length > 0"}],staticClass:"footer content_head"},[a("uni-icons",{staticStyle:{"line-height":"2.667rem  /* 50/18.75 */",float:"left","margin-left":"7.5px"},attrs:{type:t.isCheck?"checkbox-filled":"circle",size:"22",color:"#5284EC"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.changeCheck.apply(void 0,arguments)}}}),a("v-uni-scroll-view",{staticClass:"scroll-view",attrs:{"scroll-x":"true","scroll-into-view":t.toView,"scroll-with-animation":!0}},[t._l(t.selectArr,(function(e){return a("v-uni-view",{staticClass:"scroll-view-item",attrs:{id:"view"+e.id},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.clearItem(e)}}},[1==e.depart?a("v-uni-text",{staticClass:"tag"},[t._v(t._s(e.name))]):t._e(),0==e.depart?a("v-uni-image",{staticStyle:{width:"30px",height:"30px","margin-right":"10px","vertical-align":"middle"},attrs:{src:e.avatar}}):t._e()],1)})),t.selectArr.length>0?a("uni-icons",{staticStyle:{color:"#2C3E50","font-size":"0.853rem  /* 16/18.75 */"},attrs:{type:"close"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.clear.apply(void 0,arguments)}}}):t._e()],2),a("v-uni-button",{staticClass:"btn",attrs:{type:"primary"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.save.apply(void 0,arguments)}}},[t._v("确定")])],1)],1)},r=[]},"3ad5":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAkCAYAAADo6zjiAAADyklEQVRYhe1XW2hVVxDdqbaC+GE/2kqpihKw7bUamu41e5/c6AHRmKTnPiJBUGljo1HEF9FEDTEJBqwfLYFaLKnYlqIQrC9Saj4U1NZXja1G8YFPgiioETXYplXvjR+eE8fjfZybXGI/OjA/Z9bMrJmzH7MFgG8APAbQ3c8aIaJGAeD+S0ju6F+CiKYB2AWgmYgOMuMFAL+lSa+xuG32twMASgUXIvI7QCKaLdIkRPQ5i+tPBPyfQA8BpVRJvxNQSuWwxTIjjQTWsrg6EfB9BpyXLgJSygYnrpTSFxfo8/leA/DQbtUX6SIA4Gfn8DFNc0gy8HEb3Op8K1zYsiBQde5+Kjql9IcJQgiRnZ39KoC7dszTSdkSUY0Njiql3hNCCLOkYWho9dXOovqObi8aqDrfxgoKsd+6LikBv98/AsAj22Gz8z2w7EhReM3NaLLk4dr2rrx5O8bbbhkAjjnt11q/m5SAzXqj0wWt9dQeEuWHi8M17V3xkodWX7mRv2i3Yt1cwrbfT56SCyFEbm7uGwBu2c53DMMY69gmf7bp7eDKU+tC1Rc7nrX8zAWr8s8VVlnjYFZEnrOgATwAMMozASGE0FrnsV/RobWexO3FxVsHWMuPNljl+xe7fYloFoAudqh9mlJyVsUneDYrRABsklKOTkD6AwA7XVduba+SO0JEBQA6WMAIgF2maQ50MFlZWUMB7HclfkRES/qU3BEApTGGifHMXhzD3rfKbckgonoAURY4SkTbMjMzBzkgwzBGArjsIhAlorVCiIxeZyeir3nriejbRGsAQC6AvS4iG3qbfCkLclNrPTEF3zIA/7Jd8MJOSShSSh+Af+wAt3NycsakWgCAQnYOPCSicZ6diWi37fhYKWXGwkyZ/+ObVkVrk7X8yIb8/K8GxcIAKGdd3Os1+bhE/2/SnI1vBVeebArXXY/0HMHVlzsDFcfqzbp9A13wV9jt2g3go6QEAHzpVC+lHM5t+XM2vxOsudIe7y4IVp39nR/HQgihlCpia2G9FwJn4rUsuOr0nmS3oVX5x3fcx54H7tkxLyVMbprmEPuk61ZKVXNbXlnTh0VrbiWdBcJ1NyIFJd8PcxXV4nTB7/e/HpeAYRhjWbumc5tVeXyF14GkYFHLc/MkgPXsWo6/G1xTcSG3hWraa70S+HjZoUruy8dyrbURlwB/F7gJWBWt0wJV57Z70YIFv0x1xU39YeIm0Bf5bxNQSs0F8CuePpnbGIEWKWVjOhTACUbgIIBmAM1KqZkCwN8x7vJ+USLqFAB2vCQCEQBbngCRP+T0wGZVQgAAAABJRU5ErkJggg=="},"4bb6":function(t,e,i){"use strict";var a=i("ee27");i("a630"),i("c975"),i("d81d"),i("a434"),i("d3b7"),i("6062"),i("3ca3"),i("ddb0"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("96cf");var n=a(i("c964")),r=a(i("8ba9")),o={name:"chooseDepartment",props:{callBack:{type:Function,default:null},selectArray:{type:Array,default:[]}},inject:["getParameter"],components:{uniIndexedList:r.default},data:function(){return{commonUrl:this.$store.state.commonUrl,isReady:!1,id:"",id2:[],usersList:[],selectList:[],inputValue:"",isFresh:!1,searchVisible:!1,selectArr:this.selectArray,isCheck:!1,toView:""}},created:function(){this.getData()},methods:{getData:function(){var t=this;return(0,n.default)(regeneratorRuntime.mark((function e(){var i,a,n,r,o;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return uni.showLoading({title:"加载中...",duration:2e3}),e.next=3,t.axios.post("work-party/get-depart-user",{corp_id:localStorage.getItem("corpid"),uid:localStorage.getItem("uid"),user_id:localStorage.getItem("user_id"),user_ids:[],id:t.id,name:t.inputValue});case 3:if(i=e.sent,a=i.data,0!=a.error)uni.hideLoading(),uni.showToast({title:a.error_msg,image:"/static/fail.png",duration:2e3});else{if(t.usersList=a.data.users,t.isFresh&&(t.usersList.map((function(e){e.data.map((function(e){t.selectList.map((function(t){t==e.id&&(e.checked=!0)}))}))})),t.$refs.uniList.setList()),0==t.selectList.length&&t.selectArr.length>0){for(n=0;n<t.selectArr.length;n++)t.selectList.push(t.selectArr[n].id);r=t,r.selectList.map((function(t){r.usersList.map((function(e){e.data.map((function(e){t==e.id&&(e.checked=!0)}))}))}))}o=!0,t.usersList.map((function(t){t.data.map((function(t){t.checked||(o=!1)}))})),t.isCheck=o,t.isReady=!0,uni.hideLoading()}case 6:case"end":return e.stop()}}),e)})))()},bindClick:function(t){var e=this,i="",a={};function n(t){return Array.from(new Set(t))}function r(t,e){var i;if(!e)return!1;if(t.length!=e.length)return!1;for(i=0;i<t.length;i++){if(-1==e.indexOf(t[i]))return!1;if(-1==t.indexOf(e[i]))return!1}return!0}i=t.item.id,a=t.item,-1==this.selectList.indexOf(i)?(this.selectList.push(i),this.selectList=n(this.selectList),this.selectArr.push(a),this.$nextTick((function(){e.toView="view"+i}))):(this.selectList.splice(this.selectList.indexOf(i),1),this.selectList=n(this.selectList),this.selectArr.map((function(t,i){a.id==t.id&&e.selectArr.splice(i,1)})));var o=[];this.usersList.map((function(t){t.data.map((function(t){o.push(t.id)}))})),this.isCheck=r(o,this.selectList)},jumpClick:function(t){this.id2.push(this.id),this.id=t,this.isFresh=!0,this.getData()},save:function(){null!==this.callBack&&"function"===typeof this.callBack&&this.callBack(this.selectArr)},back:function(){this.id2.length-1>=0?(this.id=this.id2[this.id2.length-1],this.id2.splice(this.id2.length-1,1),this.inputValue="",this.getData()):null!==this.callBack&&"function"===typeof this.callBack&&this.callBack()},search:function(t){this.inputValue=t.value,this.isFresh=!0,this.getData()},changeCheck:function(){var t=this;this.isCheck=!this.isCheck,this.isCheck?(this.selectList=[],this.selectArr=[],this.usersList.map((function(e){e.data.map((function(e){e.checked=!0,t.selectList.push(e.id),t.selectArr.push(e)}))})),this.$nextTick((function(){t.toView="view"+t.selectList[t.selectList.length-1]}))):(this.selectList=[],this.selectArr=[],this.usersList.map((function(t){t.data.map((function(t){t.checked=!1}))}))),this.$refs.uniList.setList()},showSearch:function(){this.searchVisible=!this.searchVisible},clearItem:function(t){var e=this;function i(t){return Array.from(new Set(t))}if(this.selectList.indexOf(t.id)>-1){this.selectList.splice(this.selectList.indexOf(t.id),1),this.selectList=i(this.selectList),this.selectArr.map((function(i,a){t.id==i.id&&e.selectArr.splice(a,1)}));var a=!0;this.usersList.map((function(t){t.data.map((function(t){e.selectList.indexOf(t.id)>-1?t.checked=!0:t.checked=!1,t.checked||(a=!1)}))})),this.isCheck=a,this.$refs.uniList.setList()}},clear:function(){this.isCheck=!1,this.selectArr=[],this.selectList=[],this.usersList.map((function(t){t.data.map((function(t){t.checked=!1}))})),this.$refs.uniList.setList()}}};e.default=o},"58f2":function(t,e,i){"use strict";i.r(e);var a=i("d44c"),n=i("28a3");for(var r in n)["default"].indexOf(r)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("5db6");var o,s=i("f0c5"),l=Object(s["a"])(n["default"],a["b"],a["c"],!1,null,"1cf4de93",null,!1,a["a"],o);e["default"]=l.exports},"5db6":function(t,e,i){"use strict";var a=i("bfdd"),n=i.n(a);n.a},6062:function(t,e,i){"use strict";var a=i("6d61"),n=i("6566");t.exports=a("Set",(function(t){return function(){return t(this,arguments.length?arguments[0]:void 0)}}),n)},"63b6":function(t,e,i){"use strict";var a=i("ee27");i("c975"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=a(i("0122"));i("96cf");var r=a(i("c964")),o=a(i("2c4b")),s=a(i("c75b")),l={name:"list",inject:["initPage","getExternalId","getParameter","getChatId","forbidden","getPermissionButton"],components:{Footer:o.default,ChooseDepartment:s.default},data:function(){return{url:"",ticketData:{},agentData:{},commonUrl:this.$store.state.commonUrl,isReady:!1,name:"",avatar:"",departName:"",allExternalNum:0,allChatNum:0,todayExternalNum:0,todayExternalDelNum:0,todayExternalFollowNum:0,todayChatNum:0,isSelf:0,followId:"",followName:"",toView:"",followList:[],externalFollowNum:0,notFollowDayData:[],scopeName:"全部",userPlatform:localStorage.getItem("userPlatform"),showChooseDepartment:!1,selectDepartment:[],is_show_copyright:localStorage.getItem("is_show_copyright")}},onTabItemTap:function(t){this.selectDepartment=[];try{uni.removeStorageSync("name"),uni.removeStorageSync("follow_id"),uni.removeStorageSync("follow_name"),uni.removeStorageSync("tab_btn_type"),uni.removeStorageSync("rangetime"),uni.removeStorageSync("sex"),uni.removeStorageSync("is_fans"),uni.removeStorageSync("chat_type"),uni.removeStorageSync("link_time"),uni.removeStorageSync("from")}catch(e){}},onShow:function(){var t=this;uni.setNavigationBarTitle({title:"工作台"}),this.isReady=!1,uni.hideTabBar(),uni.showLoading({title:"加载中...",duration:2e3});var e=sessionStorage.getItem("agent_id")||localStorage.getItem("agent_id")||this.getParameter("agentId")||this.getParameter("agent_id")||"";localStorage.setItem("agent_id",e),sessionStorage.setItem("agent_id",e),this.$store.dispatch("setWx",(function(){t.initPage(t.limit)}))},methods:{addCustom:function(){this.$store.state.wx.invoke("navigateToAddCustomer",{},(function(t){0!=t.err_code&&uni.showToast({title:"请检查应用权限与客户功能权限",image:"/static/fail.png",duration:2e3})}))},limit:function(){var t=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,a,n,r;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return t.url=window.location.href,i=sessionStorage.getItem("agent_id")||localStorage.getItem("agent_id")||t.getParameter("agentId")||t.getParameter("agent_id")||"",e.next=4,t.axios.post("chat-message/get-config",{url:t.url,agent_id:i});case 4:a=e.sent,n=a.data,0!=n.error?uni.showToast({title:n.error_msg,image:"/static/fail.png",duration:2e3}):(r=t,r.uid=n.data.uid,r.corpid=n.data.corpid,localStorage.setItem("uid",r.uid),localStorage.setItem("corpid",r.corpid),r.ticketData=n.data.ticketData,r.agentData=n.data.agentData,(localStorage.getItem("corpid")||r.getParameter("corpid"))&&localStorage.getItem("user_id")&&r.getPermissionButton(),r.$store.state.wx.config({beta:!0,debug:!1,appId:r.ticketData.corpid,timestamp:r.ticketData.timestamp,nonceStr:r.ticketData.nonceStr,signature:r.ticketData.signature,jsApiList:r.ticketData.jsApiList}),r.$store.state.wx.error((function(t){uni.showToast({title:t.errMsg,image:"/static/fail.png",duration:2e3})})),r.$store.state.wx.ready((function(){var t;r.$store.state.wx.hideOptionMenu(),t=setInterval((function(){"function"===typeof r.$store.state.wx.agentConfig&&(clearInterval(t),r.$store.state.wx.agentConfig({corpid:r.agentData.corpid,agentid:r.agentData.agentid,timestamp:r.agentData.timestamp,nonceStr:r.agentData.nonceStr,signature:r.agentData.signature,jsApiList:r.agentData.jsApiList,success:function(){r.getInfoData()},fail:function(t){t.errMsg.indexOf("function not exist")>-1?uni.showToast({title:"版本过低请升级",image:"/static/fail.png",duration:2e3}):"agentConfig:not match any reliable domain."==t.errMsg?uni.showToast({title:"可信域名未填写或未验证",image:"/static/fail.png",duration:2e3}):uni.showToast({title:t.errMsg,image:"/static/fail.png",duration:2e3})}}))}),300)})));case 7:case"end":return e.stop()}}),e)})))()},getInfoData:function(){this.userPlatform=localStorage.getItem("userPlatform"),this.is_show_copyright=localStorage.getItem("is_show_copyright"),this.getFollow()},getData:function(){var t=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,a,n,r;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(uni.showLoading({title:"加载中...",duration:2e3}),i=[],t.selectDepartment.length>0)for(a=0;a<t.selectDepartment.length;a++)i.push(t.selectDepartment[a].id);return e.next=5,t.axios.post("wap-user-desktop/work-user-custom-statistic",{corp_id:localStorage.getItem("corpid"),user_id:localStorage.getItem("user_id"),user_ids:i});case 5:n=e.sent,r=n.data,0!=r.error?(uni.hideLoading(),uni.showToast({title:r.error_msg,image:"/static/fail.png",duration:2e3})):(t.name=r.data.name,t.avatar=r.data.avatar,t.departName=r.data.departName,t.allExternalNum=r.data.allExternalNum,t.allChatNum=r.data.allChatNum,t.todayExternalNum=r.data.todayExternalNum,t.todayExternalDelNum=r.data.todayExternalDelNum,t.todayExternalFollowNum=r.data.todayExternalFollowNum,t.todayChatNum=r.data.todayChatNum,t.isSelf=r.data.is_self,0==r.data.user_count?t.scopeName="全部":t.scopeName="已选择"+r.data.user_count+"个成员",t.getFollowData(),uni.hideLoading(),uni.showTabBar());case 8:case"end":return e.stop()}}),e)})))()},getFollow:function(){var t=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,a;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,t.axios.post("custom-field/follow",{uid:localStorage.getItem("uid")});case 2:i=e.sent,a=i.data,0!=a.error?uni.showToast({title:a.error_msg,image:"/static/fail.png",duration:2e3}):(t.followList=a.data.follow,t.followId=a.data.follow[0].id,t.followName=a.data.follow[0].title,t.toView="view"+t.followId,t.getData());case 5:case"end":return e.stop()}}),e)})))()},getFollowData:function(){var t=this;return(0,r.default)(regeneratorRuntime.mark((function e(){var i,a,n,r;return regeneratorRuntime.wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(i=[],t.selectDepartment.length>0)for(a=0;a<t.selectDepartment.length;a++)i.push(t.selectDepartment[a].id);return e.next=4,t.axios.post("wap-user-desktop/work-user-custom-follow",{corp_id:localStorage.getItem("corpid"),user_id:localStorage.getItem("user_id"),follow_id:t.followId,user_ids:i});case 4:n=e.sent,r=n.data,0!=r.error?(uni.hideLoading(),uni.showToast({title:r.error_msg,image:"/static/fail.png",duration:2e3})):(t.isReady=!0,uni.hideLoading(),t.externalFollowNum=r.data.externalFollowNum,t.notFollowDayData=r.data.notFollowDayData,t.notFollowDayData.length%2==1&&t.$nextTick((function(){var t=document.getElementsByClassName("title-data-content-card");t[t.length-2].style.borderBottom="1px solid #f1f4f6"})),uni.stopPullDownRefresh());case 7:case"end":return e.stop()}}),e)})))()},changeFollowId:function(t,e){this.followId=t,this.followName=e,document.getElementById("view"+t).offsetLeft+document.getElementById("view"+t).clientWidth>document.body.clientWidth&&(this.toView="view"+t),this.getFollowData()},showNotice:function(t){1==t?uni.showModal({content:"实际客户数，去除客户删除员工、员工删除客户",showCancel:!1}):2==t&&uni.showModal({content:"实际客户群数，去除已解散的群",showCancel:!1})},chooseStaff:function(){uni.pageScrollTo({scrollTop:0,duration:0}),uni.hideTabBar(),this.showChooseDepartment=!0},replyDepartment:function(t){this.showChooseDepartment=!1,uni.showTabBar(),"object"==(0,n.default)(t)&&(this.selectDepartment=t,this.getInfoData())},goToCustomList:function(){try{var t=0,e=[],i=-1,a=0,n=0,r=0,o=1;uni.setStorageSync("follow_id",this.followId),uni.setStorageSync("follow_name",this.followName),uni.setStorageSync("tab_btn_type",t),uni.setStorageSync("rangetime",e),uni.setStorageSync("sex",i),uni.setStorageSync("is_fans",a),uni.setStorageSync("chat_type",n),uni.setStorageSync("link_time",r),uni.setStorageSync("from",o)}catch(l){}var s=this.getParameter("agentId")||this.getParameter("agent_id")||localStorage.getItem("agent_id")||sessionStorage.getItem("agent_id")||"";uni.switchTab({url:"/pages/scrm/customer?follow_id="+this.followId+"&follow_name"+this.followName+"agent_id="+s})}}};e.default=l},6566:function(t,e,i){"use strict";var a=i("9bf2").f,n=i("7c73"),r=i("e2cc"),o=i("0366"),s=i("19aa"),l=i("2266"),c=i("7dd0"),d=i("2626"),u=i("83ab"),f=i("f183").fastKey,h=i("69f3"),m=h.set,g=h.getterFor;t.exports={getConstructor:function(t,e,i,c){var d=t((function(t,a){s(t,d,e),m(t,{type:e,index:n(null),first:void 0,last:void 0,size:0}),u||(t.size=0),void 0!=a&&l(a,t[c],{that:t,AS_ENTRIES:i})})),h=g(e),v=function(t,e,i){var a,n,r=h(t),o=p(t,e);return o?o.value=i:(r.last=o={index:n=f(e,!0),key:e,value:i,previous:a=r.last,next:void 0,removed:!1},r.first||(r.first=o),a&&(a.next=o),u?r.size++:t.size++,"F"!==n&&(r.index[n]=o)),t},p=function(t,e){var i,a=h(t),n=f(e);if("F"!==n)return a.index[n];for(i=a.first;i;i=i.next)if(i.key==e)return i};return r(d.prototype,{clear:function(){var t=this,e=h(t),i=e.index,a=e.first;while(a)a.removed=!0,a.previous&&(a.previous=a.previous.next=void 0),delete i[a.index],a=a.next;e.first=e.last=void 0,u?e.size=0:t.size=0},delete:function(t){var e=this,i=h(e),a=p(e,t);if(a){var n=a.next,r=a.previous;delete i.index[a.index],a.removed=!0,r&&(r.next=n),n&&(n.previous=r),i.first==a&&(i.first=n),i.last==a&&(i.last=r),u?i.size--:e.size--}return!!a},forEach:function(t){var e,i=h(this),a=o(t,arguments.length>1?arguments[1]:void 0,3);while(e=e?e.next:i.first){a(e.value,e.key,this);while(e&&e.removed)e=e.previous}},has:function(t){return!!p(this,t)}}),r(d.prototype,i?{get:function(t){var e=p(this,t);return e&&e.value},set:function(t,e){return v(this,0===t?0:t,e)}}:{add:function(t){return v(this,t=0===t?0:t,t)}}),u&&a(d.prototype,"size",{get:function(){return h(this).size}}),d},setStrong:function(t,e,i){var a=e+" Iterator",n=g(e),r=g(a);c(t,e,(function(t,e){m(this,{type:a,target:t,state:n(t),kind:e,last:void 0})}),(function(){var t=r(this),e=t.kind,i=t.last;while(i&&i.removed)i=i.previous;return t.target&&(t.last=i=i?i.next:t.state.first)?"keys"==e?{value:i.key,done:!1}:"values"==e?{value:i.value,done:!1}:{value:[i.key,i.value],done:!1}:(t.target=void 0,{value:void 0,done:!0})}),i?"entries":"values",!i,!0),d(e)}}},"65e5":function(t,e,i){"use strict";var a=i("f5b5"),n=i.n(a);n.a},"958a":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAByklEQVRYR+2XPUjDUBDH79ogOAlW2k4Kroog2DdZwcFJEVrd3ERcBKGOfkarjnZyEXFzUwtSJwdBnR6CILoKOrWlFZwESXtSKKUkNS95SaGBZHz37n93v/fuyEMw+Rhj0wCQq2+Z4ZzfmO3/z2ZHBzsuoVgstmqS1BAiLtfsRHQCAG8yhADAsg4yxkgySFvcOjqhMyI6aC4bEScB4LS+tkREdzJYRDqIuAEAizXtZkIZzvlac0A73eGkORhjRwCQspLQGABs14Ptcc6fZAgxxkx1LCckE1zGxxsJEdE7IsrOGRkwDR8iqs2pQf0dciTqlnOjy4joGxHLbgnb0SGiECL2CLvMjqiTvd641ABgGIxOqrbj6xMS0bJMKJkuGX5NCKvx7Gb4cWbnq79LqX7og/1qgYHcbu9nYr84jhR40NuvtvoMP4VeTqhsqBAr2sqlGnmZXS9ElG7lQk9A+9Hmrw8jhTm1MEJB5dhIKBTXr1kmJDp7t+x+QiKSPiHXCCXTpVu9GFW0VFaNvibUfBiDynkL+0JWjRYTan4Yg0qmxRyaku4yfzCKnkGis3fL7neZiKQ3CBHRMwDci6ppk30CEUc7+xnUpspty/4B8099Ag2BAn4AAAAASUVORK5CYII="},"9db4":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAEeklEQVRYhe2YXWxURRTHDxARrCAfYvBVn0hrfdjO/8xdWtMY06QxEHZubwhFPmPCgxUjJPgAhk3nbvmoMUQNxKQoURCSRhJ99AEeNCEixI+Q+NaYEBrszixbio34wfVh524vZbfdxSz44CTnYc/878xvz5xzJvcSVRjpdLoFQB5A1CDLp9Pplkp7VxzMvK2BMBGAiJm31Qzked5CAPuZ+UgjDMB+z/MW1gz0/5g+AHgA+oQQOx+GAegD4MUwuxqdwHXYLgJg/gMgsRlK/DgphHjmYRiAkzFHEujoQ8zho/cNJIRYycwawEVmvsbMPzPzx8wsHziQlFIBGK9y/neklO8HQTDvgQBJKV8C8KfT/gHglBBit4vWlXgdKeV7DQdKpVKPABhxulEAz0+TzAVwMBEpbigQgJdjHTN3VdMx81dON9RQIHcsEYCrMy0spVzvdFcaDXTEaS7MsnCHi+IvjQbaHXfSmapISrnFAX3TUKC2traWhO7VSpogCOYx87eu0t6qEyi+T0dqLntm/sLpJgH0JudaW1ubmPkTN1/o7Ox8sh4gIprLzO3pdPqpmoFSqdTTidKPhBA/ATjGzJ8CGIv7k5RyTfK5rdlogerP9yptPlKhOedrc97X5kQmZzZ3DV5vumejWoE8z0sD+L5Kl45bQkEIIeJn1ECxR2l7TWkTVbExFZpNdQMJIfYB+CuhHWXmz5n5AwDHAXwN4Lab20NE5OcKe6c2zk8qbYZVaPb5ObNXaXtaaTMRz/u6cLBmIGY+nND8IKXsJqK5FSK9nJm7mpub5ytte8owofkyyI6tnK7PDNxcrkJ7pgyVs9tnBQKwITE/1NzcPL9aFOOxNRstUNqMlmGCqNwm1ulfn81os6osjqI5SptTJShrN2bt4qpAqVTqCUy9LA4T0ZzZYIiIXAJHSpvJ4PBUZII3ry5UoS0qnZ8MsuPLypDZaInS9obSJurRhR1VgQDsiZthe3v70lpgiIj80Bx3QMOxb002eszPFVrLxzhQWB1kxx6v+Ew1IFfWEYCwVhgiIhWac0qbqCdn3yYqHZPSpji9wnxtbmVyhRYiIl/bN5zvUkUgz/OWAbjjSjlVD5CvzXmXP/uIiNbriVVKm9/uKfnQ/O6H+TYioky/2en8lysCCSFE7O/u7n60TqATLklPx77g3fFl6sC4iGECXXxxwzs3y91chfZDB3l2NiBbDwwRkR+aLW7jiQ3Z0fKmmwavN/na3FLa3g4GJlbE/rWH8otUaI3SJvLDQl+1HJrDzK9IKVfXC7Rp8HqT0mas9I/tGYqicnUG/YXngv7iXSmgtB0q5Y8d7z1QXNqQ16BMv9lcTt7QfrYue2PJdM3aQ/lFSueHEt36NSIiAvC3A/rxX35i2UWJDu5rc3Aqie0NPzTHXTW9XsqZ0jGVoM0xIhdJABdnujDrMSHEC8ko+Dm7XWlrq12uGW3G/dD03RU6KeUqAN8lInVfxswjHR0dK2ja2Ji1i3t0YYfSZtjX5pIKzWXVb876YaGv90Dxnob7D8zKjyxCb6DWAAAAAElFTkSuQmCC"},bfdd:function(t,e,i){var a=i("f6ec");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("7f382a9d",a,!0,{sourceMap:!1,shadowMode:!1})},c75b:function(t,e,i){"use strict";i.r(e);var a=i("31e6"),n=i("d543");for(var r in n)["default"].indexOf(r)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("65e5");var o,s=i("f0c5"),l=Object(s["a"])(n["default"],a["b"],a["c"],!1,null,"1853d04e",null,!1,a["a"],o);e["default"]=l.exports},d44c:function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return a}));var a={uniIcons:i("2ba4").default},n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",[t.isReady&&!t.showChooseDepartment?a("v-uni-view",{staticClass:"box"},[a("v-uni-view",{staticClass:"list"},[a("v-uni-view",{staticClass:"list-hd"},[a("v-uni-image",{staticStyle:{width:"2.4rem  /* 45/18.75 */",height:"2.4rem  /* 45/18.75 */","background-color":"#FFF","border-radius":"50%",float:"left"},attrs:{src:t.avatar}}),a("v-uni-view",{staticClass:"list-hd-content"},[a("v-uni-view",{staticClass:"list-hd-content-name"},[t._v(t._s(t.name))]),a("v-uni-view",{staticClass:"list-hd-content-name",staticStyle:{color:"#B2CBFF","font-size":"0.64rem  /* 12/18.75 */"}},[t._v(t._s(t.departName))])],1),"other"!=t.userPlatform?a("v-uni-view",{staticClass:"list-hd-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.addCustom.apply(void 0,arguments)}}},[a("uni-icons",{staticStyle:{color:"#FFF","margin-right":"0.107rem  /* 2/18.75 */"},attrs:{type:"plus",size:"12"}}),t._v("添加客户")],1):t._e()],1),a("v-uni-view",{staticClass:"list-bd"})],1),a("v-uni-view",{staticClass:"title"},[a("v-uni-view",{staticClass:"title-head",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.chooseStaff.apply(void 0,arguments)}}},[1==t.isSelf?a("v-uni-view",{staticStyle:{float:"left"}},[t._v("仅自己数据")]):t._e(),0==t.isSelf?a("v-uni-view",{staticStyle:{float:"left"}},[t._v(t._s(t.scopeName))]):t._e(),0==t.isSelf?a("uni-icons",{staticStyle:{color:"rgba(172,172,172,1)",float:"right"},attrs:{type:"arrowright",size:"14"}}):t._e()],1),a("v-uni-view",{staticClass:"title-data"},[a("v-uni-view",{staticClass:"title-data-left"},[a("v-uni-view",{staticClass:"title-data-left-title"},[t._v(t._s(t.allExternalNum))]),a("v-uni-view",{staticClass:"title-data-left-text"},[t._v("累计客户"),a("uni-icons",{staticStyle:{color:"rgba(172,172,172,1)","font-size":"0.747rem  /* 14/18.75 */"},attrs:{type:"help-filled"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.showNotice(1)}}})],1)],1),a("v-uni-view",{staticClass:"title-data-left"},[a("v-uni-view",{staticClass:"title-data-left-title",staticStyle:{"margin-left":"0.8rem  /* 15/18.75 */"}},[t._v(t._s(t.allChatNum))]),a("v-uni-view",{staticClass:"title-data-left-text",staticStyle:{"margin-left":"0.8rem  /* 15/18.75 */"}},[t._v("累计客户群"),a("uni-icons",{staticStyle:{color:"rgba(172,172,172,1)","font-size":"0.747rem  /* 14/18.75 */"},attrs:{type:"help-filled"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.showNotice(2)}}})],1)],1)],1),a("v-uni-view",{staticClass:"title-data-content"},[a("v-uni-view",{staticClass:"title-data-content-card"},[a("v-uni-image",{staticStyle:{width:"0.96rem  /* 18/18.75 */",height:"0.96rem  /* 18/18.75 */",float:"left",margin:"1.067rem  /* 20/18.75 */ 0.64rem  /* 12/18.75 */ 0 0.32rem  /* 6/18.75 */"},attrs:{src:i("9db4")}}),a("v-uni-view",{staticStyle:{float:"left",width:"calc(100% - 2.453rem  /* 46/18.75 */)"}},[a("v-uni-view",{staticClass:"title-data-content-card-title"},[t._v(t._s(t.todayExternalNum))]),a("v-uni-view",{staticClass:"title-data-content-card-text"},[t._v("今日新增客户")])],1)],1),a("v-uni-view",{staticClass:"title-data-content-card"},[a("v-uni-image",{staticStyle:{width:"0.853rem  /* 16/18.75 */",height:"0.96rem  /* 18/18.75 */",float:"left",margin:"1.067rem  /* 20/18.75 */ 0.747rem  /* 14/18.75 */ 0 0.32rem  /* 6/18.75 */"},attrs:{src:i("3ad5")}}),a("v-uni-view",{staticStyle:{float:"left",width:"calc(100% - 2.453rem  /* 46/18.75 */)"}},[a("v-uni-view",{staticClass:"title-data-content-card-title"},[t._v(t._s(t.todayExternalDelNum))]),a("v-uni-view",{staticClass:"title-data-content-card-text"},[t._v("今日流失客户")])],1)],1),a("v-uni-view",{staticClass:"title-data-content-card"},[a("v-uni-image",{staticStyle:{width:"0.96rem  /* 18/18.75 */",height:"0.96rem  /* 18/18.75 */",float:"left",margin:"1.067rem  /* 20/18.75 */ 0.64rem  /* 12/18.75 */ 0 0.32rem  /* 6/18.75 */"},attrs:{src:i("30b4")}}),a("v-uni-view",{staticStyle:{float:"left",width:"calc(100% - 2.453rem  /* 46/18.75 */)"}},[a("v-uni-view",{staticClass:"title-data-content-card-title"},[t._v(t._s(t.todayExternalFollowNum))]),a("v-uni-view",{staticClass:"title-data-content-card-text"},[t._v("今日跟进客户")])],1)],1),a("v-uni-view",{staticClass:"title-data-content-card"},[a("v-uni-image",{staticStyle:{width:"1.253rem  /* 23.5/18.75 */",height:"0.96rem  /* 18/18.75 */",float:"left",margin:"1.067rem  /* 20/18.75 */ 0.347rem  /* 6.5/18.75 */ 0 0.32rem  /* 6/18.75 */"},attrs:{src:i("df7d")}}),a("v-uni-view",{staticStyle:{float:"left",width:"calc(100% - 2.453rem  /* 46/18.75 */)"}},[a("v-uni-view",{staticClass:"title-data-content-card-title"},[t._v(t._s(t.todayChatNum))]),a("v-uni-view",{staticClass:"title-data-content-card-text"},[t._v("今日新增入群数")])],1)],1)],1)],1),a("v-uni-view",{staticClass:"content"},[a("v-uni-view",{staticClass:"content-hd"},[a("v-uni-view",{staticClass:"content-hd-title"},[t._v("跟进简报")]),a("v-uni-scroll-view",{staticClass:"scroll-view_H",staticStyle:{"white-space":"nowrap"},attrs:{"scroll-x":"true","scroll-left":"0","scroll-into-view":t.toView,"scroll-with-animation":!0}},t._l(t.followList,(function(e){return a("v-uni-view",{staticClass:"content-hd-tabs",style:{background:e.id==t.followId?"#5283EC":"",color:e.id==t.followId?"#FFF":""},attrs:{id:"view"+e.id},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.changeFollowId(e.id,e.title)}}},[t._v(t._s(e.title)),0==e.status?a("v-uni-text",[t._v("（已删除）")]):t._e()],1)})),1),a("v-uni-view",{staticStyle:{cursor:"pointer",overflow:"hidden"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.goToCustomList.apply(void 0,arguments)}}},[a("v-uni-text",{staticStyle:{"font-size":"0.747rem  /* 14/18.75 */",color:"#666","line-height":"3.467rem  /* 65/18.75 */"}},[t._v("当前状态人数")]),a("v-uni-view",{staticStyle:{display:"inline-block",margin:"0.8rem  /* 15/18.75 */ 0 1.067rem  /* 20/18.75 */ 0",float:"right"}},[a("v-uni-text",{staticStyle:{"font-size":"1.28rem  /* 24/18.75 */",color:"#343434","margin-right":"1.067rem  /* 20/18.75 */"}},[t._v(t._s(t.externalFollowNum))]),a("uni-icons",{staticStyle:{color:"rgba(172,172,172,1)","vertical-align":"text-top"},attrs:{type:"arrowright",size:"14"}})],1)],1)],1),a("v-uni-view",{staticClass:"content-bd"},t._l(t.notFollowDayData,(function(e){return a("v-uni-view",{staticClass:"title-data-content-card"},[a("v-uni-image",{staticStyle:{width:"0.96rem  /* 18/18.75 */",height:"0.96rem  /* 18/18.75 */",float:"left",margin:"1.067rem  /* 20/18.75 */ 0.64rem  /* 12/18.75 */ 0 0.32rem  /* 6/18.75 */"},attrs:{src:i("958a")}}),a("v-uni-view",{staticStyle:{float:"left"}},[a("v-uni-view",{staticClass:"title-data-content-card-title"},[t._v(t._s(e.num))]),e.day>0?a("v-uni-view",{staticClass:"title-data-content-card-text"},[t._v("超过"+t._s(e.day)+"天未联系")]):t._e(),0==e.day?a("v-uni-view",{staticClass:"title-data-content-card-text"},[t._v("一直未联系")]):t._e()],1)],1)})),1)],1),a("Footer",{directives:[{name:"show",rawName:"v-show",value:1==t.is_show_copyright,expression:"is_show_copyright == 1"}]})],1):t._e(),t.showChooseDepartment?a("ChooseDepartment",{attrs:{"call-back":t.replyDepartment,selectArray:t.selectDepartment}}):t._e()],1)},r=[]},d543:function(t,e,i){"use strict";i.r(e);var a=i("4bb6"),n=i.n(a);for(var r in a)["default"].indexOf(r)<0&&function(t){i.d(e,t,(function(){return a[t]}))}(r);e["default"]=n.a},df7d:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAkCAYAAAAZ4GNvAAAFYElEQVRYhe2YXWxURRTHp23wg4BiBBICIRLig5qY1s38z97tFpeEgD4Q6NxeRQOJPiA8+JUGP16Il527W5EY8EESBUwwRmOjCWAERBpRGxM1viASpE1IMIJlZ3aL1UqBdn3ovdvhcneXLbXywEnOy87M//zOuXtnzlzGJtBisdgUIlpDRJ8D6ANwGUABwDcA2mOx2J0TGW/CzLIsADgBoFjBc0TU9n+zXmEAlgEYNCAvAvgOwMcADgP4yxgbIaIXJg1uuXtmppB6o5C6W0jVa3u63QBfAKDfgNvd0tIyx1zf3Nw8HcBrflJFAMPxeHxJMG57ul1I1StkrltIvXG5e2bmhIDbmfPLhNRKSFU0vDcYJ6JPDHC3khYRrfDfgyKAk47jNDDG2Ci4qa9Vq9SPXBe4k9WWkOqCIVoQMtctPP04Y4wlk8n5AIZ9mCOMsbpqmkT0dpAs53w5Y4y1pnOr/KoXjCQutHk6MS5w1y3WC6mOGeCu4xZvMecAWBcGqWZ+wiP+uu3m2KPP9dxqp/WmIKadVr+4brG+ZnjhFRaVwD29I2oO53xrAJ9KpWZcqzYRnQJQJKKvomPrHUHs1nTu4XHA518eq0ChKWoOgF0BfC3anPOj/rofosbt9LmmIHabVK/UDN+aVh2l7LcMzJ5M+NYtA7PHKq86bsLfhJ8MeOHlM6XdJpObEzUHwE4DvuoeH4Ynou8jY2dyc4z9PlszvO2pZ8fgC4vLwG8O4C3Lmnut2gDO+esORsMXFpcq76nna4Zvleo+I/sup7PYEJ7DOX8sgI/H42uvRZdzzo124vXwuNNZbBBSdwWxnezA/TXDM8aY8NS+sQRyB5xModE88RobG2cYHWNvLBabWkWyjoj2G08LwYDrFuvtdKFJeOpg6Xzx1J5xgTPGmJ3JPyikHjKbJtvTF0UmX/ofAngzgCGivalU6rZy4AAyRtUPl+J4+Q4h1aVQ8zdkp/UDNQE7TrHBlmql7en9EYKBnwzmp1KpaUR03ID6OR6PC8uybmeMMdd16wG0ENEhY855Iro30BBSnYyOo4dsT+1p8worKvY4z7xTnNIm8+uubk9L/c1pIdURIfVHdkf/EnNtPB6/B0Bv6NZ0EcAZAH+Hfu8H0GKut2X/EtvTHwqpvxZS/1amYL12Orc+3BwyIXVbBPSwkKrLlnqt4w1W3Eksy5pLRE9HgIb9LOe8sdrTF5sH59lSrxWe7vI5jCKqUyKrR6+Sdia/OgQ9ILzc1pWyb2E58WQyeReAJwHsBnC6CnDYLwP4kYg6OOeLXNet2PKulH0Lhae3CqkGrnj3MvnVTHiqp/SCSLXNyQ7MihJpbm6e7lf3CwCXqgD+SUS/Y/QLwlC1pwFgO+d8Eatw0LW6f8y2M3qbkGrI34l6mJBqZBQ+vztqUTwef4iI3sOVl2jTjxPRu5zzpyzLakqlUtNCEnVENI+IlgJ4iYj2AtBltE4Q0QYAd5dLQkj1vl/sEWZsgbvCEwG8gbFbT+CXOOeHAKxPJpPzywWpZI7jNHDOE0SUBdATToKI8pZlRV5CbE/vCpjLwgNwQqLHiOjFRCIR2aRdj3HOuX+3LRjx+ojojnHBE9GXvsg/RLR0ooGjLBaLTSWit4wnsKYavP+f1zvNSfC/fhFR92SAG3EXBPCc81fD40LqnSV421OHhVTDtpd/IiTyqy/y7eShMxaLxeZXgm/L5FcFZxBzOosNTvbsVdvjjQrPGGNOdmBWVJdbshsZvqrdhB+HTQg8Ef3ki2gA+2pxIvoUQOc4/AMAnxlb5YZxwQNor9KT/Nc+mEgkyjaH1ayOiDYByE0y9DARHSWiyEu/af8CNRkS6XlDpo4AAAAASUVORK5CYII="},f5b5:function(t,e,i){var a=i("ff87");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var n=i("4f06").default;n("22ebd033",a,!0,{sourceMap:!1,shadowMode:!1})},f6ec:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,".box[data-v-1cf4de93]{background:#f2f3f5;text-align:left;\n\t/*position: fixed;*/\n\t/*top: 0;*/\n\t/*left: 0;*/\n\t/*right: 0;*/\n\t/*bottom: 50px;*/\n\t/*overflow-y: auto;*/font-size:.8rem ;height:calc(100% - 50px);overflow:hidden}.list[data-v-1cf4de93]{width:20rem /* 375/18.75 */;height:7.377rem /* 138.31/18.75 */;overflow:hidden;margin:auto}.list-hd[data-v-1cf4de93]{background:#5284ec;height:3.813rem /* 71.5/18.75 */;padding:.8rem .8rem 0;box-sizing:border-box}.list-hd-content[data-v-1cf4de93]{height:2.4rem /* 45/18.75 */;float:left;width:calc(100% - 7.1rem);margin:0 .267rem\n}.list-hd-content-name[data-v-1cf4de93]{white-space:nowrap;text-overflow:ellipsis;overflow:hidden;word-break:break-all;width:100%;color:#fff;font-weight:700;margin-top:.267rem\n}.list-hd-btn[data-v-1cf4de93]{width:4rem /* 75/18.75 */;height:1.28rem /* 24/18.75 */;line-height:1.28rem /* 24/18.75 */;text-align:center;color:#fff;border:1px solid #b2cbff;border-radius:.533rem ;float:left;margin-top:.507rem ;font-size:.587rem\n}.list-bd[data-v-1cf4de93]{background:#5284ec;height:3.2rem /* 60/18.75 */;border-bottom-left-radius:50%;border-bottom-right-radius:50%;width:106%;margin-left:-3%;margin-top:-1px}.title[data-v-1cf4de93]{width:18.933rem /* 355/18.75 */;background:#fff;border-radius:.533rem ;margin:-3.5632rem auto 0;padding:.64rem .8rem 0;box-sizing:border-box}.title-head[data-v-1cf4de93]{line-height:1.84rem /* 34.5/18.75 */;border-bottom:1px solid #f1f4f6;overflow:hidden}.title-data[data-v-1cf4de93]{border-bottom:.053rem solid #f1f4f6;overflow:hidden}.title-data-left[data-v-1cf4de93]{width:50%;float:left}.title-data-left-title[data-v-1cf4de93]{color:#343434;font-size:1.28rem /* 24/18.75 */;margin-top:1.067rem /* 20/18.75 */;margin-bottom:.107rem\n}.title-data-left-text[data-v-1cf4de93]{color:#5c5c5c;font-size:.747rem ;margin-bottom:.533rem\n}.title-data-content-card[data-v-1cf4de93]{border-right:.053rem solid #f1f4f6;display:inline-block;width:calc(50% - .053rem)}.title-data-content-card[data-v-1cf4de93]:nth-child(2n){border-right:0;padding-left:.8rem ;box-sizing:border-box}.title-data-content-card[data-v-1cf4de93]:not(:nth-last-child(-1n+2)){border-bottom:.053rem solid #f1f4f6}.title-data-content-card-title[data-v-1cf4de93]{font-weight:700;margin:.533rem 0 .267rem 0;font-size:.853rem\n}.title-data-content-card-text[data-v-1cf4de93]{color:#656565;font-size:.64rem ;margin-bottom:.533rem\n}.content[data-v-1cf4de93]{width:18.933rem /* 355/18.75 */;background:#fff;border-radius:.533rem ;margin:.533rem auto .533rem ;padding:1.067rem .8rem ;box-sizing:border-box}.content-hd[data-v-1cf4de93]{border-bottom:1px solid #f1f4f6}.content-hd-title[data-v-1cf4de93]{color:#000;font-weight:700;margin-bottom:.693rem\n}.content-hd-tabs[data-v-1cf4de93]{display:inline-block;padding:.16rem .427rem ;height:1.067rem /* 20/18.75 */;line-height:1.067rem /* 20/18.75 */;text-align:center;font-size:.64rem ;border:1px solid #f1f4f6;border-radius:.8rem ;margin:0 .267rem .267rem 0;cursor:pointer}",""]),t.exports=e},ff87:function(t,e,i){var a=i("24fb");e=a(!1),e.push([t.i,"[data-v-1853d04e] .uni-searchbar__box-search-input{font-size:14px!important}.box[data-v-1853d04e]{background:#f2f3f5;text-align:left;position:fixed;top:0;left:0;right:0;bottom:0;font-size:.8rem ;overflow-y:auto;height:calc(100% - 50px)}.footer[data-v-1853d04e]{height:2.667rem /* 50/18.75 */;position:fixed;bottom:0;left:0;right:0;border-top:1px solid #f1f4f6;background:#fff}.btn[data-v-1853d04e]{background-color:#5284ec;color:#e7effc;float:right;font-size:15px;width:60px;height:30px;line-height:30px;border-radius:15px;margin:.533rem 15px}[data-v-1853d04e] .uni-input-placeholder,[data-v-1853d04e] .uni-searchbar__text-placeholder{color:#ccc}[data-v-1853d04e] .uni-searchbar__box-icon-clear{line-height:unset}[data-v-1853d04e] .uni-searchbar__box-icon-clear .uni-icons{color:#ccc!important}[data-v-1853d04e] .uni-searchbar__box-icon-search{color:#ccc!important}\n/*.check-box /deep/ .uni-checkbox-input {*/\n/*\tborder-radius: 50%;*/\n/*\twidth: 0.96rem !* 18/18.75 *!;*/\n/*\theight: 0.96rem !* 18/18.75 *!;*/\n/*\tborder: 2px solid #FFF;*/\n/*}*/.scroll-view[data-v-1853d04e]{width:calc(100% - 127px);float:left;white-space:nowrap;height:2.667rem /* 50/18.75 */;line-height:2.667rem /* 50/18.75 */;margin-left:7.5px}.scroll-view-item[data-v-1853d04e]{display:inline-block}.tag[data-v-1853d04e]{display:inline-block;padding:0 .267rem ;height:1.067rem /* 20/18.75 */;line-height:1.067rem /* 20/18.75 */;text-align:center;font-size:.587rem ;border:1px solid #f1f4f6;border-radius:.8rem ;margin:0 .267rem .267rem 0;cursor:pointer}[data-v-1853d04e] .uni-searchbar{background-color:#5284ec;width:100%}.empty-img[data-v-1853d04e]{width:38%;position:fixed;left:50%;top:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}",""]),t.exports=e}}]);