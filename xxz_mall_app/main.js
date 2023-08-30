import Vue from 'vue'

import App from './App'
import directive from './common/directive.js'
import utils from './common/utils.js'
import config from './config.js'
import onfire from './common/onfire.js'
import {
	gotopage
} from './common/gotopage.js'
import footTabber from '@/components/tabbar/footTabbar.vue'

/* Vuex */
import store from "./store/index.js"
Vue.prototype.$store = store;

import uView from 'uview-ui'
Vue.use(uView);
// 公共组件
import headerBar from './components/header.vue'
Vue.component('header-bar', headerBar)




//请求加载组件
import requestLoading from './components/jjjloading.vue';
//组件挂载到全局，方便每个页面使用
Vue.component('request-loading', requestLoading);

Vue.prototype.$fire = new onfire()

Vue.config.productionTip = false

App.mpType = 'app'

Vue.prototype.config = config

const app = new Vue({
	...App
})
app.$mount()
Vue.prototype.theme = function() {
	return 'theme' + this.$store.state.theme || ''
}
/*底部数据*/
Vue.prototype.footTabberData = {
	active: 'home'
};
Vue.component('footTabber', footTabber)

Vue.prototype.websiteUrl = config.app_url;
Vue.prototype.app_id = config.app_id;
//h5发布路径
Vue.prototype.h5_addr = config.h5_addr;
/*页面跳转*/
Vue.prototype.gotoPage = gotopage;
Vue.prototype.font_url = config.font_url;

// 获取参数
Vue.prototype.getCodeNub = function(url, name) {
	if(!url) url = '';
    var reg = new RegExp('(^|&|/?)' + name + '=([^&|/?]*)(&|/?|$)', 'i')
    var r = url.substr(1).match(reg)
    if (r != null) {
		return r[2]
    }
    return null;
}

//#ifdef H5
app.$router.afterEach((to, from) => {
	const u = navigator.userAgent.toLowerCase();
	if (u.indexOf("like mac os x") < 0 || u.match(/MicroMessenger/i) != 'micromessenger') return;
	if (to.path !== global.location.pathname) {
		location.assign(config.app_url + config.h5_addr + to.fullPath);
	}
})
//#endif


//是否是ios
Vue.prototype.ios = function() {
	const u = navigator.userAgent.toLowerCase();
	if (u.indexOf("like mac os x") < 0 || u.match(/MicroMessenger/i) != 'micromessenger') {
		return false;
	}
	return true;
};

Vue.prototype.isIdNo = function (v) {
  return /^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/.test(v)
}

//get请求
Vue.prototype._get = function(path, data, success, fail, complete) {
	data = data || {};
	data.token = uni.getStorageSync('token') || '';
	data.app_id = this.getAppId();


	let usercheck = 1;
	if (path =='goods.category/index'||path =='goods.goods/lists'||path =='index/index'||path =='user.index/detail'||path =='goods.goods/lists'){
        usercheck =0;
    }


    // console.log(this.websiteUrl + '/index.php/api/' + path)

    uni.request({
		url: this.websiteUrl + '/index.php/api/' + path,
		data: data,
		dataType: 'json',
		method: 'GET',
		success: (res) => {
                // console.log(res)
			if (res.statusCode !== 200 || typeof res.data !== 'object') {
				return false;
			}
			if (res.data.code === -2) {
				this.showError(res.data.msg, function() {
					uni.removeStorageSync('token');
					this.gotoPage('/pages/index/index', 'reLaunch');
				})
			} else if (res.data.code === -1&&usercheck==1) {
				// 登录态失效, 重新登录
				console.log('登录态失效, 重新登录');
				this.doLogin();
                // success && success(res.data);
			} else if (res.data.code === 0) {
				this.showError(res.data.msg, function() {
					fail && fail(res);
				});
				return false;
			} else {
				success && success(res.data);
			}
		},
		fail: (res) => {
			fail && fail(res);
		},
		complete: (res) => {
			uni.hideLoading();
			complete && complete(res);
		},
	});
};

//get请求
Vue.prototype._post = function(path, data, success, fail, complete) {
	data = data || {};
	data.token = uni.getStorageSync('token') || '';
	data.app_id = this.getAppId();
	// console.log(data)
	// console.log(this.websiteUrl + '/index.php/api/' + path)
	uni.request({
		url: this.websiteUrl + '/index.php/api/' + path,
		data: data,
		dataType: 'json',
		method: 'POST',
		header: {
			'content-type': 'application/x-www-form-urlencoded',
		},
		success: (res) => {
		    // console.log(res)
			if (res.statusCode !== 200 || typeof res.data !== 'object') {
				return false;
			}
			if (res.data.code === -1) {
				// 登录态失效, 重新登录
				console.log('登录态失效, 重新登录');
				this.doLogin();
			} else if (res.data.code === 0) {
				this.showError(res.data.msg, function() {
					fail && fail(res);
				});
				return false;
			} else {
				success && success(res.data);
			}
		},
		fail: (res) => {
			fail && fail(res);
		},
		complete: (res) => {
			uni.hideLoading();
			complete && complete(res);
		},
	});
};

