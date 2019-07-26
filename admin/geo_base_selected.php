<? define('NO_KEEP_STATISTIC', true);
/**
 * Copyright (c) 26/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$incMod = CModule::IncludeModuleEx("geo.base");
if ($incMod == '0') {
	return false;
} elseif ($incMod == '3') {
	return false;
} else {
	echo ReaspAdminGeoIP::GetCitySelected();
}