<?php 
// ganti ke true untuk development, false untuk production
$isDev = false;

if ($isDev) {
	$_hostname = 'localhost';
	$_username = 'root';
	$_password = '';
	$_database = 'db_ybb';
} else {
	$_hostname = 'localhost';
	$_username = 'u1437096_ybb_master_app_admin_user';
	$_password = '#E1}1M^Yjs^d';
	$_database = 'u1437096_ybb_master_app_db';
}

$db = mysqli_connect($_hostname,$_username,$_password,$_database);
 
// Check connection
if (mysqli_connect_errno()){
	echo "Connection database failed : " . mysqli_connect_error();
}
 
?>