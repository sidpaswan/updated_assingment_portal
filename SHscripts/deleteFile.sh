#!/bin/bash
source "setup.conf"
fname=$2;
uname=$1;
ip=$3;
#path="../students/$1";
path="$STUDENT_FILES_DIR/$1";
logfile="log/upload.log"
gentime=`date +"%H:%M:%S"`
gendate=`date +"%d.%m.%y"`


ext=`echo $fname | gawk -F"." '{print $NF}'`;


if test "$ext" = "tgz" ; then
	rm -f  $STUDENT_FILES_DIR/$1/$fname
fi


echo "<br>"
echo "<br>"
#echo "<font color=red><big><b>No more submission will be accepted.</b></big></font>"
echo "<br>"
echo "<br>"
