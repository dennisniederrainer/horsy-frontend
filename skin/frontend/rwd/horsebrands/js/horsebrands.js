
// header navigation: redirect only on mobile devices/sizes
var redirectOnMobile = function(url) {
  if ( Modernizr.mq('(max-width: 767px)') ) {
    window.location.href = url;
  }
}

var showDealsLogin = function() {
  $j('#deals-modal').removeClass('hide');
}

var hideDealsLogin = function() {
  $j('#deals-modal').addClass('hide');
}

var toggleDealsLogin = function(that) {
  if($j(that).is('#switch-signup') && !$j(that).hasClass('active')) {
    $j('#signup').removeClass('hide');
    $j('#login').addClass('hide');
    $j('#forgotpassword').addClass('hide');
    $j('#switch-signup').addClass('active');
    $j('#switch-login').removeClass('active');

    if(!$j('#reg-step-two').hasClass('hide')) {
      $j('#deals-modal_hide').addClass('hide');
    }
  }

  if($j(that).is('#switch-login') && !$j(that).hasClass('active')) {
    $j('#login').removeClass('hide');
    $j('#deals-modal_hide').removeClass('hide');
    $j('#signup').addClass('hide');
    $j('#forgotpassword').addClass('hide');
    $j('#switch-signup').removeClass('active');
    $j('#switch-login').addClass('active');
  }
};

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
