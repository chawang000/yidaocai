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

		    function add_image($con,$index,$max_index){
		    	$updated_num = 0;
		    	if($index<1 || $max_index<$index):echo 'invalid index!';return;endif;
		    	$ingred_boss = mysqli_query($con,"SELECT * FROM food_nutrition");
				$ingred_list = mysqli_fetch_all($ingred_boss);
				// print_r($ingred_list[1][5]);
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
		    		$expand_name = $ingred_name_expand_list[$index-1];
		    		$id = $expand_name['id'];
		    		$fullname = $expand_name['name'];
		    		$nameroot = $expand_name['name_root'];
		    		$name_pare = $expand_name['name_pare_c'];
		    		$name_pinyin = $expand_name['pinyin'];
		    		$imgUrl = $expand_name['imgUrl'];
		    		// echo $fullname;
		    		if(!empty($imgUrl)):goto endImageLoop;endif;//如果数据库有图片直接跳过
		    		$targetImg = get_webImage($name_pinyin);
		    		print_r($targetImg);
					if(!empty($targetImg) && !empty($fullname)){
						// print_r($targetImg);
						$patern = '/src\=\'(.*)\' alt/iUs';
						preg_match_all($patern,$targetImg, $ImgUrl);
						$ImgUrl = $ImgUrl[1][0];
						// $patern = '/alt\=\"(.*)\"/iUs';
						// preg_match_all($patern,$targetImg, $alt);
						// $alt = $alt[1][0];
						// $img = file_get_contents($ImgUrl);
						saveImage($id,$ImgUrl, '_img/foodpics/');
						$updated_num += 1;
					}else{
						if(!empty($nameroot)){
							$pinyin = get_pinyin($nameroot);
							$targetImg = get_webImage($pinyin);
							if(!empty($targetImg) && !empty($fullname)){
								$patern = '/src\=\'(.*)\' alt/iUs';
								preg_match_all($patern,$targetImg, $ImgUrl);
								$ImgUrl = $ImgUrl[1][0];
								saveImage($id,$ImgUrl, '_img/foodpics/');
								echo '【';
					    		echo $id;
					    		echo ' ';
				    			echo $pinyin;
				    			echo '】图片添加成功。';
								$updated_num += 1;
							}
						}else{
							echo '【';
				    		echo $id;
				    		echo ' ';
			    			echo $name_pinyin;
			    			echo '】没有添加成功。';
						}
					}

					endImageLoop:;
					$index += 1;
				}
				echo '总共添加了';
				echo $updated_num;
				echo '个图片';
			}

			function get_webImage($name_pinyin){
				$url='http://www.boohee.com/shiwu/' . $name_pinyin;
				$html = file_get_contents($url);
				$patern = '/class="lightbox">(.*)<\/a>/iUs';
				preg_match_all($patern, $html, $targetImg);
				$targetImg = $targetImg[1][0];
				return $targetImg;
			}

			function get_pinyin($word){
				$url = 'http://hn216.api.yesapi.cn/';
				$bodys = array(
					's'=>'Ext.Pinyin.Convert',
					'app_key' =>'D4683F5BD840E827A2889EAF381C0E8B',
				    'text' => $word,
				);
				$pinyin = request_post($url, $bodys);
    			$pinyin = json_decode($pinyin)->data->pinyin;
    			$pinyin = preg_replace('# #','',$pinyin);
    			return $pinyin;
			}

			/**
			 * 图片下载方法，提供两种图片保存方式：
			 *     1.按照图片自带的名称保存
			 *     2.按照自定义文件名保存
			 * 其中使用自带的文件名的方式中有两种获取文件名的方式：
			 *     1.如果图片URL中包含文件名，则直接使用图片中的文件名
			 *     2.否则，如果图片的响应头信息中包含文件名信息，使用该文件名
			 * 获取文件扩展名有两种方式：
			 *     1.如果图片的响应头信息中包含图片类型信息，直接使用类型信息作为扩展名
			 *       如：Content-Type: image/jpeg，这时候会使用jpeg作为文件扩展名
			 *     2.如果文件URL地址中包含扩展名，则使用URL中的扩展名
			 *
			 * 使用的时候直接调用saveImage()
			 *
			 * 以下是一个例子，涉及三个参数：
			 * $url       图片地址
			 * $path      图片存储路径
			 * $file_name 图片名称
			 *
			 * 如果不需要指定文件名则可以只传前两个参数：
			 *     saveImage($url, $path);
			 * 如果需要指定文件名则需要三个参数同时传：
			 *     saveImage($url, $path, $file_name);
			 * @author lrx2005123@sina.com
			 */

			/**
			 * 获取图片名称
			 * @param  string $url     图片的地址
			 * @param  string $header  图片的响应头信息
			 * @return string 返回文件名或空
			 */
			function getImgName($url, $header)
			{
			    $image_name = '';

			    /* 从URL中获取文件名 */
			    $tmp_name = getNameFromURL($url);
			    /* URL中不包含文件名 */
			    if (empty ($tmp_name))
			    {
			        $tmp_name = getNameFromHeader ($header);
			    }

			    /* 文件名不为空 */
			    if (!empty ($tmp_name))
			    {
			        /* 但是文件名中不包含扩展名 */
			        if(!strpos ($tmp_name, '.'))
			        {
			            $tmp_ext = getExt ($url, $header);
			            /* 从头信息中获取的文件扩展名不为空 */
			            if (!empty ($tmp_ext))
			            {
			                $image_name = sprintf("%s.%s", $tmp_name, $tmp_ext);
			            }
			        }
			        /* 文件名中包含扩站名 */
			        else
			        {
			            $image_name = $tmp_name;
			        }
			    }
			    /* 头信息中没有文件名 */
			    else
			    {
			        $image_name = '';
			    }

			    return $image_name;
			}

			/**
			 * 获取图片的扩展名，先通过分析响应头信息中的Content-type的信息来确定
			 * 然后通过分析图片的地址URL来获取扩展名
			 * @param  string $url     图片的地址
			 * @param  string $header  图片的响应头信息
			 * @return string 返回扩展名或空
			 */
			function getExt($url, $header)
			{
			    $file_ext = '';
			    $file_ext = getExtFromHeader ($header);
			    if (empty ($file_ext))
			    {
			        $file_ext = getExtFromURL ($url);
			    }

			    return $file_ext;
			}

			/**
			 * 通过分析图片的地址URL来获取扩展名
			 * @param  string $url     图片的地址
			 * @return string 返回扩展名或空
			 */
			function getExtFromURL($url)
			{
			    $name = getNameFromURL ($url);
			    $ext = '';
			    if (!empty ($name) && strpos ($name, '.') !== false)
			    {
			        $ext = substr ($name, strrpos ($name, '.'));
			    }

			    return $ext;
			}

			/**
			 * 通过图片地址URL获取图片名称
			 * @param  string $url  图片地址
			 * @return string 返回文件名或空
			 */
			function getNameFromURL($url)
			{
			    $name = '';
			    /* URL中包含文件名 */
			    if (preg_match ('/\/([^\/]+\.[a-z]{3,4})(\?.*?)?$/i',$url, $matches))
			    {
			        $name = $matches[1] ? trim ($matches[1]) : '';
			    }

			    return $name;
			}

			/**
			 * 通过分析响应头信息中的Content-type的信息获取扩展名
			 * @param  string $header  图片的响应头信息
			 * @return string 返回扩展名或空
			 */
			function getExtFromHeader($header)
			{
			    $file_ext = '';
			    if (preg_match ('/Content-Type: image\/(.*?)\n/', $header, $matches))
			    {
			        $file_ext = $matches[1] ? trim ($matches[1]) : '';
			    }

			    return $file_ext;
			}

			/**
			 * 通过分析图片响应头信息获取图片名称
			 * @param  string $header  图片的响应头信息
			 * @return string 返回文件名或空
			 */
			function getNameFromHeader($header)
			{
			    $file_name = '';
			    if (preg_match('/Content-Disposition:.*?filename="([^"]+)".*?\n/', $header, $matches))
			    {
			        $file_name = $matches[1] ? trim($matches[1]) : '';
			    }

			    return $file_name;
			}
			function saveImage($id,$url, $path, $file_name = ''){
			    $handle = curl_init ($url);
			    /* 显示响应头信息 */
			    curl_setopt ($handle, CURLOPT_HEADER, true);
			    curl_setopt ($handle, CURLOPT_RETURNTRANSFER, 1);
			    $img = curl_exec ($handle);
			    $file_size = curl_getinfo ($handle, CURLINFO_SIZE_DOWNLOAD);
			    $http_code = curl_getinfo ($handle, CURLINFO_HTTP_CODE);
			    curl_close ($handle);
			    list ($header, $body) = explode ("\r\n\r\n", $img, 2);
			    wlog ("http code: $http_code");

			    if ($http_code == 301 || $http_code == 302)
			    {
			        wlog ("[$url]重定向...");
			        $matches = array();
			        if (!preg_match('/(Location:|URI:)(.*?)\n/', $header, $matches))
			        {
			            wlog ('解析头信息失败，结束。');
			            return false;
			        }
			        $redirect_url = trim (array_pop ($matches));
			        $url_parsed = parse_url ($redirect_url);
			        if (isset ($url_parsed))
			        {
			            wlog ("已获取重定向地址[$redirect_url]，\n正在跳转...");
			            return saveImage ($id,$redirect_url, $path, $file_name);
			        }
			        else
			        {
			            wlog ('获取重定向地址失败，结束。');
			            return false;
			        }
			    }
			    elseif ($http_code == 200)
			    {
			        wlog ('请求成功...');
			    }
			    else
			    {
			        wlog ('无效的请求，结束。');
			        return false;
			    }

			    if (!empty ($file_name))
			    {
			        $file_ext = getExt ($url, $header);
			        if (empty ($file_ext))
			        {
			            wlog ('无效的图片地址！');
			            return false;
			        }

			        $image_name = sprintf ("%s.%s", $file_name, trim($file_ext));
			    }
			    else
			    {
			        $image_name = getImgName ($url, $header);

			        if (empty($image_name))
			        {
			            wlog ('无效的图片地址！');
			            return false;
			        }
			    }

			    if (!file_exists ($path))
			    {
			        wlog ("目录$path不存在，正在创建...");
			        if (mkdir ($path))
			        {
			            wlog ('目录创建成功...');
			        }
			        else
			        {
			            wlog ('目录创建失败，结束。');
			            return false;
			        }
			    }

			    $file_path = rtrim ($path, '/') . '/' . $image_name;
			    $fp = fopen ($file_path, 'w');
			    $length = fwrite ($fp, $body);
			    fclose ($fp);

			    if ($length)
			    {
			        wlog ("文件保存成功！\n大小: $length\n位置: $file_path");
			        filePathToDB($id,$file_path);
			    }
			    else
			    {
			        wlog ('文件保存失败。');
			        return false;
			    }

			    return true;
			}
			function wlog ($msg, $path = ''){
			    if (empty ($path))
			    {
			        $path = 'log/save_img.log';
			    }

			    if (!file_exists (dirname ($path)))
			    {
			        if (!mkdir (dirname ($path)))
			        {
			            die('can not create directory' . dirname ($path));
			        }
			    }

			    $fp = @fopen ($path, 'a');
			    flock ($fp, LOCK_EX);
			    fwrite ($fp, $msg . "\n");
			    flock ($fp, LOCK_UN);
			    fclose ($fp);
			}

			function filePathToDB($id,$filepath){
				require('req_globals.php');
				$sql = "UPDATE food_nutrition SET imgUrl='".$filepath."' WHERE id = " . $id;
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



