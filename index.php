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
			function https_post($url,$param){
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
		    }//end of https_post

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
		        $data = curl_exec($curl);//运行curl
		        curl_close($curl);
		        return $data;
		    }

			$url = 'https://aip.baidubce.com/oauth/2.0/token';
		    $post_data['grant_type']       = 'client_credentials';
		    $post_data['client_id']      = 'MBMGLqc9So5SnKN4gdw4llj8';
		    $post_data['client_secret'] = 'jxqE8mcYPaTeNZ1CGuF8KinbsBQC2Obb';
		    $o = "";
		    foreach ( $post_data as $k => $v ) 
		    {
		    	$o.= "$k=" . urlencode( $v ). "&" ;
		    }
		    $post_data = substr($o,0,-1);
		    
		    $res = request_post($url, $post_data);
			$token = json_decode($res)->access_token;
			$url = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/dish?access_token=' . $token;
			$img = file_get_contents('_img/example.jpg');
			$img = base64_encode($img);
			$bodys = array(
			    'image' => $img,
			    'sub_lib' => "[appid]",
			    'brief' => 1
			);
			$res = https_post($url, $bodys);
			$dish_name = json_decode($res)->result[0]->name;
			echo $dish_name;//最高可能性菜名，＊＊＊＊应改为可手动选择
			// var_dump($res);//baidu AI finished


			//*************聚合api菜品材料******************

			//配置您申请的appkey
			$appkey = "246b72a3a96733d21f64d25502e85d16";

			//************1.菜谱大全************
			$url = "http://apis.juhe.cn/cook/query.php";
			$params = array(
			      "menu" => $dish_name,//需要查询的菜谱名
			      "key" => $appkey,//应用APPKEY(应用详细页查询)
			      "dtype" => "",//返回数据的格式,xml或json，默认json
			      "pn" => "",//数据返回起始下标
			      "rn" => "1",//数据返回条数，最大30
			      "albums" => "",//albums字段类型，1字符串，默认数组
			);
			$paramstring = http_build_query($params);
			$content = juhecurl($url,$paramstring);
			$result = json_decode($content,true);
			if($result){
			    if($result['error_code']=='0'){
			        echo json_decode($content)->result->data[0]->ingredients;
			    }else{
			        echo $result['error_code'].":".$result['reason'];
			    }
			}else{
			    echo "请求失败";
			}

			function juhecurl($url,$params=false,$ispost=0){
			    $httpInfo = array();
			    $ch = curl_init();
			 
			    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
			    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
			    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
			    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
			    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
			    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			    if( $ispost )
			    {
			        curl_setopt( $ch , CURLOPT_POST , true );
			        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
			        curl_setopt( $ch , CURLOPT_URL , $url );
			    }
			    else
			    {
			        if($params){
			            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
			        }else{
			            curl_setopt( $ch , CURLOPT_URL , $url);
			        }
			    }
			    $response = curl_exec( $ch );
			    if ($response === FALSE) {
			        //echo "cURL Error: " . curl_error($ch);
			        return false;
			    }
			    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
			    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
			    curl_close( $ch );
			    return $response;
			}
			// 聚合api 结束
		?>
    </body>
    
</html>



