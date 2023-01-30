#!/bin/bash
## FILENAME:queueScrmPQ.sh
## DESC: Restart the scrm queue
## ATTENTION: DO NOT MODIFY THIS FILE UNLESS YOU KNOWN ABOUT IT.
## CODE By Dove 16:41 Jan 7 2020 WED CST
export LD_LIBRARY_PATH=./

scrmYii=`ps aux | grep "server/crm/yii sq/listen" | grep -v grep`
if [ ! "$scrmYii" ];then
	echo 'Scrm Shop Queue Not Work!'
else
	lines=`ps aux| grep "server/crm/yii sq/listen" | grep -v "grep" |wc -l`

	for (( i=1; i<=$lines; i++ ))
	do
		queueYii=`ps aux | grep "server/crm/yii sq/listen" | grep -v "grep" | head -1 | awk -F " " '{print $2}'`

		kill -9 $queueYii
	done
fi

for (( i=1; i<=10; i++ ))
do
	nohup /root/scrmShop.sh &
done

exit 0
