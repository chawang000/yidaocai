<?php 
	require('req_globals.php');
	require_once 'qcloudapi-sdk-php-master/src/QcloudApi/QcloudApi.php';//腾讯文智sdk同义词api
	mysqli_query($con, 'set names utf8');
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
			$img = file_get_contents('_img/example_10.jpg');
			$img64 = base64_encode($img);
			// $img64 = $_POST['img64'];
			// $img64 = str_replace('data:image/jpeg;base64,', '', $img64);

			$dishes = ImgRecog_dish($img64);//通过百度api获取菜品名称
			// print_r($dishes);
			$results = get_result($con,$dishes);
			// $nutrition = get_nutrition($ingredients);
			// print_r($ingredients);
			// 先直接和食材数据库对比，如果菜名和食材名吻合，直接从食材数据库提取信息





			function request_post($url = '', $param = '') {
		        if (empty($url) || empty($param)) {
		            return false;
		        }
		        $postUrl = $url;
		        $curlPost = $param;
		        $curl = curl_init();//初始化curl
		        curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
		        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
		        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		        curl_setopt($curl, CURLOPT_SSLVERSION, 1);
		        $data = curl_exec($curl);//运行curl
		        curl_close($curl);
		        return $data;
		    }

		    function ImgRecog_dish($img64){
		    	$url = 'https://aip.baidubce.com/oauth/2.0/token';
			    $post_data['grant_type']       = 'client_credentials';
			    $post_data['client_id']      = 'iS8tPhGxvzAYrDIzzpcCvmyl';
			    $post_data['client_secret'] = 'l1vn7rFprctTjMjl03VqpfUGd9Bda8pl';
			    $o = "";
			    foreach ( $post_data as $k => $v ) 
			    {
			    	$o.= "$k=" . urlencode( $v ). "&" ;
			    }
			    $post_data = substr($o,0,-1);
			    
			    $res = request_post($url, $post_data);
				$token = json_decode($res)->access_token;
				$url = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/dish?access_token=' . $token;
				$bodys = array(
				    'image' => $img64,
				    // 'sub_lib' => "[appid]",
				    // 'brief' => 1
				);
				$res = request_post($url, $bodys);
				$dishes = array();
				$first_dish = json_decode($res)->result[0]->name;
				if($first_dish == '非菜'){
					// echo '这不是菜。';
					$url = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/advanced_general?access_token=' . $token;
					$res = request_post($url, $bodys);
					$dish = array();
					foreach(json_decode($res)->result as $v){
						$dish_type = $v->root;
						$varified = strpos($dish_type,'食物') !== false || strpos($dish_type,'植物') !== false || strpos($dish_type,'水果');
						if($varified) {
							// echo $dish_type . ' | ';
							$name = $v->keyword;
							$score = $v->score;
							$dish = array(
								'name' => $name,
								'score' => $score
							);
							array_push($dishes,$dish);
							return $dishes;
						}
						// echo $dish_type;
						array_filter($dishes);
					}
				}else{
					foreach(json_decode($res)->result as $v){
						$name = $v->name;
						$score = $v->probability;
						$dish = array(
							'name' => $name,
							'score' => $score
						);
						array_push($dishes,$dish);
					}
				}
				// print_r($dishes);
				return $dishes;
		    }

		    function get_result($con,$dishes){
		    	$scores = array_column($dishes,'score');
		    	$dish_length = sizeof($scores);//
		    	if($dish_length == 0){
		    		echo "不是食物，返回false";
		    		return false;//判断不是食物
		    	}
		    	array_multisort($scores,SORT_DESC,$dishes);//将最高分菜品放在最前
		    	$scores = array_column($dishes,'score');//得到排序后的score排序
		    	$selected_dishes = array();
		    	array_push($selected_dishes,$dishes[0]);//无论如何都会选入第一个菜品
		    	if($dish_length == 1){
		    		//如果长度是1，说明是百度通用识别获得的结果
		    		//通过通用识别的结果（一般不是菜品），所以直接和营养库对比
		    		$exist = get_nutrition($con,$selected_dishes);
		    		if($exist){
		    			echo "通用识别直接对比食材库，有结果返回。";
		    			print_r($selected_dishes);
		    			return;
		    			// $res = array(
		    			// 	'dish' => 
		    			// );
		    		}else{
		    			echo "没有最终结果";
		    			return;
		    		}
		    		// return $selected_dishes;
		    	}
		    	// 选出可能的菜品后，得到$selected_dishes
				// 现在因为很多步骤的数据是通过api获得，为了不影响速度，只从中选择第一个菜品来进一步处理信息
				// $scale = 2;
		  //   	for ($i=0; $i<$dish_length; $i++) {
		  //   		// echo "循环";
				// 	if(($i+1)<$dish_length && $scores[$i] <= ($scale*$scores[($i+1)])){
				// 		array_push($selected_dishes,$dishes[($i+1)]);
				// 	}else{
				// 		break;//终止循环
				// 	}
				// } 
				

		    	// $test_dishes = array();
		    	// $text_2 = array('硬五花');
		    	// foreach($text_2 as $v){
		    	// 	$dish = array(
		    	// 		'name' => $v,
		    	// 	);
		    	// 	array_push($test_dishes, $dish);
		    	// }

				// 第一步是先和食材库对比，看次菜品是不是就是食材
				$exist = get_nutrition($con,$selected_dishes);
				if($exist !== false){
					echo "菜品识别直接对比食材库，有结果返回。";
					return;
				}else{
					echo '发送到菜谱库';
				}
		    }

			function get_nutrition($con,$ingredients){
				$ingred_boss = mysqli_query($con,"SELECT * FROM food_nutrition");
				$ingred_list = mysqli_fetch_all($ingred_boss);
				$ingred_name_list = array_column($ingred_list, 2);
				$ingred_othername_list = array_column($ingred_list, 3);

				// echo ($ingred_othername_list[535]);
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
				// print_r($ingred_name_expand_list);
				// echo sizeof($ingredients);
				$ingredient_names = array_column($ingredients,'name');
				
				foreach($ingredient_names as $v){
					if($key =  array_search($v, array_column($ingred_name_expand_list,'name'))):;
					elseif($key =  array_search($v, array_column($ingred_name_expand_list,'name_root'))):;
					elseif($key =  array_search($v, array_column($ingred_name_expand_list,'name_pare_c'))):;
					elseif($key =  array_search($v, array_column($ingred_name_expand_list,'add_infront'))):;
					elseif($key =  array_search($v, array_column($ingred_name_expand_list,'add_toback'))):;
					else://将别名名单分割成独立别名并且比较
						foreach ($ingred_othername_list as $os){
							$othernames = explode(';', $os);
							foreach($othernames as $o){
								if($v == $o){
									$key = array_keys($ingred_othername_list,$os)[0];
									echo $key . '从别名识别到了食材';
									goto endOthernameLoop;
								}
							}
						}
					endOthernameLoop:;
					endif;

					if($key){
						echo $key;
						echo ' 食材存在并返回营养，食材名称: '. $ingred_list[$key][2] .' | ';
						return $ingredient_names;
					}else{
						echo $v . '食材不存在'. ' | ';
						return false;
					}
				}
				// print_r($ingredient_names);
				
			}

		?>
    </body>
    
</html>
