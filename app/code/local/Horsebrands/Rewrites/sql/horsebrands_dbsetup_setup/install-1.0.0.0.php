<?php

$installer = $this;
$installer->startSetup();

// Variables
$headername = 'Grafenfels Header';
$footername = 'Grafenfels Footer';
$newordername = 'Grafenfels Bestellbestätigung';
$shippmentname = 'Grafenfels Versandbestätigung';

// otherwise script inserts the $order and $shipment value which were ''
$order = '$order';
$shipment = '$shipment';

/* ************************ */
/* Start Setup Email HEADER */
/* ************************ */

$emailTemplate = Mage::getModel('core/email_template')->loadByCode($headername);

if($emailTemplate->getId())
  $emailTemplate->delete();

$content = <<<EOF
<!--@subject Email - Header @-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--[if !mso]><!-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" type="text/css" href="stylesheets/styles.css" />
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
                <td class="inner header">
                  <div><a href="https://www.grafenfels.de/de/">
                    <img
                      src="{{var logo_url}}"
                      alt="{{var logo_alt}}"
                      class="email-img"
                     />
                  </a></div>
                </td>
              </tr>
                <tr>
                  <td class="inner contents header">
                    <div><a href="https://www.grafenfels.de/de/prinzip">Prinzip</a></div>
                    <div class="talign-left"><a href="https://www.grafenfels.de/de/manufaktur">Manufaktur</a></div>
                    <div><a href="https://www.grafenfels.de/de/erleben">Erleben</a></div>
                    <div><a href="https://www.grafenfels.de/de/shop">Shop</a></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <!-- Begin Content -->
EOF;

$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setTemplateCode($headername);
$emailTemplate->setTemplateText($content);
$emailTemplate->setTemplateSubject('Email - Header');
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
<!--@subject Email - Footer @-->
  <!-- End Content -->
  <!-- Footer -->
  <tr>
    <td class="one-column">
      <table width="100%">
        <tr>
          <td class="inner contents">
            <div class="footer">
              <div class="support">
                <p class="bold">
                  Haben Sie Fragen zu Ihrer Bestellung? Wir helfen Ihnen gerne weiter:
                </p>
                <p class="justify">
                  Email: <a href="mailto:hallo@grafenfels.de">hallo@grafenfels.de</a> | Pers&ouml;nliche Beratung: <span class="telephone nobreak">+49 (0)30 239 36 105</span>
                </p>
              </div>
              <div class="teaser">
                <h3>Enter Your Comfort Zone</h3>
              </div>
              <div class="social">
                <a href="https://www.facebook.com/grafenfels">
                  <img src="{{skin url='img_grafenfels/facebook.svg'}}" class="social-link" alt="facebook" />
                </a>
                <a href="https://www.instagram.com/grafenfels/">
                  <img src="{{skin url='img_grafenfels/instagram.svg'}}" class="social-link" alt="instagram" />
                </a>
                <a href="https://de.pinterest.com/grafenfels/">
                  <img src="{{skin url='img_grafenfels/pinterest.svg'}}" class="social-link" alt="pinterest" />
                </a>
              </div>
              <div class="legal">
                <p class="company-adress">
                  Grafenfels Manufaktur GmbH | Kurfürstendamm 196 | 10707 Berlin
                </p>
                <p>
                  Geschäftsführer Andreas van Bon, Stefan Müller<br/>
                  Amtsgericht Berlin-Charlottenburg | HRB 168 779 B | Ust-IdNr. DE 301689241
                </p>
                <p>
                  <a href="https://www.grafenfels.de/de/" class="home">www.grafenfels.de</a>
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
$emailTemplate->setTemplateSubject('Email - Footer');
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
{{inlinecss file="gf-emails/gf-email-inline.css"}}

    <!-- Bild -->
    <tr>
      <td class="one-column full-width">
        <table width="100%">
          <tr>
            <td class="inner contents">
              <img src="{{skin url='img_grafenfels/GF_Danke_Mail.png'}}" alt="Vielen Dank f&uuml;r Ihre Bestellung." class="email-img">
            </td>
          </tr>
        </table>
      </td>
    </tr>
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
                herzlichen Dank für Ihre Bestellung bei Grafenfels.
              </p>
              {{block type='core/template' area='frontend' template='grafenfels/email/order/paymentText.phtml' order=$order}}
              <p>
                Hier finden Sie unsere <a href="http://grafenfels.de/media/agb.pdf">Allgemeinen Geschäftsbedingungen (AGB)</a>.
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
                Hier Ihre Bestellung in der &Uuml;bersicht
              </h2>
              <dl class="order-details-data">
                <dt>
                  Datum:
                </dt>
                <dd>
                  {{var order.getCreatedAtFormated('short')}}
                </dd>
                <dt>
                  Bestellnummer:
                </dt>
                <dd>
                  <span class="no-link">{{var order.increment_id}}</span>
                </dd>
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
                      DHL <!-- {{var order.shipping_description }} -->
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
$emailTemplate->setTemplateSubject("Ihre Bestellung {{var order.increment_id}} vom {{var order.getCreatedAtFormated('short')}}");
// Template Type 2 = HTML EMail
$emailTemplate->setTemplateType(2);
$emailTemplate->save();

