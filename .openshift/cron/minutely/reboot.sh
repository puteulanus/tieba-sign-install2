#!/bin/bash

export TZ="Asia/Shanghai"

# 每天 00:10 00:20 12:00 各重启一次防止计划任务失败
hour="`date +%H%M`"
if [ "$hour" = "0010" -o "$hour" = "0020" -o "$hour" = "1200" ]
then
  gear restart --all-cartridges
  exit
fi

# 十分钟检查一次网站数据库是否正常
let tenmin="`date +%M` % 10"
if [ "$tenmin" -eq 0 ]
then
  curl -s -I -m 30 http://"${OPENSHIFT_APP_DNS}" | head -1 | grep -q 200 ||
    gear restart --all-cartridges
  exit
fi
