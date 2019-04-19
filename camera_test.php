<?php 
	require('req_globals.php');
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
	<video id="video"></video>
	<button class="recorderControl">录制</button>
    </body>
    <script>
    	var promise=navigator.mediaDevices.getUserMedia({audio: false,video: { width: 1280, height: 720 }});
    	promise.then(function(stream){
		var video=document.querySelector("video")
		video.src=URL.createObjectURL(stream);
		var recorder=new MediaRecorder(stream);

		var recorderControl = document.querySelector(".recorderControl");
		recorderControl.onclick=function(){
			this.textContent==="录制"?audio.play():audio.pause();
			this.textContent==="录制"?recorder.start():recorder.stop();
			this.textContent=this.textContent==="录制"?"停止":"录制";
		}
		});	

    </script>
</html>



