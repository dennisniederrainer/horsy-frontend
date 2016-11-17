<?php

$installer = $this;
$installer->startSetup();

// Variables
$headername = 'Horsebrands | Header';
$footername = 'Horsebrands | Footer';
$newordername = 'Horsebrands | Bestellbestätigung';

// otherwise script inserts the $order and $shipment value which were ''
$order = '$order';

/* ************************ */
/* Start Setup Email HEADER */
/* ************************ */

$emailTemplate = Mage::getModel('core/email_template')->loadByCode($headername);

if($emailTemplate->getId())
  $emailTemplate->delete();

$content = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!--[if (gte mso 9)|(IE)]>
    <style type="text/css">
    table {border-collapse: collapse;}
  </style>
  <![endif]-->
  </head>
  <body>
    {{var non_inline_styles}}
    <center class="wrapper">
      <div class="webkit">
        <!--[if (gte mso 9)|(IE)]>
        <table width="600" align="center">
        <tr>
        <td>
        <![endif]-->
        <table class="outer" align="center">
          <!-- Header -->
          <tr>
            <td class="one-column">
              <table width="100%">
                <tr>
                  <td class="inner header" style="border-bottom: 1px solid #1d1d1d;">
                    <div><a href="http://www.horsebrands.de/"><img src="{{var logo_url}}" alt="{{var logo_alt}}" class="email-img"/></a></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
EOF;

$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setTemplateCode($headername);
$emailTemplate->setTemplateText($content);
$emailTemplate->setTemplateSubject('Header');
// Template Type 2 = HTML EMail
$emailTemplate->setTemplateType(2);
$emailTemplate->save();

/* ************************ */
/* Start Setup Email FOOTER */
/* ************************ */

$emailTemplate = Mage::getModel('core/email_template')->loadByCode($footername);

if($emailTemplate->getId())
  $emailTemplate->delete();

$content = <<<EOF
<!-- Footer -->
<tr>
  <td class="one-column">
    <table width="100%">
      <tr>
        <td class="inner contents">
          <div class="footer">
            <div class="support">
              <p class="bold title">
                Hast Du Fragen zu Deiner Bestellung? Wir helfen Dir gerne weiter:
              </p>
              <p class="justify">
                Email: <a href="mailto:service@horsebrands.de">service@horsebrands.de</a> | Kostenlose Hotline: <span class="telephone nobreak">+49 (0)251 / 67 44 85 55</span>
              </p>
            </div>
            <div class="legal">
              <p>
                <a href="http://www.horsebrands.de/" class="home">www.horsebrands.de</a>
              </p>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
</div>
</center>
</body>
</html>
EOF;

$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setTemplateCode($footername);
$emailTemplate->setTemplateText($content);
$emailTemplate->setTemplateSubject('Footer');
// Template Type 2 = HTML EMail
$emailTemplate->setTemplateType(2);
$emailTemplate->save();

/* *************************** */
/* Start Setup Email ORDER_NEW */
/* *************************** */

$emailTemplate = Mage::getModel('core/email_template')->loadByCode($newordername);

if($emailTemplate->getId())
  $emailTemplate->delete();

