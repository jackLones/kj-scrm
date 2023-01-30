import moment from 'moment';
import {Toast} from "vant";

export default {
    //获取cookie、
    getCookie(name) {
        var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
        if (arr = document.cookie.match(reg))
            return (arr[2]);
        else
            return null;
    },
    //设置cookie,增加到vue实例方便全局调用
    setCookie(c_name, value, expiredays) {
        var exdate = new Date();
        exdate.setDate(exdate.getDate() + expiredays);
        document.cookie = c_name + "=" + escape(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString());
    },
    //删除cookie
    delCookie(name) {
        var exp = new Date();
        exp.setTime(exp.getTime() - 1);
        var cval = getCookie(name);
        if (cval != null)
            document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
    },
    addslashes(string) {
        return string.replace(/[\/\[\]\(\)\|\$\*\?\+\-\_]/g, function (m) {
            return {
                "\/": "\\/",
                "\[": "\\[",
                "\]": "\\]",
                "\(": "\\(",
                "\)": "\\)",
                "\|": "\\|",
                "\$": "\\$",
                "\*": "\\*",
                "\?": "\\?",
                "\+": "\\+",
                "\-": "\\-",
                "\_": "\\_",
            }[m];
        });
    },
    isToday(signDate) {
        if (!signDate) {
            return false;
        }
        const currentDate = moment().format('YYYYMMDD');
        signDate = moment(signDate).format('YYYYMMDD');
        return signDate === currentDate;
    },

    // config
    setConfig({agent_id}) {
        return new Promise(async (resolve) => {
            const {data: res} = await this.axios.post("chat-message/get-config", {
                url: window.location.href,
                agent_id
            });
            if (res.error != 0) {
                Toast.fail(res.error_msg);
            } else {
                localStorage.setItem('uid', res.data.uid)
                this.$store.dispatch('setCorpId', res.data.corpid)
                this.$store.state.wx.config({
                    beta: true,// 必须这么写，否则wx.invoke调用形式的jsapi会有问题
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: res.data.ticketData.corpid, // 必填，企业微信的corpID
                    timestamp: res.data.ticketData.timestamp, // 必填，生成签名的时间戳
                    nonceStr: res.data.ticketData.nonceStr, // 必填，生成签名的随机串
                    signature: res.data.ticketData.signature,// 必填，签名，见 附录-JS-SDK使用权限签名算法
                    jsApiList: res.data.ticketData.jsApiList // 必填，需要使用的JS接口列表，凡是要调用的接口都需要传进来
                })
                this.$store.state.wx.ready(() => {
                    resolve(res)
                })
            }
        })
    },

    //agentConfig
    setAgentConfig(config) {
        return new Promise(async (resolve) => {
            this.$store.state.wx.agentConfig({
                corpid: config.data.agentData.corpid,
                agentid: config.data.agentData.agentid,
                timestamp: config.data.agentData.timestamp,
                nonceStr: config.data.agentData.nonceStr,
                signature: config.data.agentData.signature,
                jsApiList: config.data.agentData.jsApiList,
                success: (res) => {
                    console.log(res, 'agentConfig成功')
                    this.getContext((entry = '') => {
                        if (entry == '') {
                            uni.showToast({
                                title: '版本过低请升级',
                                image: '/static/fail.png',
                                duration: 2000
                            });
                        }
                    }, () => {
                        uni.showToast({
                            title: '版本过低请升级',
                            image: '/static/fail.png',
                            duration: 2000
                        });
                    })
                },
                fail: function (res) {
                    console.log(res, 'agentConfig失败')
                    if (res.errMsg.indexOf('function not exist') > -1) {
                        uni.showToast({
                            title: '版本过低请升级',
                            image: '/static/fail.png',
                            duration: 2000
                        });
                    } else if (res.errMsg == 'agentConfig:not match any reliable domain.') {
                        uni.showToast({
                            title: '可信域名未填写或未验证',
                            image: '/static/fail.png',
                            duration: 2000
                        });
                    } else {
                        uni.showToast({
                            title: res.errMsg,
                            image: '/static/fail.png',
                            duration: 2000
                        });
                    }
                }
            })
        })
    }
}
