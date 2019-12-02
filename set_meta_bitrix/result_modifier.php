<?
//устанавливаем шаблоны для мета тегов
$arResult['SEO_H1'] = $arResult['SEO_TITLE'] = $arResult['SEO_DESCRIPTION'] = $arResult['NAME'];
// получаем индекс текущего раздела
$cur_index = count($arResult['SECTION']['PATH']) - 1;
$parent_inex = $cur_index - 1;
// если метатег задан вручную, то перебиваем в шаблон 
if($arResult['SECTION']['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'] && $arResult['SECTION']['PATH'][$cur_index]['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'] != $arResult['SECTION']['PATH'][$parent_inex]['IPROPERTY_VALUES']['SECTION_PAGE_TITLE']) {
	$arResult['SEO_H1'] = $arResult['SECTION']['IPROPERTY_VALUES']['SECTION_PAGE_TITLE']." в ".$arParams["_SEO"]["UF_CITY_NAME_2"];
}
if($arResult['SECTION']['IPROPERTY_VALUES']['SECTION_META_TITLE'] && $arResult['SECTION']['PATH'][$cur_index]['IPROPERTY_VALUES']['SECTION_META_TITLE'] != $arResult['SECTION']['PATH'][$parent_inex]['IPROPERTY_VALUES']['SECTION_META_TITLE']) {
	$arResult['SEO_TITLE'] = $arResult['SECTION']['IPROPERTY_VALUES']['SECTION_META_TITLE']." в ".$arParams["_SEO"]["UF_CITY_NAME_2"];
}
if($arResult['SECTION']['IPROPERTY_VALUES']['SECTION_META_DESCRIPTION'] && $arResult['SECTION']['PATH'][$cur_index]['IPROPERTY_VALUES']['SECTION_META_DESCRIPTION'] != $arResult['SECTION']['PATH'][$parent_inex]['IPROPERTY_VALUES']['SECTION_META_DESCRIPTION']) {
	$arResult['SEO_DESCRIPTION'] = $arResult['SECTION']['IPROPERTY_VALUES']['SECTION_META_DESCRIPTION']." в ".$arParams["_SEO"]["UF_CITY_NAME_2"];
}
//передаем переменные в эпилог
$this->__component->SetResultCacheKeys([
	'SEO_H1',
	'SEO_TITLE',
	'SEO_DESCRIPTION',
]);