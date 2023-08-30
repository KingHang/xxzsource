import Vue from 'vue'
import Vuex from "vuex"
Vue.use(Vuex)

export default new Vuex.Store({
	// 全局属性变量
	state: { // state的作用是定义属性变量。定义一个参数
		theme: '',
		footTab:'',
		points_name:'积分'
	},
	// 全局同步方法，在methods{this.$store.commit("changeTheme")}
	mutations: {
		changeTheme(state, value) {
			state.theme = value;
		},
		changefootTab(state, value) {
			state.footTab = value;
		},
		changePoints(state, value) {
			state.points_name = value;
		}
	},
	getters: {

	},
	actions: {

	}

})
