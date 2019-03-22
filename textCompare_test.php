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
			$url = 'https://aip.baidubce.com/rpc/2.0/nlp/v2/simnet?access_token=' . $token;
			$text_1 = "椒炒肉丝";
			$text_2 = array('青椒炒肉丝','酸萝卜青椒炒肉丝','茭白辣椒炒肉丝','甜椒炒肉丝','尖椒炒肉丝','酸萝卜青椒炒肉丝','茭白辣椒炒肉丝','甜椒炒肉丝','尖椒炒肉丝','酸萝卜青椒炒肉丝','茭白辣椒炒肉丝','甜椒炒肉丝','尖椒炒肉丝','酸萝卜青椒炒肉丝','茭白辣椒炒肉丝','甜椒炒肉丝','尖椒炒肉丝');
			foreach($text_2 as $v){
				$bodys = array(
				    'text_1' => $text_1,
				    'text_2' => $v,
				);
				$bodys = json_encode($bodys);
				$bodys = iconv("UTF-8","gbk//TRANSLIT",$bodys);
				$res = request_post($url, $bodys);
				$res = iconv('GB2312', 'UTF-8',$res);
				echo (json_decode($res)->texts->text_2 . ' : ' . json_decode($res)->score . ' | ');
			}
			// $text_1 = iconv("UTF-8","gbk//TRANSLIT",$text_1);
			// $text_2 = iconv("UTF-8","gbk//TRANSLIT",$text_2);
			

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
