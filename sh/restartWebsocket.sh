#!/bin/bash
## FILENAME:restartWebsocket.sh
## DESC: Restart the websocket
## ATTENTION: DO NOT MODIFY THIS FILE UNLESS YOU KNOWN ABOUT IT.
## CODE By Dove 16:41 Jan 7 2020 WED CST
export LD_LIBRARY_PATH=./

websocketListen=`ps aux | grep "server/crm/yii websocket/start" | grep -v grep`
if [ ! "$websocketListen" ];then
	echo 'Websocket not work!'
else
	websocketId=`ps aux | grep "server/crm/yii websocket/start" | grep Sl | grep -v "grep" | head -1 | awk -F " " '{print $2}'`

	kill -9 $websocketId
fi

nohup /data/software/php71/bin/php /data/web/kj-scrm/server/crm/yii websocket/start -p 7099 &

exit 0
