#!/bin/bash 
source "setup.conf"
#path="../students/$1";
path="$STUDENT_FILES_DIR";
username=$1;
passwd=$2;




echo "<br><br><br>"
#echo "$path<br>"
#ls -l "$path/$username"
echo "Here is the listing of your files..."
#output=`( ls -lrt --time-style=long-iso $path/$username/ | sed '1d' | gawk 'BEGIN{print "<table border=1 width=45%><TR><TH align=center>Filename</TH><TH align=center>Upload time</TH><TH align=center>Size</TH></TR>"} {print "<TR><TD align=center >"$8"</TD><TD align=center >"$7":"$6"</TD><TD align=center > "$5"</TD></TR>"}END{print "</table>"}')`
output=`( cd $path/$username/ ; ls -lrt --time-style=long-iso *.c *.cpp *.h *.ps *.txt *.tgz *.pdf *.java *.l *.sh *.zip| gawk 'BEGIN{print "<table border=1 width=45%><TR><TH align=center>Filename</TH><TH align=center>Upload time</TH><TH align=center>Size</TH></TR>"} {print "<TR><TD align=center >"$8"</TD><TD align=center >"$7":"$6"</TD><TD align=center > "$5"</TD></TR>"}END{print "</table>"}')`
#output=`ls -lrt --time-style=long-iso $path/$username | sed '1d' | gawk '{print $8"\t"$7":"$6"\tSIZE: "$5}'`
if test -z "$output"
then
	echo "<pre>No files.</pre>";
else
	echo "<pre>$output</pre>";
fi

output=`ls -1 $path/$username | sed -n '/log$/p' | gawk -v dir="$path/$username" -v uname=$username -v pass=$passwd ' {print "<form action=\"view.php\" method=\"post\"> <input type=hidden name=\"fname\" value=\""dir"/"$1"\"> <input type=submit method=post value=\"View "$1"\"> <input type=hidden name=uname method=post value=\""uname"\"> <input type=hidden method=post name=passwd value=\""pass"\"> </form>  "}'`
echo "<br><br><br>"
if test -z $output
then
	echo " ";
else
	echo "View log files...";
	echo "$output";
fi
