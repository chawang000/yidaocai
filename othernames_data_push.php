<?php 
	require('req_globals.php');
	require_once 'qcloudapi-sdk-php-master/src/QcloudApi/QcloudApi.php';//腾讯文智sdk同义词api
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
		$mat_list = mysqli_query($con,"SELECT * FROM mats_data");
		$mat_list_worker = mysqli_fetch_all($mat_list);
		$mat_name_list = array();
		foreach ($mat_list_worker as $value) {
			array_push($mat_name_list, $value[1]);
		}


		$index = 1;
		$max_index = 1;
		while($index <= $max_index){
			$mat_boss = mysqli_query($con,"SELECT * FROM mats_data WHERE id = $index ");
			$mat = mysqli_fetch_assoc($mat_boss);
			$mat_other_names = explode(";",$mat["other_names"]);
			// print_r($mat_other_names);

			// $Synonyms = tongyici_wenzhi($mat["name"]);
			$Synonyms = ["肥肠","猪肉","大肉","肉末"];
			if($Synonyms){
				// echo $index . " " . $mat["name"] . ":";
				// print_r($Synonyms);
				foreach($Synonyms as $Synonym){
					if (!in_array($Synonym, $mat_name_list) && !in_array($Synonym, $mat_other_names)){//如果没有在现有数据库的"name"列中
						echo $Synonym . "已添加到" . $mat["name"] . "别名。";
					}else{
						echo $Synonym . "已经存在。";
					}
				}
			}
			

			$index += 1;
		}
		

		function update_othernames($index, $othernames){
			mysql_query("UPDATE mats_data SET other_names = $othernames WHERE id = $index");
		}


		function tongyici_wenzhi($word_ori){
			$config = array('SecretId'        => 'AKID7D4e6ZO4QAnbsluShmfmf09GoJl3YjlG',
			             'SecretKey'       => 'RdkT3XGmusbDSQNLs8JNHxVqyUwI59xV',
			             'RequestMethod'  => 'POST',
			             'DefaultRegion'    => 'gz');

			$wenzhi = QcloudApi::load(QcloudApi::MODULE_WENZHI, $config);

			$package = array("text"=>$word_ori);

			$a = $wenzhi->LexicalSynonym($package);

			if ($a === false) {
			    // $error = $wenzhi->getError();
			    // echo "Error code:" . $error->getCode() . ".\n";
			    // echo "message:" . $error->getMessage() . ".\n";
			    // echo "ext:" . var_export($error->getExt(), true) . ".\n";
			    return nulll;
			} else {
			    // print ($a["syns"][0]["word_syns"][0]["text"]);
			    $Synonyms = $a["syns"][0]["word_syns"];
			    if($Synonyms){
			    	$content = array();
			    	foreach ($Synonyms as $value) {
				    	$Synonym = $value["text"];//所有同义词个体
				    	array_push($content, $Synonym);
				    	// print($Synonym . " ");
				    }
				    return $content;
			    }else{
			    	// echo "无同义词";
			    	return null;
			    }
			    // print_r($content);
			}
		}
		


		?>
    </body>
    
</html>



