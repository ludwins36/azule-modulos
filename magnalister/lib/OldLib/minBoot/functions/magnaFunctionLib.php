<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');


/******************************************************************************\
 *                        Magnalister Specific Functions                      *
\******************************************************************************/

function magnaContribVerify($hookname, $version) {
	$path = DIR_MAGNALISTER_CONTRIBS.$hookname.'_'.$version.'.php';
	if (($path[0] != '/') && !preg_match('/^[a-zA-Z]:\\\/', $path)) {
		if (defined('DIR_FS_ADMIN')) {
			$path = rtrim(DIR_FS_ADMIN, '/').'/'.$path;
		} else {
			return false;
		}
	}
	#echo var_dump_pre($path);
	return file_exists($path) ? $path : false;
}

function getCurrentModulePage() {
	global $_modules, $_MagnaSession;
	$aGet=  MLRequest::gi()->data();
	$modulePages = $_modules[$_MagnaSession['currentPlatform']]['pages'];

	if (isset($_modules[$_MagnaSession['currentPlatform']]['settings']['defaultpage'])) {
		$page = $_modules[$_MagnaSession['currentPlatform']]['settings']['defaultpage'];
	} else {
		$page = array_first(array_keys($modulePages));
	}
	if (array_key_exists('mode', $aGet) && array_key_exists($aGet['mode'], $modulePages)) {
		$page = $aGet['mode'];
	}
	return $page;
}

function requirementsMet($product, $requirements, &$failed) {
	if (!is_array($product) || empty($product) || !is_array($requirements) || empty($requirements)) {
		$failed = true;
		return false;
	}
	$failed = array();
	foreach ($requirements as $req => $needed) {
		if (!$needed) continue;
		if (!array_key_exists($req, $product) || (empty($product[$req]) && ($product[$req] !== '0'))) {
			$failed[] = $req;
		}
	}
	return empty($failed);
}

function generateProductCategoryThumb($fName, $w, $h, $noImageIcon = false) {
	if (!function_exists('imagecreatetruecolor')) {
		return 'GD Missing';
	}
	$imagePath = '';

	if (!eempty(trim($fName)) && !$noImageIcon) {
		if (file_exists(SHOP_FS_PRODUCT_THUMBNAILS.$fName)) {
			$imagePath = SHOP_FS_PRODUCT_THUMBNAILS;
			$cachePath = DIR_MAGNALISTER_IMAGECACHE.'product_';
		} else if (file_exists(SHOP_FS_PRODUCT_IMAGES.$fName)) {
			$imagePath = SHOP_FS_PRODUCT_IMAGES;
			$cachePath = DIR_MAGNALISTER_IMAGECACHE.'product_';
		} else if (file_exists(SHOP_FS_CATEGORY_IMAGES.$fName)) {
			$imagePath = SHOP_FS_CATEGORY_IMAGES;
			$cachePath = DIR_MAGNALISTER_IMAGECACHE.'category_';
		}
	} else if ($noImageIcon) {
		$imagePath = DIR_MAGNALISTER_RESOURCE;
		$cachePath = DIR_MAGNALISTER_IMAGECACHE.'resource_';
	}
	if ($imagePath == '') {
		return generateProductCategoryThumb('noimage.png', $w, $h, true);
	}
	$newfName = basename($fName);
	if (($dotpos = strrpos($newfName, '.')) > 0) {
		$newfName = str_split($newfName, $dotpos);
		$newfName = $newfName[0];
	}
	$newfName .= '_'.$w.'x'.$h.'.jpg';
	if (!is_dir(dirname(DIR_FS_ADMIN.$cachePath))) {
		if (MLSetting::gi()->get('blSaveMode')) return '&nbsp';
		mkdir(dirname(DIR_FS_ADMIN.$cachePath), 0775, true);
	}
	$destFile = DIR_FS_ADMIN.$cachePath.$newfName;
	if (file_exists($destFile)) {
		list($width, $height) = getimagesize($destFile);
		if (($w == $width) || ($h == $height)) {
			return '<img width="'.$width.'" height="'.$height.'" src="'.$cachePath.$newfName.'" alt="'.$fName.'"/>';
		}
	}

	$success = resizeImage($imagePath.$fName, $w, $h, $destFile);
	if (!$success && !$noImageIcon) {
		return generateProductCategoryThumb('noimage.png', $w, $h, true);
	} else if (!$success && $noImageIcon) {
		return 'X';
	}
	list($width, $height) = getimagesize($destFile);
	return '<img width="'.$width.'" height="'.$height.'" src="'.$cachePath.$newfName.'" alt="'.$fName.'"/>';
}

