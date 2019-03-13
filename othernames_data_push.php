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

			// $url = "wenzhi.api.qcloud.com";
			// $params = {
		 //        'Action' => 'LexicalSynonym',
		 //        'Nonce' => 345122,
		 //        'Region' => 'sz',
		 //        'SecretId' => 'AKID7D4e6ZO4QAnbsluShmfmf09GoJl3YjlG',
		 //        'Timestamp' => 1408704141,
		 //        'text'=> '周杰伦结婚'
		 //    }

		 //    $content = tongyici($url,$paramstring)；
		 //    var_dump($content);



		require_once 'qcloudapi-sdk-php-master/src/QcloudApi/QcloudApi.php';

		$config = array('SecretId'        => 'AKID7D4e6ZO4QAnbsluShmfmf09GoJl3YjlG',
		             'SecretKey'       => 'RdkT3XGmusbDSQNLs8JNHxVqyUwI59xV',
		             'RequestMethod'  => 'POST',
		             'DefaultRegion'    => 'gz');

		$wenzhi = QcloudApi::load(QcloudApi::MODULE_WENZHI, $config);

		$package = array("text"=>"蛤蜊");

		$a = $wenzhi->LexicalSynonym($package);

		if ($a === false) {
		    $error = $wenzhi->getError();
		    echo "Error code:" . $error->getCode() . ".\n";
		    echo "message:" . $error->getMessage() . ".\n";
		    echo "ext:" . var_export($error->getExt(), true) . ".\n";
		} else {
		    print ($a["syns"][0]["word_syns"][0]["text"]);
		}

		// echo "\nRequest :" . $wenzhi->getLastRequest();
		// echo "\nResponse :" . $wenzhi->getLastResponse();
		// echo "\n";

			function tongyici($url,$params=false,$ispost=0){
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
			        //echo "cURL Error: " . curl_error($ch);
			        return false;
			    }
			    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
			    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
			    curl_close( $ch );
			    return $response;
			}
		?>
    </body>
    
</html>