Vue.prototype.doLogin = function() {
	let pages = getCurrentPages();
	if (pages.length) {
		let currentPage = pages[pages.length - 1];
		if ("pages/login/login" != currentPage.route &&
			"pages/login/weblogin" != currentPage.route &&
			"pages/login/openlogin" != currentPage.route) {
			uni.setStorageSync("currentPage", currentPage.route);
			uni.setStorageSync("currentPageOptions", currentPage.options);
		}
	}
	//公众号
	// #ifdef  H5
	if (this.isWeixin()) {
		window.location.href = this.websiteUrl + '/index.php/api/user.usermp/login?app_id=' + this.getAppId() +
			'&referee_id=' + uni.getStorageSync('referee_id');
	} else {
		this.gotoPage("/pages/login/weblogin");
	}
	// #endif
	// #ifdef APP-PLUS
	this.gotoPage("/pages/login/openlogin", 'redirect');
	return;
	// #endif
	// 非公众号,跳转授权页面
	// #ifndef  H5
	this.gotoPage("/pages/login/login");
	// #endif
};


/**
 * 显示失败提示框
 */
Vue.prototype.showError = function(msg, callback) {
	uni.showModal({
		title: '友情提示',
		content: msg,
		showCancel: false,
		success: function(res) {
			callback && callback();
		}
	});
};


Vue.prototype.points_name = function(e) {
	if (!e) {
		return store.state.points_name;
	} else {
		let re = new RegExp("积分", "g");
		return e.replace(re, store.state.points_name);
	}

}

Vue.prototype.getTabBarLinks = function() {
	let vars = uni.getStorageSync('TabBar');
	let theme = uni.getStorageSync('theme');
	if(vars != null && vars != ''){
		this.setTabBarLinks(vars, theme);
	}else{
		uni.request({
			url: this.config.app_url + '/index.php/api/index/nav',
			data: {
				app_id: this.config.app_id
			},
			success: (result) => {
				let vars = result.data.data.vars.data;
				let theme = result.data.data.theme.theme;
				this.$store.commit('changeTheme', theme);
				uni.setStorageSync('TabBar', vars);
				uni.setStorageSync('theme', theme);
				this.setTabBarLinks(vars, theme);
			}
		});
	}
}

/**
 * 显示失败提示框
 */
Vue.prototype.showSuccess = function(msg, callback) {
	uni.showModal({
		title: '友情提示',
		content: msg,
		showCancel: false,
		success: function(res) {
			callback && callback();
		}
	});
};

/**
 * 获取应用ID
 */
Vue.prototype.getAppId = function() {
	return this.app_id || 10001;
};

Vue.prototype.compareVersion = function(v1, v2) {
	v1 = v1.split('.')
	v2 = v2.split('.')
	const len = Math.max(v1.length, v2.length)

	while (v1.length < len) {
		v1.push('0')
	}
	while (v2.length < len) {
		v2.push('0')
	}

	for (let i = 0; i < len; i++) {
		const num1 = parseInt(v1[i])
		const num2 = parseInt(v2[i])

		if (num1 > num2) {
			return 1
		} else if (num1 < num2) {
			return -1
		}
	}

	return 0
};


/**
 * 生成转发的url参数
 */
Vue.prototype.getShareUrlParams = function(params) {
	let self = this;
	return utils.urlEncode(Object.assign({
		referee_id: self.getUserId(),
		app_id: self.getAppId()
	}, params));
};

/**
 * 当前用户id
 */
Vue.prototype.getUserId = function() {
	return uni.getStorageSync('user_id');
};

//#ifdef H5
var jweixin = require('jweixin-module');

Vue.prototype.configWx = function(signPackage, shareParams, params) {
	if (signPackage == '') {
		return;
	}
	let self = this;
	jweixin.config(JSON.parse(signPackage));

	let url_params = self.getShareUrlParams(params);

	jweixin.ready(function(res) {
		jweixin.updateAppMessageShareData({
			title: shareParams.title,
			desc: shareParams.desc,
			link: self.websiteUrl + self.h5_addr + shareParams.link + '?' + url_params,
			imgUrl: shareParams.imgUrl,
			success: function() {

			}
		});
		jweixin.updateTimelineShareData({
			title: shareParams.title,
			desc: shareParams.desc,
			link: self.websiteUrl + self.h5_addr + shareParams.link + '?' + url_params,
			imgUrl: shareParams.imgUrl,
			success: function() {

			}
		});
	});
};

