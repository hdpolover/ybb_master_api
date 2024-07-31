<?php 
$db = mysqli_connect("localhost","root","","db_ybb");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Connection database failed : " . mysqli_connect_error();
}
 
?>