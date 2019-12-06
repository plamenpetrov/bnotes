

(function($) {
	$.fn.validationEngineLanguage = function() {};
	$.validationEngineLanguage = {
		newLang: function() {
			$.validationEngineLanguage.allRules = 	{"required":{    			// Add your regex rules here, you can take telephone as an example
						"regex":"none",
						"alertText":"* ж­¤з‚єеї…еЎ«ж¬„дЅЌ",
						"alertTextCheckboxMultiple":"* и«‹йЃёж“‡дёЂеЂ‹йЃёй …",
						"alertTextCheckboxe":"* This checkbox is required"},
					"length":{
						"regex":"none",
						"alertText":"* й•·еє¦еї…й €ењЁ ",
						"alertText2":" и‡і ",
						"alertText3": " д№‹й–“"},
					"maxCheckbox":{
						"regex":"none",
						"alertText":"* жњЂе¤љйЃёж“‡ ",
						"alertText2":" й …"},	
					"minCheckbox":{
						"regex":"none",
						"alertText":"* жњЂе°‘йЃёж“‡ ",
						"alertText2":" й …"},	
					"confirm":{
						"regex":"none",
						"alertText":"* е…©ж¬Ўијёе…ҐдёЌдёЂи‡ґпјЊи«‹й‡Ќж–°ијёе…Ґ"},		
					"telephone":{
						"regex":"/^[0-9\-\(\)\ ]+$/",
						"alertText":"* и«‹ијёе…Ґжњ‰ж•€зљ„й›»и©±и™џзўј"},	
					"email":{
						"regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",
						"alertText":"* и«‹ијёе…Ґжњ‰ж•€зљ„йѓµд»¶ењ°еќЂ"},	
					"date":{
                         "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                         "alertText":"* Invalid date, must be in YYYY-MM-DD format"},
					"onlyNumber":{
						"regex":"/^[0-9\ ]+$/",
						"alertText":"* Numbers only"},	
					"noSpecialCaracters":{
						"regex":"/^[0-9a-zA-Z]+$/",
						"alertText":"* и«‹ијёе…Ґи‹±ж–‡е­—жЇЌе’Њж•ёе­—"},	
					"ajaxUser":{
						"file":"validateUser.php",
						//"extraData":"name=eric",
						"alertTextOk":"* ж­¤еёіи™џеЏЇд»ҐдЅїз”Ё",
						"alertTextLoad":"* жЄўжџҐдё­пјЊи«‹зЁЌеѕЊ...",
						"alertText":"* ж­¤еёіи™џе·Іиў«иЁ»е†Љ"},	
					"ajaxName":{
						"file":"validateUser.php",
						"alertText":"* This user is already taken",
						"alertTextOk":"* This user is available",	
						"alertTextLoad":"* Loading, please wait"},		
					"onlyLetter":{
						"regex":"/^[a-zA-Z\ \']+$/",
						"alertText":"* Letters only"},
					"validate2fields":{
    					"nname":"validate2fields",
    					"alertText":"* You must have a firstname and a lastname"},
    				"checkHasAnsA":{
    					"nname":"checkHasAnsA",
    					"alertText":"* и«‹иЁ­е®љйЃ©з•¶ж•ёй‡Џзљ„ж­Јзўєз­”жЎ€"}
					}	
					
		}
	}
})(jQuery);

$(document).ready(function() {	
	$.validationEngineLanguage.newLang()
});