$content = <<<EOF
{{template config_path="design/email/header"}}
{{inlinecss file="hb-email-inline.css"}}

  <!-- Anschreiben -->
  <tr>
    <td class="one-column hello">
      <table width="100%">
        <tr>
          <td class="inner contents">
            <p class="title">
              Hallo {{htmlescape var=$order.getCustomerName()}},
            </p>
            <p>
              herzlichen Dank für Deine Bestellung. Wir werden Dich benachrichtigen, sobald Deine Artikel versandt wurden. Bitte beachten: Bei Vorkasse wird Deine Bestellung erst nach erfolgtem Zahlungseingang versendet.
            </p>
            <p>
              Hier findest Du unsere <a href="http://www.horsebrands.de/agb">Allgemeinen Geschäftsbedingungen (AGB)</a>.
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- Bestellung Start -->
  <tr>
    <td class="one-column">
      <table width="100%">
        <tr>
          <td class="inner contents">
            <h2 class="order-title">
              Hier Deine Bestellung in der &Uuml;bersicht
            </h2>
            <dl class="order-details-data">
              <dt>Datum:</dt>
              <dd>{{var order.getCreatedAtFormated('short')}}</dd>
              <dt>Bestellnummer:</dt>
              <dd><span class="no-link">{{var order.increment_id}}</span></dd>
            </dl>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <!-- Addresses -->
  <tr>
    <td class="two-column">
      <!--[if (gte mso 9)|(IE)]>
      <table width="100%">
      <tr>
      <td width="50%" valign="top">
      <![endif]-->
      <div class="column">
        <table width="100%">
          <tr>
            <td class="inner">
              <table class="contents">
                <tr>
                  <td>
                    <h2 class="order-title thin-border">Rechnungsadresse</h2>
                  </td>
                </tr>
                <tr>
                  <td>
                    {{var order.getBillingAddress().format('html')}}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
      <!--[if (gte mso 9)|(IE)]>
      </td><td width="50%" valign="top">
      <![endif]-->
      <div class="column">
        <table width="100%">
          <tr>
            <td class="inner">
              <table class="contents">
                <tr>
                  <td>
                    <h2 class="order-title thin-border">Versandadresse</h2>
                  </td>
                </tr>
                <tr>
                  <td>
                    {{var order.getShippingAddress().format('html')}}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
      <!--[if (gte mso 9)|(IE)]>
          </td>
        </tr>
      </table>
      <![endif]-->
    </td>
  </tr>
  <!-- Delivery and Payment -->
  <tr>
  <td class="two-column">
    <!--[if (gte mso 9)|(IE)]>
    <table width="100%">
    <tr>
    <td width="50%" valign="top">
    <![endif]-->
    <div class="column">
      <table width="100%">
        <tr>
          <td class="inner">
            <table class="contents">
              <tr>
                <td>
                  <h2 class="order-title thin-border">Versandart</h2>
                </td>
              </tr>
        <tr>
          <td>
            {{var order.shipping_description}}
          </td>
        </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
    <!--[if (gte mso 9)|(IE)]>
    </td><td width="50%" valign="top">
    <![endif]-->
    <div class="column">
      <table width="100%">
        <tr>
          <td class="inner">
            <table class="contents">
              <tr>
                <td>
                  <h2 class="order-title thin-border">Zahlungsart</h2>
                </td>
              </tr>
              <tr>
                <td>
                  {{var payment_html}}
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
    <!--[if (gte mso 9)|(IE)]>
    </td>
    </tr>
  </table>
  <![endif]-->
  </td>
  </tr>
  {{layout handle="sales_email_order_items" order=$order}}
{{template config_path="design/email/footer"}}
EOF;

$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setTemplateCode($newordername);
$emailTemplate->setTemplateText($content);
$emailTemplate->setTemplateSubject("Danke für deine Bestellung {{var order.incremend_id}}  bei Horsebrands");
// Template Type 2 = HTML EMail
$emailTemplate->setTemplateType(2);
$emailTemplate->save();


/* ************ */
/* Config Setup */
/* ************ */
$configSwitch = new Mage_Core_Model_Config();
$scope = 'default';
$scopeId = 0;

// Set Header
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($headername);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('design/email/header', $emailTemplate->getId(), $scope, $scopeId);
}

// Set Footer
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($footername);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('design/email/footer', $emailTemplate->getId(), $scope, $scopeId);
}

// Set non-inline css (here: same as inline)
$configSwitch->saveConfig('design/email/css_non_inline', 'hb-email-non-inline.css', $scope, $scopeId);

// Set Order New
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($newordername);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('sales_email/order/template', $emailTemplate->getId(), $scope, $scopeId);
  $configSwitch->saveConfig('sales_email/order/guest_template', $emailTemplate->getId(), $scope, $scopeId);
}

// Set Payment Custom Text
$paymenttext = 'Bitte überweisen Sie den fälligen Rechnungsbetrag auf unser Konto: Horsebrands GmbH, IBAN DE652003000020015620511, BIC HYVEDEMM300. Die Ware liefern wir nach Zahlungseingang.';
$configSwitch->saveConfig('payment/bankpayment/customtext', $paymenttext, $scope, $scopeId);

?>
