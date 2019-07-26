<?
/**
 * Copyright (c) 26/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

CUtil::InitJSCore(array("jquery", "window"));

$module_id = "geo.base";
$reaspekt_city_manual_default = Option::get($module_id, "reaspekt_city_manual_default");

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "S") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$use_source = array(
	"not_using" => Loc::getMessage("GEOBASE_NOT_USING"),
	"local_db" => Loc::getMessage("GEOBASE_LOCAL_DB"),
);

global $DB;

$arCityOption = array();

function ShowParamsHTMLByArray($arParams)
{
	foreach ($arParams as $Option)
	{
		__AdmSettingsDrawRow("geo.base", $Option);
	}
}


$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('GEOBASE_TAB_SETTINGS')
    ),
	array(
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("GEOBASE_TAB_CITY_NAME")
    ),
	array(
		"DIV"	=> "edit3",
		"TAB"	=> Loc::getMessage("GEOBASE_TAB_UPDATE_BD"),
		"TITLE" => Loc::getMessage("TAB_TITLE_DATA")
	),
    array(
        "DIV" => "edit4",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    ),
);

$arAllOptions = array(
	"edit1" => array(
        array(
            'reaspekt_set_local_sql', 
            Loc::getMessage('GEOBASE_FIELD_SET_SQL'),
            'local_db',
            array('selectbox',$use_source)
        ),
		array(
            'reaspekt_enable_jquery', 
            Loc::getMessage('GEOBASE_JQUERY'),
            'Y',
            array('checkbox')
        ),
	),
    "edit2" => $arCityOption,
    "edit3" => array(
		array("reaspekt_set_timeout", Loc::getMessage("GEOBASE_SET_TIMEOUT"), 3, array("text"))
	),
);


$reaspekt_set_local_sql = (($request->isPost() && check_bitrix_sessid()) ? $request->getPost("reaspekt_set_local_sql") : Option::get($module_id, "reaspekt_set_local_sql"));


if ($reaspekt_set_local_sql != "local_db") {
    $arAllOptions["edit1"][] = Loc::getMessage("GEOBASE_ELIB_TITLE");
    $arAllOptions["edit1"][] = array("reaspekt_elib_site_code", Loc::getMessage("GEOBASE_CODE_FOR"), "", array("text"));
    $arAllOptions["edit1"][] = Loc::getMessage("GEOBASE_DESC_CODE_ELIB");
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);


if (
	$request->isPost() 
	&& strlen($Update.$Apply.$RestoreDefaults) > 0 
	&& check_bitrix_sessid()
) {
    
    if(strlen($RestoreDefaults) > 0) {
		Option::delete("geo.base");
	} else {
		foreach ($aTabs as $aTab) {
            
            foreach ($arAllOptions[$aTab["DIV"]] as $arOption) {
                
                if (!is_array($arOption)) 
                    continue;

                if ($arOption['note']) 
                    continue;

                $optionName = $arOption[0];

                $optionValue = $request->getPost($optionName);

                Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue):$optionValue);
            }
        }
	}
    
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0) {
		LocalRedirect($_REQUEST["back_url_settings"]);
	} else {
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
    }
}

?>

<style>
.reaspekt_option-main-box{
	display: none;
	padding-bottom: 10px;
	margin-bottom: 5px;
	position: relative;
	width: 100%;
}
.reaspekt_option-main-box span{
	display: inline-block;
}
.reaspekt_option-main-box > div{
	margin: 10px 0 5px 0;
	text-align: right;
}
.reaspekt_option-progress-bar{
	width: 100%;
	height: 15px
}
.reaspekt_option-progress-bar span{
	position: absolute;
}
.reaspekt_option-progress-bar > span{
	border: 1px solid silver;
	width: 95%;
	left: 2px;
	height: 15px;
	text-align: left;
}
.reaspekt_option-progress-bar > span + span{
	border: none;
	width: 4%;
	height: 15px;
	left: auto;
	right: 2px;
	text-align: right
}
#progress{
	height: 15px;
	background: #637f9c;
}
#progress_MM{
	height: 15px;
	background: #637f9c;
}
.geo_base_light{
	color: #3377EE;
}
#geo_base_info{
	display: none;
	margin-bottom: 15px;
	margin-top: 1px;
	width: 75%;
}
#geo_base_info option{
	padding: 3px 6px;
}
#geo_base_info option:hover{
	background-color: #D6D6D6;
}
td #geo_base_btn{
	margin: 10px 0px 80px;
}
#reaspekt_description_full{
	display: none;
	transition: height 250ms;
}
#reaspekt_description_close_btn{
	display: none;
}
.reaspekt_description_open_text{
	border-bottom: 1px solid;
	color: #2276cc !important;
	cursor: pointer;
	transition: color 0.3s linear 0s;
}
.reaspekt_gb_uf_edit{
	background-color: #d7e3e7;
	background: -moz-linear-gradient(center bottom , #d7e3e7, #fff);
	background-image: url("/bitrix/images/geo.base/correct.gif");
	background-position: right 20px center;
	background-repeat: no-repeat;
	color: #3f4b54;
	display: inline-block;
	font-size: 13px;
	margin: 2px;
	outline: medium none;
	vertical-align: middle;
	border: medium none;
	border-radius: 4px;
	box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 1px rgba(0, 0, 0, 0.3), 0 1px 0 #fff inset, 0 0 1px rgba(255, 255, 255, 0.5) inset;
	cursor: pointer;
	font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
	font-weight: bold;
	position: relative;
	text-decoration: none;
	text-shadow: 0 1px rgba(255, 255, 255, 0.7);
	padding: 1px 13px 3px;
}
.reaspekt_gb_uf_edit:hover{
	background: #f3f6f7 -moz-linear-gradient(center top , #f8f8f9, #f2f6f8) repeat scroll 0 0;
	background-image: url("/bitrix/images/geo.base/correct.gif");
	background-position: right 20px center;
	background-repeat: no-repeat;
}
#geo_base_table_header td{
    text-align: left !important;
}
</style>

<script language="JavaScript">
$(document).ready(function(){
	$('#reaspektOptionManualUpdate').html("<?=Loc::getMessage("CHECK_UPDATES")?>");

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/geo_base_file_check.php",
		timeout: 10000,
		success: function(data){
			if(data == ''){
				$('#reaspektOptionManualUpdate').hide();
				$('#reaspektOptionUpdateUI').show();
				return;
			}
			objData = JSON.parse(data);
			if(objData.IPGEOBASE == 1){
				BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', {'action':'UPDATE'}, obHandler);
			} else {
				document.getElementById('reaspektOptionNotices').innerHTML = "<?=Loc::getMessage("GEOBASE_URL_NOT_FOUND")?>";
				$('#reaspektOptionManualUpdate').hide();
				$('#reaspektOptionUpdateUI').hide();
			}
		}
	});
});

var timer, obData, updateMode;
obHandler = function (data) {
	var progress, value, title, send, notices, loader;
	updateMode = false;
	obData = JSON.parse(data);
	
    progress = document.getElementById('progress');
    value = document.getElementById('value');
    title = document.getElementById('title');
    notices = document.getElementById('reaspektOptionNotices');
    loader = document.getElementById('reaspektOptionLoaderUI');
    	
    if (obData.STATUS == 3) {
		send = {
			"action": obData.NEXT_STEP,
			"timeout": document.getElementsByName('reaspekt_set_timeout')[0].value
		};
        
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if(typeof obData.FILENAME != 'undefined')
			title.innerHTML = "<?=Loc::getMessage("TITLE_LOAD_FILE")?> " + obData.FILENAME;
		BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', send, obHandler);
	
    } else if (obData.STATUS == 2) {
		send = {
			"action": obData.NEXT_STEP,
			"by_step": "Y",
			"filename": obData.FILENAME,
			"seek": obData.SEEK,
			"timeout": document.getElementsByName('reaspekt_set_timeout')[0].value,
            "update_db": "Y"
		};
        
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		
        if (obData.PROGRESS == 100) {
			timer = setInterval(function (){
				title.innerHTML = "<?=Loc::getMessage("TITLE_UNPACK_FILE")?> " + (typeof obData.FILENAME != 'undefined' ? obData.FILENAME : '');
				progress.style.width = 0 + '%';
				value.innerHTML = 0 + '%';
				BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', send, obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', send, obHandler);
			if(typeof obData.FILENAME != 'undefined')
				title.innerHTML = "<?=Loc::getMessage("TITLE_LOAD_FILE")?> " + obData.FILENAME;
		}
	}
	else if (obData.STATUS == 1) {
		send = {
			"action"	: obData.NEXT_STEP,
			"filename"	: obData.FILENAME,
			"seek"		: obData.SEEK ? obData.SEEK : 0,
			"drop_t"	: obData.DROP_T,
			"timeout"	: document.getElementsByName('reaspekt_set_timeout')[0].value
		};
        
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		
        if (obData.PROGRESS == 100) {
			timer = setInterval(function (){
				title.innerHTML = "<?=Loc::getMessage("TITLE_DB_UPDATE")?>";
				progress.style.width = 0 + '%';
				value.innerHTML		 = 0 + '%';
				BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', send, obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', send, obHandler);
		}
	}
	else if (obData.STATUS == 0) {
		loader.style.display = 'none';
		notices.innerHTML = "<?=Loc::getMessage("NOTICE_DBUPDATE_SUCCESSFUL")?>";

		notices.style.display = 'block';
	}
    
	if (obData.UPDATE == "Y") {
		if (!updateMode) {
			document.getElementById('reaspektOptionUpdateUI').style.display	 = 'block';
			document.getElementById('reaspektOptionManualUpdate').style.display = 'none';
		}
	} else if (obData.UPDATE == "N") {
		notices.innerHTML = "<?=Loc::getMessage("NOTICE_UPDATE_NOT_AVAILABLE")?>";
        
		if(!$('#dbupdater').is(':visible'))
			document.getElementsByName('reaspekt_set_timeout')[0].readOnly = true;
		else
			document.getElementsByName('reaspekt_set_timeout')[0].readOnly = false;
	}
};

function updateDB(dst) {
	document.getElementsByName('reaspekt_set_timeout')[0].readOnly = true;
	
    document.getElementById('reaspektOptionNotices').style.display = 'none';
    document.getElementById('reaspektOptionLoaderUI').style.display = 'block';
    BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php',
        {'action': 'LOAD', "timeout": document.getElementsByName('reaspekt_set_timeout')[0].value}, obHandler);
}

var geo_base = new Object();
geo_base = {'letters':'', 'timer':'0'};

function geo_base_delete_click(cityid) {
	var id = '';
	if(typeof cityid !== 'undefined')
		id = cityid;
	else
		return false;

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/geo_base_selected.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
				'entry_id': id,
				'delete_city': 'Y'},
		timeout: 10000,
		success: function(data){
			geo_base_update_table();
		}
	});
}

function geo_base_update_table() {
	$.ajax({
		type: "POST",
		url: "/bitrix/admin/geo_base_selected.php",
		dataType: 'html',
		data: { 'sessid': BX.message('bitrix_sessid'),
			'update': 'Y'},
		timeout: 10000,
		success: function(data){
			$('#geo_base_cities_table .geo_base_city_line').empty().remove();
			$('#geo_base_cities_table').append(data);
		}
	});
}

function geo_base_onclick(cityid){ // click button "Add"
	var id = '';
	if(typeof cityid == 'undefined' && $('#geo_base_btn').prop('disabled')==true && cityid != 'Enter')
		return false;
	if(typeof cityid !== 'undefined' && cityid != 'Enter')
		id = cityid;
	else if(typeof geo_base.selected_id !== 'undefined'){
		id = geo_base.selected_id;
	}

	$.ajax({
		type: "POST",
		url: "/bitrix/admin/geo_base_selected.php",
		dataType: 'json',
		data: { 'sessid': BX.message('bitrix_sessid'),
			'city_id': id,
			'add_city': 'Y'
		},
		timeout: 10000,
		success: function(data){
			var list = $('select#geo_base_info');
			list.html('');
			if(data == '' || data == null)
				list.animate({ height: 'hide' }, "fast");
			else{
				if(data >= 0){
					$('#geo_base_btn').prop('disabled',true);
					$('input#geo_base_search').val('');
					geo_base_update_table();
				}
			}
		}
	});
	return false;
}

function geo_base_select_change(event){
	t = event.target || event.srcElement;
	var sel = t.options[t.selectedIndex];
	$('input#geo_base_search').val(geo_base.letters = BX.util.trim(sel.value));
	var id = sel.id.substr(20);
	geo_base.selected_id = id;
}

function geo_base_select_sizing(){
	var count = $("select#geo_base_info option").size();
	if (count < 2)
		$("select#geo_base_info").attr('size', count+1);
	else if (count < 20)
		$("select#geo_base_info").attr('size', count);
	else
		$("select#geo_base_info").attr('size', 20);
}

$(function(){
	$(document).click(function(event){
		var search = $('input#geo_base_search');
		if($(event.target).closest("#geo_base_info").length) return;
		$("#geo_base_info").animate({ height: 'hide' }, "fast");
		if(search.val() == '' && !$('#geo_base_btn').prop('disabled'))
				$('#geo_base_btn').prop('disabled', true);

		if($(event.target).closest("#geo_base_search").length) return;
		search.val('');
		event.stopPropagation();
	});
	var reaspektOption_obtn = $('#reaspekt_description_open_btn'),
		reaspektOption_cbtn = $('#reaspekt_description_close_btn'),
		full = $('#reaspekt_description_full');

	reaspektOption_obtn.click(function(event){
		full.show(175);
		$(this).hide();
		reaspektOption_cbtn.show();
	});

	reaspektOption_cbtn.click(function(event){
		full.hide(175);
		$(this).hide();
		reaspektOption_obtn.show();
	});
});

function geo_base_add_city(){ // on click Select
	$('#geo_base_btn').prop('disabled', false);
	$("#geo_base_info").animate({ height: 'hide' }, "fast");
}

function geo_base_load(){
	geo_base.timer = 0;
	$.ajax({
		type: "POST",
		url: '/bitrix/admin/geo_base_selected.php',
		dataType: 'json',
		data: { 'city_name': geo_base.letters,
			'lang': BX.message('LANGUAGE_ID'),
			'sessid': BX.message('bitrix_sessid')
		},
		timeout: 10000,
		success: function(data){
			var list = $('select#geo_base_info');

			list.html('');
			if(data == '' || data == null)
				list.animate({ height: 'hide' }, "fast");
			else{
				var arOut = '';
				for(var i=0; i < data.length; i++){
					var sOptVal = data[i]['CITY'] + (typeof(data[i]['REGION']) == "undefined" || data[i]['REGION'] == null ? '' : ', ' + data[i]['REGION'])
					+ (typeof(data[i]['OKRUG']) == "undefined" || data[i]['OKRUG'] == ' ' || data[i]['OKRUG'] == null ? '' : ', ' + data[i]['OKRUG']);
					arOut += '<option id="geo_base_inp'+ (typeof(data[i]['ID']) == "undefined" ? data[i]['ID'] : data[i]['ID']) +'"'
					+'value = "'+ sOptVal +'">'+ sOptVal +'</option>\n';
				}
				list.html(arOut);
				list.geo_base_light(geo_base.letters);
				geo_base_select_sizing();
				list.animate({ height: 'show' }, "fast");
			}
		}
	});
}

function geo_base_selKey(e){ // called when a key is pressed in Select
	e=e||window.event;
	t=(window.event) ? window.event.srcElement : e.currentTarget; // The object which caused

	if(e.keyCode == 13){ // Enter
		geo_base_onclick('Enter');
		$("#geo_base_info").animate({ height: 'hide' }, "fast");
		return;
	}
	if(e.keyCode == 38 && t.selectedIndex == 0){ // up arrow
		$('.geo_base_find input[name=geo_base_search]').focus();
		$("#geo_base_info").animate({ height: 'hide' }, "fast");
	}
}

function geo_base_inpKey(e){ // input search
	e = e||window.event;
	t = (window.event) ? window.event.srcElement : e.currentTarget; // The object which caused
	var list = $('select#geo_base_info');

	if(e.keyCode==40){	// down arrow
		if(list.html() != ''){
			list.animate({ height: 'show' }, "fast");
		}
		list.focus();
		return;
	}
	var sFind = BX.util.trim(t.value);

	if(geo_base.letters == sFind)
		return; // prevent frequent requests to the server
	geo_base.letters = sFind;
	if(geo_base.timer){
		clearTimeout(geo_base.timer);
		geo_base.timer = 0;
	}
	if(geo_base.letters.length < 2){
		list.animate({ height: 'hide' }, "fast");
		return;
	}
	geo_base.timer = window.setTimeout('geo_base_load()', 190); // Load through 70ms after the last keystroke
}

jQuery.fn.geo_base_light = function(pat){
	function geo_base_innerLight(node, pat){
		var skip = 0;
		if (node.nodeType == 3){
			var pos = node.data.toUpperCase().indexOf(pat);
			if (pos >= 0){
				var spannode = document.createElement('span');
				spannode.className = 'geo_base_light';
				var middlebit = node.splitText(pos);
				var endbit = middlebit.splitText(pat.length);
				var middleclone = middlebit.cloneNode(true);
				spannode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(spannode, middlebit);
				skip = 1;
			}
		}
		else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)){
			for (var i = 0; i < node.childNodes.length; ++i){
				i += geo_base_innerLight(node.childNodes[i], pat);
			}
		}
		return skip;
	}
	return this.each(function(){
		geo_base_innerLight(this, pat.toUpperCase());
	});
};

jQuery.fn.geo_base_removeLight = function(){
	return this.find("span.geo_base_light").each(function(){
		this.parentNode.firstChild.nodeName;
		with(this.parentNode){
			replaceChild(this.firstChild, this);
			normalize();
		}
	}).end();
};


</script>
<?
$incMod = CModule::IncludeModuleEx($module_id);
if ($incMod == '0')
{
    CAdminMessage::ShowMessage(Array("MESSAGE" => Loc::getMessage("GEOBASE_NF", Array("#MODULE#" => $module_id)), "HTML"=>true, "TYPE"=>"ERROR"));
}
elseif ($incMod == '2')
{
    ?><span class="errortext"><?=Loc::getMessage("GEOBASE_DEMO_MODE", Array("#MODULE#" => $module_id))?></span><br/><?
}
elseif ($incMod == '3')
{
    CAdminMessage::ShowMessage(Array("MESSAGE" => Loc::getMessage("GEOBASE_DEMO_EXPIRED", Array("#MODULE#" => $module_id)), "HTML"=>true, "TYPE"=>"ERROR"));
}
?>
<form method='POST' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>' name='geo_base_settings'><?

    $tabControl->Begin();
	$tabControl->BeginNextTab();
        
        ShowParamsHTMLByArray($arAllOptions["edit1"]);
    
    
    $tabControl->BeginNextTab();
        if ($reaspekt_set_local_sql == "local_db") {
    ?>
        <tr class="heading">
            <td colspan="2"><?=Loc::getMessage("INP_CITY_LIST")?></td>
        </tr>
        
        <tr>
            <td colspan="2">
                <table class="internal" width="100%">
                    <tbody id="geo_base_cities_table">
                    <tr class="heading" id="geo_base_table_header">
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD1")?></td>
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD2")?></td>
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD3")?></td>
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD4")?></td>
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD5")?></td>
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD6")?></td>
                        <td><?=Loc::getMessage("GEOBASE_TABLE_DEFAULT_CITY_TD7")?></td>
                    </tr>
                    <?
                    if($incMod != '0' && $incMod != '3') {
                        echo ReaspAdminGeoIP::UpdateCityRows();
                    }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?=Loc::getMessage("INP_CITY_ADD")?></td>
        </tr>
        <tr>
            <td>
                <input type="hidden" value="<?=$reaspekt_city_manual_default?>" name="reaspekt_city_manual_default" />
                <input type="text" size="100" maxlength="255" id="geo_base_search" onkeyup="geo_base_inpKey(event);" autocomplete="off" placeholder="<?=Loc::getMessage("INP_ENTER_CITY");?>" name="geo_base_search" value="">
                <br/>
                <select id="geo_base_info" ondblclick="geo_base_onclick();" onkeyup="geo_base_selKey(event);" onchange="geo_base_select_change(event);" onclick="geo_base_add_city();" size="2" style="display: none;">
                </select>
            </td>
        </tr>
        <tr>
            <td><input type="submit" id="geo_base_btn" value="<?=Loc::getMessage("TABLE_CITY_ADD");?>" onclick="geo_base_onclick(); return false;" disabled="true">
            </td>
        </tr>
    
    <?
        } else {
            echo Loc::getMessage("GEOBASE_DISABLED_NO_LOCAL_DB");
        }
    $tabControl->BeginNextTab();
        if ($reaspekt_set_local_sql == "local_db") {
    ?>
    
	<tr class="heading">
		<td colspan="2"><?=Loc::getMessage("GEOBASE_DB_UPDATE_IPGEOBASE")?></td>
	</tr>

	<tr>
		<td colspan="2">
			<div style="text-align: center">
				<div id="reaspektOptionNotices" class="adm-info-message" style="display: block">
					
                    <div style="display: none;" id="reaspektOptionUpdateUI">
                        <?=Loc::getMessage("NOTICE_UPDATE_AVAILABLE")?>
                        <br><br>
                        <input id="dbupdater" type="button" value="<?=Loc::getMessage("GEOBASE_UPDATE");?>" onclick="updateDB()">
                    </div>
                    <div id="reaspektOptionManualUpdate">
                        <?=Loc::getMessage("NOTICE_UPDATE_MANUAL_MODE")?>
                        <br><br>
                        <input type="button" onclick="BX.ajax.post('/bitrix/admin/geo_base_update_ipgeobase.php', {'action':'UPDATE'}, obHandler); return false;" value="<?=Loc::getMessage("GEOBASE_CHECK_UPDATE");?>">
                    </div>
				</div>
				<div class="reaspekt_option-main-box" id="reaspektOptionLoaderUI">
					<h3 id="title"><?=Loc::getMessage("TITLE_LOAD_FILE")?></h3>
					<span class="reaspekt_option-progress-bar">
						<span>
							<span id="progress"></span>
						</span>
						<span id="value">0%</span>
					</span>
				</div>
			</div>
		</td>
	</tr>
	<?ShowParamsHTMLByArray($arAllOptions["edit3"]);
    
        } else {
            echo Loc::getMessage("GEOBASE_DISABLED_NO_LOCAL_DB");
        }
    $tabControl->BeginNextTab();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
    
    $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="<?echo Loc::getMessage('MAIN_SAVE')?>" class="adm-btn-save" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
    <input type="submit" name="Apply" value="<?echo Loc::getMessage('MAIN_OPT_APPLY')?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>" >
    <input type="reset" name="reset" value="<?echo Loc::getMessage('MAIN_RESET')?>">
    <input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <?=bitrix_sessid_post();?>
    
<? $tabControl->End(); ?>

</form>