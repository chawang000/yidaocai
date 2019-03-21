<?php 
	require('req_globals.php');
	mysqli_query($con, 'set names utf8');

?>

<html>
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="Content-Language" content="zh-CN" /> 
<script type="text/javascript" src="_js/jquery-3.3.1.min.js"></script>
<title>Hello World</title>
    <style>
        body{font-size:45px; color:#000;font-family:Arial,Helvetica,sans-serif;}
        a{color:#039;text-decoration:none;}
    </style> 
	</head>
	<body>
		<input id="file" type="file" accept="image/*;" >
		<script>
			$(document).ready(function(){
				document.getElementById('file').addEventListener('change', function() {
				    var reader = new FileReader();
				    reader.onload = function (e) {
				        compress(this.result);
				    };
				    reader.readAsDataURL(this.files[0]);
				}, false);

				var compress = function (res) {
				    var img = new Image(),
				        maxH = 640;
				    img.onload = function () {
				        var cvs = document.createElement('canvas'),
				            ctx = cvs.getContext('2d');
				        if(img.height > maxH) {
				            img.width *= maxH / img.height;
				            img.height = maxH;
				        }
				        cvs.width = img.width;
				        cvs.height = img.height;
				        ctx.clearRect(0, 0, cvs.width, cvs.height);
				        ctx.drawImage(img, 0, 0, img.width, img.height);
				        var dataUrl = cvs.toDataURL('image/jpeg', 0.8);
				        // dataUrl = encodeURIComponent(dataUrl);
				        // dataUrl = 'img64=' + dataUrl;
				        // $.post('ImgRecog_dish.php',dataUrl,function(data,status){alert("数据: \n" + data + "\n状态: " + status)});
				        // 上传略
				        $.ajax({
				        	type: 'POST',
				            url:"ImgRecog_dish.php",
				            // dataType:"json", 
				            data: {
					        	"img64": dataUrl,
					        },
				            success:function(data)
				            {
				                alert(data);
				            }
				        });
				    }
				    img.src = res;
				}
			});
		</script>
		<?php 

		?>
    </body>
    
</html>



