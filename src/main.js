import '@babel/polyfill'
import 'mutationobserver-shim'
import Vue from 'vue'
import './plugins/bootstrap-vue'
import App from './App.vue'


Vue.config.productionTip = false
export const bus = new Vue();
new Vue({
    render: h => h(App),
}).$mount('#filechecksum')
