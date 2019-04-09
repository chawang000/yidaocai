<?php 
	require('req_globals.php');
	header( 'Content-Type:text/html;charset=utf-8;application/json'); 
	mysqli_query($con, 'set names utf8');

	$ingred_boss = mysqli_query($con,"SELECT * FROM food_nutrition");
	$ingred_list = mysqli_fetch_all($ingred_boss);
	$ingred_name_list = array_column($ingred_list, 2);
	$ingred_othername_list = array_column($ingred_list, 3);


	$search_input = $_POST["searchInput"];
	$keys = array();
	foreach($ingred_name_list as $n){
		if(stripos($n,$search_input) !== false){
			$key  = array_keys($ingred_name_list,$n)[0];
			array_push($keys, $key);
			// echo $key .' : ' . $n;
		}
	}

	if( sizeof($keys) < 5){
		foreach($ingred_othername_list as $os){
			$onames = explode(';', $os);
			foreach($onames as $oname){
				if(stripos($oname,$search_input) !== false){
					// echo $oname .' | ';
					$key  = array_keys($ingred_othername_list,$os)[0];
					array_push($keys, $key);
				}
			}
		}
	}

	$keys = array_unique($keys);
	$keys = array_slice($keys, 0,5);//只取出五个有效值

	$json_result = array();
	foreach($keys as $k){
		// $id = $k+1;
		// $data = mysqli_query($con,"SELECT * FROM food_nutrition WHERE id = '$id' ");
		$key_result= array(
			"id"=> $ingred_list[$k][1],
			"name" => $ingred_list[$k][2]
		);
		array_push($json_result, $key_result);
	}
	$json_result = json_encode($json_result);
	echo $json_result;

	// print_r($keys);

	
	
	// echo $search_input;
?>
