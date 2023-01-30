#!/bin/bash
source /etc/profile

php -v
java -version

webroot=/data/web/kj-scrm

#启动crontab
/usr/sbin/crond

#启动nginx 和php-fpm
/etc/init.d/nginx start
/etc/init.d/php-fpm start

#开启队列和websocket服务
sh ${webroot}/sh/queueScrmYii.sh
sh ${webroot}/sh/restartWebsocket.sh


#启用回话存档服务
#nohup java -jar ${webroot}/msgAudit/msgAudit-0.0.1-SNAPSHOT.jar &
nohup java -Xmx128m -Xss256k -XX:ParallelGCThreads=2 -Djava.compiler=NONE -jar ${webroot}/msgAudit/msgAudit-0.0.1-SNAPSHOT.jar &
nohup ${webroot}/sh/msgStart.sh &


/bin/bash
