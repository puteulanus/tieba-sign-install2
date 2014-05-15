#!/bin/bash

# 读取用户名密码
username=$1
password=$2

# 封闭文件管理器目录
cd ${OPENSHIFT_REPO_DIR}php/filebrowser/
htpasswd -bc .htpasswd $username $password
chmod 600 .htpasswd
echo 'AuthName "Please type your username and password"'>.htaccess
echo 'AuthType Basic'>>.htaccess
echo 'AuthUserFile '${OPENSHIFT_REPO_DIR}'php/filebrowser/.htpasswd'>>.htaccess
echo 'Require valid-user'>>.htaccess

# 修改文件管理器密码
cd ${OPENSHIFT_REPO_DIR}php/filebrowser/
md5=`echo -n $password|md5sum|awk '{print $1}'`
echo '<?php exit;?>{"'$username'":{"name":"'$username'","password":"'$md5'","role":"root","status":0}}'>${OPENSHIFT_REPO_DIR}php/filebrowser/data/system/member.php
mv -f ${OPENSHIFT_REPO_DIR}php/filebrowser/data/User/admin ${OPENSHIFT_REPO_DIR}php/filebrowser/data/User/$username

# 备份配置文件
cd ${OPENSHIFT_REPO_DIR}php/system/
cp -f ${OPENSHIFT_REPO_DIR}php/system/config.inc.php ${OPENSHIFT_REPO_DIR}.openshift/config/config.inc.php

# 执行cron
sleep 5
php ${OPENSHIFT_REPO_DIR}php/cron.php
sleep 2
php ${OPENSHIFT_REPO_DIR}php/cron.php
sleep 2
php ${OPENSHIFT_REPO_DIR}php/cron.php
sleep 2
php ${OPENSHIFT_REPO_DIR}php/cron.php
sleep 2
php ${OPENSHIFT_REPO_DIR}php/cron.php
sleep 2
php ${OPENSHIFT_REPO_DIR}php/cron.php

# 删除安装文件
cd ${OPENSHIFT_REPO_DIR}php/install/
rm -rf ${OPENSHIFT_REPO_DIR}php/install/deploy.sh

