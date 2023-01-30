import Vue from 'vue'

export default function ( url, params, isClickTab, name='data') {
    return new Promise((resolve, reject) => {
        this.isShowKong = false; //空的图标隐藏
        this.hasMore = true; //当进来数据最下面显示‘玩了命的加载中’
        if (params.pageNo === 1) {
            this.pageNo = 1;
            this.pageList = []
        }
        this.axios.post(url, {...params}).then((res)=>{
            var a = name.split('.');
            var d = {}; // 这段为了兼容后端返回数据多层级和每个层级的字段不一样
            if (a.length ===1 ) {
                d = res[a[0]]
            } else if (a.length ===2 ) {
                d = res[a[0]][a[1]]
            } else if (a.length === 3 ) {
                d = res[a[0]][a[1]][a[2]]
            }
            if (d.length > 0) {
                this.pageList = isClickTab ? d : [...this.pageList, ...d]; //当前某个更新数据
                this.pageNo = this.pageNo + 1;
                this.isLodingLock = true; // 当前某个锁关闭
                this.hasMore = false
            } else {
                this.hasMore = false //当加载完所有数据最下面显示‘没有跟多数据了’
            }
            if (this.pageList.length === 0) {
                this.isShowKong = true
            }
            resolve(res)
        })
    })
}
