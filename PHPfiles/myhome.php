<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user'])) {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
elseif ($_SESSION['user'] == "admin") {
	header("Location: admin.php");
	exit;
}
if(isset($_SESSION['aname'])){
	unset($_SESSION['aname']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<style>
		a:hover{
			cursor:pointer;	
		}
	</style>
</head>
<body>
<?php
$username=$_SESSION['user'];
echo "<title>$username - My home</title>\n";
echo "<H1>Welcome $username</H1><br>\n";
$output=shell_exec("$shscripts/gen_option.sh $username");
echo $output;
$output=shell_exec("$shscripts/genFileList.sh $username");
echo $output;
$output=shell_exec("$shscripts/genAssignTable.sh");
echo $output;
?>
<p id="box"></p>
	<script>
		function go_to(asign_name){
			document.cookie="aname="+asign_name;
			window.location.replace("viewStudAsign.php");
		}
		function go_2(asign_name){
			document.cookie="aname="+asign_name;
			window.location.replace("uploadInterface.php");
		}
		var elms = document.querySelectorAll("[id='sub']");
		var count = elms.length;
		for(var i = 0; i < elms.length; i++){
			var x = document.getElementById("tablegen").rows[i+1].cells;
			var dead = new Date(x[2].innerHTML);
			var cur = new Date();
			//document.getElementById('box').innerHTML = dead + '<br>' + cur ;
			if(dead < cur)
	  			elms[i].style.display='none';
		}
	</script>
<noscript>Your browser does not support JavaScript!</noscript>
</body>
</html>
