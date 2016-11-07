Event.observe(window, 'load', function () {

    if (payment.toggleOpsDirectDebitInputs) {
        payment.toggleOpsDirectDebitInputs = payment.toggleOpsDirectDebitInputs.wrap(function (parentMethod, country) {
          var accountno = 'ops_directdebit_account_no';
          var bankcode = 'ops_directdebit_bank_code';
          var bic = 'ops_directdebit_bic';
          var iban = 'ops_directdebit_iban';
          var showInput = function (id) {
              $$('#' + id)[0].up().show();
              if (!$(id).hasClassName('required-entry') && id != 'ops_directdebit_bic' && $('ops_directdebit_iban').value == '') {
                  $(id).addClassName('required-entry');
              }
          };
          var hideInput = function (id) {
              $$('#' + id)[0].up().hide();
              $(id).removeClassName('required-entry');
          };
          // if ('NL' == country) {
          //     hideInput(bankcode);
          //     showInput(bic);
          //     showInput(iban);
          // }
          if ('DE' == country || 'AT' == country || 'NL' == country) {
            showInput(iban);
            showInput(bic);
            hideInput(bankcode);
            hideInput(accountno);
          }

          if ('DE' == country) {
            $(bic).removeClassName('required-entry');
            $(bic).setAttribute('placeholder','optional...');
          } else {
            $(bic).addClassName('required-entry');
            $(bic).setAttribute('placeholder','');
          }
          // if ('AT' == country) {
          //     hideInput(iban)
          // }
        });
    }
});
