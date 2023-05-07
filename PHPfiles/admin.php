<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user'] != "admin") {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
if(isset($_SESSION['aname'])){
	unset($_SESSION['aname']);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Assignment Page</title>
	<style>
		#cur{
			cursor:pointer;
		}
	</style>
</head>
<body>
	<h1> Welcome Admin </h1>
	<br>
	<?php
		$output=shell_exec("$shscripts/gen_option.sh admin");
		echo $output;
	?>

	<br><b>Add new assignment for the course </b><br><br>
	<form action="newassign.php" method="POST">
		<label for="filename">Filename: </label>
  		<input type="text" name="filename" required><br>
		<label for="deadline">Deadline: </label>
  		<input type="datetime-local" name="deadline1" required><br>
  		<label for="deadline">Last Deadline: </label>
  		<input type="datetime-local" name="deadline2" required><br>
  		<label for="ta_contact">Enquiry Mail: </label>
  		<input type="email" name="ta_contact" required><br>
		<label for="max_marks">Maximum Marks for the assignment: </label>
		<input type="number" name="max_marks" required><br>
  		<label for="weightage">Weightage of the assignment: </label>
		<input type="number" name="weightage" required><br>
		<label for="late_penalty">Late submission penalty: </label>
		<input type="number" name="late_penalty" required min="1" max=""><br>
		<input type="submit">
	</form>
	
	<?php 
		$output=shell_exec("$shscripts/genAssignTable.sh");
		echo $output;
	?>
	<script>
		function go_to(asign_name){
			document.cookie="aname="+asign_name;
			window.location.replace("viewStudAsign.php");
		}
	</script>
	
</body>
</html>
