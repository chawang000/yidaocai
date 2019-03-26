<?php 
	require('req_globals.php');
	require_once 'qcloudapi-sdk-php-master/src/QcloudApi/QcloudApi.php';//腾讯文智sdk同义词api
	header( 'Content-Type:text/html;charset=utf-8;application/json'); 
	mysqli_query($con, 'set names utf8');

	$img64 = $_POST['img64'];
	$img64 = str_replace('data:image/jpeg;base64,', '', $img64);
	$baidu_token = '';
	$dishes = ImgRecog_dish($img64);//通过百度api获取菜品名称
	// print_r($dishes);
	$results = get_result($con,$dishes,$baidu_token);
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
		global $baidu_token;
		$baidu_token = $token;
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
				$varified = strpos($dish_type,'食物') !== false || strpos($dish_type,'植物') !== false || strpos($dish_type,'水果') || strpos($dish_type,'食品')!== false;
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

    function get_result($con,$dishes,$baidu_token){
    	$scores = array_column($dishes,'score');
    	$dish_length = sizeof($scores);//
    	if($dish_length == 0){
    		// echo "不是食物，返回false";
    		$json_result = json_encode(array(
				'resultcode' => 10003,//菜品为通用识别非食物物体，无结果
				'dish' => '',
				'nutrition' => ''
			));
			echo ($json_result);
    		return false;//判断不是食物
    	}
    	array_multisort($scores,SORT_DESC,$dishes);//将最高分菜品放在最前
    	$scores = array_column($dishes,'score');//得到排序后的score排序
    	$selected_dishes = array();
    	array_push($selected_dishes,$dishes[0]);//无论如何都会选入第一个菜品
    	if($dish_length == 1){
    		//如果长度是1，说明是百度通用识别获得的结果
    		//通过通用识别的结果（一般不是菜品），所以直接和营养库对比
    		$res = get_nutrition($con,$selected_dishes);
    		if(!empty($res)){
    			// echo "通用识别直接对比食材库，有结果返回。";
    			$json_result = json_encode(array(
					'resultcode' => 10001,//菜品为通用识别食物，有结果
					'dish' => $selected_dishes[0],
					'nutrition' => $res
				));
				echo ($json_result);
    			return;
    		}else{
    			// echo "没有最终结果";
    			$json_result = json_encode(array(
					'resultcode' => 10002,//菜品为通用识别食物，无结果
					'dish' => $selected_dishes[0],
					'nutrition' => ''
				));
				echo ($json_result);
    			return;
    		}
    		// return $selected_dishes;
    	}

		$res = get_nutrition($con,$selected_dishes);
		if(!empty($res)){
			$json_result = json_encode(array(
				'resultcode' => 20001,//菜品为图像识别菜名，有结果
				'dish' => $selected_dishes[0],
				'nutrition' => $res
			));
			echo ($json_result);
			// echo "菜品识别直接对比食材库，有结果返回。";
			return;
		}else{
			foreach($selected_dishes as $v){
				// echo '发送到菜谱库';
				$appkey = 'fe2a7568d9a10323';//你的appkey
				$num = 20;
				$keyword = $v['name'];//(utf-8)
				$url = "http://api.jisuapi.com/recipe/search";
				$bodys = array(
					'appkey' => $appkey,
					'keyword' => $keyword,
					'num' => $num
				);
				$api_dishes = request_post($url, $bodys);
				if(empty(json_decode($api_dishes)->result)){
					// echo '食谱库没有数据。';
					return false;
				}
				// echo sizeof(json_decode($api_dishes)->result->list);
				$unique_names = array_column(json_decode($api_dishes)->result->list,'name');
				$unique_names = array_unique($unique_names);
				$scored_names = array();
				// 发送无重复的名字到短文本api查看近似度
				// echo $baidu_token;
				$url = 'https://aip.baidubce.com/rpc/2.0/nlp/v2/simnet?access_token=' . $baidu_token;
				$text_1 = $keyword;
				// echo '【' . $text_1 . '】';
				foreach($unique_names as $t2){
					$bodys = array(
					    'text_1' => $text_1,
					    'text_2' => $t2,
					);
					$bodys = json_encode($bodys);
					$bodys = iconv("UTF-8","gbk//TRANSLIT",$bodys);
					$res = request_post($url, $bodys);
					$res = iconv('GB2312', 'UTF-8',$res);
					$scored_name = array(
						'name' => json_decode($res)->texts->text_2,
						'score' => json_decode($res)->score 
					);
					array_push($scored_names, $scored_name);
				}
				array_multisort(array_column($scored_names, 'score'),SORT_DESC,$scored_names);
				$highest_score_name = $scored_names[0]['name'];
				$api_dishes = json_decode($api_dishes)->result->list;

				// 处理同名菜
				$same_name_dishes = array();//和最高分菜名同名的所有菜谱
				foreach($api_dishes as $d){
					if($d->name == $highest_score_name){
						$main_ingredients = array();
						// echo '【' . $d->name . '】';
						// 把所有主料推入到$main_ingredients
						foreach($d->material as $m){
							if($m->type == 1){
								// 查询食材库中此食材的所有别名
								$m_othernames = get_othernames($con,$m->mname);
								$ingred_w_othernames = array(
									'm_name' => $m->mname,
									'othernames' => $m_othernames
								);
								array_push($main_ingredients, $ingred_w_othernames);
								// print_r($ingred_w_othernames);
							}
						}
						if(empty($main_ingredients)){
							// echo '没有主料。蛋疼了';
						}

						$same_dish = array(
							'id' => $d->id,
							'name' => $d->name,
							'material' => $main_ingredients,
						);
						array_push($same_name_dishes, $same_dish);
					}
				}
				$scores = array();
				foreach($same_name_dishes as $d){
					array_push($scores, 0);
				}
				$max_i = sizeof($same_name_dishes);
				for ($i=0; $i < $max_i ; $i++) { 
					$d1_id = $same_name_dishes[$i]['id'];
					$materials = $same_name_dishes[$i]['material'];
					foreach($materials as $m){
						$m_name = $m['m_name'];
						foreach($same_name_dishes as $d2){
							$d2_id = $d2['id'];
							if($d1_id != $d2_id){
								$d2m_names = array_column($d2['material'], 'm_name');
								$o_names = array_column($d2['material'], 'othernames');
								foreach($o_names as $o_n){
									if(in_array($m_name, $o_n) || in_array($m_name, $d2m_names)){
										$scores[$i] += 1;
									}
								}
							}
							
						}
					}
				}	
				array_multisort($scores,SORT_DESC,$same_name_dishes);
				$mnames = array_column($same_name_dishes[0]['material'], 'm_name');
				$ingredients = array();
				foreach($mnames as $m){
					$mname = array(
						'name' => $m
					);
					array_push($ingredients, $mname);
				}
				$nutrition = get_nutrition($con,$ingredients);
				// print_r($nutrition);
				// print_r($same_name_dishes[0]);
				$final_dish_id = $same_name_dishes[0]['id'];
				foreach($api_dishes as $d){
					if($d->id == $final_dish_id){
						if(!empty($nutrition)){
							$final_dish = $d;
							$json_result = json_encode(array(
								'resultcode' => 30001,//菜谱菜识别有食材信息
								'dish' => $final_dish,
								'nutrition' => $nutrition
							));
							echo ($json_result);
						}else{
							$final_dish = $d;
							$json_result = json_encode(array(
								'resultcode' => 30002,//菜谱菜识别无食材信息
								'dish' => $final_dish,
								'nutrition' => ''
							));
							echo ($json_result);
						}
					}
				}
			}
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
		$result = array();
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
							// echo $v . '从别名识别到了食材';
							goto endOthernameLoop;
						}
					}
				}
			endOthernameLoop:;
			endif;

			if($key){
				// echo $key;
				// echo ' 食材存在，食材名称: '. $ingred_list[$key][2] .' | ';
				$nutri_basic = $ingred_list[$key][5];
				$nutri_fat = $ingred_list[$key][6];
				$nutri_mineral = $ingred_list[$key][7];
				$nutri_vitam = $ingred_list[$key][8];
				$nutri_amino = $ingred_list[$key][9];
				$purine = $ingred_list[$key][10];

				$nutri_basic = unserialize($nutri_basic);
				$nutri_fat = unserialize($nutri_fat);
				$nutri_mineral = unserialize($nutri_mineral);
				$nutri_vitam = unserialize($nutri_vitam);
				$nutri_amino = unserialize($nutri_amino);
				// print_r($nutrition);
				$ingred_info = array(
					'id' => $ingred_list[$key][0],
					'name' => $ingred_list[$key][2],
					'nutri_basic' => $nutri_basic,
					'nutri_fat' => $nutri_fat,
					'nutri_mineral' => $nutri_mineral,
					'nutri_vitam' => $nutri_vitam,
					'nutri_amino' => $nutri_amino,
					'purine' => $purine
				);
				array_push($result, $ingred_info);
				// return $ingredient_names;
			}else{
				// echo $v . '食材不存在'. ' | ';
				// return false;
			}
		}
		// print_r($result);
		return $result;
	}

	function get_othernames($con,$mname){
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
		
		$keys = array();
		if($key =  array_search($mname, array_column($ingred_name_expand_list,'name'))):array_push($keys, $key);
		elseif($key =  array_search($mname, array_column($ingred_name_expand_list,'name_root'))):array_push($keys, $key);
		elseif($key =  array_search($mname, array_column($ingred_name_expand_list,'name_pare_c'))):array_push($keys, $key);
		elseif($key =  array_search($mname, array_column($ingred_name_expand_list,'add_infront'))):array_push($keys, $key);
		elseif($key =  array_search($mname, array_column($ingred_name_expand_list,'add_toback'))):array_push($keys, $key);
		endif;

		foreach ($ingred_othername_list as $os){
			$othernames = explode(';', $os);
			foreach($othernames as $o){
				if($mname == $o){
					$key = array_keys($ingred_othername_list,$os)[0];
					array_push($keys, $key);
				}
			}
		}
		
		$keys = array_unique($keys);
		$unique_othernames = array();
		foreach($keys as $k){
			$othernames = explode(';',$ingred_othername_list[$k]);
			array_push($othernames, $ingred_name_list[$k]);
			array_filter($othernames);
			$unique_othernames = array_merge($unique_othernames,$othernames);
		}
		$unique_othernames = array_unique($unique_othernames);
		return $unique_othernames;
	}
?>
