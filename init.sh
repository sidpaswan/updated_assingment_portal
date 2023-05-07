#!/bin/bash

set -e

currDir=$(pwd)
source 'setup.conf'

#The userList should be complete
echo "Initializing password file with the following rollList: $userList"
users=$(cat $userList)

# make sure 'gawk' is installed
echo "Installing gawk dependency"
sudo apt install gawk
sudo apt-get install php-gd

# creating base directory if not existing
[ ! -d $base_dir ] && mkdir $base_dir

# storing logs
[ ! -d $base_dir/log ] && mkdir $base_dir/log

# preparing directory for password change
rm -f $oldfile
touch $oldfile
touch $assignfile

[ ! -d $STUDENT_FILES_DIR ] && mkdir $STUDENT_FILES_DIR

cd $STUDENT_FILES_DIR
pwd

echo "Set 'admin' user password for faculty, TA"
read -p "Enter your password: "$'\n' -s passvar

echo "Set 'admin Email' "
read adminMail
echo ""

salt=$(echo $RANDOM | md5sum | head -c 6)
en_pass=$(echo -n "$passvar$salt" | md5sum | gawk '{print $1}')


echo "admin:$en_pass:$salt::$adminMail:" >> $oldfile

OIFS=$IFS

for t in $users
do
	IFS=','
	read -ra VAR <<< $t
	size=${#VAR[@]}
	if [ $size -eq 2 ]
	then
		[ ! -d ${VAR[0]} ] && mkdir ${VAR[0]}
		[ ! -d ${VAR[0]}/log ] && mkdir ${VAR[0]}/log
		salt=$(echo $RANDOM | md5sum | head -c 6)
		echo "${VAR[0]}::$salt::${VAR[1]}:" >> $oldfile
	fi
done

IFS=$OIFS

server_loc="/var/www/html/$course/submission/"

cd /var/www/html/
[ ! -d $course ] && mkdir $course
[ ! -d $server_loc ] &&	mkdir $server_loc

cd $currDir
cp -r "./PHPfiles/"* $server_loc
cp -r "./SHscripts" $base_dir
cp "./plist.txt" $base_dir
cp -r  "./capt" "/var/www/html/$course/"

_config="
<?php
\$dbfile=\"$oldfile\";
\$assignfile=\"$assignfile\";
\$studentdir=\"$STUDENT_FILES_DIR\";
\$base_dir=\"$base_dir\";
\$shscripts=\"$base_dir/SHscripts\";
?>
"

echo $_config > $server_loc/phpConfig.php
cp "setup.conf" $server_loc/"setup.conf"

# preparing permissions for file upload
echo "Changing base folder group ownership to www-data"
sudo chown -R :www-data $base_dir
sudo chmod -R g+rwX $base_dir
sudo chown -R :www-data "/var/www/html/$course/capt"
chmod 774 $oldfile
