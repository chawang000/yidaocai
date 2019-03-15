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
			$index = 1001;
			$max_index = 1766;
			$data_position = [116,308,500,692,884,1076,1268,1460,1652,1844,2036,2228,2420,2612,2804,2996];
			$ori_id_list = array();
			$food_list = mysqli_query($con,"SELECT * FROM food_nutrition");
			$food_list = mysqli_fetch_all($food_list);
			foreach ($food_list as $value) {
				array_push($ori_id_list, $value[1]);
			}

			web_mat_name($index,$max_index,$data_position,$con,$ori_id_list);
			// print_r($web_other_name_list);

			function web_mat_name($index,$max_index,$data_position,$con,$ori_id_list){
				// mysqli_query($con,"SELECT * FROM food_nutrition WHERE ori_id = '$index'");
				if(in_array($index, $ori_id_list)){
					echo $index . " 该条已经添加到数据库。";
					return;
				}

				while($index <= $max_index){
					global $index;
					$url='http://www.foodwake.com/food/' . $index;  
					$html = file_get_contents($url);  

					// [1][0] 【基本营养】
					// 能量 [116], 蛋白质 [308], 脂肪 [500], 碳水化合物 [692], 粗纤维 [884]
					// 合计 5
					$genra_index = 0;
					$count = 0;
					$total_num = 5;
					$basic_switch = "ON";
					$nutri_basic_list = array();
					$nutri_basic_name = array('能量','蛋白质','脂肪','碳水化合物','粗纤维');
					$nutri_basic_unit = array('千卡','克','克','克','克');
					$patern = '/<tbody>(.*)<\/tbody>/iUs';
					preg_match_all($patern, $html, $matches);
					$raw_content = strip_tags($matches[1][$genra_index]);
					$array_content = explode(' ', $raw_content);
					$array_content = array_filter($array_content);
					while($count < $total_num && $basic_switch == "ON"){
						$new_array = array(
							$nutri_basic_name[$count] => $array_content[$data_position[$count]] . $nutri_basic_unit[$count]
						);
						$nutri_basic_list = array_merge($nutri_basic_list, $new_array);
						$count += 1;
					}

					// [1][1] 【脂类】
					// 单不饱和脂肪酸 [116], 多不饱和脂肪酸 [308], 多不饱和脂肪酸占总脂肪酸的比例 [500], 反式脂肪酸 [692], 反式脂肪酸占总脂肪酸的比例 [884], 胆固醇 [1076], 植物固醇 [1268], 胡萝卜素 [1460], 叶黄素类 [1652], 番茄红素 [1844]
					// 合计 10
					$genra_index = 1;
					$count = 0;
					$total_num = 10;
					$basic_switch = "ON";
					$nutri_fat_list = array();
					$nutri_fat_name = array('单不饱和脂肪酸','多不饱和脂肪酸','多不饱和脂肪酸占总脂肪酸的比例','反式脂肪酸','反式脂肪酸占总脂肪酸的比例','胆固醇','植物固醇','胡萝卜素','叶黄素类','番茄红素');
					$nutri_fat_unit = array('克','克','%','克','%','毫克','毫克','微克','微克','微克');
					$patern = '/<tbody>(.*)<\/tbody>/iUs';
					preg_match_all($patern, $html, $matches);
					$raw_content = strip_tags($matches[1][$genra_index]);
					$array_content = explode(' ', $raw_content);
					$array_content = array_filter($array_content);
					while($count < $total_num && $basic_switch == "ON"){
						$new_array = array(
							$nutri_fat_name[$count] => $array_content[$data_position[$count]] . $nutri_fat_unit[$count]
						);
						$nutri_fat_list = array_merge($nutri_fat_list, $new_array);
						$count += 1;
					}

					// [1][2] 【矿物质】
					// 钙 [116], 镁 [308], 钠 [500], 钾 [692], 磷 [884], 硫 [1076], 氯 [1268], 铁 [1460], 碘 [1652], 锌 [1844], 硒 [2036], 铜 [2228], 锰 [2420], 氟 [2612]
					// 合计 14
					$genra_index = 2;
					$count = 0;
					$total_num = 14;
					$basic_switch = "ON";
					$nutri_mineral_list = array();
					$nutri_mineral_name = array('钙','镁','钠','钾','磷','硫','氯','铁','碘','锌','硒','铜','锰','氟');
					$nutri_mineral_unit = array('毫克','毫克','毫克','毫克','毫克','毫克','毫克','毫克','微克','毫克','微克','毫克','毫克','微克');
					$patern = '/<tbody>(.*)<\/tbody>/iUs';
					preg_match_all($patern, $html, $matches);
					$raw_content = strip_tags($matches[1][$genra_index]);
					$array_content = explode(' ', $raw_content);
					$array_content = array_filter($array_content);
					while($count < $total_num && $basic_switch == "ON"){
						$new_array = array(
							$nutri_mineral_name[$count] => $array_content[$data_position[$count]] . $nutri_mineral_unit[$count]
						);
						$nutri_mineral_list = array_merge($nutri_mineral_list, $new_array);
						$count += 1;
					}

					// [1][3] 【维生素】
					// 维生素A [116], 维生素C [308], 维生素D [500], 维生素E [692], 维生素K [884], 维生素P（类黄酮） [1076], 维生素B1（硫胺素） [1268], 维生素B2（核黄素） [1460], 维生素B3（烟酸） [1652], 维生素B4（胆碱） [1844], 维生素B5（泛酸） [2036], 维生素B6 [2228], 维生素B7（生物素） [2420], 维生素B9（叶酸） [2612], 维生素B12 [2804], 维生素B14（甜菜碱） [2996]
					// 合计 16
					$genra_index = 3;
					$count = 0;
					$total_num = 16;
					$basic_switch = "ON";
					$nutri_vitam_list = array();
					$nutri_vitam_name = array('维生素A','维生素C','维生素D','维生素E','维生素K','维生素P（类黄酮）','维生素B1（硫胺素）','维生素B2（核黄素）','维生素B3（烟酸）','维生素B4（胆碱）','维生素B5（泛酸）','维生素B6','维生素B7（生物素）','维生素B9（叶酸）','维生素B12','维生素B14（甜菜碱）');
					$nutri_vitam_unit = array('微克','毫克','微克','毫克','微克','毫克','毫克','毫克','毫克','毫克','毫克','毫克','微克','微克','微克','毫克');

					$patern = '/<tbody>(.*)<\/tbody>/iUs';
					preg_match_all($patern, $html, $matches);
					$raw_content = strip_tags($matches[1][$genra_index]);
					$array_content = explode(' ', $raw_content);
					$array_content = array_filter($array_content);
					while($count < $total_num && $basic_switch == "ON"){
						$new_array = array(
							$nutri_vitam_name[$count] => $array_content[$data_position[$count]] . $nutri_vitam_unit[$count]
						);
						$nutri_vitam_list = array_merge($nutri_vitam_list, $new_array);
						$count += 1;
					}

					// [1][4] 【氨基酸】
					// 亮氨酸 [116], 蛋氨酸 [308], 苏氨酸 [500], 赖氨酸 [692], 色氨酸 [884], 缬氨酸 [1076], 组氨酸 [1268], 异亮氨酸 [1460], 苯丙氨酸 [1652]
					// 合计 9
					$genra_index = 4;
					$count = 0;
					$total_num = 9;
					$basic_switch = "ON";
					$nutri_amino_list = array();
					$nutri_amino_name = array('亮氨酸','蛋氨酸','苏氨酸','赖氨酸','色氨酸','缬氨酸','组氨酸','异亮氨酸','苯丙氨酸');
					$nutri_amino_unit = array('毫克','毫克','毫克','毫克','毫克','毫克','毫克','毫克','毫克');

					$patern = '/<tbody>(.*)<\/tbody>/iUs';
					preg_match_all($patern, $html, $matches);
					$raw_content = strip_tags($matches[1][$genra_index]);
					$array_content = explode(' ', $raw_content);
					$array_content = array_filter($array_content);
					while($count < $total_num && $basic_switch == "ON"){
						$new_array = array(
							$nutri_amino_name[$count] => $array_content[$data_position[$count]] . $nutri_amino_unit[$count]
						);
						$nutri_amino_list = array_merge($nutri_amino_list, $new_array);
						$count += 1;
					}
					// print_r($nutri_vitam_list);
					// print_r($nutri_amino_list);
					
					
					//食材名称
					$patern = '/<h1 class="color-yellow">(.*)<\/h1>/iUs';
					preg_match_all($patern, $html, $matches);
					$web_name = $matches[1][0];
					//食材别名
					$patern = '/<h2 class="h3 text-light">别名：(.*)<\/h2>/iUs';
					preg_match_all($patern, $html, $matches);
					if($matches[1][0] && $matches[1][0] != '无'){
						$web_other_name = $matches[1][0];
					}else{
						$web_other_name = '';
					}
					// $web_other_name = explode('，', $web_other_name);
					// print_r($web_other_name);


					

					$nutri_basic_list=serialize($nutri_basic_list);
					$nutri_fat_list=serialize($nutri_fat_list);
					$nutri_mineral_list=serialize($nutri_mineral_list);
					$nutri_vitam_list=serialize($nutri_vitam_list);
					$nutri_amino_list=serialize($nutri_amino_list);

					// print_r($nutri_vitam_list);
					// print_r($nutri_amino_list);

					// var_dump($web_name);
					// var_dump($web_other_name);
					// var_dump($nutri_basic_list);
					// var_dump($nutri_fat_list);
					// var_dump($nutri_mineral_list);
					// var_dump($nutri_vitam_list);
					// var_dump($nutri_amino_list);



					// $sql = "INSERT INTO food_nutrition (id, ori_id, name, other_names, name_filter, nutri_basic, nutri_fat, nutri_mineral, nutri_vitam, nutri_amino) VALUES ( " . $index .",". $index .",". $web_name . ",". $web_other_name . ", '', ". $nutri_basic_list .",". $nutri_fat_list .",". $nutri_mineral_list .",". $nutri_vitam_list .",". $nutri_amino_list .")";
					$sql = "INSERT INTO food_nutrition (ori_id, name, other_names, nutri_basic, nutri_fat, nutri_mineral, nutri_vitam, nutri_amino) VALUES ( '$index', '$web_name', '$web_other_name', '$nutri_basic_list', '$nutri_fat_list', '$nutri_mineral_list', '$nutri_vitam_list', '$nutri_amino_list')";

					mysqli_query($con,$sql);
					echo mysqli_error($con);
					$index += 1;
				}
			}

			echo "ENDED AT: " . $index;
			// $web_name_list = array();
			// $web_other_name_list = array();
		?>
    </body>
    
</html>