function html_image($image, $alt = "", $width = "", $height = "") {
	return '<img src="'.$image.'"'.(!empty($alt) ? (' alt="'.$alt.'" title="'.$alt.'"') : '').(!empty($width) ? (' width="'.$width.'"') : '').(!empty($height) ? (' height="'.$height.'"') : '').'>';
}

function parseShippingStatusName($status, $fallback) {
	/* Extract largest number */
	$largestNumber = 0;
	if (preg_match_all('/([0-9]+)/', $status, $matches)) {
		$numbers = $matches[0];
		foreach ($numbers as $number) {
			if ($number > $largestNumber) {
				$largestNumber = $number;
			}
		}
	}
	if (preg_match('/(Day|Days|Tag|Tage)/i', $status)) {
		return $largestNumber;
	}
	if (preg_match('/(Week|Weeks|Woche|Wochen)/i', $status)) {
		return $largestNumber * 7;
	}
	if (preg_match('/(Month|Months|Monat|Monate)/i', $status)) {
		return $largestNumber * 30;
	}
	
	return $fallback;
}

function shopAdminDiePage($content) {
	global $_MagnaSession;
	$_MagnaSession['currentPlatform'] = '';
	$content = func_get_args();
	$content = $content[0];
        $oEx=new OldMagnaExeption( $content,  OldMagnaExeption::iShopAdminDiePage);
        throw $oEx;
}

function sanitizeProductDescription($str, $allowable_tags = '', $allowable_attributes = '') {
	$str = !magnalisterIsUTF8($str) ? utf8_encode($str) : $str;

	$str = stripEvilBlockTags($str);

	/* Convert Gambio-Tabs to H1-Headlines */
	$str = preg_replace('/\[TAB:([^\]]*)\]/', '<h1>${1}</h1>', $str);

	if (stripos($allowable_tags, '<br') === false) {
		/* Convert (x)html breaks with or without atrributes to newlines. */
		$str = preg_replace('/\<br(\s*)?([[:alpha:]]*=".*")?(\s*)?\/?\>/i', "\n", $str);
	} else {
		$str = str_replace('<br/>', '<br />', $str);
	}
	$str = preg_replace("/<([^([:alpha:]|\/)])/", '&lt;\\1', $str);
	$str = strip_tags_attributes($str, $allowable_tags, $allowable_attributes);

	if ($allowable_tags == '') {
		$str = str_replace(array("\n", "\t", "\v", "|"), " ", $str);
		$str = str_replace(array("&quot;", "&qout;"), " \"", $str);

		$str = str_replace(array("&nbsp;"), " ", $str);

		/* Converts all html entities to "real" characters */
		$str = html_entity_decode($str,null,"UTF-8");
		$str = str_replace(array(";", "'"), ", ", $str);
	}

	/* Strip excess whitespace */
	$str = preg_replace('/\s\s+/', ' ', trim($str));
	return $str;
}


function magnalisterIsUTF8($str) {
    $len = strlen($str);
    for($i = 0; $i < $len; ++$i){
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) return false;
            elseif ($c > 239) $bytes = 4;
            elseif ($c > 223) $bytes = 3;
            elseif ($c > 191) $bytes = 2;
            else return false;
            if (($i + $bytes) > $len) return false;
            while ($bytes > 1) {
                ++$i;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) return false;
                --$bytes;
            }
        }
    }
    return true;
}



