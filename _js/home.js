$(document).ready(function(){
	// console.log('hello');
	$('#searchSection').find('input')[0].addEventListener("focus", inputFocusStyle);
	$('#searchSection').find('input')[0].addEventListener("blur", inputBlurStyle);
	searchTitleLoop();
});


function searchBoxIn(){
	$('.leftBox').css({'width':'15%','transition':'width 0.5s'});
	$('.medBox').css({'width':'70%','transition':'width 0.5s'});
	$('.rightBox').css({'width':'15%','transition':'width 0.5s'});
}

function searchBoxOut(){
	$('.leftBox').css({'width':'20%','transition':'width 0.5s'});
	$('.medBox').css({'width':'80%','transition':'width 0.5s'});
	$('.rightBox').css({'width':'0%','transition':'width 0.5s'});
}

function searchTitleLoop(){
	var moveDis = $('#searchTitle').height();
	setInterval(loop,5000);
	function loop(){
		// $('#titleWrapper').css({'margin-top':'-='+moveDis+'px'});
		$("#titleWrapper").animate({marginTop:"-"+moveDis+"px"},500,function(){
			$('.titleContent').first().appendTo($('#titleWrapper'));
			$('#titleWrapper').css({'margin-top':'0px'});
		});
	}
}

function searchUpperUp(){
	var moveDisUp = $('#searchUpper').height();
	var moveDisLeft = $('#phaseB img').height();
	$('#phaseA').stop().animate({marginTop:'-'+moveDisUp+'px'},100,function(){
		$('#phaseB img').stop().animate({left:'0px'},500);
		$('#textWrapper').stop().animate({opacity:'1'},1000);
	});
}

function searchUpperDown(){
	var moveDisUp = $('#searchUpper').height();
	var moveDisLeft = $('#phaseB img').height();
	$('#phaseB img').stop().animate({left:-moveDisLeft+'px'},300);
	$('#textWrapper').stop().animate({opacity:'0'},300,function(){
		$('#phaseB img').stop().animate({left:moveDisLeft+'px'},0);
		$('#phaseA').stop().animate({marginTop:'0px'},300);
	});
}

function searchLowerUp(){
	var moveDis = $('#searchLower').height();
	$('#lowerWrapper').css({'margin-top':'0px','transition':'1s'});
}

function searchLowerDown(){
	var moveDis = $('#searchLower').height();
	$('#lowerWrapper').css({'margin-top':-moveDis+'px','transition':'1s'});
}

function inputFocusStyle(){
	searchBoxIn();
	searchUpperUp();
	searchLowerUp();
	$('#content').hide();
	$('#floater').hide();
}

function inputBlurStyle(){
	if($('#search_ingred').val() == ''){
		searchBoxOut();
		searchUpperDown();
		searchLowerDown();
		$('#content').show();
		$('#floater').show();
	}
}