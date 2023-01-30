#!/bin/bash
## FILENAME:watchPQ.sh
## DESC: Watch the pq queue 
## ATTENTION: DO NOT MODIFY THIS FILE UNLESS YOU KNOWN ABOUT IT.
## CODE By Dove 10:28 Nov 10 2020 Tues CST
export LD_LIBRARY_PATH=./

scrmYii=`ps aux | grep "server/crm/yii pq/listen" | grep -v grep`
if [ ! "$scrmYii" ];then
        /data/web/kj-scrm/sh/queueScrmPQ.sh
else
        lines=`ps aux| grep "server/crm/yii pq/listen" | grep -v "grep" |wc -l`

    	if [ $lines -lt 10 ];then
    		needNum=10-$lines

    		for (( i=1; i<=$needNum; i++ ))
    		do
    			nohup /data/web/kj-scrm/sh/scrmPQ.sh &
    		done
    	fi
fi

exit 0
