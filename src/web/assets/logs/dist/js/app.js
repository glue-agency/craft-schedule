(function(t){function e(e){for(var n,l,i=e[0],o=e[1],c=e[2],d=0,h=[];d<i.length;d++)l=i[d],s[l]&&h.push(s[l][0]),s[l]=0;for(n in o)Object.prototype.hasOwnProperty.call(o,n)&&(t[n]=o[n]);u&&u(e);while(h.length)h.shift()();return r.push.apply(r,c||[]),a()}function a(){for(var t,e=0;e<r.length;e++){for(var a=r[e],n=!0,i=1;i<a.length;i++){var o=a[i];0!==s[o]&&(n=!1)}n&&(r.splice(e--,1),t=l(l.s=a[0]))}return t}var n={},s={app:0},r=[];function l(e){if(n[e])return n[e].exports;var a=n[e]={i:e,l:!1,exports:{}};return t[e].call(a.exports,a,a.exports,l),a.l=!0,a.exports}l.m=t,l.c=n,l.d=function(t,e,a){l.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:a})},l.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},l.t=function(t,e){if(1&e&&(t=l(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var a=Object.create(null);if(l.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)l.d(a,n,function(e){return t[e]}.bind(null,n));return a},l.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return l.d(e,"a",e),e},l.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},l.p="/";var i=window["webpackJsonp"]=window["webpackJsonp"]||[],o=i.push.bind(i);i.push=e,i=i.slice();for(var c=0;c<i.length;c++)e(i[c]);var u=o;r.push([0,"chunk-vendors"]),a()})({0:function(t,e,a){t.exports=a("cd49")},"034f":function(t,e,a){"use strict";var n=a("64a9"),s=a.n(n);s.a},1:function(t,e){},"574d":function(t,e,a){},"64a9":function(t,e,a){},"6cec":function(t,e,a){"use strict";var n=a("574d"),s=a.n(n);s.a},cd49:function(t,e,a){"use strict";a.r(e);a("cadf"),a("551c"),a("f751"),a("097d");var n=a("2b0e"),s=a("5c96"),r=a.n(s),l=(a("0fae"),function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("el-container",[a("el-aside",[a("schedule-list")],1),a("el-main",[a("router-view")],1)],1)}),i=[],o=a("d225"),c=a("308d"),u=a("6bb5"),d=a("4e2b"),h=a("9ab4"),f=a("60a3"),p=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"schedule-list"},[a("div",{staticClass:"search"},[a("el-input",{attrs:{placeholder:"Search all schedules",learable:"","suffix-icon":"el-icon-search",size:"small"},model:{value:t.search,callback:function(e){t.search=e},expression:"search"}})],1),a("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"list"},t._l(t.schedules,function(e){return a("el-card",{staticClass:"box-card",class:{passed:e.status,failed:!e.status},attrs:{shadow:"never"}},[a("h2",[a("router-link",{attrs:{to:{name:"logs",params:{handle:e.handle}}}},[a("span",[!0===e.status?a("i",{staticClass:"el-icon-check"}):!1===e.status?a("i",{staticClass:"el-icon-check"}):a("i",{staticClass:"el-icon-close"})]),a("span",[t._v(t._s(e.name))])])],1),a("p",{staticClass:"right"},[a("i",{staticClass:"el-icon-caret-right"}),t._v(" "+t._s(e.total))]),a("p",[a("i",{staticClass:"el-icon-time"}),t._v(" Duration: "+t._s(e.duration)+" ms")]),a("p",[a("i",{staticClass:"el-icon-date"}),t._v(" Finished: "+t._s(e.finished))])])}),1)])},b=[],g=(a("386d"),a("b0b4")),v=function(t){function e(){var t;return Object(o["a"])(this,e),t=Object(c["a"])(this,Object(u["a"])(e).apply(this,arguments)),t.loading=!0,t.schedules=[],t.search="",t}return Object(d["a"])(e,t),Object(g["a"])(e,[{key:"onSearch",value:function(){this.fetchSchedules()}},{key:"mounted",value:function(){this.fetchSchedules()}},{key:"fetchSchedules",value:function(){var t=this;this.$http.post(window.Craft.schedule.logs.api.schedules,{criteria:{search:this.search}}).then(function(e){t.loading=!1,t.schedules=e.data.data})}}]),e}(f["b"]);h["a"]([Object(f["c"])("search")],v.prototype,"onSearch",null),v=h["a"]([f["a"]],v);var m=v,y=m,w=(a("6cec"),a("2877")),_=Object(w["a"])(y,p,b,!1,null,"7a7461b6",null),O=_.exports,S=function(t){function e(){return Object(o["a"])(this,e),Object(c["a"])(this,Object(u["a"])(e).apply(this,arguments))}return Object(d["a"])(e,t),e}(f["b"]);S=h["a"]([Object(f["a"])({components:{ScheduleList:O}})],S);var j=S,C=j,k=(a("034f"),Object(w["a"])(C,l,i,!1,null,null,null)),x=k.exports,D=a("8c4f"),z=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"grid-logs"},[a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticStyle:{width:"100%"},attrs:{data:t.tableData}},[a("el-table-column",{attrs:{type:"index",index:t.indexMethod}}),a("el-table-column",{attrs:{prop:"status",label:t.translations.Status,width:"180"},scopedSlots:t._u([{key:"default",fn:function(e){return["successful"===e.row.status?a("el-tag",{attrs:{type:"success",size:"medium"}},[t._v("\n                    "+t._s(e.row.status)+"\n                ")]):"failed"===e.row.status?[a("el-popover",{attrs:{placement:"top-start",width:"200",trigger:"hover",title:t.translations.Reason,content:e.row.reason}},[a("el-tag",{attrs:{slot:"reference",type:"danger",size:"medium"},slot:"reference"},[t._v(t._s(e.row.status))])],1)]:a("el-tag",{attrs:{type:"info",size:"medium"}},[t._v(t._s(e.row.status))])]}}])}),a("el-table-column",{attrs:{prop:"startTime",label:t.translations["Start Date"],width:"200"}}),a("el-table-column",{attrs:{prop:"endTime",label:t.translations["End Date"],width:"200"}}),a("el-table-column",{attrs:{label:t.translations.Duration},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n                "+t._s(e.row.duration)+" ms\n            ")]}}])}),a("el-table-column",{attrs:{prop:"output",label:t.translations.Output,type:"expand",width:"100"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-form",{staticClass:"demo-table-expand",attrs:{"label-position":"left",inline:""}},[a("div",{staticStyle:{"background-color":"black"}},[t._v(t._s(e.row.output))])])]}}])})],1),a("el-pagination",{staticStyle:{"margin-top":"10px"},attrs:{"current-page":t.currentPage,"page-sizes":[20,50,100,500],"page-size":t.pageSize,layout:"total, sizes, prev, pager, next, jumper",total:t.total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}})],1)},P=[],E=function(t){function e(){var t;return Object(o["a"])(this,e),t=Object(c["a"])(this,Object(u["a"])(e).apply(this,arguments)),t.loading=!0,t.tableData=[],t.total=0,t.currentPage=1,t.pageSize=20,t.translations={Status:"Status",Date:"Date",Reason:"Reason","Start Date":"Start Date","End Date":"End Date",Duration:"Duration",Output:"Output"},t}return Object(d["a"])(e,t),Object(g["a"])(e,[{key:"onChangeRoute",value:function(){this.fetchLogs()}},{key:"created",value:function(){this.fetchLogs()}},{key:"mounted",value:function(){this.fetchLogs(),"undefined"!==typeof window.Craft.translations.schedule&&(this.translations=window.Craft.translations.schedule)}},{key:"indexMethod",value:function(t){return this.tableData[t].sortOrder}},{key:"fetchLogs",value:function(){var t=this;this.loading=!0,this.tableData=[],this.$http.post(window.Craft.schedule.logs.api.logs,{criteria:{schedule:t.$route.params["handle"],offset:(t.currentPage-1)*t.pageSize,limit:t.pageSize}}).then(function(e){t.loading=!1,t.total=e.data.total,t.tableData=e.data.data})}},{key:"handleSizeChange",value:function(t){this.pageSize=t,this.fetchLogs()}},{key:"handleCurrentChange",value:function(t){this.currentPage=t,this.fetchLogs()}}]),e}(f["b"]);h["a"]([Object(f["c"])("$route")],E.prototype,"onChangeRoute",null),E=h["a"]([f["a"]],E);var L=E,$=L,M=Object(w["a"])($,z,P,!1,null,null,null),T=M.exports;n["default"].use(D["a"]);var R=new D["a"]({mode:"history",base:window.Craft.schedule.logs.cpTrigger,routes:[{path:"/schedule/logs/:handle",name:"logs",component:T}]}),N=a("b2d6"),J=a.n(N);n["default"].use(r.a,{locale:J.a});var F=a("28dd");n["default"].config.productionTip=!1,n["default"].use(r.a),n["default"].use(F["a"]),window.ELEMENT=r.a,new n["default"]({router:R,render:function(t){return t(x)}}).$mount("#app")}});
//# sourceMappingURL=app.js.map