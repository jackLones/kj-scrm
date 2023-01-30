import global from "./global.js"

export default {
	WS_SCHEME       : 'wss',
	WS_HOST         : 'pscrm-adm.51lick.com/wss',
	WS_PORT         : 7099,
	timeoutObj      : null,
	serverTimeoutObj: null,
	timeout         : 5 * 60 * 1000,
	websocket       : null,
	reConnect       : "",
	callback        : null,
	setCallback     : function (callback) {
		if (typeof callback == 'function') {
			this.callback = callback;
		} else {
			this.callback = null;
		}
	},
	heart () {
		this.resetHeart();
		this.startHeart();
	},

	resetHeart () {
		clearTimeout(this.timeoutObj);
		clearTimeout(this.serverTimeoutObj);
	},
	startHeart () {
		let that = this;
		that.timeoutObj = setTimeout(function () {
			//这里发送一个心跳，后端收到后，返回一个心跳消息，
			//onmessage拿到返回的心跳就说明连接正常
			that.websocketSend(
				JSON.stringify({
					channel: "heart",
					info   : {
						session_id: global.session_id,
						uid       : localStorage.getItem("uid") != null ? localStorage.getItem("uid") : '1',
						subId     : (localStorage.getItem('user_type') == 2 && localStorage.getItem("sub_id") != null) ? localStorage.getItem("sub_id") : '',
						openid    : localStorage.getItem("openid") != null ? localStorage.getItem("openid") : '',
						bindType  : 3,
					}
				})
			);

			that.serverTimeoutObj = setTimeout(function () {
				that.websocket.close();
			}, that.timeout);
		}, that.timeout);
	},

	initWebSocket () {
		//初始化weosocket
		const wsuri =
			this.WS_SCHEME +
			"://" +
			this.WS_HOST; //ws地址
		this.websocket = new WebSocket(wsuri);

		this.websocket.onopen = this.websocketOnOpen.bind(this);

		this.websocket.onerror = this.websocketOnError.bind(this);

		this.websocket.onmessage = this.websocketOnMessage.bind(this);

		this.websocket.onclose = this.websocketClose.bind(this);
	},

	websocketOnOpen () {
		this.heart();

		this.websocketSend(
			JSON.stringify({
				channel: "bind",
				info   : {
					session_id: global.session_id,
					uid       : localStorage.getItem("uid") != null ? localStorage.getItem("uid") : '1',
					subId     : (localStorage.getItem('user_type') == 2 && localStorage.getItem("sub_id") != null) ? localStorage.getItem("sub_id") : '',
					openid    : localStorage.getItem("openid") != null ? localStorage.getItem("openid") : '',
					bindType  : 3,
				}
			})
		);
		console.log("WebSocket连接成功");
	},
	websocketOnError (e) {
		//错误
		console.log(e);
		console.log("WebSocket连接发生错误");

		this.websocket.close();
	},

	websocketOnMessage (e) {
		this.heart();

		//数据接收
		const redata = JSON.parse(e.data);
		// console.log('websocket', redata);

		if (typeof this.callback == 'function') {
			this.callback(redata);
		}
	},

	websocketSend (agentData, tryNum = 1) {
		if (tryNum < 11) {
			if (this.websocket.readyState == 1) {
				//数据发送
				this.websocket.send(agentData);
			} else {
				setTimeout(() => {
					tryNum++
					this.websocketSend(agentData, tryNum)
				}, 100)
			}
		}
	},

	websocketClose (e) {
		//关闭
		console.log("connection closed (" + e.code + ")");

		let that = this;
		clearTimeout(that.reConnect);
		that.reConnect = setTimeout(function () {
			that.initWebSocket();
		}, 1000);
	}
}