#!/bin/bash
source "setup.conf"
username=$1
token=$2
forgot=$3

exec <>200 $oldfile #Open fd to file
flock -x 200 #Lock the file

#Get UserInfo like password and salt
read=$(cat $oldfile | grep -F -m 1 $username)

# If token is not passed
if [ -z "$2" ]
then
	echo -n "No Token passed"
	exit;
fi

OIFS=$IFS
IFS=":"
read -ra VAR <<< $read
IFS=$OIFS

if [ "$3" == "-o" ]
then
	salt=$(echo $RANDOM | md5sum | head -c 6)
	enpasswd=$(echo -n "$token$salt" | md5sum | gawk '{print $1}')
	chpass_cmd="sed -i \"/$username:/c\\\\${VAR[0]}:$enpasswd:$salt::${VAR[4]}:\" $oldfile"
else
	chpass_cmd="sed -i \"/$username:/c\\\\${VAR[0]}:${VAR[1]}:${VAR[2]}:$token:${VAR[4]}:$(date -d '+1 hour' +%s)\" $oldfile"
fi
eval $chpass_cmd
