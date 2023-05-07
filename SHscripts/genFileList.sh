#!/bin/bash 
source "setup.conf"
path="$STUDENT_FILES_DIR/$1";
username=$1;

echo "<br><br><br>"
echo "Here is the listing of your submitted files:"

ext_all=$(printf "*.%s " "${ext_arr[@]}")

# echo $path
file_list=`( cd $path; ls $ext_all )`

# echo $file_list
echo "<pre><table border=1 width=45%><TR><TH align=center>Filename</TH><TH align=center>Upload time</TH><TH align=center>Size</TH><TH align=center>MD5 Checksum</TH></TR>"
(
    cd $path;
    for file in $file_list
    do
        echo "<TR>"
        ls -lrt --time-style=long-iso $file | gawk '{print "<TD align=center ><a onclick=go_to('\''"$8"'\'')>"$8"</a></TD><TD align=center >"$7":"$6"</TD><TD align=center > "$5"</TD>"}'
        md5sum $file | gawk '{print "<TD align=center >"$1"</TD>"}'
        echo "</TR>"
    done
)
echo "</table></pre>"


# output=`( cd $path ; ls -lrt --time-style=long-iso $ext_all| gawk 'BEGIN{print "<table border=1 width=45%><TR><TH align=center>Filename</TH><TH align=center>Upload time</TH><TH align=center>Size</TH></TR>"} {print "<TR><TD align=center >"$8"</TD><TD align=center >"$7":"$6"</TD><TD align=center > "$5"</TD></TR>"}END{print "</table>"}')`


output=`ls -1 $path/log | sed -n '/log$/p' | gawk -v dir="$path/log" -v uname=$username ' {print "<form action=\"view.php\" method=\"post\"> <input type=hidden name=\"fname\" value=\""dir"/"$1"\"> <input type=submit value=\"View "$1"\"> </form>  "}'`
echo "<br><br><br>"
if test -z $output
then
        echo " ";
else
        echo "View log files:";
        echo "$output";
fi
