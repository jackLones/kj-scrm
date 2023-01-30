export default function (url, type) { //跳转 url: 地址 type：类型
    switch (type) {
        case 'redirect':
            uni.redirectTo({
                url,
            })
            break;
        case 'reLaunch':
            uni.reLaunch({
                url,
            })
            break;
        case 'back':
            uni.navigateBack({
                delta: 1
            })
            break;
        default:
            if (getCurrentPages().length > 8) {
                uni.redirectTo({
                    url,
                })
            } else {
                uni.navigateTo({
                    url
                })
            }
    }
}