Vue.prototype.configWxScan = function(signPackage) {
	if (signPackage == '') {
		return;
	}
	jweixin.config(JSON.parse(signPackage));
	return jweixin;
};
//#endif

/**
 * 获取当前平台
 */
Vue.prototype.getPlatform = function(params) {
	let platform = 'wx';
	// #ifdef  APP-PLUS
	platform = 'app';
	// #endif
	// #ifdef  H5
	if (this.isWeixin()) {
		platform = 'mp';
	} else {
		platform = 'h5';
	}
	// #endif
	return platform;
};

/**
 * 订阅通知,目前仅小程序
 */
Vue.prototype.subMessage = function(temlIds, callback) {
	let self = this;
	// #ifdef  MP-WEIXIN
	//小程序订阅消息
	const version = wx.getSystemInfoSync().SDKVersion;
	if (temlIds && temlIds.length != 0 && self.compareVersion(version, '2.8.2') >= 0) {
		wx.requestSubscribeMessage({
			tmplIds: temlIds,
			success(res) {},
			fail(res) {},
			complete(res) {
				callback();
			},
		});
	} else {
		callback();
	}
	// #endif
	// #ifndef MP-WEIXIN
	callback();
	// #endif
};

Vue.prototype.isWeixin = function() {
	var ua = navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == "micromessenger") {
		return true;
	} else {
		return false;
	}
};

/**
 * 获取访客
 */
Vue.prototype.getVisitcode = function() {
	let visitcode = uni.getStorageSync('visitcode');
	if (!visitcode) {
		visitcode = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = Math.random() * 16 | 0,
				v = c == 'x' ? r : (r & 0x3 | 0x8);
			return v.toString(16);
		});
		visitcode = visitcode.replace(/-/g, "");
		uni.setStorageSync('visitcode', visitcode);
	}

	return visitcode;
};

Vue.prototype.topBarTop = function() {
	// #ifdef MP-WEIXIN
	return uni.getMenuButtonBoundingClientRect().top;
	// #endif
	// #ifndef MP-WEIXIN
	const SystemInfo = uni.getSystemInfoSync();
	return SystemInfo.statusBarHeight;
	// #endif
};
Vue.prototype.topBarHeight = function() {
	// #ifdef MP-WEIXIN
	return uni.getMenuButtonBoundingClientRect().height;
	// #endif
	// #ifndef MP-WEIXIN
	return 0
	// #endif
};

/* 小数点截取 */
Vue.prototype.subPrice = function(str, val) {
	let strs = String(str);
	if (val == 1) {
		return strs.substring(0, strs.indexOf("."));
	} else if (val == 2) {
		let indof = strs.indexOf(".");
		return strs.slice(indof + 1, indof + 3);
	}
}

Vue.prototype.setTabBarLinks = function(vars, theme){
	if (vars.is_auto == '0') {
		uni.showTabBar();
		vars.list = [];
		let color = ['#ff5704', '#19ad57', '#ffcc00', '#1774ff', '#e4e4e4', '#c8ba97',
			'#623ceb'
		]
		uni.setTabBarStyle({
			color: '#333333',
			selectedColor: color[theme],
		})
		uni.setTabBarItem({
			index: 0,
			text: '首页',
			iconPath: 'static/tabbar/home.png',
			selectedIconPath: 'static/tabbar/home_' + theme + '.png'
		})
		uni.setTabBarItem({
			index: 1,
			text: '分类',
			iconPath: 'static/tabbar/category.png',
			selectedIconPath: 'static/tabbar/category_' + theme + '.png'
		})
		uni.setTabBarItem({
			index: 2,
			text: '店铺',
			iconPath: 'static/tabbar/shop.png',
			selectedIconPath: 'static/tabbar/shop_' + theme + '.png'
		})
		uni.setTabBarItem({
			index: 3,
			text: '购物车',
			iconPath: 'static/tabbar/cart.png',
			selectedIconPath: 'static/tabbar/cart_' + theme + '.png'
		})
		uni.setTabBarItem({
			index: 4,
			text: '我的',
			iconPath: 'static/tabbar/user.png',
			selectedIconPath: 'static/tabbar/user_' + theme + '.png'
		})
	} else {
		uni.hideTabBar();
	}
}
Vue.prototype.getThemeColor = function() {
	let theme = this.$store.state.theme;
	let color = ['#ff5704', '#19ad57', '#ffcc00', '#1774ff', '#e4e4e4', '#c8ba97', '#623ceb'];
	return color[theme]
}

