<?php 
	require('req_globals.php');
	require_once 'qcloudapi-sdk-php-master/src/QcloudApi/QcloudApi.php';//腾讯文智sdk同义词api
	mysqli_query($con, 'set names utf8');
?>

<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8;" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<title>Hello World</title>
    <style>
        body{font-size:45px; color:#000;font-family:Arial,Helvetica,sans-serif;}
        a{color:#039;text-decoration:none;}
    </style> 
	</head>
	<body>
		<?php 
			$appkey = 'fe2a7568d9a10323';//你的appkey
			$num = 20;
			$keyword = '白菜';//(utf-8)
			$url = "http://api.jisuapi.com/recipe/search";
			$bodys = array(
				'appkey' => $appkey,
				'keyword' => $keyword,
				'num' => $num
			);

			$res = request_post($url, $bodys);
			var_dump($res);



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

		    

		?>
    </body>
    
</html>
