<?
/**
 * Copyright (c) 26/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */
use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Page\Asset; 

global $DBType;
IncludeModuleLangFile(__FILE__);

$arClassesList = array(
        // main classes
        "ReaspGeoIP"	    => "classes/general/geoip.php",
        "ReaspAdminGeoIP"   => "classes/general/geoip_admin.php",
        // API classes
);

function GetPathLoadClasses($notDocumentRoot = false) {
	if($notDocumentRoot)
		return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
	else
		return dirname(__DIR__);
}

$nameCompany = "reaspekt";
// fix strange update bug
if (method_exists(CModule, "AddAutoloadClasses")) {
	
	$asd = CModule::AddAutoloadClasses(
			$nameCompany.".geobase",
			$arClassesList
	);
} else {
	foreach ($arClassesList as $sClassName => $sClassFile) {
		require_once(GetPathLoadClasses() . "/" . $nameCompany.".geobase/" . $sClassFile);
	}
}



class ReaspGeoBaseLoad {
	
	const MID = "geo.base";
    
    function OnPrologHandler() {
		global $APPLICATION;
		if(IsModuleInstalled(self::MID)) {
			if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true) {
				ReaspGeoBaseLoad::addScriptsOnSite();
                return true;
			}
		}
	}
    
    function addScriptsOnSite() {
		if(ADMIN_SECTION !== true && Option::get(self::MID, "reaspekt_enable_jquery", "Y") == "Y")
			CJSCore::Init(array('jquery'));
        
        Asset::getInstance()->addJs("/bitrix/js/main/core/core.min.js", true);
        Asset::getInstance()->addCss("/local/css/reaspekt/". self::MID ."/style.css", true);
        Asset::getInstance()->addJs("/local/js/reaspekt/". self::MID ."/script.js", true);
    }
}