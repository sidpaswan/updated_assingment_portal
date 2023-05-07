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


if test "$fname" = "NNNassg01.tgz" 
then
	glofs=`tar -tzf $STUDENT_FILES_DIR/samrat/$fname | sort` #why?
	lofs=`tar -tzf $STUDENT_FILES_DIR/$1/$fname | sort`
	if test "$glofs" = "$lofs"
	then
		echo "OK"
	else
		echo "NOTOK"
	fi
elif test "$fname" = "NNNassg02.tgz" 
then
	glofs=`tar -tzf $STUDENT_FILES_DIR/samrat/gold/$fname | sed '/[mM]akefile/d' | sort` #why different?
	lofs=`tar -tzf $STUDENT_FILES_DIR/$1/$fname |sed '/[mM]akefile/d' |  sort`
	if test "$glofs" = "$lofs"
	then
		echo "OK"
	else
		echo "NOTOK"
	fi
else
	echo "OK"
fi


