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
		$mat_name_list = get_mat_name($con);
		// print_r($mat_name_list);
		$updated_sym = 0;

		update_othernames_wenzhi($con,$mat_name_list,1,0);//第二个数值若小于第一个则不会运行
		update_othernames_ebs($con,$mat_name_list,136,0);//第二个数值若小于第一个则不会运行
		// tongyici_ebs("猪肉");
		echo "新添加别名" . $updated_sym . "个。";




		function update_othernames_ebs($con,$mat_name_list,$index,$max_index){
			$updated_sym = 0;
			while($index <= $max_index){
				$mat_boss = mysqli_query($con,"SELECT * FROM mats_data WHERE id = $index ");
				$mat = mysqli_fetch_assoc($mat_boss);
				$mat_other_names = explode(";",$mat["other_names"]);
				$mat_other_names = array_filter($mat_other_names);
				$mat_name_filters = explode(";",$mat["name_filter"]);
				$mat_name_filters = array_filter($mat_name_filters);
				$Synonyms = tongyici_ebs($mat["name"]);
				preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $Synonyms, $Synonyms);
				$Synonyms = array_filter($Synonyms);
				$Synonyms = $Synonyms[0];
				// print_r($Synonyms[0]);
				// $Synonyms = ["肥肠","猪肉","大肉","肉末"," ","西瓜 ","a b c","大猪肉"];
				if($Synonyms){
					foreach($Synonyms as $Synonym){
						// $Synonym = trim($Synonym);
						$Synonym = preg_replace('/ /', '', $Synonym);//去除所有空格
						if (!in_array($Synonym, $mat_name_list) && !in_array($Synonym, $mat_other_names) && !in_array($Synonym, $mat_name_filters) && $Synonym){//如果没有在现有数据库的"name",没有存在在"other_names"，也没存在在"name_filter"中
							array_push($mat_other_names, $Synonym);//push新的别名到现有别名
							echo $Synonym . "已添加到【" . $mat["name"] . "】";
							global $updated_sym;
							$updated_sym += 1;
						}else{
							// echo $Synonym . "已经存在。";
						}
					}
					
					$othernames_pushlist = implode(";", $mat_other_names);//将更新后的别名转换成mysql表格存储格式
					// print_r($othernames_pushlist);
					update_othernames($con,$index, $othernames_pushlist);
				}
				$index += 1;
			}
		}

		function update_othernames_wenzhi($con,$mat_name_list,$index,$max_index){
			$updated_sym = 0;
			while($index <= $max_index){
				$mat_boss = mysqli_query($con,"SELECT * FROM mats_data WHERE id = $index ");
				$mat = mysqli_fetch_assoc($mat_boss);
				$mat_other_names = explode(";",$mat["other_names"]);
				$mat_other_names = array_filter($mat_other_names);
				$mat_name_filters = explode(";",$mat["name_filter"]);
				$mat_name_filters = array_filter($mat_name_filters);
				$Synonyms = tongyici_wenzhi($mat["name"]);
				// $Synonyms = ["肥肠","猪肉","大肉","肉末"," ","西瓜 ","a b c","大猪肉"];
				if($Synonyms){
					foreach($Synonyms as $Synonym){
						$Synonym = trim($Synonym);
						$Synonym = preg_replace('/ /', '', $Synonym);//去除所有空格
						// preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $Synonym, $matches);
						// $Synonym = join('', $matches[0]);//只提取中文
						if (!in_array($Synonym, $mat_name_list) && !in_array($Synonym, $mat_other_names) && !in_array($Synonym, $mat_name_filters) && $Synonym){//如果没有在现有数据库的"name",没有存在在"other_names"，也没存在在"name_filter"中
							array_push($mat_other_names, $Synonym);//push新的别名到现有别名
							echo $Synonym . "已添加到【" . $mat["name"] . "】";
							global $updated_sym;
							$updated_sym += 1;
						}else{
							// echo $Synonym . "已经存在。";
						}
					}
					
					$othernames_pushlist = implode(";", $mat_other_names);//将更新后的别名转换成mysql表格存储格式
					update_othernames($con,$index, $othernames_pushlist);
				}
				$index += 1;
			}
		}


		function get_mat_name($con){
			$mat_name_list = array();
			$sql = "SELECT * FROM mats_data";
			$mat_list = mysqli_query($con,$sql);
			$mat_list_worker = mysqli_fetch_all($mat_list);
			foreach ($mat_list_worker as $value) {
				array_push($mat_name_list, $value[1]);
			}
			return $mat_name_list;
		}

		function update_othernames($con,$index, $othernames){
			$sql = "UPDATE mats_data SET other_names='".$othernames."' WHERE id = " . $index;
			mysqli_query($con,$sql);
		}

// 同义词 http://ebs.ckcest.cn
		function tongyici_ebs($word_ori,$ispost=0){
				$url = 'http://ebs.ckcest.cn/SynonymWeb/synonymApi';
				$params = array(
					'apikey' => 'Ft8p1GOt',
					'searchEntity' => $word_ori,
				);
				$paramstring = http_build_query($params);
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
			            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$paramstring );
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
			    // var_dump($response);
			    return $response;
			}



// 同义词 WENZHI
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
				    return null;
				} else {
				    $Synonyms = $a["syns"][0]["word_syns"];
				    if($Synonyms){
				    	$content = array();
				    	foreach ($Synonyms as $value) {
					    	$Synonym = $value["text"];//所有同义词个体
					    	array_push($content, $Synonym);
					    }
					    return $content;
				    }else{
				    	return null;
				    }
				}
			}

		?>
    </body>
    
</html>



