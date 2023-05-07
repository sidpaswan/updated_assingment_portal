<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user'])){
	header("Location: index.php");
    exit; //to make sure nothing else executes
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<?php
 $pos=32;
 //$pos=30;
$username=$_SESSION['user'];
$filename=$_POST['fname'];
$pos=strrpos($filename,'/');
$name=substr($filename,$pos+1);
echo "<title>$username - $name</title>\n";
echo "<H1>Welcome $username</H1><br>";
$output=shell_exec("$shscripts/gen_option.sh $username");
echo "$output<br>";
echo "<H1>$name</H1><br>";
$fcontent=shell_exec("cat $filename");
echo "<p>$fcontent</p>";
echo "<br><br><br>\n";
?>
</html>

