#!/bin/bash

export TZ="Asia/Shanghai"

#配置你的Openshift ssh用户名
sshid=${OPENSHIFT_APP_UUID}

#脚本运行部分
curl -I ${OPENSHIFT_APP_DNS} 2> /dev/null | head -1 | grep -q 200
s=$?
let t=`date +"%M"`%10
if [ $t -eq 0 ];
then
  if [ $s != 0 ];
  then
      /usr/bin/gear stop 2>&1 /dev/null
      /usr/bin/gear start 2>&1 /dev/null
      echo "`date +"%Y-%m-%d %I:%M:%S"` restarted" > /var/lib/$sshid/app-root/data/web_error.log
  fi
else
    echo "`date +"%Y-%m-%d %I:%M:%S"` is ok" > /var/lib/openshift/$sshid/app-root/data/web_run.log
fi
