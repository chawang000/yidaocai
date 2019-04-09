<?php 
	require('req_globals.php');
	mysqli_query($con, 'set names utf8');

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8; application/json"/> 
<script type="text/javascript" src="_js/jquery-3.3.1.min.js"></script>
<title>search box</title>

<style>
	.search_text{
		color:#777;
		font-size: 0.4em;
	}

	.searchResult p{
		border-bottom: 1px solid #eee;
		margin: 0;
		padding: 1em 0 1em 0;
	}
	/*.searchResult p:last-of-type{
		border-bottom: 1px solid #ccc;
	}*/
	
</style>
</head>
<body>
	<form action="">
		<input id="search_ingred" type="search" value="" placeholder="请输入搜索关键词" />
		<div class="searchResult"></div>
	</form>

<script>
	var flag = false;
	$('#search_ingred').on({
	    'compositionstart': function() {
	      flag = true;
	    },
	    'compositionend': function() {
	      flag = false;
	      if(!flag) {
	        input_search();
	      }
	    },
	    'input propertychange': function() {
	        if(!flag) {
	          input_search();
	        }
	    }
	});


    function input_search(){
    	var search_input = $('#search_ingred').val();
        $('.searchResult').empty();
        if(search_input){
        	$.ajax({
	        	type: 'POST',
	            url:"test_search_bkend.php",
	            data: {"searchInput":search_input},
		        dataType:"json",
		        error: function(xhr) { console.log('ERROR\n'+xhr.responseText); }, 
	            success:function(data)
	            {
	            	if(data.length != 0){
	            		$.each(data, function(){   
		            		var ingred_id = this.id;
						    var p = document.createElement("p");
						    p.setAttribute('ingred_id',this.id);
						    $(p).addClass('search_text');
						    p.innerHTML = this.name;
						    if($('#search_ingred').val()){
						    	$('.searchResult').append(p);
						    }
						});
	            	}
	            }
	        });//end of AJAX
        }
    }
</script>
</body>
</html>