function substituteTemplate($tmplStr, $substitution) {
	$find = array();
	$replace = array();

	# Sonderfall: PICTURE1, ersetze durch URL wenn img oder a href Tag angegeben, sonst durch vollst. img Tag.
	if (    isset($substitution['#PICTURE1#'])
		&& !empty($substitution['#PICTURE1#'])) {
		$tmplStr = str_replace(
			'#PICTURE1#',
			"<img src=\"".$substitution['#PICTURE1#']."\" style=\"border:0;\" alt=\"\" title=\"\" />",
            preg_replace(
            	'/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(#PICTURE1#)/',
            	'\1\2\3'.$substitution['#PICTURE1#'],
            	$tmplStr
            )
        );
	}
	foreach ($substitution as $f => $r) {
		$find[] = $f;
		$replace[] = $r;
	}
	$str = str_replace($find, $replace, $tmplStr);

	# Bild 1 leer? entfernen
    $str = preg_replace('/<img[^>]*src=(""|\'\')[^>]*>/i', '', $str);

	# relative Pfade bei (nicht von uns eingesetzten) Bildern und Links ersetzen
	if (   ('none_none' != getDBConfigValue('general.editor',0,'tinyMCE')) 
	    && (preg_match('/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(?!http|HTTP|mailto|javascript|#|\+|\'\+|"\+)/', $str))
	) {
		$str = preg_replace(
			'/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(?!http|HTTP|mailto|javascript|#|\+|\'\+|"\+)/',
			'\1\2\3'.HTTP_CATALOG_SERVER.DIR_WS_CATALOG.'\4', 
			preg_replace(
				'/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(\/)/',
				'\1\2\3'.HTTP_CATALOG_SERVER.'\4',
				$str
			)
		);
	}
	return $str;
}

function magnaGetMarketplaceByID($mpID) {
	global $magnaConfig;
	$mpID = (string)$mpID;
	if (!ctype_digit((string)$mpID)) {
		if (MLSetting::gi()->get('blDebug')) {
			echo print_m(prepareErrorBacktrace(2));
		}
		trigger_error(__FUNCTION__.': Parameter $mpID ('.trim(var_dump_pre($mpID)).') must be numeric.');
	}
	if (!array_key_exists($mpID, $magnaConfig['maranon']['Marketplaces'])) {
		return false;
	}
	return $magnaConfig['maranon']['Marketplaces'][$mpID];
}

function magnaGetIDsByMarketplace($mp) {
	global $magnaConfig;
	
	if (!array_key_exists('maranon', $magnaConfig) || 
	    !array_key_exists('Marketplaces', $magnaConfig['maranon']) || 
	    empty($magnaConfig['maranon']['Marketplaces'])
	) {
		return false;
	}
	$ids = array();
	foreach ($magnaConfig['maranon']['Marketplaces'] as $mpID => $marketplace) {
		if ($marketplace == $mp) {
			$ids[] = $mpID;
		}
	}
	sort($ids, SORT_NUMERIC);
	return $ids;
}

function sendTestMail($mpID) {
    return MLService::getImportOrdersInstance()->sendPromotionMailTest();
}


function priceToFloat($price, $format = array()) {	
	$r = '/^([0-9\.,]*)$/';
	if (!preg_match($r, $price)) {
		return -1;
	}
	$frac = array();
	if (preg_match('/([\.,]{1,2}[0-9]{1,2})$/', $price, $frac)) {
		$frac = $frac[0];
		$price = substr($price, 0, -strlen($frac));
	} else {
		$frac = '0';
	}
	$price = str_replace(array('.', ','), '', $price).'.'.str_replace(array('.', ','), '', $frac);
	return (float)$price;
}


# show the quantity of all variations of a product,
# without actually filling the variations table
# minus: subtract from each variation's stock, for the case this is set by config
function getProductVariationsQuantity($pID, $minus = 0) {
	$quantity = 0;
	require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/VariationsCalculator.php');
	$skutype = (getDBConfigValue('general.keytype', '0') == 'artNr') ? 'model' : 'id';
	$vc = new VariationsCalculator(array(
		'skubasetype' => $skutype,
		'skuvartype'  => $skutype,
	));
	return $vc->getProductVariationsTotalQuantity($pID, $minus);
}


