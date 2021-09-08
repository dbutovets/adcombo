$('a[href^="#"]').click(function () {
    var elementClick = $(this).attr("href");
    var destination = $(elementClick).offset().top;
    jQuery("html:not(:animated), body:not(:animated)").animate({
        scrollTop: destination
    }, 800);
    return false;
});

$('.rev').slick({
    infinite: true,
    dots: true,
    arrows: true,
    autoplay: true,
    autoplaySpeed: 3000,
    prevArrow: $('.prev-slide'),
    nextArrow: $('.next-slide'),
    slidesToShow: 1, //сколько слайдов показывать в карусели
    slidesToScroll: 1
});
