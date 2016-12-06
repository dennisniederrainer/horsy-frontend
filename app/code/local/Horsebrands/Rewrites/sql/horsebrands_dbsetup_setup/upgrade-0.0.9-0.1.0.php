<?php

$installer = $this;
$installer->startSetup();

$confirmationname = 'Horsebrands | Konto Bestätigung';

/* ************************************** */
/* Start Setup Email ACCOUNT CONFIRMATION */
/* ************************************** */

$emailTemplate = Mage::getModel('core/email_template')->loadByCode($confirmationname);

if($emailTemplate->getId())
  $emailTemplate->delete();

$content = <<<EOF
{{template config_path="design/email/header"}}
{{inlinecss file="hb-email-inline.css"}}
  <tr>
    <td class="one-column hello">
      <table width="100%">
        <tr>
          <td class="inner contents">
            <h1 class="title">
              Herzlich Willkommen bei Horsebrands!
            </h1>
            <p>
              Deine Registrierung ist fast geschafft! Um ab sofort bei Horsebrands shoppen zu können, bestätige deine Anmeldung bitte indem du auf den folgenden Button klickst und schon kann‘s los gehen!
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="one-column">
      <table width="100%">
        <tr>
          <td class="inner contents">
            <a class="button full-width gold" href="{{store url="customer/account/confirm/" _query_id=$customer.id _query_key=$customer.confirmation _query_back_url=$back_url}}">
              <span>Registrierung bestätigen</span>
            </a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="one-column">
      <table width="100%">
        <tr>
          <td class="inner contents">
            <p>Liebe Grüße,<br />dein Horsebrands-Team
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
{{template config_path="design/email/footer"}}
EOF;

$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setTemplateCode($confirmationame);
$emailTemplate->setTemplateText($content);
$emailTemplate->setTemplateSubject("Bestätige Dein Kundenkonto bei Horsebrands");
// Template Type 2 = HTML EMail
$emailTemplate->setTemplateType(2);
$emailTemplate->save();


/* ************ */
/* Config Setup */
/* ************ */
$configSwitch = new Mage_Core_Model_Config();
$scope = 'default';
$scopeId = 0;

// Set Order New
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($confirmationame);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('customer/create_account/email_confirmation_template', $emailTemplate->getId(), $scope, $scopeId);
}

$installer->endSetup();

?>
