// import Request from '../js_sdk/luch-request/luch-request'
const http = [];

// #ifdef H5
export const h5Share = function (data = {}) {
    console.log('h5share')
    let  jweixin = require('jweixin-module');
    let jumpurl =  'https://'+location.host+'/h5/#/pages/index/index?shareid='+data.id;
    console.log(jumpurl)
    var jssdkconfig;
    console.log('进入sdk')
    http.post('groupbuy.wechatjssdk', {
        url:location.href
    }).then(function (response) {
        jssdkconfig = response.data.jssdkconfig;
        jweixin.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId:jssdkconfig.appId, // 必填，公众号的唯一标识
            timestamp:jssdkconfig.timestamp, // 必填，生成签名的时间戳
            nonceStr:jssdkconfig.nonceStr, // 必填，生成签名的随机串
            signature:jssdkconfig.signature, // 必填，签名，见附录1
            jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ"]// 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });
        console.log('sdk配置')
        jweixin.ready(function () {
            console.log('sdk配置完成')
            //获取“分享给朋友”按钮点击状态及自定义分享内容接口（即将废弃）
            jweixin.onMenuShareAppMessage({
                title: '分销商邀请', // 分享标题
                desc: '邀请', // 分享描述
                link: jumpurl, // 分享链接
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                }
            })
            //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口（即将废弃）
            jweixin.onMenuShareTimeline({
                title: '分销商邀请', // 分享标题
                desc: '邀请', // 分享描述
                link: jumpurl, // 分享链接
                // imgUrl: _self.data.goods.thumb, // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {

                }
            })
        })
    }).catch(function (error) {
        console.log(error)
    });

}


// #endif

// #ifdef MP-WEIXIN
// 微信小程序分享
export const wxShare = function (data = {}) {
	let shareInfo = {
		title: data.title || '分销商邀请',
        desc: '邀请'
	};
	if(data.path && typeof(data.path) == "string"){
		shareInfo.path = data.path;
	} else if(data.path != 1){
		shareInfo.path = "/pages/index/index";
	}
	if(data.imageUrl){
		shareInfo.imageUrl = data.imageUrl;
	}

	if (data.id) {
		if(data.query && typeof(data.query) == "object"){
			data.query.shareid = data.id;
		} else {
			data.query = {
                shareid: data.id
			};
		}
	}
	if(data.query && typeof(data.query) == "object"){
		Object.keys(data.query).forEach((key,index) => {
			if(index > 0 && shareInfo.query){
				shareInfo.query += "&" + key + "=" + data.query[key];
				shareInfo.path += "&" + key + "=" + data.query[key];
			} else {
				shareInfo.query = key + "=" + data.query[key];
				shareInfo.path += "?" + key + "=" + data.query[key];
			}
		});
	}
	console.log(shareInfo)

	return shareInfo;
}
// #endif




