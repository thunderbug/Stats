var donut;
var hitchart;
    
$(document).ready(function(){
	ratioGraph();
	hitGraph();
	var ctx = $("#chart-area").get(0).getContext("2d");
	hitchart = new Chart(ctx).Doughnut(donut, {
		responsive : true,
		segmentShowStroke: true,
		segmentStrokeColor: "#EEE",
		segmentStrokeWidth : 2,
		animationEasing: "easeInOutExpo",
		animationSteps: 60
	});
});

function ratioGraph(){
	var tot = k+d;
	var k_ratio = (100*k)/tot;
	var d_ratio = 100-k_ratio;
	
	$('#k').width(k_ratio+"%");
	$('#d').width(d_ratio+"%");
}

function hitGraph(){
    $.get(
    		URL + "index.php?section=" + Game + "&action=graph&user="+user,
    		function(data, status){
    			$("#hits_list").empty();
    			$.each(data, function(key, value){
    				var name = hitName(key);
    				hitchart.addData({
    				    value: value,
    				    color: hitColor(key),
    				    highlight: "#FFF",
    				    label: name
    				});
    				$("#hits_list").append("<dt>"+name+"</dt><dd>"+value+"</dd>");
    			});
    		}
    	, "json");
}

function hitColor(name){
	switch (name){
		case "head": 		return "#FF8080"; break;
		case "torso":		return "#FFEE99"; break;
		case "left_arm": 	return "#99CC99"; break;
		case "right_arm": 	return "#66CC99"; break;
		case "left_leg": 	return "#66CCFF"; break;
		case "right_leg": 	return "#66AAFF"; break;
		default:			return "#202020"; break;
	}
}

function hitName(name){
	var parts = name.split("_");
	var fullname = "";
	for (var i=0; i<parts.length; i++){
		fullname += parts[i]+" ";
	}
	return fullname.charAt(0).toUpperCase() + fullname.slice(1);
}

$("#ForumURL").click(function(){
    $(this).select();
});

$("#htmlURL").click(function(){
    $(this).select();
});