/* *************************** */
/* Start Setup Email ORDER_NEW */
/* *************************** */

$emailTemplate = Mage::getModel('core/email_template')->loadByCode($shipmentname);

if($emailTemplate->getId())
  $emailTemplate->delete();

$content = <<<EOF
<!--@subject Versandbest&auml;tigung: Ihre Bestellung ist auf dem Weg @-->
<!--@vars
{"store url=\"\"":"Store Url",
"var logo_url":"Email Logo Image Url",
"var logo_alt":"Email Logo Image Alt",
"htmlescape var=$order.getCustomerName()":"Customer Name",
"var store.getFrontendName()":"Store Name",
"store url=\"customer/account/\"":"Customer Account Url",
"var shipment.increment_id":"Shipment Id",
"var order.increment_id":"Order Id",
"var order.billing_address.format('html')":"Billing Address",
"var payment_html":"Payment Details",
"var order.shipping_address.format('html')":"Shipping Address",
"var order.shipping_description":"Shipping Description",
"layout handle=\"sales_email_order_shipment_items\" shipment=$shipment order=$order":"Shipment Items Grid",
"block type='core/template' area='frontend' template='email/order/shipment/track.phtml' shipment=$shipment order=$order":"Shipment Track Details",
"var comment":"Shipment Comment"}
@-->

{{template config_path="design/email/header"}}
{{inlinecss file="gf-emails/gf-email-inline.css"}}

    <!-- Bild -->
    <tr>
      <td class="one-column full-width">
        <table width="100%">
          <tr>
            <td class="inner contents">
              <img src="{{skin url='img_grafenfels/GF_Versand_Mail.png'}}" alt="Ihre Bestellung ist auf dem Weg" class="email-img" />
            </td>
          </tr>
        </table>
      </td>
    </tr>
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
                Ihre Bestellung {{var order.increment_id}} vom {{var order.getCreatedAtFormated('short')}} befindet sich auf dem Weg zu Ihnen. Im Anhang dieser E-Mail finden Sie außerdem Ihre Rechnung als PDF-Dokument für Ihre Unterlagen.
              </p>
              <p>
                Um den Anhang öffnen zu können, benötigen Sie den Adobe Acrobat Reader. Sollten Sie diese Software nicht installiert haben, können Sie dies kostenlos unter folgendem Link tun: <a href="http://get.adobe.com/de/reader/">http://get.adobe.com/de/reader/</a>
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <!-- Tracking -->
    <tr>
      <td class="one-column">
        <table width="100%">
          <tr>
            <td class="inner contents">
              {{block type='core/template' area='frontend' template='grafenfels/email/order/shipment/track.phtml' shipment=$shipment order=$order}}
              <!-- <h2 class="order-title">DHL-Sendungsnummer</h2>
              <p>
                Ihre Bestellung {{var order.increment_id}} vom {{var order.getCreatedAtFormated('short')}} befindet sich auf dem Weg zu Ihnen. Im Anhang dieser E-Mail finden Sie außerdem Ihre Rechnung als PDF-Dokument für Ihre Unterlagen.
              </p> -->
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
    <!-- Shipping Addresses -->
    <tr>
      <td class="one-column">
        <table width="100%">
          <tr>
            <td class="inner contents">
              <h2 class="order-title thin-border">Versandadresse</h2>
              <p>
                {{var order.shipping_address.format('html')}}
              </p>
            </td>
          </tr>
        </table>
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
                      DHL <!-- {{var order.shipping_description }} -->
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
                      {{if order.getPayment().getMethod()=='bankpayment'}}
                        Vorkasse
                      {{else}}
                        {{var payment_html}}
                      {{/if}}
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
    {{if comment}}
    <tr>
      <td class="one-column">
        <table width="100%">
          <tr>
            <td class="inner contents">
              <h2 class="order-title thin-border">Kommentar</h2>
              <p>
                {{var comment}}
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    {{/if}}

    {{layout handle="sales_email_order_shipment_items" shipment=$shipment order=$order}}

