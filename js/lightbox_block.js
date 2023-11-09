(function ($, Drupal) {
  Drupal.behaviors.lightboxBlock = {
    attach: function (context, settings) {
      $hideLightbox = sessionStorage.getItem('hideLightbox_Block');
      if ($hideLightbox) {
        $(".bg").hide();
      }
      else {
        $(".bg").show();
      }
      $(".dismiss-this").click(function () {
        $(".bg").hide();
        sessionStorage.setItem('hideLightbox_Block', true);
      });
    }
  };
})(jQuery, Drupal);
