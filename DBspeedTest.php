<?php 
	require('req_globals.php');
	mysqli_query($con, 'set names utf8');
	// if (!$mat_first) {
	// printf("Error: %s\n", mysqli_error($con));
	// exit();
	// }
?>
<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<title>Hello World</title>
    <style>
        body{font-size:45px; color:#000;font-family:Arial,Helvetica,sans-serif;}
        a{color:#039;text-decoration:none;}
    </style> 
	</head>
	<body>
		<?php 

			run($con);
			function run($con){
				$food_name_list = array();
				$food_list = mysqli_query($con,"SELECT * FROM food_nutrition");
				$food_list = mysqli_fetch_all($food_list);
				foreach ($food_list as $value) {
					$food_name = $value[2];
					array_push($food_name_list, $food_name);
					// print_r($value[2]);
				}
				print_name($food_name_list);
			}

			function print_name($food_name_list){
				print_r($food_name_list);
			}
			

		?>
    </body>
    
</html>



