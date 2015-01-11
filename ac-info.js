//(c) by tkon99
//AC-Info

function ACinfo(server, port, callback){
	var url = "api/serverinfo.php?s="+server+"&p="+port;
	$.getJSON(url, function(data){
		callback(data);
	});
}