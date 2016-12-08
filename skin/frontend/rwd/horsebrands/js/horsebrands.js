
// header navigation: redirect only on mobile devices/sizes
var redirectOnMobile = function(url) {
  if ( Modernizr.mq('(max-width: 767px)') ) {
    window.location.href = url;
  }
}

jQuery(document).ready(function($) {
  $(function() {
    $('.toggle-nav').click(function() {
      toggleNav();
    });
  });

  function toggleNav() {
    if (jQuery('#site-wrapper').hasClass('show-nav')) {
      jQuery('#site-wrapper').removeClass('show-nav');
    } else {
      jQuery('#site-wrapper').addClass('show-nav');
    }
  }
});

var updateCoupon = function(requestid) {
	if(requestid == 2) $('coupon_code').setValue('');

	$('discount-coupon-form').request({
		method: 'post',
		onComplete: payment.onComplete,
		onSuccess: payment.onSave,
		onFailure: checkout.ajaxFailure.bind(checkout),
	});
}

var proceedToCheckout = function(multishippingUrl, singleshippingUrl) {
  // if(document.getElementById('enable-splitshipping').checked) {
    window.location= singleshippingUrl;
  // } else {
  //   window.location= multishippingUrl;
  // }
}