function getCurrencyFromMarketplace($mpID) {
	global $magnaConfig, $_modules;
	
	$mp = magnaGetMarketplaceByID($mpID);
	if ($mp === false) {
		return false;
	}
	if (!array_key_exists($mp, $_modules)) {
		return false;
	}
	$currency = $_modules[$mp]['settings']['currency'];
	if ($currency != '__depends__') {
		return $currency;
	}
	if ($mp == 'amazon') {
		$cur = getDBConfigValue('amazon.currency', $mpID, false);
		return empty($cur) ? false : $cur;
	}
	if ($mp == 'ebay') {
		$cur = getDBConfigValue('ebay.currency', $mpID, false);
		return empty($cur) ? false : $cur;
	}
	return false;
}

function magnaSKU2pID($sSku, $mainOnly = false) {
   $oProduct = MLProduct::factory()->getByMarketplaceSKU($sSku,$mainOnly);
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
   if($oProduct->exists()){
          return $oProduct ->get('id') ;
   }else{
       return 0;
   }
    
}

function magnaSKU2aID($sku) {
	//$aID = false;
   $oProduct = MLProduct::factory() ;
    $oProduct->getByMarketplaceSKU($sku);
    if($oProduct->exists()){
        return $oProduct->get("id");
    }else{
        return 0;
    }
}

function magnaPID2SKU($pID) {
     $oMLProduct = MLProduct::factory() ;
     $oMLProduct->set('id',$pID);
     $oMLProduct->load();
     return $oMLProduct->get('marketplaceidentsku');
}

function magnaAID2SKU($aID) {
    
    $oMLProduct = MLProduct::factory() ;
     $oMLProduct->set('id',$pID);
     $oMLProduct->load();
     return $oMLProduct->get('marketplaceidentsku');
}


function renderPagination ($currentPage, $pages, $baseURL, $type = 'link') {
	$html = '';
	if ($pages > 23) {
		for ($i = 1; $i <= 5; ++$i) {
			$class = ($currentPage == $i) ? 'class="bold"' : '';
			if ($type == 'submit') {
				$html .= ' <input type="submit" '.$class.' name="page" value="'.$i.'" title="'.ML_LABEL_PAGE.' '.$i.'"/>';
			} else {
				$html .= ' <a '.$class.' href="'.toUrl($baseURL, array('page' => $i)).'" title="'.ML_LABEL_PAGE.' '.$i.'">'.$i.'</a>';
			}
		}
		if (($currentPage - 5) < 7) {
			$start = 6;
			$end = 15;
		} else {
			$start = $currentPage - 4;
			$end = $currentPage + 4;
			$html .= ' &hellip; ';
		}
		if (($currentPage + 5) > ($pages - 7)) {
			$start = ($pages - 15);
			$end = $pages;
		}
		for ($i = $start; $i <= $end; ++$i) {
			$class = ($currentPage == $i) ? 'class="bold"' : '';
			if ($type == 'submit') {
				$html .= ' <input type="submit" '.$class.' name="page" value="'.$i.'" title="'.ML_LABEL_PAGE.' '.$i.'"/>';
			} else {
				$html .= ' <a '.$class.' href="'.toUrl($baseURL, array('page' => $i)).'" title="'.ML_LABEL_PAGE.' '.$i.'">'.$i.'</a>';
			}
		}
		if ($end != $pages) {
			$html .= ' &hellip; ';
			for ($i = $pages - 5; $i <= $pages; ++$i) {
				$class = ($currentPage == $i) ? 'class="bold"' : '';
				if ($type == 'submit') {
					$html .= ' <input type="submit" '.$class.' name="page" value="'.$i.'" title="'.ML_LABEL_PAGE.' '.$i.'"/>';
				} else {
					$html .= ' <a '.$class.' href="'.toUrl($baseURL, array('page' => $i)).'" title="'.ML_LABEL_PAGE.' '.$i.'">'.$i.'</a>';
				}
			}
		}
	} else {
		for ($i = 1; $i <= $pages; ++$i) {
			$class = ($currentPage == $i) ? 'class="bold"' : '';
			if ($type == 'submit') {
				$html .= ' <input type="submit" '.$class.' name="page" value="'.$i.'" title="'.ML_LABEL_PAGE.' '.$i.'"/>';
			} else {
				$html .= ' <a '.$class.' href="'.toUrl($baseURL, array('page' => $i)).'" title="'.ML_LABEL_PAGE.' '.$i.'">'.$i.'</a>';
			}
		}
	}
	return $html;
}

