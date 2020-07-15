<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Manager");
?>

<?

function get_template_from_file($path) {
    $csvFile = new CCSVData();  //создаем объект для вывода данных из csv-файла
    $file = $csvFile->LoadFile($path);    //загружаем искомый файл
    $fields_type = 'R'; //дописываем строки в файл
    $delimiter = ";";   //разделитель для csv-файла
    $csvFile->SetFieldsType($fields_type);
    $csvFile->SetDelimiter($delimiter);
    $string = '';
    $variables = array();
    $cnt = 0;
    while($arToImport = $csvFile->Fetch()){
    	if($arToImport[2] != '') {
			$string .= '<p>'.$arToImport[2].'</p>';
		}
    	if($arToImport[0] != '') {
    		$last_var = strtolower($arToImport[0]); 
    		$variables[$last_var] = array();
    		$variables[$last_var][] = $arToImport[1];
    	}
    	else {
    		$variables[$last_var][] = $arToImport[1];
    	}
    	$cnt++;
    }

    return array('vars' => $variables, 'template' => $string);
}

function get_template($name, $path) {
	$templ = get_template_from_file($path);
	$res_str = $templ['template'];
	foreach ($templ['vars']  as $var => $values) {
		$randIndex = array_rand($values);
		$val = $values[$randIndex];
		$res_str = str_replace('['.$var.']', $val, $res_str);
	}
	$res_str = str_replace(array('[H1]', 'H1'), $name, $res_str);
	return $res_str;
}


if($_POST['ajax']) {
   	$APPLICATION->RestartBuffer();
   	if(!empty($_FILES['file'])) {
   		$uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/manager/uploads';
   		if($_FILES['file']['error'] == 0) {
   			//Загружаем файл
   			$file_name = basename($_FILES["file"]["name"]);
   			$tmp_name = $_FILES["file"]["tmp_name"];
   			$path = $uploads_dir."/".$file_name;
   			if(move_uploaded_file($tmp_name, $path )) {
   				echo 'файл загружен на сервер <hr> Примеры текстов: <br><br>';
				$templ = get_template_from_file($path);
				$test_string = array();
				for ($i=0; $i < 10; $i++) { 
					echo(get_template('Название элемента №'.$i, $path).'<hr>');
				}
	        }
	        else {
   				echo 'Ошибка загрузки файла на сервер';
	        }
	    }
   	}
   	else{
   		if($_POST['type'] == 'SECTION') {
	    	$getfile = $_SERVER['DOCUMENT_ROOT'].'/manager/uploads/sections.csv';
	        if(!file_exists($getfile)) {
	        	echo json_encode(['page' => '-1', 'count' => 0, 'path' => $getfile, 'error' => 'Загрузите файл шаблона разделов']);
	        	die();
	        }
        	$arSelect = Array("ID", "NAME");
			$arFilter = Array("IBLOCK_ID"=>IntVal(4), "ACTIVE"=>"Y");
			$page = $_POST['page'];
			$post_per_page = 200;
			$sections=array();
			$db_list = CIBlockSection::GetList(Array(), $arFilter, false, $arSelect, Array("nPageSize"=>$post_per_page, "iNumPage"=> $page));
			while($ar_result = $db_list->GetNext()) {
				$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues(IntVal(4), $ar_result['ID']);
				$IPROPERTY  = $ipropValues->queryValues();
				$section = array();
				$section['ID'] = $ar_result['ID'];
				$section['NAME'] = $IPROPERTY['SECTION_PAGE_TITLE']['ENTITY_ID'] == $ar_result['ID'] ? $IPROPERTY['SECTION_PAGE_TITLE']['VALUE'] : $ar_result['NAME'];
				$temaple = get_template($section['NAME'], $getfile);

				$bs = new CIBlockSection;
				$arSectionFields = Array(
				 	"UF_GENERATED_TEXT" => $temaple
				);
				$bs->Update($section["ID"], $arSectionFields); 
				
			 	$sections[] = $ar_result;
			}
			$elems_count = $page * $post_per_page + count($sections);
			if($post_per_page > count($sections)) {
				echo json_encode(['page' => '-1', 'count' => $elems_count, 'path' => $getfile]);
			}
			else {
				$page = $page + 1;
				echo json_encode(['page' => $page, 'count' => $elems_count, 'path' => $getfile]); 
			}
   		}
   		if($_POST['type'] == 'ELEMENTS') {
	    	$getfile = $_SERVER['DOCUMENT_ROOT'].'/manager/uploads/elements.csv';
	        if(!file_exists($getfile)) {
	        	echo json_encode(['page' => '-1', 'count' => 0, 'path' => $getfile, 'error' => 'Загрузите файл шаблона товаров']);
	        	die();
	        }
        	$arSelect = Array("ID", "NAME");
			$arFilter = Array("IBLOCK_ID"=>IntVal(4), "ACTIVE"=>"Y");
			$page = $_POST['page'];
			$post_per_page = 500;
			$products=array();
			
			$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>$post_per_page, "iNumPage"=> $page), $arSelect);
			while($ob = $res->GetNextElement()) {
			 	$arFields = $ob->GetFields();
			 	$temaple = get_template($arFields['NAME'], $getfile);
			 	CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFilter['IBLOCK_ID'], array('ATT_GENERATED_TEXT' => $temaple));
			 	// $el = new CIBlockElement;
			 	// $res = $el->Update($arFields['ID'], array(
				 // 		'PROPERTY_VALUES' => array(
				 // 			'ATT_GENERATED_TEXT' => $temaple
				 // 		)
				 // 	)
			 	// );

			 	$products[] = $arFields;
			}
			$elems_count = $page * $post_per_page + count($products);
			if($post_per_page > count($products)) {
				echo json_encode(['page' => '-1', 'count' => $elems_count, 'path' => $getfile]);
			}
			else {
				$page = $page + 1;
				echo json_encode(['page' => $page, 'count' => $elems_count, 'path' => $getfile]); 
			}
   		}
   	}
   	die();
}
?>
<div class="col-md-12">
	<div class="form form--file">
		<form action="/manager/" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="ajax" value="Y">
			<div class="row">
				<div class="col-md-12">Кодировка файла "UTF-8 без BOM", в формате CSV. Название должно быть sections.csv или elements.csv</div>
				<div class="col-md-12"><br><input class="" type="file" name="file" id="file" /><br><button class="btn js-star" name="file_submit">Загрузить файл</button></div>
				<div class="col-md-12 mt-40">
					<div class="result"></div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="col-md-12 mt-40">
	<div class="form">
		<form action="/manager/" class="js-form-update" method="POST">
			<input type="hidden" name="type" value="SECTION">
			<input type="hidden" name="ajax" value="Y">
			<input type="hidden" name="page" value="0">
			<div class="row">
				<div class="col-md-12"><button class="btn js-update-text" name="file_submit">Применить шаблона из файла к разелам</button></div>
				<div class="col-md-12">
					<div class="result"></div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="col-md-12 mt-40">
	<div class="form">
		<form action="/manager/" class="js-form-update" method="POST">
			<input type="hidden" name="type" value="ELEMENTS">
			<input type="hidden" name="ajax" value="Y">
			<input type="hidden" name="page" value="0">
			<div class="row">
				<div class="col-md-12"><button class="btn js-update-text" name="file_submit">Применить шаблона из файла к товарам</button></div>
				<div class="col-md-12 mt-40">
					<div class="result"></div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- JQUERY 1.12.2 -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<!-- COMMON SCRIPT -->
