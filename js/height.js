$(document).ready(function () {
     var maxHeight = -1;

     $('.reviews__slide ').each(function () {
         maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
     });

     $('.reviews__slide ').each(function () {
         $(this).height(maxHeight);
     });
 });
