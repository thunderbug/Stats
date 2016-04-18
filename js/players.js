var lastOrder ="name";
var sort = "ASC";

var search = "";
var limit = 100;
var page = 1;
var pagenum;

$("#searchbar").keyup( function(){
	var text = $(this).val();
	if(validate(text)){
		search = text;
		loadTable();
	}
});

$("#next").click(next);
$("#prev").click(prev);

$("#name").click(function() {
	changeOrder("name");
	loadTable();
});

$("#kills").click(function() {
	changeOrder("kills");
	loadTable();
});

$("#deaths").click(function() {
	changeOrder("deaths");
	loadTable();
});

$("#suicides").click(function() {
	changeOrder("suicides");
	loadTable();
});

$("#kd_ratio").click(function() {
	changeOrder("kd_ratio");
	loadTable();
});

$("#playtime").click(function() {
	changeOrder("time");
	loadTable();
});

$("#connection").click(function() {
	changeOrder("connections");
	loadTable();
});

$("#last_seen").click(function() {
	changeOrder("last_seen");
	loadTable();
});

$("#score").click(function() {
	changeOrder("score");
	loadTable();
});

$("#page-results").change(function(){
	limit = $(this).val();
	page = 1;
	loadTable();
});

function changeOrder(order){
	lastOrder = order;
	sort = (sort == "ASC") ? "DESC" : "ASC";
}

function loadTable(){
    $.get(
        URL + "index.php?section=" + Game + "&action=plr&order="+lastOrder+"&sort="+sort+"&src="+search+"&lim="+limit+"&page="+page,
        function(data){
			 if (data){
				 pagenum = data['pages'];
				 $('#tabnames').nextAll('tr').remove();
				 
                 var html;

                 for(var i in data){
                	 if (data[i]['name_id'] != null){
                		 if (i % 2 === 0){
                			 html += "<tr class=\"row1\">";
                		 }else{
                			 html += "<tr class=\"row2\">";
                		 }

                		 html += "<td class=\"name\">";
                		 if(data[i]["Flag"] != ""){
                			 html += "<img src=\"" + URL + "/css/images/flags/" + data[i]["Flag"] + ".png\">&nbsp;";
                		 }
                		 html += "<a href=\"" + URL + "game/" + Game + "/action/usr/user/" + data[i]["name_id"] + "\">" + data[i]["name"] + "</a></td>";
                		 html += "<td class=\"kills\">" + data[i]["kills"] + "</td>";
                		 html += "<td class=\"deaths\">" + data[i]["deaths"] + "</td>";
                		 html += "<td class=\"suicides\">" + data[i]["suicides"] + "</td>";
                		 html += "<td class=\"kd_ratio\">" + data[i]["kd_ratio"] + "</td>";
                		 html += "<td class=\"score\">" + data[i]["score"] + "</td>";
                		 html += "<td class=\"time\">" + data[i]["time"] + "</td>";
                		 html += "<td class=\"connections\">" + data[i]["connections"] + "</td>";
                		 html += "<td class=\"last_seen\">" + data[i]["last_seen"] + "</td>";
                		 html += "</tr>";
                	 }
                 } 
				 $("#tabnames").after(html);
				 $("#pagenum").html(data['total']);
			 }
			 else{
				 errorbox("No results found");
			 }
		 }, "json");
}

function next(){
	if (page<pagenum){
		page++;
		loadTable();
	}
}

function prev(){
	if (page>1){
		page--;
		loadTable();
	}
}