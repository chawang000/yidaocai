<?php 
	require('req_globals.php');
	header( 'Content-Type:text/html;charset=utf-8;application/json'); 
	mysqli_query($con, 'set names utf8');
 ?>
<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8; application/json" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<script type="text/javascript" src="_js/jquery-3.3.1.min.js"></script>
<title>Hello World</title>
    <style>
        body{font-size:45px; color:#000;font-family:Arial,Helvetica,sans-serif;}
        a{color:#039;text-decoration:none;}
    </style> 
	</head>
	<body>
		<?php 
			add_image($con,1,0);

		    function add_image($con,$index,$max_index){
		    	$updated_num = 0;
		    	if($index<1 || $max_index<$index):echo 'invalid index!';return;endif;
		    	$ingred_boss = mysqli_query($con,"SELECT * FROM food_nutrition");
				$ingred_list = mysqli_fetch_all($ingred_boss);
				$ingred_name_expand_list = array();//目标数据库的食物扩展名称，名称里含有小括号的将进行扩展
				foreach ($ingred_list as $value) {
					$ingred_id = $value[0];
					$ingred_name = $value[2];
					$ingred_pinyin = $value[5];
					$ingred_imgurl = $value[6];
					if(strstr($ingred_name, '（')){
						$Pare = array();
						$preg = '|（(.*)）|U';
						preg_match_all($preg,$ingred_name,$Pare); 
						$Pare_c = $Pare[1][0];//括号中的内容
						$name_root = preg_replace($preg,'',$ingred_name);
					}else{
						$Pare_c = '';
						$name_root = '';
					}
						$push_expand_name = array();
						$push_expand_name = array(
							'id'=>$ingred_id,
							'name'=>$ingred_name,
							'name_root' => $name_root,
							'name_pare_c' => $Pare_c,
							'pinyin'=> $ingred_pinyin,
							'imgUrl'=>$ingred_imgurl,
							// 'add_infront'=> $Pare_c . $name_root,
							// 'add_toback'=> $name_root . $Pare_c,
						);
						array_push($ingred_name_expand_list, $push_expand_name);
				}

		    	while ($index <= $max_index) {
			    	if($index<1 || $max_index<$index):echo 'invalid index!';return;endif;
		    		$expand_name = $ingred_name_expand_list[$index-1];
		    		$id = $expand_name['id'];
		    		$fullname = $expand_name['name'];
		    		$nameroot = $expand_name['name_root'];
		    		$name_pare = $expand_name['name_pare_c'];
		    		$name_pinyin = $expand_name['pinyin'];
		    		$imgUrl = $expand_name['imgUrl'];
		    		// echo $fullname;
		    		// 如果有imgUrl才触发
		    		if(!empty($imgUrl) && !empty($fullname)){
		    			for ($i=0; $i < sizeof($ingred_name_expand_list); $i++) { 
		    				# code...
		    				$T_expand_name = $ingred_name_expand_list[$i];
		    				$T_id = $T_expand_name['id'];
				    		$T_fullname = $T_expand_name['name'];
				    		$T_nameroot = $T_expand_name['name_root'];
				    		$T_name_pare = $T_expand_name['name_pare_c'];
				    		$T_name_pinyin = $T_expand_name['pinyin'];
				    		$T_imgUrl = $T_expand_name['imgUrl'];
				    		if(empty($nameroot)){
				    			if($fullname == $T_nameroot && empty($T_imgUrl)){
				    				echo '【';
				    				echo $id;
				    				echo ' url added to ';
				    				echo $T_id;
				    				echo '】';
				    				expand_imgUrl($id,$T_id,$ingred_name_expand_list);
				    				$updated_num += 1;
				    			}
				    		}else{
				    			if( ($nameroot == $T_nameroot && empty($T_imgUrl)) || ($nameroot == $T_fullname && empty($T_imgUrl))){
				    				echo '【';
				    				echo $id;
				    				echo ' url added to ';
				    				echo $T_id;
				    				echo '】';
				    				expand_imgUrl($id,$T_id,$ingred_name_expand_list);
				    				$updated_num += 1;
				    			}
				    		}
		    			}
		    		}


					$index += 1;
				}
				echo '总共添加了';
				echo $updated_num;
				echo '个图片';
			}


			function expand_imgUrl($id,$T_id,$ingred_name_expand_list){
				require('req_globals.php');
				$key = $id -1;
				$imgUrl = $ingred_name_expand_list[$key]['imgUrl'];
				$sql = "UPDATE food_nutrition SET imgUrl='".$imgUrl."' WHERE id = " . $T_id;
				// echo $sql;
				mysqli_query($con,$sql);
	    		if(mysqli_affected_rows($con)){
					// $updated_num += 1;
				}else{
					echo '【';
		    		echo $id;
	    			echo '】没有添加成功。';
				}
	    		echo mysqli_error($con);
			}
		?>
    </body>
    
</html>



