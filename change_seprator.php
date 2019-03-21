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
			$T_food_list = mysqli_query($con,"SELECT * FROM food_nutrition");//Target
			$T_food_list = mysqli_fetch_all($T_food_list);
			$T_food_name_expand_list = array();//目标数据库的食物名称数组
			$index = 0;
			foreach ($T_food_list as $value) {
				$T_food_id = $value[0];
				$T_food_names = $value[2];
				$T_food_othernames = $value[3];

				// if(strstr($T_food_othernames, '（')){
				// 	echo '【' . $T_food_othernames . '】 ';
				// 	$index += 1;
				// }
				if(strstr($T_food_othernames, '（')){
					$othernames = explode(';', $T_food_othernames);
					foreach ($othernames as $value) {
						if(strstr($value, '（')){
							$Pare = array();
							$preg = '|（(.*)）|U';
							preg_match_all($preg,$value,$Pare); 
							$Pare_c = $Pare[1][0];//括号中的内容
							if($Pare_c != '均值' && $Pare_c != '标准粉'){
								$name_root = preg_replace($preg,'',$value);
								$result = $Pare_c . $name_root;
								array_push($othernames, $result);
								$index += 1;
							}
						}
					}
					$result_pushlist = implode(";", $othernames);
					// update_othernames($con,$T_food_id,$result_pushlist);
					print_r($T_food_id . ' : ' . $result_pushlist) . ' ; ';
					
				}
			}
			echo $index;
			// foreach ($T_food_list as $value) {
			// 	$T_food_id = $value[0];
			// 	$T_food_othernames = $value[3];
			// 	if(strstr($T_food_othernames, '（U）')){
			// 		// echo $value[0] . ' : ' . $T_food_othernames . '. ';
			// 		$USnames = explode('、', $T_food_othernames);
			// 		$result = array();
			// 		foreach ($USnames as $v) {
			// 			if(sizeof($USnames)>= 1 && !strstr($v, '（U）')){
			// 				array_push($result, $v);
			// 			}
			// 		}
			// 		$result_pushlist = implode(";", $result);
			// 		update_othernames($con,$T_food_id,$result_pushlist);
			// 	}
			// }

			function update_othernames($con,$index, $othernames){
				$sql = "UPDATE food_nutrition SET other_names='".$othernames."' WHERE id = " . $index;
				mysqli_query($con,$sql);
			}				
		?>
    </body>
    
</html>



