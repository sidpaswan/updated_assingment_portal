<?php
include "phpConfig.php";
session_start();
if(!isset($_SESSION['user']) && $_SESSION['user']!="admin") {
	header("Location: index.php");
	exit; //to make sure nothing else executes
}
function calculate_score(& $arr1, & $weights,& $denom){
	$val = 0;
	for($x=1; $x<sizeof($arr1); $x++){
		$open = strpos($weights[$x], "(");
		$close = strpos($weights[$x], ")");
		$w = explode(',',substr($weights[$x], $open+1, $close-$open-1))[1]	;
		$val = $val + ( (int)$arr1[$x] * (int)$w);
	}
	return round( (float)( ($val*100)/$denom ), 2 );
}


$arr = array();

if( !is_dir($studentdir)){
	echo "Student directory not found!";
	exit;
}

$res =  scandir($studentdir.'/');
\array_splice($res,0,2);

$denom = 0;
$handle = fopen($assignfile, "r");
if ($handle) {
	$temp=array("Roll No");
    while (($line = fgets($handle)) !== false) {
    	list($assign,$dead1,$dead2,$mail,$max,$weight,$pen) = explode(",", $line);
    	$str =$assign.'('.$max.','.$weight.')';
		$denom=$denom+( (int)$max * (int)$weight );
        array_push($temp,$str);
    }
    array_push($arr,$temp);
    fclose($handle);
}

for($i=0; $i<sizeof($res);$i++){
	$inside_arr = array($res[$i]);
	$log=$studentdir.'/'.$res[$i].'/log/';
	if(!is_dir($log)){
		echo $res[$i]."'s marks log not found!<br>";
		continue;
	}
	for($x=1; $x < sizeof($arr[0]); $x++ ){
		$file=$log.explode('(',$arr[0][$x])[0].'.log';
		if(file_exists($file))
			array_push($inside_arr,explode('/',shell_exec("cat $file"))[0]);
		else
			array_push($inside_arr,0);
	}	
	array_push($arr,$inside_arr);
}

echo "<table border=1>";
for($i=0 ; $i < sizeof($arr) ; $i++ ){
	echo "<tr>";
	for($j=0 ; $j < sizeof($arr[$i]) ; $j++ ){
		echo "<td align=center>";
		echo $arr[$i][$j];
		echo "</td>";
	}
	if($i > 0){
		$score = calculate_score($arr[$i],$arr[0],$denom);
		echo "<td align=center>".$score."</td>";
		array_push($arr[$i],$score);
	}
	else{
		array_push($arr[$i],"Overall Marks(100)");
		echo "<td align=center> Overall Marks(100) </td>";
	}
	echo "</tr>";
}
echo "</table>";

if(isset($_POST['csv'])) {
	$filename = "".$base_dir."/".date("h:i:sa")."_compiled_res.csv";
	$file = fopen($filename,"w");
	foreach ($arr as $line) {
	  fputcsv($file, $line);
	}
	fclose($file);
	echo "File saved in: ".$filename . "!";
}

?>
<html>
<head>
	<style>
		body{
			height: 100vh;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
		}
	</style>
</head>
<body>
	<form action="compiledRes.php" method="post">
		<input type="submit" name="csv" value="Save as CSV" >
	</form>
	<a href="admin.php"> Home </a>
</body>
</html>
