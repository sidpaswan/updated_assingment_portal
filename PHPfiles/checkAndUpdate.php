<?php
session_start();
if(!isset($_SESSION['user'])) {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>
<?php 
include "phpConfig.php";

$username=$_SESSION['user'];

// update 1 is here
# when redirected from chpasswd.php page old password, new password 1 and new passsword 2 are also sent as session variables to update the password
$oldpasswd=$_SESSION['oldpass'];
$newpasswd1=$_SESSION['newpasswd1'];
$newpasswd2=$_SESSION['newpasswd2'];
// 1 ends here
// update 2 here
# delete ths session variables as soon as the execution is complete for security measures
unset($_SESSION['oldpass']);
unset($_SESSION['newpasswd1']);
unset($_SESSION['newpasswd2']);
// 2 ends here

if($newpasswd1 != $newpasswd2){
	echo "<H1>Welcome $username</H1><br>";
	$output=shell_exec("$shscripts/gen_option.sh $username");
	echo "$output<br>";
	echo "<br>New Password(s) didn't match...<br><br>\n";
}
else{
	echo "<H1>Welcome $username</H1><br>";
	$output=shell_exec("$shscripts/gen_option.sh $username");
	echo "$output<br>";
	$output=shell_exec("$shscripts/updateRollList.sh $username $oldpasswd $newpasswd1");
	// echo $output;
	if($output == "NOP"){
		echo "<br>Incorrect login name or password...<br><br>\n";
	} else {
		
		echo "Password successfully updated.<br>";
	}
}


?>

</body>
</html>