function renderCategoryPath($id, $from = 'category') {
	$calculated_category_path_string = '';
	$appendedText = '&nbsp;<span class="cp_next">&gt;</span>&nbsp;';
	$calculated_category_path = MLDatabase::getDbInstance()->generateCategoryPath($id, $from);
	for ($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i ++) {
		for ($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j ++) {
			$calculated_category_path_string .= fixHTMLUTF8Entities($calculated_category_path[$i][$j]['text']).$appendedText;
		}
		$calculated_category_path_string = substr($calculated_category_path_string, 0, -strlen($appendedText)).'<br>';
	}
	$calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

	if (strlen($calculated_category_path_string) < 1)
		$calculated_category_path_string = ML_LABEL_CATEGORY_TOP;

	return $calculated_category_path_string;
}

function loadConfigForm($lang, $files, $replace = array()) {
	$form = array();
	foreach ($files as $file => $options) {
		$fC = file_get_contents(DIR_MAGNALISTER.'config/'.$lang.'/'.$file);
		if (!empty($replace)) {
			$fC = str_replace(array_keys($replace), array_values($replace), $fC);
		}
		$fC = json_decode($fC, true);
		if (array_key_exists('unset', $options)) {
			foreach ($options['unset'] as $key) {
				unset($fC[$key]);
			}
		}
		$form = array_merge($form, $fC);
	}
	return $form;
}

function getTinyMCEDefaultConfigObject() {
	$langCode = MLI18n::gi()->getLang();
	if (!empty($langCode) && file_exists(DIR_MAGNALISTER.'js/tiny_mce/langs/'.$langCode.'.js')) {
		$langCode = 'language: "'.$langCode.'",';
	} else {
		$langCode = '';
	}

	return '
if (typeof tinyMCEMagnaDefaultConfig == "undefined") {
	var tinyMCEMagnaDefaultConfig = {
		// General options
		mode : "textareas",
		theme : "advanced",
		'.$langCode.'
		skin : "o2k7",
		skin_variant : "silver",
		editor_selector : "tinymce",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	
		// Theme options
		theme_advanced_buttons1 : "fullscreen,preview,code,|,undo,redo,|,bold,italic,underline,strikethrough,|,styleprops,|,forecolor,backcolor",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,image,cleanup,|,charmap,emotions,iespell,media,advhr,|,insertdate,inserttime",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,insertlayer,moveforward,movebackward,absolute,|,visualchars,nonbreaking",
		theme_advanced_buttons4 : "justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,
		// Example content CSS (should be your site CSS)
		//content_css : "style/style.css",
	
		width: "100%",
		valid_elements : "*[*]",
		invalid_elements : "",
		valid_children : "+body[style]",
		extended_valid_elements : "style[width], a[href|#]",
	
		// Drop lists for link/image/media/template dialogs
		//template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
	
		relative_urls : false,
		document_base_url : "'.MLHttp::gi()->getResourceUrl().'",
		remove_script_host : false,
		media_strict: false,
		
		gecko_spellcheck : true,
	
		autosave_ask_before_unload : true
	}
}';
}

function magna_wysiwyg($params, $value = '') {
	if (array_key_exists('class', $params)) {
		$params['class'] .= ' tinymce';
	} else {
		$params['class'] = 'tinymce';
	}
	$html = '<textarea';
	foreach ($params as $attr => $val) {
		$html .= ' '.$attr.'="'.$val.'"';
	}
	$html .= '>'.str_replace('<', '&lt;', (string)$value).'</textarea>';

    if ('tinyMCE' == getDBConfigValue('general.editor',0,'tinyMCE')) {

	    $html .= '<script type="text/javascript" src="includes/magnalister/js/tiny_mce/tiny_mce.js"></script>';

	    ob_start();?>
        <script type="text/javascript">/*<![CDATA[*/
    	    <?php echo getTinyMCEDefaultConfigObject(); ?>
		    $(document).ready(function() {
			    tinyMCE.init(tinyMCEMagnaDefaultConfig);
		    });
	    /*]]>*/</script><?php
	    $html .= ob_get_contents();
	    ob_end_clean();
    }
	return $html;
}

