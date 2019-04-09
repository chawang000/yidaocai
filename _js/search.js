$(document).ready(function(){
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

	$('#searchBox div.rightBox').on({
	    'click': function() {
	    clearText();
	    inputBlurStyle();
	    }
	});

	$('#phaseB img').on({
	    'click': function() {
	    clearText();
	    inputBlurStyle();
	    }
	});

    function input_search(){
    	var search_input = $('#search_ingred').val();
        $('#searchResult').empty();
        if(search_input){
        	$.ajax({
	        	type: 'POST',
	            url:"test_search_bkend.php",
	            data: {"searchInput":search_input},
		        dataType:"json",
		        error: function(xhr) { console.log('ERROR\n'+xhr.responseText); }, 
	            success:function(data)
	            {
	            	$('#searchResult').empty();
	            	if(data.length != 0){
	            		var line = '<div class="line"></div>';
	            		$('#searchResult').append(line);
	            		$.each(data, function(){   
		            		var ingred_id = this.id;
						    var wrapper = document.createElement("div");
						    var imgL = '<div class="leftBoxS"> <img src="_img/icons/cookedfood.svg" class="resultImgL"></div>';
						    var p = '<div class="medBoxS"> <p class="text_regular">'+this.name+'</p></div>';
						    var imgR = '<div class="rightBoxS"> <img src="_img/icons/searchIcon.svg" class="resultImgR"></div>';
						    $(wrapper).addClass('resultWrapper');
						    // p.setAttribute('ingred_id',this.id);
						    // $(p).addClass('text_regular');
						    // p.innerHTML = this.name;
						    wrapper.innerHTML =imgL + p + imgR;
						    if($('#search_ingred').val()){
						    	$('#searchResult').append(wrapper);
						    }else{
						    	$('#searchResult').empty();
						    }
						});
	            	}
	            }
	        });//end of AJAX
        }
    }

    function clearText(){
    	document.getElementById("search_ingred").value="";
    	$('#searchResult').empty();
    }

});





