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
			$page = 1;
			$url = 'http://api.avatardata.cn/Cook/List';
			$param = array(
				'key' => '2608619e03614de38db42d34281b4810',
				'page' => $page,
				'rows' => '',
				'dtype' => '',
				'format' => ''
			);
			$paramstring = http_build_query($param);
			$content = menu_afd($url,$paramstring);
			var_dump($content);


			function menu_afd($url,$params=false,$ispost=0){
			    $httpInfo = array();
			    $ch = curl_init();
			 
			    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
			    // curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
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
			        echo "cURL Error: " . curl_error($ch);
			        // return false;
			    }
			    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
			    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
			    curl_close( $ch );
			    return $response;
			}
		?>
    </body>
    
</html>



