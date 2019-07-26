<?
/**
 * Copyright (c) 26/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Config\Option;

$module_id = "geo.base";

$incMod = CModule::IncludeModuleEx($module_id);

if ($incMod == '0' || $incMod == '3') {
	ShowError(GetMessage("GEOIP_MODULE_NOT_INSTALLED"));
	return;
}

$arParams["CHANGE_CITY_MANUAL"] = (!isset($arParams["CHANGE_CITY_MANUAL"]) ? 'Y' : $arParams["CHANGE_CITY_MANUAL"]);
$arResult["SET_LOCAL_DB"] = Option::get($module_id, "reaspekt_set_local_sql");

$arResult["GEO_CITY"] = ReaspGeoIP::GetAddr();

$arResult["DEFAULT_CITY_ID"] = array();

$arResult["CHANGE_CITY"] = "N";

if ($arParams["CHANGE_CITY_MANUAL"] == "Y") {
    $arResult["CHANGE_CITY"] = ReaspGeoIP::ChangeCity();
}

$this->IncludeComponentTemplate();