#!/bin/bash
source "setup.conf"
username=$1
opasswd=$2
npasswd=$3

exec <>200 $oldfile #Open fd to file
flock -x 200 #Lock the file

#Get UserInfo like password and salt
user_info=$(sed -n "/$username/p" $oldfile | gawk -F: '{print $1" "$2" "$3}')
osalt=$(echo $user_info | gawk '{print $NF}')

oenpass=$(echo -n "$opasswd$osalt" | md5sum | gawk '{print $1}')

nuser_info="$username $oenpass $osalt"

#For default passwords
if [ -z "$3" ]
then
	nuser_info="$username $3 $osalt"
	npasswd=$2
fi

#if old password matches or not
if [ "$user_info" != "$nuser_info" ] 
then
	echo -n "NOP"
	exit
fi

# New password and salt generation
salt=$(echo $RANDOM | md5sum | head -c 6)
enpasswd=$(echo -n "$npasswd$salt" | md5sum | gawk '{print $1}')

read=$(cat $oldfile | grep -F -m 1 $username)

OIFS=$IFS
IFS=":"
read -ra VAR <<< $read
IFS=$OIFS


chpass_cmd="sed -i \"/$username:/c\\\\$username:$enpasswd:$salt:${VAR[3]}:${VAR[4]}:${VAR[5]}\" $oldfile"
eval $chpass_cmd
