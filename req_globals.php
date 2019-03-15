<?php 
	$server = "localhost";
	$username = "root";
	$password = "root";
	$db = "mats";
	$con = mysqli_connect($server,$username,$password,$db);

	if(mysqli_connect_error())
	{
		echo "Failed to connect to MySQL" . mysqli_connect_error();
	}

 ?>