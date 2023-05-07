#!/bin/bash
source "setup.conf"

echo "<br>"
echo "Here is the listing of assignments given:<br>"

echo "<pre><table border=1 id=tablegen width=45%><TR><TH align=center>Filename</TH><TH align=center>Deadline</TH><TH align=center>Last Submission Allowed</TH><TH align=center>For Query</TH><TH>Submission Link</TH></TR>"
file=$(tac $assignfile)
for line in $file
do
	now=$(date +%s)
	deadline=`echo $line | gawk -F , '{print $3}'`
	deadline=$(date -d $deadline +%s)
	if [ $now -gt $deadline ]; then
		echo $line | gawk -F , '{print "<TR><TD id=cur align=center ><a onclick=go_to('\''"$1"'\'')>"$1"</a></TD><TD align=center >"$2"</TD><TD align=center >"$3"</TD><TD align=center >"$4"</TD><TD align=center id=sub></TD></TR>"}'
	else
		echo $line | gawk -F , '{print "<TR><TD id=cur align=center ><a onclick=go_to('\''"$1"'\'')>"$1"</a></TD><TD align=center >"$2"</TD><TD align=center >"$3"</TD><TD align=center >"$4"</TD><TD align=center id=sub><a onclick=go_2('\''"$1"'\'')>Submit</a></TD></TR>"}'
	fi 
done

echo "</table></pre>"
