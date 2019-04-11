// if (window.devicePixelRatio == 2) {
// 	var metaList = document.getElementsByTagName("meta");
// 	for (var i = 0; i < metaList.length; i++) {
// 	  if (metaList[i].getAttribute("name") == "viewport") {
// 	    metaList[i].setAttribute("content","width=device-width, initial-scale=0.5, maximum-scale=0.5, minimum-scale=0.5, user-scalable=no");
// 	  }
// 	}
// }

// if (window.devicePixelRatio == 3) {
// 	var metaList = document.getElementsByTagName("meta");
// 	for (var i = 0; i < metaList.length; i++) {
// 	  if (metaList[i].getAttribute("name") == "viewport") {
// 	    metaList[i].setAttribute("content","width=device-width, initial-scale=0.3333333333333333, maximum-scale=0.3333333333333333, minimum-scale=0.3333333333333333, user-scalable=no");
// 	  }
// 	}
// }

$(document).ready(function(){
	// console.log('hello');
	$('#searchSection').find('input')[0].addEventListener("focus", inputFocusStyle);
	$('#searchSection').find('input')[0].addEventListener("blur", inputBlurStyle);
	searchTitleLoop();
	cursorLoop();
	$('div.showmore p').click(hiddingtbsIn);
	$('div.showless img').click(hiddingtbsOut);
});


function cursorLoop(){
	setInterval(cLoop,2500);
	function cLoop(){
		$('#myCursor').animate({opacity:'0'},1000,function(){
			// console.log('myCursor loop');
			$('#myCursor').animate({opacity:'1'},500);
		});
	}
	
}

function hiddingtbsIn(){
	// console.log($(this).parents('.foodSection'));
	var parentSection = $(this).parents('.foodSection');
	parentSection.children('div.contentLeft').stop().animate({width:'0%'},100);
	parentSection.children('div.contentRight').stop().animate({width:'100%'},100,function(){
		parentSection.children('div.showmore').stop().animate({height:'0',opacity:'0'},200);
		parentSection.children('div.showless').stop().animate({height:'50px',opacity:'1',marginTop:'30px'},200);
		parentSection.children('div.hiddingtbs').show();
		parentSection.children('div.hiddingtbs').stop().animate({opacity:'1'},200);
	});
}

function hiddingtbsOut(){
	var parentSection = $(this).parents('.foodSection');
	var cScroll = $(window).scrollTop();
	var height1 = parentSection.height();
	// var secBotToWin = parentSection[0].getBoundingClientRect();
	// secBotToWin = secBotToWin.top + height1;
	parentSection.children('div.hiddingtbs').stop().animate({opacity:'0'},0);
	parentSection.children('div.hiddingtbs').hide();
	var height2 = parentSection.height();
	var posTop = cScroll - (height1 - height2);
	window.scrollTo(0,posTop);	
	parentSection.children('div.showmore').stop().animate({height:'50px',opacity:'1'},300);
	parentSection.children('div.showless').stop().animate({height:'0',opacity:'0',marginTop:'0px'},300);
	parentSection.children('div.contentLeft').stop().animate({width:'23%'},200);
	parentSection.children('div.contentRight').stop().animate({width:'77%'},200);
}

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
		// console.log($('.titleContent')[1]);
		var nextContent = $('.titleContent')[1];
		var nextColor = nextContent.getAttribute("bgc");
		$("#myCursor").css({'background-color': nextColor});
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
	$('#myCursor').hide();
}

function inputBlurStyle(){
	if($('#search_ingred').val() == ''){
		searchBoxOut();
		searchUpperDown();
		searchLowerDown();
		$('#content').show();
		$('#floater').show();
		$('#myCursor').show();
	}
}