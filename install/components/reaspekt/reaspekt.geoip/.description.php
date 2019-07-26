<?
/**
 * Copyright (c) 26/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
        "NAME" => GetMessage("GEOIP_NAME"),
        "DESCRIPTION" => GetMessage("GEOIP_DESC"),
        "ICON" => "/images/icon.gif",
        "CACHE_PATH" => "Y",
        "PATH" => array(
                "ID" => "REASPEKT.RU",
                "NAME" => GetMessage("DESC_SECTION_NAME"),
                "CHILD" => array(
                        "ID" => "serv",
                        "NAME" => GetMessage("GEOIP_SERVICE")
                )
        ),
);

?>
