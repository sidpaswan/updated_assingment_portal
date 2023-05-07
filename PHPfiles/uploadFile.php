<?php
session_start();
if(!isset($_SESSION['user'])) {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
?>
<?php 
function iptype1 () {
	if (getenv("HTTP_CLIENT_IP")) {
		return getenv("HTTP_CLIENT_IP");
	}
	else {
		return "none";
	}
}
function iptype2 () {
	if (getenv("HTTP_X_FORWARDED_FOR")) {
		return getenv("HTTP_X_FORWARDED_FOR");
	}
	else {
		return "none";
	}
}
function iptype3 () {
	if (getenv("REMOTE_ADDR")) {
		return getenv("REMOTE_ADDR");
	}
	else {
		return "none";
	}
}
function ip() {
	$ip1 = iptype1();
	$ip2 = iptype2();
	$ip3 = iptype3();
	return ":::$ip1:::$ip2:::$ip3";
	if (isset($ip1) && $ip1 != "none" && $ip1 != "unknown") {
		return $ip1;
	}
	elseif (isset($ip2) && $ip2 != "none" && $ip2 != "unknown") {
		return $ip2;
	}
	elseif (isset($ip3) && $ip3 != "none" && $ip3 != "unknown") {
		return $ip3;
	}
	else {
		return "unknown";
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>
<?php
include "phpConfig.php";

	$username=$_SESSION['user']; 
	$uploadpath="$studentdir/$username/";
	
	$fname=basename( $_FILES['uploadedfile']['name']);
	echo "<title>$username - File upload</title>\n";
	//$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	//$ip=$_SERVER['HTTP_CLIENT_IP'];
	$ip = ip();
	$output=shell_exec("$shscripts/checkFile.sh $username $fname $ip");
	echo "<H1>Welcome $username</H1><br>";
	$output_opt=shell_exec("$shscripts/gen_option.sh $username");
	echo $output_opt;
	echo "\n\n<br><br>";
	if(trim($output) == "OK"){
		//echo "XXX - $uploadpath   $_FILES['uploadedfile']['tmp_name']";
		$uploadpath = $uploadpath . $fname; 
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploadpath)) {
			chmod($uploadpath,0664);
			echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
			" has been uploaded";
		} else{
				echo "There was an error uploading the file, please try again!";
		}
	} elseif (trim($output) == "LATE"){
		$uploadpath = $uploadpath ."late_". $fname; 
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $uploadpath)) {
			chmod($uploadpath,0664);
			echo "The file ".  basename( $_FILES['uploadedfile']['name']). 
			" has been uploaded";
		} else{
				echo "There was an error uploading the file, please try again!";
		}
	} else{
		echo "$output <br>";
		//echo "<font color=red><big><b>No more submission will be accepted.</b></big></font><br>";
		//echo "There was an error uploading the file, please try again!";
	}
?>
</body>
</html>