{{template config_path="design/email/footer"}}
EOF;

$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setTemplateCode($shippmentname);
$emailTemplate->setTemplateText($content);
$emailTemplate->setTemplateSubject('Versandbestätigung: Ihre Bestellung ist auf dem Weg');
// Template Type 2 = HTML EMail
$emailTemplate->setTemplateType(2);
$emailTemplate->save();


/* ************ */
/* Config Setup */
/* ************ */
$configSwitch = new Mage_Core_Model_Config();

$scopeId = Mage::getModel('core/website')->load('de');
if($scopeId && $scopeId > 0) {
  $scope = 'websites';
} else {
  $scope = 'default';
  $scopeId = 0;
}

// Set GF Header
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($headername);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('design/email/header', $emailTemplate->getId(), $scope, $scopeId);
}

// Set GF Footer
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($footername);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('design/email/footer', $emailTemplate->getId(), $scope, $scopeId);
}

// Set non-inline css (here: same as inline)
$configSwitch->saveConfig('design/email/css_non_inline', 'gf-emails/gf-email-inline.css', $scope, $scopeId);

// Set GF Order New
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($newordername);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('sales_email/order/template', $emailTemplate->getId(), $scope, $scopeId);
  $configSwitch->saveConfig('sales_email/order/guest_template', $emailTemplate->getId(), $scope, $scopeId);
}

// Set GF Shipping
$emailTemplate = Mage::getModel('core/email_template')->loadByCode($shipmentname);
if($emailTemplate->getId()) {
  $configSwitch->saveConfig('sales_email/shipment/template', $emailTemplate->getId(), $scope, $scopeId);
  $configSwitch->saveConfig('sales_email/shipment/guest_template', $emailTemplate->getId(), $scope, $scopeId);
}

// Set Payment Custom Text
$configSwitch->saveConfig('payment/bankpayment/customtext', 'Geben Sie bitte als Verwendungszweck unbedingt Ihre Bestellnummer an, so können wir Ihre Zahlung schneller zuordnen.
', $scope, $scopeId);

// Set Tax Configuration
$configSwitch->saveConfig('tax/sales_display/grandtotal', 0, $scope, $scopeId);
$configSwitch->saveConfig('tax/sales_display/full_summary', 0, $scope, $scopeId);
$configSwitch->saveConfig('tax/sales_display/zero_tax', 1, $scope, $scopeId);
$configSwitch->saveConfig('tax/sales_display/no_sum_on_details', 0, $scope, $scopeId);
$configSwitch->saveConfig('tax/sales_display/hide_grandtotal_excl_tax', 0, $scope, $scopeId);

?>
