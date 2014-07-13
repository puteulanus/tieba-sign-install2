#!/bin/bash

export TZ="Asia/Shanghai"

#判断是否为重启时间
hour="`date +%H%M`"
if [ "$hour" = "0010" -o "$hour" = "0020" ]; then
  ctl_all restart
  exit
fi

#配置你的Openshift ssh用户名
sshid="${OPENSHIFT_APP_UUID}"

#脚本运行部分
curl -I "${OPENSHIFT_APP_DNS}" 2> /dev/null | head -1 | grep -q 200
s="$?"
let t="`date +"%M"`%10"
if [ "$t" -eq 0 ];then
  if [ "$s" != 0 ];then
  	ctl_all restart
  fi
fi