function magnaFixRamSize() {
	$nr = MLSetting::gi()->get('sMemoryLimit');
	$ramsize = @ini_get('memory_limit');
	if (!is_string($ramsize) || empty($ramsize)) {
		return @(bool)ini_set('memory_limit', $nr);
	}
	if (convert2Bytes($ramsize) < convert2Bytes($nr)) {
		return @(bool)ini_set('memory_limit', $nr);
	}
	return false;
}

function magnaFooterDebugTimers() {
	if (!defined('MAGNALISTER_PLUGIN') && file_exists(DIR_FS_DOCUMENT_ROOT.'magnaCallback.php')
		&& MLSetting::gi()->get('blDebug')
	) {
		global $_magnacallbacktimer, $_executionTime;

		echo '
		<style>
		textarea.apiRequestTime {
			border: 2px solid #ccc;
			background: #f8f8f8;
			color: #f8f8f8;
			border-radius: 5px;
			-moz-border-radius: 5px;
			-webkit-border-radius: 5px;
			width: 5px !important;
			height: 5px !important;
			padding: 0 !important;
			margin: 0 0 3px 0;
			overflow: hidden;
			font: 11px/1em monospace;
			display: block;
			-moz-box-sizing: content-box;
			-webkit-box-sizing: content-box;
			resize: none;
		}
		textarea.apiRequestTime:hover {
			color: #333;
			width: 100% !important;
			height: 300px !important;
			margin-top: -298px;
			position: relative;
			overflow: auto;
			padding: 3px 3px 0 3px !important;
			box-shadow: 0 0 15px rgba(0, 0, 0, 0.7), 0 0 2px 2px rgba(0, 0, 0, 0.3);
			-moz-box-shadow: 0 0 15px rgba(0, 0, 0, 0.7), 0 0 2px 2px rgba(0, 0, 0, 0.3);
			-webkit-box-shadow: 0 0 15px rgba(0, 0, 0, 0.7), 0 0 2px 2px rgba(0, 0, 0, 0.3);
		}
		div#magnaErrors {
			position: relative;
			width: 100%;
		}
		div#magnaErrors table {
			border-collapse: separate;
			border-spacing: 2px;
			width: 100%;
			position: relative;
			border: 2px solid #f00;
		}
		div#magnaErrors table th,
		div#magnaErrors table td {
			padding: 3px 5px;
			vertical-align: top;
		}
		div#magnaErrors table thead th {
			background: #444;
			color: #fff;
			font-weight: bold;
		}
		div#magnaErrors table tbody.odd td {
			background: #aaa;
			color: #000;
		}
		div#magnaErrors table tbody.even td {
			background: #ccc;
			color: #000;
		}
		div#magnaErrors table tbody td.level {	
			font-weight: bold;
		}
		div#magnaErrors table tbody td.level.notice {	
			background: #C9FF48;
			color: #000;
		}
		div#magnaErrors table tbody td.level.warning {	
			background: #FFB548;
			color: #000;
		}
		div#magnaErrors table tbody td.level.fatal {	
			background: #FF4848;
			color: #000;
		}
		div#magnaErrors table tbody td.message {
			position: relative;
		}
		div#magnaErrors table tbody td.action textarea,
		div#magnaErrors table tbody td.message pre {
			display: none;
			position: absolute;
			background: #fff;
			color: #000;
			border: 2px solid #999;
			z-index: 2;
			padding: 3px;
			margin-top: -2px;
			left: 0;
			width: 100%;
			overflow: auto;
		}
		div#magnaErrors table tbody td.message:hover pre {
			bottom: 1em;
			display: block;
			max-height: 450px;
		}
		div#magnaErrors table tbody td.action:hover textarea {
			display: block;
		    color: #333333;
		    height: 300px !important;
		    overflow: auto;
		    padding: 3px 3px 0 !important;
		    width: 100% !important;
		    font: 11px/1em monospace;
		    margin-top: -330px;
		}
		#magnaFootDebugger {
			text-align: left;
			background: #fff;
			border: 2px solid #999;
			font: 11px/1.2em sans-serif;
			padding: 5px;
		}
		#magnaFootDebugger hr {
			border-top: none;
			border-left: none;
			border-right: none;
			border-bottom: 1px solid #999;
		}
		</style>
		<div id="magnaFootDebugger">
			magnaCallback Time: <b>'.microtime2human($_magnacallbacktimer).'</b><br/><hr/>';
	
		if (class_exists('MagnaDB') && class_exists('MagnaConnector')) {
			$_executionTime = microtime(true) -  $_executionTime;
			$memory = memory_usage();
			echo '
				Entire page served in <b>'.microtime2human($_executionTime).'.</b><br/><hr/>
				API-Request Time: '.microtime2human(MagnaConnector::gi()->getRequestTime()).'. <br/>
				Processing Time: '.microtime2human($_executionTime - MagnaConnector::gi()->getRequestTime()).'. <br/><hr/>
				'.(($memory !== false) ? 'Max. Memory used: <b>'.$memory.'</b>. <br/><hr/>' : '').'
		     	DB-Stats (only MagnaDB Queries!): <br/>
		     	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Queries used: <b>'.MLDatabase::getDbInstance()->getQueryCount().'</b><br/>
		     	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Query time: '.microtime2human(MLDatabase::getDbInstance()->getRealQueryTime()).'<br/><hr/>
			';
		}
		if (class_exists('MagnaConnector')) {
			$tpR = MagnaConnector::gi()->getTimePerRequest();
			if (!empty($tpR)) {
				echo '<textarea class="apiRequestTime" readonly="readonly" spellcheck="false" wrap="off">';
				foreach ($tpR as $item) {
					echo print_m($item['request'], microtime2human($item['time']).' ['.$item['status'].']', true)."\n";
				}
				echo '</textarea>';
			}
		}
		if (class_exists('MagnaDB')) {
			$tpR = MLDatabase::getDbInstance()->getTimePerQuery();
			if (!empty($tpR)) {
				echo '<textarea class="apiRequestTime" readonly="readonly" spellcheck="false" wrap="off">';
				foreach ($tpR as $item) {
					echo print_m(ltrim(rtrim($item['query'], "\n"), "\n"), microtime2human($item['time']), true)."\n";
				}
				echo '</textarea>';
			}
		}
		$err = MagnaError::gi()->exceptionsToHTML(false);
		if (!empty($err)) {
			echo '<br/><hr/><div id="magnaErrors"><div>'.$err.'</div></div>';
		}
		
		echo '</div>';
	}
}

