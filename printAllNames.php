<?php 
	require('req_globals.php');
	mysqli_query($con, 'set names utf8');
	header( 'Content-Type:text/html;charset=utf-8;application/json'); 
	// if (!$mat_first) {
	// printf("Error: %s\n", mysqli_error($con));
	// exit();
	// }
?>
<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<title>printNames</title>
    <style>
        body{font-size:45px; color:#000;font-family:Arial,Helvetica,sans-serif;}
        a{color:#039;text-decoration:none;}
    </style> 
	</head>
	<body>
		<?php 
			$ingred_boss = mysqli_query($con,"SELECT * FROM food_nutrition");
			$ingred_list = mysqli_fetch_all($ingred_boss);
			$ingred_name_list = array_column($ingred_list, 2);
			$ingred_othername_list = array_column($ingred_list, 3);
			$nameList = array();
			$characterList = array();

			foreach ($ingred_name_list as $n) {
				array_push($nameList, $n);
			}

			// foreach($ingred_othername_list as $os){
			// 	$onames = explode(';', $os);
			// 	$onames = array_filter($onames);
			// 	if(!empty($onames)){
			// 		foreach($onames as $oname){
			// 			array_push($nameList, $oname);
			// 		}
			// 	}
				
			// }

			$nameList = array_unique($nameList);
			foreach ($nameList as $pname) {

    			preg_match_all("/./u", $pname, $cs);
    			$cs = $cs[0];
    			// print_r($cs[0]);
				// echo $pname[0];
				// $cs = explode('', $pname);
				foreach($cs as $c){
					if(!in_array($c, $characterList)){
						array_push($characterList, $c);
					}
				}
			}

			foreach($characterList as $character){
				echo $character;
			}
			// print_r($characterList);
			// echo 'hello';
		?>
			
 	</body>
</html>
