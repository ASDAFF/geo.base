<?
/**
 * Copyright (c) 26/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$statusMod = CModule::IncludeModuleEx("geo.base");

if ($statusMod == '0' || $statusMod == '3')
	return false;
?>
<?if ($_REQUEST['bxsender'] != 'fileman_html_editor') {
    if ($statusMod == '0') {
        ShowError(GetMessage("GEOBASE_MODULE_NOT_FOUND"));
    } elseif ($statusMod == '3') {
        ShowError(GetMessage("GEOBASE_DEMO_EXPIRED"));
    } elseif ($statusMod == '2') {
        ShowNote(GetMessage("GEOBASE_DEMO"));
    }
}


$arComponentParameters = array(
    "GROUPS" => array(
	),
	"PARAMETERS" => Array(
        "CHANGE_CITY_MANUAL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("GEOBASE_CHANGE_CITY_MANUAL"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	), 
);