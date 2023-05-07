<?php
session_start();
if(!isset($_SESSION['user'])) {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
if($_COOKIE['aname']){
	$_SESSION['aname'] = $_COOKIE['aname'];
}
setcookie("aname", "", time()-3600);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>
<?php
include "phpConfig.php";
	$username=$_SESSION['user'];
	echo "<title>$username - File upload</title>\n";
	echo "<H1>Welcome $username</H1><br>\n";
	$output=shell_exec("$shscripts/gen_option.sh $username");
	echo "$output<br>";
	$fx = $_SESSION['aname'];
	$output=shell_exec("tac $assignfile | grep -F -m 1 $fx");
	if($output == ''){
		echo "No such assignment exists!";
	}else{
	//$output=shell_exec("tail -1 $assignfile");
	list($fsub, $deadline1, $deadline2)=explode(",",$output);
	echo "<br>Latest Assignment: <b>$fsub</b>";
	echo "<br>Deadline: <b>$deadline1</b>";
	echo "<br>Late submissions allowed till <b>$deadline2</b>.<br><br>";
	if(time() > strtotime($deadline2)){
		echo "Submission time Expired! Please contact the TA!";
	}else{
	echo "<form enctype=\"multipart/form-data\" action=\"uploadFile.php\" method=\"POST\">\n";
	echo "<b>Choose a file to upload:</b> <input name=\"uploadedfile\" type=\"file\">\n";
	echo "<input type=hidden name=uname value=\"$username\">\n";
	echo "<input type=\"submit\" value=\"Upload File\">\n";
	}
	echo "<br><br><br>\n";
	// echo "<font color=red>\n";
	echo "<b>Notes:\n";
	echo "<ul>\n";
	// echo "<li>No more submission is possible!!!</li><br>\n";
	echo "<li>Please make sure that your filename is correct. Filenames are case sensitive.</li>\n";
	echo "<li>You can also upload the old assignments, if their deadline has yet not ended.</li>\n";
	echo "</ul>\n";
	echo "</b>\n";
	}
?>
</body>
</html>


