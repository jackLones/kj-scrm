let sessionKey = 'session_id';
export default {
	sessionKey  : sessionKey,
	session_id  : localStorage.getItem(sessionKey) ? localStorage.getItem(sessionKey) : '',
	authKey     : 'AUTH_KEY', //验证key
	setSessionId: function (sessionId) {
		this.session_id = sessionId
		localStorage.setItem('session_id', this.session_id)
	},
	clearLocalStorage (flag = false) {
		if (flag) {
			localStorage.clear()
		} else {
			if (localStorage.length > 0) {
				let keyIndex = 0
				const localLength = localStorage.length
				for (let i = 0; i < localLength; i++) {
					let localKey = localStorage.key(keyIndex);
					console.log(localKey, 'localKey');
					if (localKey !== this.sessionKey && localKey != this.authKey) {
						localStorage.removeItem(localKey);
					} else {
						keyIndex++
					}
				}
			}
		}


        // if (process.env.NODE_ENV==='development') {
        //     console.log(process.env.NODE_ENV, '????????????????????????')
        //     localStorage.setItem('uid', 2)
        //     localStorage.setItem('corpid', 'ww93caebeee67d134b')
        //     localStorage.setItem('token', 'TWFpblVzZXItMGNkN2VhMTBlMzk1MjFmZDYyOWZjNDA3MGU3ZGI1ZDc=')
        // }

	},
}