function magnaGenerateNavStructure() {
	global $magnaConfig, $_modules;
	
	$alwaysDisplayedModules = array();
	foreach ($_modules as $key => $item) {
		if ($item['displayAlways']) {
			$alwaysDisplayedModules[$key] = true;
		}
	}
	
	if (!isset($magnaConfig['maranon']['Marketplaces']) 
		|| !is_array($magnaConfig['maranon']['Marketplaces'])
	) {
		$magnaConfig['maranon']['Marketplaces'] = array();
	}
	$structure = array();
	
	$doinavtive = true;
	
	if (!empty($magnaConfig['maranon']['Marketplaces'])) {
		foreach ($magnaConfig['maranon']['Marketplaces'] as $mpID => $key) {
			$curItem = array ();
			
			$item = $_modules[$key];
	
			$classes = array();
			if (array_key_exists($key, $alwaysDisplayedModules)) {
				unset($alwaysDisplayedModules[$key]);
			}
			if (array_key_exists('mp', $_GET) && ($_GET['mp'] == $mpID)) {
				$classes[] = 'selected';
			}
			$curItem['title'] = isset($item['subtitle']) 
							? $item['subtitle'] 
							: $item['title'];
			$curItem['label'] = getDBConfigValue(array('general.tabident', $mpID), '0', '');
			$curItem['url']   = toURL(array('mp' => $mpID));
			$curItem['image'] = isset($item['logo'])
							? DIR_MAGNALISTER_IMAGES.'logos/'.$item['logo'].'.png'
							: '';
			$curItem['class'] = implode(' ', $classes);
			$curItem['key']   = $key.'_'.$mpID;
			
			$structure[] = $curItem;
		}
	} else {
		$doinavtive = false;
	}
	if (!empty($alwaysDisplayedModules)) {
		$curModule = isset($_GET['module']) ? $_GET['module'] : '';
		foreach ($alwaysDisplayedModules as $key => $null) {
			$item = $_modules[$key];
	
			$classes = array();
			if ($curModule == $key) {
				$classes[] = 'selected';
			}
			if ((!isset($item['type']) || ($item['type'] != 'system')) && $doinavtive) {
				$classes[] = 'inactive';
			}
			$curItem = array();
			
			$curItem['title'] = isset($item['subtitle']) 
							? $item['subtitle'] 
							: $item['title'];
			$curItem['label'] = isset($item['label'])
                            ? $item['label']
                            : '';
			$curItem['url']   = toURL(array('module' => $key));
			$curItem['image'] = isset($item['logo'])
							? DIR_MAGNALISTER_IMAGES.'logos/'.$item['logo'].'_inactive.png'
							: '';
			$curItem['class'] = implode(' ', $classes);
			$curItem['key']   = $key;
			
			$structure[] = $curItem;
		}
	}
	return $structure;
}

