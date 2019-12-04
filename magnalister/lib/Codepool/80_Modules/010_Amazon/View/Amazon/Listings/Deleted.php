<?php class_exists('ML', false) or die() ?>
<?php
MLSetting::gi()->add('aCss', array('magnalister.productlist.css?%s'), true);
/* @var $this   ML_Listings_Controller_Listings_Deleted */
$this->includeView('widget_listings_misc_listingbox');
?>
<form action="<?php echo $this->getCurrentUrl() ?>"  method="post" class="ml-plist ml-js-plist">
    <div>
        <?php
        foreach(MLHttp::gi()->getNeededFormFields() as $sName=>$sValue ){
            ?><input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue?>" /><?php
        }

        if (isset($this->aPostGet['sorting'])) { ?>
            <input type="hidden" name="ml[sorting]" value="<?php echo $this->aPostGet['sorting'] ?>" />
            <?php
        }
        ?>
    </div>
    <?php
    $this->initAction();
    $this->prepareData();
    $langCode = MLLanguage::gi()->getCurrentIsoCode();
    $fromDate = date('Y', $this->delFromDate).', '.(date('n', $this->delFromDate) - 1).', '.date('j', $this->delFromDate);
    $toDate   = date('Y', $this->deToDate).', '.(date('n', $this->deToDate) - 1).', '.date('j', $this->deToDate);
    ?>
    <table class="datagrid ml-plist-old-fix">
        <thead>
            <tr>
                <th>Zeitraum</th>
            </tr>
        </thead>
            <tbody>
                <tr>
                    <td class="fullWidth">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Von:</td>
                                    <td>
                                        <input type="text" id="fromDate" readonly="readonly"/>
                                        <input type="hidden" id="fromActualDate" name="<?php echo MLHttp::gi()->parseFormFieldName('date[from]') ?>" value=""/>
                                    </td>
                                    <td>Bis:</td>
                                    <td>
                                        <input type="text" id="toDate" readonly="readonly"/>
                                        <input type="hidden" id="toActualDate" name="<?php echo MLHttp::gi()->parseFormFieldName('date[to]') ?>" value=""/>
                                    </td>
                                    <td><input class="mlbtn" type="submit" value="Los"/></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
    </table>
    <script type="text/javascript">
        (function($){
            $(document).ready(function() {
                $.datepicker.setDefaults($.datepicker.regional['']);
                $("#fromDate").datepicker(
                    $.datepicker.regional['<?php echo $langCode?>']
                ).datepicker(
                    "option", "altField", "#fromActualDate"
                ).datepicker(
                    "option", "altFormat", "yy-mm-dd"
                ).datepicker(
                    "option", "defaultDate", new Date(<?php echo $fromDate?>)
                );
                var dateFormat = $("#fromDate").datepicker("option", "dateFormat");
                $("#fromDate").val($.datepicker.formatDate(dateFormat, new Date(<?php echo $fromDate?>)));
                $("#fromActualDate").val($.datepicker.formatDate("yy-mm-dd", new Date(<?php echo $fromDate?>)));

                $("#toDate").datepicker(
                    $.datepicker.regional['<?php echo $langCode?>']
                ).datepicker(
                    "option", "altField", "#toActualDate"
                ).datepicker(
                    "option", "altFormat", "yy-mm-dd"
                ).datepicker(
                    "option", "defaultDate", new Date(<?php echo $toDate?>)
                );
                $("#toDate").val($.datepicker.formatDate(dateFormat, new Date(<?php echo $toDate?>)));
                $("#toActualDate").val($.datepicker.formatDate("yy-mm-dd", new Date(<?php echo $toDate?>)));
            });
        })(jqml);
    </script>
    <table class="datagrid ml-plist-old-fix">
        <thead>
        <tr>
            <?php foreach ($this->getFields() as $aFiled) { ?>
                <td> <?php
                    echo $aFiled['Label'];
                    if (isset($aFiled['Sorter'])) {
                        if ($aFiled['Sorter'] != null) {
                            ?>
                            <input class="noButton ml-right arrowAsc" type="submit" value="<?php echo $aFiled['Sorter'] ?>-asc" title="<?php echo $this->__('Productlist_Header_sSortAsc') ?>"  name="<?php echo MLHttp::gi()->parseFormFieldName('sorting'); ?>" />
                            <input class="noButton ml-right arrowDesc" type="submit" value="<?php echo $aFiled['Sorter'] ?>-desc" title="<?php echo $this->__('Productlist_Header_sSortDesc') ?>"  name="<?php echo MLHttp::gi()->parseFormFieldName('sorting'); ?>" />
                        <?php } } ?>
                </td>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (empty($this->aData)) {
            ?>
            <tr>
                <td colspan="<?php echo count($this->getFields()) + 1; ?>">
                    <?php echo $this->__($this->getEmptyDataLabel()) ?>
                </td>
            </tr>
            <?php
        } else {
            $oddEven = false;
            foreach ($this->aData as $item) {
                ?>
                <tr class="<?php echo(($oddEven = !$oddEven) ? 'odd' : 'even') ?>">
                    <?php
                    foreach ($this->getFields() as $aField) {
                        if ($aField['Field'] != null) {?>
                        <td><?php
                            if (array_key_exists($aField['Field'], $item)) {
                                echo $item[$aField['Field']] ;
                            }?>
                        </td>
                        <?php
                        } else {
                            echo call_user_func(array($this, $aField['Getter']), $item);
                        }
                    }
                    ?>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <?php $this->includeView('widget_listings_misc_action'); ?>

    <script type="text/javascript">/*<![CDATA[*/
        jqml(document).ready(function() {
            jqml('#selectAll').click(function() {
                state = jqml(this).attr('checked') !== undefined;
                jqml('.ml-js-plist input[type="checkbox"]:not([disabled])').each(function() {
                    jqml(this).attr('checked', state);
                });
            });
        });
        /*]]>*/</script>
</form>