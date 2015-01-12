<?php

error_reporting(0);

require("functions.php");


$getServer = $_GET["s"];
$getPort = $_GET["p"];

if(!empty($getServer)){
	$server = $getServer;
}else{
	$server = "localhost";
}

if(!empty($getPort)){
	$port = $getPort;
}else{
	$port = "28764";
}

function duration($init){

	$hours = floor($init / 3600);
	$minutes = floor(($init / 60) % 60);
	$seconds = $init % 60;
	$tmp["h"] = $hours;
	$tmp["m"] = $minutes;
	$tmp["s"] = $seconds;
	
	return $tmp;
}

function server_uptime($ip, $port) {
	$s = stream_socket_client("udp://".$ip.":".$port);
	fwrite($s, pack("ccc", 0, 0, 0)); 
	$b = new buf();
	$g = fread($s, 50);
	$x = unpack("C*", $g);
	$b->stack = unpack("C*", $g);
	for ($a = 0; $a <= 4; $a++) 
		$b->getint();
	
	return duration($b->getint());
}

function server_info_simple($ip, $port) {

	$s = stream_socket_client("udp://".$ip.":".$port);
	fwrite($s, chr(0x19).chr(0x01)); 
	$b = new buf();
	$g = fread($s, 4096);
    $b->stack = unpack("C*", $g);
    
    $a = get_info($b);
    
    fclose($s);
    
    return html_escape_array($a);
}

$return = server_info_simple($server, $port);
$return["online"] = server_uptime($server, $port);

echo(json_encode($return));
?>
