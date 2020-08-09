(function(e){function t(t){for(var i,a,o=t[0],c=t[1],l=t[2],f=0,p=[];f<o.length;f++)a=o[f],Object.prototype.hasOwnProperty.call(r,a)&&r[a]&&p.push(r[a][0]),r[a]=0;for(i in c)Object.prototype.hasOwnProperty.call(c,i)&&(e[i]=c[i]);u&&u(t);while(p.length)p.shift()();return s.push.apply(s,l||[]),n()}function n(){for(var e,t=0;t<s.length;t++){for(var n=s[t],i=!0,o=1;o<n.length;o++){var c=n[o];0!==r[c]&&(i=!1)}i&&(s.splice(t--,1),e=a(a.s=n[0]))}return e}var i={},r={app:0},s=[];function a(t){if(i[t])return i[t].exports;var n=i[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,a),n.l=!0,n.exports}a.m=e,a.c=i,a.d=function(e,t,n){a.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},a.r=function(e){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.t=function(e,t){if(1&t&&(e=a(e)),8&t)return e;if(4&t&&"object"===typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(a.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)a.d(n,i,function(t){return e[t]}.bind(null,i));return n},a.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return a.d(t,"a",t),t},a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.p="/";var o=window["webpackJsonp"]=window["webpackJsonp"]||[],c=o.push.bind(o);o.push=t,o=o.slice();for(var l=0;l<o.length;l++)t(o[l]);var u=c;s.push([0,"chunk-vendors"]),n()})({0:function(e,t,n){e.exports=n("56d7")},"034f":function(e,t,n){"use strict";var i=n("85ec"),r=n.n(i);r.a},"56d7":function(e,t,n){"use strict";n.r(t);n("e260"),n("e6cf"),n("cca6"),n("a79d");var i=n("2b0e"),r=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"section",attrs:{id:"filechecksum"}},[n("h2",[e._v("File Checksum")]),n("FileChart")],1)},s=[],a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{attrs:{id:"filechart"}},[n("button",{on:{click:e.startScanning}},[e._v("Getting Files Meta Data")]),e.scannedFilesNum>0?n("MyAnimatedNumber",{attrs:{value:e.scannedFilesNum,duration:8e3}}):e._e(),e.showSpinner?n("div",{attrs:{id:"fetching-file-spinner"}},[n("Spinner",{attrs:{size:"medium"}})],1):e._e(),n("br"),e.fileListVisibility?n("div",{attrs:{id:"fileList"}},[e._v(e._s(e.fileListJson))]):e._e(),n("br")],1)},o=[],c=n("f7b4"),l=n.n(c),u=n("6ca4"),f=n("5b7e"),p=n.n(f),h=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("animated-number",{attrs:{value:e.value,formatValue:e.formatToPrice,duration:e.duration}})},d=[],m=(n("a9e3"),n("b680"),n("044d")),b=n.n(m),v={components:{AnimatedNumber:b.a},props:["value","duration"],methods:{formatToPrice:function(e){return"<h3> ".concat(Number(e).toFixed(0),"</h1>")}},watch:{value:function(){console.log(this.value)}}},g=v,y=n("2877"),S=Object(y["a"])(g,h,d,!1,null,null,null),_=S.exports,w={name:"FileChart",data:function(){return{fileListJson:null,fileListVisibility:!1,showSpinner:!1,scannedFilesNum:0}},methods:{startScanning:function(){var e=this;this.showSpinner=!0,this.fileListVisibility=!1,l.a.get(Object(u["generateUrl"])("apps/filechecksum/api/statistic/startscanning")).then((function(t){e.fileListJson=t,e.fileListVisibility=!0,e.showSpinner=!1})),setInterval(this.checkingScanningProgress,1e4)},checkingScanningProgress:function(){var e=this;l.a.get(Object(u["generateUrl"])("apps/filechecksum/api/statistic/status")).then((function(t){var n=t.data[0];e.fileListJson=n,e.fileListVisibility=!0,e.showSpinner=!1,"not_finished"==n.progress?e.scannedFilesNum=n.fileNum:"finished"==n.progress?(e.scannedFilesNum=n.fileNum,clearInterval(e.checkingScanningProgress),e.getFileStatistic()):e.scannedFilesNum=n.fileNum}))},getFileStatistic:function(){var e=this;l.a.get(Object(u["generateUrl"])("apps/filechecksum/api/statistic")).then((function(t){e.fileListJson=t,e.fileListVisibility=!0,e.showSpinner=!1}))}},components:{Spinner:p.a,MyAnimatedNumber:_}},O=w,j=Object(y["a"])(O,a,o,!1,null,null,null),F=j.exports,k={name:"App",components:{FileChart:F}},N=k,L=(n("034f"),Object(y["a"])(N,r,s,!1,null,null,null)),P=L.exports;i["a"].config.productionTip=!1,new i["a"]({render:function(e){return e(P)}}).$mount("#filechecksum")},"85ec":function(e,t,n){}});
//# sourceMappingURL=main.js.map