/** 
 * Returns the offset from the origin timezone to the remote timezone, in seconds.
 * @param $remote_tz
 * @param $origin_tz	If null the servers current timezone is used as the origin.
 * @return 	The offset in seconds as int or false incase of a failure.
 */
function magnaGetTimezoneOffset($remote_tz, $origin_tz = null) {
	if ($origin_tz === null) {
		$origin_tz = @date('T');
	}
	if (!class_exists('DateTimeZone')) {
		global $_MagnaSession;
		if (isset($_MagnaSession['TimezoneOffset'])) {
			return $_MagnaSession['TimezoneOffset'];
		}
		try {
			$response = MagnaConnector::gi()->submitRequest(array (
				'ACTION' => 'GetTimezoneOffset',
				'SUBSYSTEM' => 'Core',
				'FROM' => $remote_tz,
				'TO' => $origin_tz,
			));
			$_MagnaSession['TimezoneOffset'] = (int)$response['DATA'];
			return $_MagnaSession['TimezoneOffset'];
		} catch (MagnaException $me) {
			return false;
		}
	}
	try {
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
	} catch (Exception $e) {
		return false;
	}
	$origin_dt = new DateTime("now", $origin_dtz);
	$remote_dt = new DateTime("now", $remote_dtz);
	$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
	return $offset;
}

/**
 * Returns a local time
 * which matches a given time on the magnalister server
 * If offset cannot be determined, return = input
 * @param $magnaTimeval  (YYYY-mm-dd HH:MM:ss)
 * @return $localTimeval (YYYY-mm-dd HH:MM:ss)
 */
function magnaTimeToLocalTime($magnaTimeval, $reverse = false) {
    $offset = magnaGetTimezoneOffset('Europe/Berlin');
    if (false == $offset) {
		return $magnaTimeval;
	}
	if ($reverse) {
		$offset = (int)((-1) * $offset);
	}
    return date('Y-m-d H:i:s', mktime(
        substr($magnaTimeval, 11,2), substr($magnaTimeval, 14,2), substr($magnaTimeval, 17,2),
        substr($magnaTimeval, 5,2),  substr($magnaTimeval, 8,2),  substr($magnaTimeval, 0,4)
        ) + $offset);
}   

function localTimeToMagnaTime($localTimeval) {
	# call magnaTimeToLocalTime with reverse == true
	return magnaTimeToLocalTime($localTimeval, true);
}
