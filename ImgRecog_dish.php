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
			// $img = file_get_contents('_img/example_1.jpg');
			// $img64 = base64_encode($img);
			$img64 = $_POST['img64'];
			$img64 = str_replace('data:image/jpeg;base64,', '', $img64);
			ImgRecog_dish($img64);




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


		    function ImgRecog_dish($img64){
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
				// $img = file_get_contents('_img/example_9.jpg');
				// $img = base64_encode($img);
				// echo $img;
				$bodys = array(
				    'image' => $img64,
				    'sub_lib' => "[appid]",
				    'brief' => 1
				);
				$res = https_post($url, $bodys);
				var_dump($res);
		    }

			

		?>
    </body>
    
</html>
