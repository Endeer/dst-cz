<?php
ini_set("memory_limit", "2G");

function rcut($str) {
	return preg_match('/[._]/', $str)
		? preg_replace('/[._]([^._]+)$/', '', $str)
		: "";
}
function cnt($str) {
	return count(preg_split('/[._]/', $str));
}

$a = file_get_contents("dst-source-files/strings.pot");
preg_match_all('/#\. (.*)\r\nmsgctxt "(.*)"\r\nmsgid "(.*)"\r\nmsgstr "(.*)"/', $a, $m, PREG_SET_ORDER);
$prefixes = [];
foreach($m as $mm) {
	if($mm[1] !== $mm[2]) exit("???");
	
	@$prefixes[rcut(preg_replace('/^STRINGS\./', '', $mm[1]))]++;
}

$cuts = [];
foreach(array_keys($prefixes) as $k) {
	while($k) {
		$k = rcut($k);
		$cuts["$k+"] = true;
	}
}

while(true) {
	$k = null;
	foreach($prefixes as $xk => $count) {
		if(isset($cuts["$xk+"])) continue;
		if($prefixes[$xk] > 25) continue;
		
		if($k !== null) {
			if($prefixes[$k] < $count) continue;
			if(cnt($k) > cnt($xk)) continue;
		}
		
		$k = $xk;
	}
	if(!$k) break;

	$kcut = rcut($k);

	echo count($prefixes) . " $k => $kcut\n";
	@$prefixes[$kcut] += $prefixes[$k];
	unset($prefixes[$k]);
	
	$have = false;
	foreach($prefixes as $kk) {
		if(strlen($kk) > strlen($kcut) && $kcut === substr($kk, 0, strlen($kcut))) {
			$have = true;
		}
	}
	if(!$have) {
		unset($cuts["$kcut+"]);
	}
}
	
print_r($prefixes);
print_r($cuts);