<script>
	$(document).ready(function(){


    // аякс-загрузка файла
    $('.js-star').on('click', function(e){
        e.preventDefault();
        var th = $(this);
        var file_data = $('#file').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('update_data', 'Y');
        form_data.append('ajax', 'Y');
        $.ajax({
            type: "POST",
            url: $(this).closest('form').attr('action'),
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            beforeSend: function() {
            },
            success: function(result){
            	th.closest('form').find('.result').html(result);
            },
            complete: function() {
            }
        });
    });

    function update_elemets(page, type, form) {
        var  url = "/manager/";
        var form_data = new FormData();
        form_data.append('page', page);
        form_data.append('type', type);
        form_data.append('ajax', 'Y');
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            processData: false,
            contentType: false,
            data: form_data,
            success: function(result){
                if(result.error) {
                    form.find('.result').html(result.error);
                }
                else if(result.page == -1) {
                    form.find('.result').html("Обновление закончено. Обновлено "+result.count+" элементов");
                }
                else {
                    form.find('.result').html("Идет обновление. Обновлено "+result.count+" элементов");
                    setTimeout(update_elemets, 1000, result.page, type, form);
                }
            }
        }); 
    }

    //
    $('.js-form-update').submit(function(e) {
        e.preventDefault();
        var url = "/manager/";
        var th = $(this),
            type = $(this).find('[name="type"]').val(),
            data = th.serialize();
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: data,
            success: function(result){
                if(result.error) {
                    th.find('.result').html(result.error);
                }
                else if(result.page == -1) {
                    th.find('.result').html("Обновление закончено. Обновлено "+result.count+" элементов");
                }
                else {
                    th.find('.result').html("Идет обновление. Обновлено "+result.count+" элементов");
                    setTimeout(update_elemets, 1000, result.page, type, th);
                }
            }
        });   
    })

// end document.ready
});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>