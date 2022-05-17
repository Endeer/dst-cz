<?php
ini_set("memory_limit", "2G");

$categories = [
	"", // catch-all
	"ACTIONS",
	"BEEFALONAMING",
	"BOARLORD",
	"BUNNYMANNAMES",
	"CARNIVAL",
	"EPITAPHS",
	"GOATMUM",
	"HERMITCRAB",
	"CHARACTER",
	"CHARACTERS.GENERIC",
	"CHARACTERS.WALTER",
	"CHARACTERS.WANDA",
	"CHARACTERS.WARLY",
	"CHARACTERS.WATHGRITHR",
	"CHARACTERS.WAXWELL",
	"CHARACTERS.WEBBER",
	"CHARACTERS.WENDY",
	"CHARACTERS.WICKERBOTTOM",
	"CHARACTERS.WILLOW",
	"CHARACTERS.WINONA",
	"CHARACTERS.WOLFGANG",
	"CHARACTERS.WOODIE",
	"CHARACTERS.WORMWOOD",
	"CHARACTERS.WORTOX",
	"CHARACTERS.WURT",
	"CHARACTERS.WX78",
	"CHARACTERS",
	"KITCOON_NAMING",
	"LAVALUCY",
	"LUCY",
	"MAXWELL",
	"MERM",
	"NAMES",
	"PIG_TALK",
	"PIGNAMES",
	"QUAGMIRE",
	"RABBIT",
	"RECIPE_DESC",
	"SIGNS",
	"SKIN_DESCRIPTIONS",
	"SKIN_NAMES",
	"SKIN_QUOTES",
	"SKIN_TAG_CATEGORIES",
	"STALKER_ATRIUM",
	"STORYTELLER",
	"TABS",
	"UI.ACHIEVEMENTS",
	"UI.BROADCASTING",
	"UI.BUILTINCOMMANDS",
	"UI.CONTROLSSCREEN",
	"UI.COOKBOOK",
	"UI.CRAFTING",
	"UI.CREDITS",
	"UI.CUSTOMIZATIONSCREEN",
	"UI.EMAILSCREEN",
	"UI.EMOTES",
	"UI.FESTIVALEVENTSCREEN",
	"UI.HELP",
	"UI.HUD",
	"UI.LAVAARENA_COMMUNITY_UNLOCKS",
	"UI.LOADING_SCREEN_SURVIVAL_TIPS",
	"UI.LOBBYSCREEN",
	"UI.MAINSCREEN",
	"UI.MODSSCREEN",
	"UI.MORGUESCREEN",
	"UI.MVP_LOADING_WIDGET",
	"UI.NETWORKDISCONNECT",
	"UI.NOTIFICATION",
	"UI.OPTIONS",
	"UI.PAUSEMENU",
	"UI.PLANTREGISTRY",
	"UI.PLAYERSTATUSSCREEN",
	"UI.PLAYERSUMMARYSCREEN",
	"UI.PURCHASEPACKSCREEN",
	"UI.RECIPE_BOOK",
	"UI.SANDBOXMENU",
	"UI.SERVERADMINSCREEN",
	"UI.SERVERCREATIONSCREEN",
	"UI.SERVERLISTINGSCREEN",
	"UI.SKINSSCREEN",
	"UI.TRADESCREEN",
	"UI.WORLDGEN",
	"UI.WXP",
	"UI",
	"WAGSTAFF",
	"YOTB", 
];
usort($categories, function($a, $b) {
	return strlen($b) - strlen($a);
});

$locales = [
	"french.po" => "fr",
	"german.po" => "de",
	"chinese_s.po" => "zh-CN",
	"chinese_t.po" => "zh-TW",
	"italian.po" => "it",
	"japanese.po" => "ja",
	"korean.po" => "ko",
	"polish.po" => "pl",
	"portuguese_br.po" => "pt-BR",
	"russian.po" => "ru",
	"spanish.po" => "es",
	"spanish_mex.po" => "es-MX",
	"strings.pot" => "templates",
];

foreach(scandir("dst-source-files") as $fn) {
	if($fn[0] == ".") continue;
	$locale = $locales[$fn];
	$ext = array_slice(explode(".", $fn), -1)[0];

	$data = file_get_contents("dst-source-files/$fn");
	preg_match_all('/#\. (.*)\r\nmsgctxt "(.*)"\r\nmsgid "(.*)"\r\nmsgstr "(.*)"/', $data, $m, PREG_SET_ORDER);

	$prefixes = [];
	$output = $xoutput = [];
	foreach($m as $mm) {
		if($mm[1] !== $mm[2]) exit("???");
		
		$context = preg_replace('/^STRINGS\./', '', $mm[1]);
		foreach($categories as $category) {
			$split = false;
			if(substr($category, -1) == "-") {
				$category = substr($category, 0, -1);
			}
		
			if(substr($context, 0, strlen($category)) === $category) {
				@$output[$category]++;
				@$xoutput[$category][$context] = [$mm[3], $mm[4]];
				break;
			}
		}
	}
	
	foreach($xoutput as $category => $messages) {
		if($category === '') {
			$category = 'other';
		}
		@mkdir('locales/' . $locale);
		$fn = 'locales/' . $locale . "/" . strToLower(str_replace('.', '-', $category)) . ".$ext";
		file_put_contents($fn, 'msgid ""
msgstr ""
"Application: Dont\' Starve\n"
"POT Version: 2.0\n"

');
	
		foreach($messages as $a => [$b, $c]) {
			file_put_contents($fn, '#. '.$a.'
msgctxt "'.$a.'"
msgid "'.$b.'"
msgstr "'.$c.'"

', FILE_APPEND);
		}
	}
}
