#!/bin/bash
source "setup.conf"
fname=$2;
uname=$1;
ip=$3;

path="$STUDENT_FILES_DIR/$1";
logfile="$base_dir/log/upload.log"
gendatetime=`date +"%Y-%m-%dT%H:%M"`

fullfname="$path/$fname"

checkDeadline() {
	deadline=$2
	currtime=$1

	# echo "$currtime $deadline"

	if [[ "$currtime" < "$deadline" ]]
	then
		return 1
	fi
	return 0
}

ext=`echo $fname | gawk -F"." '{print $NF}'`;

assignDetail=`tac $assignfile | grep -F -m 1 "$fname"`

if [ "$assignDetail" ] 
then
	# echo "hi1";
	IFS=','
	read -ra arr <<< "$assignDetail"
	# echo "${arr[0]} - ${arr[1]} - ${arr[2]}"
	fsub="${arr[0]}"
	deadline1=${arr[1]}
	deadline2=${arr[2]}
	if test "$fname" = "$fsub"
	then
		checkDeadline "$gendatetime" "$deadline1"
		if [ $? -eq 1 ]
		then
			echo "OK"
			size=`ls -l $fullfname | awk '{print $5}'`
			flock -x $logfile -c "echo '$gendatetime $uname $fname $ip $size OK'  >> $logfile"
			exit
		fi
		checkDeadline "$gendatetime" "$deadline2"
		if [ $? -eq 1 ]
		then
			echo "LATE"
			size=`ls -l $fullfname | awk '{print $5}'`
			flock -x $logfile -c "echo '$gendatetime $uname $fname $ip $size LATE'  >> $logfile"
			exit
		fi
		echo "Deadline Ended. Contact the respective TA for submission."
		exit
	fi
else
	echo "File name mismatch"
	exit
fi

echo "File name mismatch"
echo "<br>"
echo "<br>"
#echo "<font color=red><big><b>No more submission will be accepted.</b></big></font>"
echo "<br>"
echo "<br>"
