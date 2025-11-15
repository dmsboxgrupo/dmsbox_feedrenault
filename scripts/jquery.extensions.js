$.fn.isInViewport = function() {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();

    var viewportTop = $(window).scrollTop();
    var middle = viewportTop + ($(window).height() / 2);

    var tolerance = 120; // Metade da altura minima de cabecalhos/texto

    return (elementBottom + tolerance) > middle && (elementTop - tolerance) < middle;
};