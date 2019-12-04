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
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class_exists('ML', false) or die();
?>
<label class="<?php echo !empty($translationData['label']['missing_key']) ? 'missing_translation' : ''?>" for="<?php echo $aField['id'] ?>"><?php echo $aField['i18n']['label'] ?></label>
<?php if (isset($aField['requiredField']) === true && $aField['requiredField'] === true) { ?><span>•</span><?php } ?>
<?php if (MLI18n::gi()->isTranslationActive()) { ?>
    <div class="ml-translate-toolbar">
        <a href="#" title="Translate label" class="translate-label abutton" <?php echo 'data-ml-translate-modal="#modal-tr-' . str_replace('.', '\\.', $aField['id']) . '-label"'; ?>>&nbsp;</a>
        <div class="ml-modal-translate dialog2" id="modal-tr-<?php echo str_replace('.', '\\.', $aField['id']) ?>-label">
            <script type="text/plain" class="data"><?php echo json_encode($translationData['label']); ?></script>
        </div>
    </div>
<?php } ?>