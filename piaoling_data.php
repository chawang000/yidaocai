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
			$index = 701;
			$max_index = 888;//嘌呤网站总数据888

			get_piaoling($index,$max_index,$con);

			function get_piaoling($index,$max_index,$con){
				$updated_num = 0;
				$ingred_list = mysqli_query($con,"SELECT * FROM food_nutrition");
				$ingred_list = mysqli_fetch_all($ingred_list);
				$ingred_name_list = array_column($ingred_list, 2);
				$ingred_name_list = array_filter($ingred_name_list);
				$othername_list = array_column($ingred_list, 3);
				// $othername_list = array_filter($othername_list);
				$ingred_name_expand_list = array();//目标数据库的食物扩展名称，名称里含有小括号的将进行扩展
				foreach ($ingred_list as $value) {
					$ingred_id = $value[0];
					$ingred_name = $value[2];
					if(strstr($ingred_name, '（')){
						$Pare = array();
						$preg = '|（(.*)）|U';
						preg_match_all($preg,$ingred_name,$Pare); 
						$Pare_c = $Pare[1][0];//括号中的内容
						$name_root = preg_replace($preg,'',$ingred_name);
					}else{
						$Pare_c = '';
						$name_root = '';
					}
						$push_expand_name = array();
						$push_expand_name = array(
							'id'=>$ingred_id,
							'name'=>$ingred_name,
							'name_root' => $name_root,
							'name_pare_c' => $Pare_c,
							'add_infront'=> $Pare_c . $name_root,
							'add_toback'=> $name_root . $Pare_c,
						);
						array_push($ingred_name_expand_list, $push_expand_name);
				}

				while($index <= $max_index){
					$url='http://www.gd2063.com/pl/' . $index . '.html';
					$html = file_get_contents($url);
					$html = iconv("gb2312", "utf-8//IGNORE",$html); 
					// 提取名字
					$patern = '/<p><b>食物名称：<\/b>(.*)<\/p>/iUs';
					preg_match_all($patern, $html, $matches);
					$patern = '/>(.*)</iUs';
					preg_match_all($patern, $matches[1][0], $name);
					$mat_name = $name[1][0];
											// 提取嘌呤含量
					$patern = '/<p><b>嘌呤含量：<\/b>(.*)<\/p>/iUs';
					preg_match_all($patern, $html, $purine);
					$purine = $purine[1][0];
					$purine = str_replace("mg/100g","",$purine);

					$keys = array();
					if(strstr($mat_name, '(')){
						// echo $mat_name;
						$Pare = array();
						$preg = "/(?:\()(.*)(?:\))/i";
						preg_match_all($preg,$mat_name,$Pare); 
						// print_r($Pare);
						$Pare_c = $Pare[1][0];//括号中的内容
						$name_root = preg_replace($preg,'',$mat_name);

						$max_i = sizeof($ingred_name_expand_list);
						for ($i=0; $i < $max_i; $i++) { 

						}

						foreach($ingred_name_expand_list as $expand){
							// print_r($ingred_name_expand_list);
							if( $mat_name == $expand['name']):
								$key = array_keys($ingred_name_expand_list,$expand);
								$key = $key[0];
								array_push($keys, $key);
							endif;
							if( $mat_name == $expand['name_root']):
								$key = array_keys($ingred_name_expand_list,$expand);
								$key = $key[0];
								array_push($keys, $key);
							endif;
							if( $name_root == $expand['name']):
								$key = array_keys($ingred_name_expand_list,$expand);
								$key = $key[0];
								array_push($keys, $key);
							endif;
							if( $name_root == $expand['name_root']):
								$key = array_keys($ingred_name_expand_list,$expand);
								$key = $key[0];
								array_push($keys, $key);;
							endif;
						}

						// if($key = array_search($mat_name, array_column($ingred_name_expand_list,'name'))):array_push($keys, $key);endif;
						// if($key = array_search($name_root, array_column($ingred_name_expand_list,'name'))):array_push($keys, $key);endif;
						// if($key = array_search($mat_name, array_column($ingred_name_expand_list,'name_root'))):array_push($keys, $key);endif;
						// if($key = array_search($name_root, array_column($ingred_name_expand_list,'name_root'))):array_push($keys, $key);endif;
					}else{
						// print_r($ingred_name_expand_list);
						// if($key = array_search($mat_name, array_column($ingred_name_expand_list,'name'))):array_push($keys, $key);endif;
						// if($key = array_search($mat_name, array_column($ingred_name_expand_list,'name_root'))):array_push($keys, $key);endif;
						foreach($ingred_name_expand_list as $expand){
							// print_r($ingred_name_expand_list);
							if( $mat_name == $expand['name']):
								$key = array_keys($ingred_name_expand_list,$expand);
								$key = $key[0];
								array_push($keys, $key);
							endif;
							if( $mat_name == $expand['name_root']):
								$key = array_keys($ingred_name_expand_list,$expand);
								$key = $key[0];
								array_push($keys, $key);
							endif;

						}
					}

				

					// $add_infront = $Pare_c . $name_root;
					// $add_toback = $name_root . $Pare_c;
					// 顺序为
					// $ingred_name, $name_root, $add_infront, $add_toback
					// 不要$Pare_c,因为它可能和数据库中的别名的形容词重复如（干）
					$max_i = sizeof($othername_list);
					for ($i=0; $i < $max_i; $i++) { 
						$othernames = $othername_list[$i];
						$othernames = explode(';', $othernames);
						if(in_array($mat_name, $othernames)):
							// echo($i);
							// echo 'from othernames';
							array_push($keys, $i);
						elseif(in_array($name_root, $othernames)):
							// echo($i);
							// echo 'from othernames';
							array_push($keys, $i);
							
						endif;
					}
					
					$keys = array_unique($keys);

					foreach($keys as $k){
						$db_purine = $ingred_list[$k][10];
						if(empty($db_purine)){
							// echo 'its empty';
							$id = $k + 1;
							$sql = "UPDATE food_nutrition SET purine='$purine' WHERE id = '$id' ";
							// echo $sql;
							mysqli_query($con,$sql);
							if(mysqli_affected_rows($con))
							{
								$updated_num += 1;
							}
							echo mysqli_error($con);
						}
					}

					$index += 1;
				}
				echo $updated_num;
			}

			// print_r($matches[1][0]);
			// echo "ENDED AT: " . $index;
			// $web_name_list = array();
			// $web_other_name_list = array();
		?>
    </body>
    
</html>



