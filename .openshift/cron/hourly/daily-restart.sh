hour=`date +%H`
if [ $hour = "00" ]; then
ctl_app restart
else
exit
fi