
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
