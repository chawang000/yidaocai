<?php 
	require('req_globals.php');
	// mysql_query('set names utf8')//面向过程， 编程方式；
	// mysqli_set_charset ($link,'utf8')//面向对象 ，编程方式；
	// mysqli::set_charset('utf8')//面向对象，编程方式；
	mysqli_query($con, 'set names utf8');
	$mat_boss = mysqli_query($con,"SELECT * FROM mats_data WHERE id = 1 ");
	$mat_first = mysqli_fetch_assoc($mat_boss);
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

		echo $mat_first["name"];




		// require_once 'qcloudapi-sdk-php-master/src/QcloudApi/QcloudApi.php';
		// $config = array('SecretId'        => 'AKID7D4e6ZO4QAnbsluShmfmf09GoJl3YjlG',
		//              'SecretKey'       => 'RdkT3XGmusbDSQNLs8JNHxVqyUwI59xV',
		//              'RequestMethod'  => 'POST',
		//              'DefaultRegion'    => 'gz');

		// $wenzhi = QcloudApi::load(QcloudApi::MODULE_WENZHI, $config);

		// $package = array("text"=>"好像");

		// $a = $wenzhi->LexicalSynonym($package);

		// if ($a === false) {
		//     $error = $wenzhi->getError();
		//     echo "Error code:" . $error->getCode() . ".\n";
		//     echo "message:" . $error->getMessage() . ".\n";
		//     echo "ext:" . var_export($error->getExt(), true) . ".\n";
		// } else {
		//     // print ($a["syns"][0]["word_syns"][0]["text"]);
		//     $Synonyms = $a["syns"][0]["word_syns"];
		//     foreach ($Synonyms as $value) {
		//     	$Synonym = $value["text"];//所有同义词个体
		//     	// print($Synonym);
		//     }
		// }


		?>
    </body>
    
</html>



