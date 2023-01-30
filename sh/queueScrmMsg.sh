#!/bin/bash
## FILENAME:queueScrmMsg.sh
## DESC: Restart the scrm queue
## ATTENTION: DO NOT MODIFY THIS FILE UNLESS YOU KNOWN ABOUT IT.
## CODE By Dove 16:41 Jan 7 2020 WED CST
export LD_LIBRARY_PATH=./

scrmYii=`ps aux | grep "server/crm/yii msg/listen" | grep -v grep`
if [ ! "$scrmYii" ];then
	echo 'Msg Work Queue Not Work!'
else
	lines=`ps aux| grep "server/crm/yii msg/listen" | grep -v "grep" |wc -l`
	
	for (( i=1; i<=$lines; i++ ))
	do
		queueYii=`ps aux | grep "server/crm/yii msg/listen" | grep -v "grep" | head -1 | awk -F " " '{print $2}'`
		
		kill -9 $queueYii
	done
fi

for (( i=1; i<=10; i++ ))
do
	nohup /data/web/kj-scrm/sh/scrmMsg.sh &
done

exit 0
