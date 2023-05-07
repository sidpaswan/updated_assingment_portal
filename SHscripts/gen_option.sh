#!/bin/bash
source "setup.conf"

echo "<TABLE WIDTH=30%>"
echo "<TR>"

echo "<TD>"
echo "<FORM action=\"myhome.php\">"
echo "<button type=submit>My home</button>"
echo "</FORM>"
echo "</TD>"

echo "<TD>"
echo "<FORM action=\"chpasswd.php\">"
echo "<button type=submit>Change password</button>"
echo "</FORM>"
echo "</TD>"

if [ "$1" == "admin" ]
then
echo "<TD>" 
echo "<FORM action=\"compiledRes.php\">"
echo "<button type=submit>Compiled Results</button>"
echo "</FORM>"
echo "</TD>"
fi

echo "<TD>"
echo "<FORM action=\"logout.php\">"
echo "<button type=submit>Logout</button>"
echo "</FORM>"
echo "</TD>"

echo "</TR>"
echo "</TABLE>"


