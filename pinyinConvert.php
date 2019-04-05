<?php 
	require('req_globals.php');
	header( 'Content-Type:text/html;charset=utf-8;application/json'); 
	mysqli_query($con, 'set names utf8');
 ?>
<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8; application/json" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<script type="text/javascript" src="_js/jquery-3.3.1.min.js"></script>
<title>Hello World</title>
    <style>
        body{font-size:45px; color:#000;font-family:Arial,Helvetica,sans-serif;}
        a{color:#039;text-decoration:none;}
    </style> 
	</head>
	<body>
		<?php 
			get_pinyin($con,1,0);

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

		    function get_pinyin($con,$index,$max_index){
		    	$updated_num = 0;
		    	if($index<1 || $max_index<$index):echo 'invalid index!';return;endif;
		    	$ingred_boss = mysqli_query($con,"SELECT * FROM food_nutrition");
				$ingred_list = mysqli_fetch_all($ingred_boss);
				// print_r($ingred_list[1][5]);
				$ingred_name_expand_list = array();//目标数据库的食物扩展名称，名称里含有小括号的将进行扩展
				foreach ($ingred_list as $value) {
					$ingred_id = $value[0];
					$ingred_name = $value[2];
					$ingred_pinyin = $value[5];
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
							'pinyin'=> $ingred_pinyin,
							// 'add_infront'=> $Pare_c . $name_root,
							// 'add_toback'=> $name_root . $Pare_c,
						);
						array_push($ingred_name_expand_list, $push_expand_name);
				}
		    	while ($index <= $max_index) {
		    		$expand_name = $ingred_name_expand_list[$index-1];
		    		$id = $expand_name['id'];
		    		$fullname = $expand_name['name'];
		    		$nameroot = $expand_name['name_root'];
		    		$name_pare = $expand_name['name_pare_c'];
		    		$name_pinyin = $expand_name['pinyin'];
		    		// echo $fullname;


		    		if(strstr($fullname, '（')){
		    			$url = 'http://hn216.api.yesapi.cn/';
						$bodys = array(
							's'=>'Ext.Pinyin.Convert',
							'app_key' =>'D4683F5BD840E827A2889EAF381C0E8B',
						    'text' => $nameroot,
						);
		    			$p_nameroot = request_post($url, $bodys);
		    			$p_nameroot = json_decode($p_nameroot)->data->pinyin;

		    			$bodys = array(
							's'=>'Ext.Pinyin.Convert',
							'app_key' =>'D4683F5BD840E827A2889EAF381C0E8B',
						    'text' => $name_pare,
						);
		    			$p_name_pare = request_post($url, $bodys);
		    			$p_name_pare = json_decode($p_name_pare)->data->pinyin;
		    			$p_full = $p_nameroot . '_' . $p_name_pare;
		    			$p_full = preg_replace('# #','',$p_full);
		    			// echo '【';
		    			// echo $p_full;
		    			// echo '】';
			    	}elseif(!empty($fullname)){
			    		$url = 'http://hn216.api.yesapi.cn/';
						$bodys = array(
							's'=>'Ext.Pinyin.Convert',
							'app_key' =>'D4683F5BD840E827A2889EAF381C0E8B',
						    'text' => $fullname,
						);
						$p_full = request_post($url, $bodys);
						$p_full = json_decode($p_full)->data->pinyin;
						$p_full = preg_replace('# #','',$p_full);
			    		// echo '【';
			    		// echo $id;
			    		// echo ' ';
		    			// echo $p_full;
		    			// echo '】';
			    	}
			    	if($p_full&&empty($name_pinyin)){
			    		// echo '【';
			    		// echo $id;
			    		// echo ' ';
		    			// echo $p_full;
		    			// echo '】';
			    		$sql = "UPDATE food_nutrition SET pinyin='".$p_full."' WHERE id = " . $index;
			    		mysqli_query($con,$sql);
			    		if(mysqli_affected_rows($con)){
							$updated_num += 1;
						}else{
							echo '【';
				    		echo $id;
				    		echo ' ';
			    			echo $p_full;
			    			echo '】没有添加成功。';
						}
			    		echo mysqli_error($con);
			    	}
					// var_dump($res);
					$index += 1;
				}
				echo '总共添加了';
				echo $updated_num;
				echo '个拼音';
			}
		?>
    </body>
    
</html>



