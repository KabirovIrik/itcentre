<?
function replace_between($str, $needle_start, $needle_end, $replacement) {
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    return substr_replace($str, $replacement, $start, $end - $start);
}

function clear_h2($content) {
	$dom = new DOMDocument;
	$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $content);                  // load HTML into it
	// $dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">' . $content);                  // load HTML into it
	$xpath = new DOMXPath($dom);
	$nodes = $xpath->query('//h2');
	foreach ($nodes as $node) {
	    $attributes = $node->attributes;
		while ($attributes->length) {
		    $node->removeAttribute($attributes->item(0)->name);
		}
	    $node->nodeValue = $node->textContent;
	}
	$res = substr_replace($dom->saveHTML(), '<head>', '</head>', '');
	return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>', '<head>', '</head>', '<meta http-equiv="content-type" content="text/html; charset=utf-8">'), '', $res));
}

$servername = 'localhost';
$username = '';
$password = '';
$dbname = '';
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

//SELECT * FROM `modx_site_tmplvar_contentvalues` WHERE `value` LIKE '%h2%'
//SELECT id, content FROM `modx_site_content` WHERE id != 303 AND content LIKE '%h2%'


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else {
	if ($result = $conn->query("SELECT id, content FROM `modx_site_content` WHERE id != 303 AND content LIKE '%h2%'")) {
		while ($row = $result->fetch_assoc()) {
			$res_id = $row['id'];
		    $res = clear_h2($row['content']);
		    if($res_up = $conn->query("UPDATE `modx_site_content` SET content = '$res' WHERE id = '$res_id'")) {
		    	echo $row['id'].' ok <br>';
		    }
		    else {
		    	echo '<b>'.$row['id'].' not ok</b> <br>';
		    }
		}
	}
}
