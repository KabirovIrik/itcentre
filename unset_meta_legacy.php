<?
// В файле section_horizontal.php перед вызовом компонента catlog.section
global $arSectName;

// в компоненте catlog.section в $arSectName записыфваем название раздела
$arSectName = $arResult['NAME'];

//после вызова catalog.section: 
$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(
    $arParams['IBLOCK_ID'], $arResult["VARIABLES"]["SECTION_ID"]
);
$arElMetaProp = $ipropValues->queryValues();
if($arElMetaProp['SECTION_META_TITLE']['ENTITY_ID'] != $arResult["VARIABLES"]["SECTION_ID"] || !$arElMetaProp['SECTION_META_TITLE']['VALUE']) {
	$APPLICATION->SetPageProperty('title', $arSectName.' купить по низкой цене в Уфе - Руфсталь');
}
if($arElMetaProp['SECTION_META_DESCRIPTION']['ENTITY_ID'] != $arResult["VARIABLES"]["SECTION_ID"] || !$arElMetaProp['SECTION_META_DESCRIPTION']['VALUE']) {
	$APPLICATION->SetPageProperty('description', 'Купить '. $arSectName.' по низким ценам оптом и в розницу от производителя. Широкий ассортимент. Доставка - Руфсталь');
}