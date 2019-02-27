<?php



function check($domain){
$watchlist=[
	"mail",
	"remote",
	"secure",
	"vpn",
	"ftp",
	"dev",
	"api",
	"vps",
	
];
	for($i=0;$i<sizeof($watchlist)-1;$i+=1){
		$entry=$watchlist[$i];
		print("[~!~] /^(\*\.)?($entry)/\n");
		$r=preg_match("/^(\*\.)?($entry)/",$domain);
		if($r)
			return true;
		else
			return false;
	}
}



$p=["vpn.ogyei.gov.hu","www.vpn.ogyei.gov.hu","sekkure.d3n.it"];
foreach($p as $e){
	$m=(check($e))?"i need":"garbage";
	print("$e: ".$m."\n");
}
