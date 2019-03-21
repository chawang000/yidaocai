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

			combine_DB($con);
			function combine_DB($con){
				
				$T_food_list = mysqli_query($con,"SELECT * FROM food_nutrition");//Target
				$T_food_list = mysqli_fetch_all($T_food_list);
				$T_name_list = array_column($T_food_list, 2);
				// print_r($T_name_list);
				$T_othername_list = array_column($T_food_list, 3);
				$T_food_name_expand_list = array();//目标数据库的食物名称数组

				foreach ($T_food_list as $value) {
					$T_food_id = $value[0];
					$T_food_name = $value[2];
					if(strstr($T_food_name, '（')){
						$Pare = array();
						$preg = '|（(.*)）|U';
						preg_match_all($preg,$T_food_name,$Pare); 
						$Pare_c = $Pare[1][0];//括号中的内容
						$name_root = preg_replace($preg,'',$T_food_name);
						$push_expand_name = array();
						$push_expand_name = array(
							'id'=>$T_food_id,
							'name'=>$T_food_name,
							'name_root' => $name_root,
							'name_pare_c' => $Pare_c,
							'add_infront'=> $Pare_c . $name_root,
							'add_toback'=> $name_root . $Pare_c,
						);
						// print_r($value);
						array_push($T_food_name_expand_list, $push_expand_name);
					}
				}
				// print_r($T_name_list);
				// print_r(array_column($T_food_name_expand_list,'name'));

				$O_food_list = mysqli_query($con,"SELECT * FROM mats_data");//ORIGIN
				$O_food_list = mysqli_fetch_all($O_food_list);

				$added_index = 0;
				foreach ($O_food_list as $o) {
					$O_food_id = $o[0];
					$O_food_name = $o[1];
					$O_food_othernames = explode(';', $o[2]);
					$O_food_namefilter = explode(';', $o[3]);
					$O_food_othernames = array_filter($O_food_othernames);
					$O_food_namefilter = array_filter($O_food_namefilter);
					$has_data = $O_food_othernames || $O_food_namefilter;
					if ($has_data){
						// echo '【' . $O_food_name . '】';
						if($key = array_search($O_food_name, $T_name_list)){
							$added_index = push_data($con,$key,$O_food_othernames,$O_food_namefilter,$added_index);
						}elseif ($key = array_search($O_food_name, array_column($T_food_name_expand_list,'name_root'))) {
							$key = ($T_food_name_expand_list[$key]['id']-1);
							$added_index = push_data($con,$key,$O_food_othernames,$O_food_namefilter,$added_index);
						}elseif ($key = array_search($O_food_name, array_column($T_food_name_expand_list,'name_pare_c'))) {
							$key = ($T_food_name_expand_list[$key]['id']-1);
							$added_index = push_data($con,$key,$O_food_othernames,$O_food_namefilter,$added_index);
						}elseif ($key = array_search($O_food_name, array_column($T_food_name_expand_list,'add_infront'))) {
							$key = ($T_food_name_expand_list[$key]['id']-1);
							$added_index = push_data($con,$key,$O_food_othernames,$O_food_namefilter,$added_index);
						}elseif ($key = array_search($O_food_name, array_column($T_food_name_expand_list,'add_toback'))) {
							$key = ($T_food_name_expand_list[$key]['id']-1);
							$added_index = push_data($con,$key,$O_food_othernames,$O_food_namefilter,$added_index);
						}
					}
				}
				echo $added_index;
				// print_name($T_food_name_list);
			}

			function push_data($con,$key,$new_othernames,$new_namefilter,$added_index){
				$T_food_list = mysqli_query($con,"SELECT * FROM food_nutrition");//Target
				$T_food_list = mysqli_fetch_all($T_food_list);
				$old_othernames = explode(';', $T_food_list[$key][3]);
				$old_namefilters = explode(';', $T_food_list[$key][4]);
				foreach($new_othernames as $v){
					if(!in_array($v,$old_othernames) && !in_array($v, $old_namefilters) && !in_array($v, $new_namefilter)){
						array_push($old_othernames, $v);
					}
				}
				foreach($new_namefilter as $v){
					if(!in_array($v, $old_namefilters)){
						array_push($old_namefilters, $v);
					}
				}

				$old_othernames = array_filter($old_othernames);
				if($old_othernames){
					$othernames_pushlist = implode(";", $old_othernames);
				}else{
					$othernames_pushlist = '';
				}

				$old_namefilters = array_filter($old_namefilters);
				if($old_namefilters){
					$namefilters_pushlist = implode(";", $old_namefilters);
				}else{
					$namefilters_pushlist = '';
				}
				
				$id = $key + 1;
				$sql = "UPDATE food_nutrition SET other_names='$othernames_pushlist', name_filter='$namefilters_pushlist' WHERE id = '$id' ";
				// mysqli_query($con,$sql);
				// echo mysqli_error($con);
				// echo '【' . $id . ' ' . $T_food_list[$key][2] . '】' . $othernames_pushlist. ' | '. $namefilters_pushlist. ' //';
				return $added_index+1;
			}	
			

		?>
    </body>
    
</html>



