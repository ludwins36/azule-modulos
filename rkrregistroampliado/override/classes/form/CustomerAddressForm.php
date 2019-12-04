<?php
/*
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by Rekire.com
*
* @author     Rekire - Tecnología web
* @copyright  2018 Rekire.com All right reserved
* @license    Rekire
* @contact    info@rekire.com
*
*/

use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: FIXME:
 * In the old days, when updating an address, we actually:
 * - checked if the address was used by an order
 * - if so, just mark it as deleted and create a new one
 * - otherwise, update it like a normal entity
 * I *think* this is not necessary now because the invoicing thing
 * does its own historization. But this should be checked more thoroughly.
 */
class CustomerAddressForm extends CustomerAddressFormCore
{

  public $module_enabled_and_configured = false;

  public function __construct(
      Smarty $smarty,
      Language $language,
      TranslatorInterface $translator,
      CustomerAddressPersister $persister,
      CustomerAddressFormatter $formatter
  ) {
      parent::__construct(
          $smarty,
          $language,
          $translator,
          $persister,
          $formatter
      );

      $this->language = $language;
      $this->persister = $persister;
  }

  public function getTemplateVariables()
  {
      $context = Context::getContext();

      if (!$this->formFields) {
          // This is usually done by fillWith but the form may be
          // rendered before fillWith is called.
          // I don't want to assign formFields in the constructor
          // because it accesses the DB and a constructor should not
          // have side effects.
          $this->formFields = $this->formatter->getFormat();
      }

      $this->setValue('token', $this->persister->getToken());
      $formFields = array_map(
          function (FormField $item) {
              return $item->toArray();
          },
          $this->formFields
      );

      $datoPhone = Db::getInstance()->executeS('
          SELECT cc.name, cr.reponse
          FROM '._DB_PREFIX_.'rkr_ra_campos cc
          INNER JOIN '._DB_PREFIX_.'rkr_reponses cr
          ON cr.id_champ = cc.id
          WHERE cr.id_customer = '.$context->customer->id
      );

      if (empty($formFields['firstname']['value'])) {
          $formFields['firstname']['value'] = $context->customer->firstname;
      }

      if (empty($formFields['lastname']['value'])) {
          $formFields['lastname']['value'] = $context->customer->lastname;
      }

      for($i=0; $i<count($datoPhone);$i++){
        if($datoPhone[$i]['name'] == 'Address'){
          $formFields['address1']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Address Complement'){
          $formFields['address2']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Zip/Postal Code'){
          $formFields['postcode']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'City'){
          $formFields['city']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Other'){
          $formFields['other']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Phone'){
          $formFields['phone']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Mobile phone'){
          $formFields['phone_mobile']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'VAT number'){
          $formFields['vat_number']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Identification number'){
          $formFields['dni']['value'] = $datoPhone[$i]["reponse"];
        }
        elseif($datoPhone[$i]['name'] == 'Company'){
          $formFields['company']['value'] = $datoPhone[$i]["reponse"];
        }
      }

      return array(
          'id_address' => (isset($this->address->id)) ? $this->address->id : 0,
          'action' => $this->action,
          'errors' => $this->getErrors(),
          'formFields' => $formFields,
      );
  }


}
