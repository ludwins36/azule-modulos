<?php class_exists('ML', false) or die(); ?>
<input class="mlbtn action" type="button" value="<?php echo $this->__('ML_ETSY_BUTTON_SAVE_SHIPPING_TEMPLATE') ?>"
       id="saveTemplate"/>
<script type="text/javascript">/*<![CDATA[*/
    function getSCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function setSCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function checkCookie() {
        var showInfo = getSCookie("showInfo");
        return parseInt(showInfo, 10) === 1;
    }

    function getAlertBox(message, type) {
        return '<div class="'+ type +'Box">' +
               '   <table style="width:100%;border-spacing: 0;">' +
               '       <tbody class="hideChild">' +
               '          <tr>' +
               '              <th colspan="7">' + message +
               '                   <a role="button" class="ml-js-noBlockUi close-message" href="#" style="">' +
               '                      <span class="close-message-icon">close</span>' +
               '                   </a>' +
               '              </th>' +
               '         </tr>' +
               '      </tbody>' +
               '   </table>' +
               '</div>';
    }

    function getNoticeBox(message) {
        return getAlertBox(message, 'notice');
    }

    function getSuccessBox(message) {
        return getAlertBox(message, 'success');
    }

    function getErrorBox(message) {
        return getAlertBox(message, 'error');
    }


    jqml(document).ready(function () {
        var message = jqml('#ml-js-pushMessages');
        if (checkCookie()) {
            message.empty().append(getSuccessBox('You\'ve successfully created shipping template'));
            setSCookie("showInfo", '');
        }

        jqml('#saveTemplate').click(function (e) {

            e.preventDefault();

            var title = jqml("[name='ml[field][shippingtemplatetitle]']");
            var originCountry = jqml("[name='ml[field][shippingtemplatecountry]']");
            var primaryCost = jqml("[name='ml[field][shippingtemplateprimarycost]']");
            var secondaryCost = jqml("[name='ml[field][shippingtemplatesecondarycost]']");
            var errors = [];

            if (!title.val()) {
                errors.push('Title is required.');
            }

            if (!originCountry.val()) {
                errors.push('Country is required.');
            }

            if (!primaryCost.val()) {
                errors.push('Primary cost is required.');
            } else {
                if (!jqml.isNumeric(primaryCost.val())) {
                    errors.push('Invalid format for primary cost.');
                }
            }

            if (!secondaryCost.val()) {
                errors.push('Secondary cost is required.');
            } else {
                if (!jqml.isNumeric(secondaryCost.val())) {
                    errors.push('Invalid format for secondary cost.');
                }
            }

            if (errors.length) {
                message.empty().append(getErrorBox(errors.join('<br>')));
                window.scrollTo(0, 0);
                return;
            }

            jqml.blockUI(blockUILoading);
            jqml.post('<?php echo MLHttp::gi()->getCurrentUrl(array('what' => 'SaveShippingTemplate', 'kind' => 'ajax')) ?>',
                {
                    title: title.val(),
                    originCountry: originCountry.val(),
                    primaryCost: primaryCost.val(),
                    secondaryCost: secondaryCost.val()
                }
            ).done(function () {
                jqml.post('<?php echo MLHttp::gi()->getUrl(array('controller' => 'main_tools_filesystem_cache', 'deleteallcache' => true)); ?>')
                    .done(function () {
                        setSCookie('showInfo', 1);
                        location.reload();
                    });

            }).fail(function () {
                message.empty().append(getErrorBox('Server error'));
            });
        });

        jqml('#etsy_config_prepare_fieldset_shippingtemplate').hide();
        jqml('#etsy_prepare_apply_form_fieldset_shippingtemplate').hide();
        jqml('#shippingtemplateajax').click(function (e) {
            e.preventDefault();
            jqml('#etsy_config_prepare_fieldset_shippingtemplate').toggle('slow', function () {
                if (jqml(this).is(':visible')) {
                    jqml('#shippingtemplateajax').html('-');
                } else {
                    jqml('#shippingtemplateajax').html('+');
                }
            });
        });
        jqml('#shippingtemplateajax').click(function (e) {
            e.preventDefault();
            jqml('#etsy_prepare_apply_form_fieldset_shippingtemplate').toggle('slow', function () {
                if (jqml(this).is(':visible')) {
                    jqml('#shippingtemplateajax').html('-');
                } else {
                    jqml('#shippingtemplateajax').html('+');
                }
            });
        });
    });
    /*]]>*/</script>