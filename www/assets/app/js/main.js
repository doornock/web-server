$(function(){
    $('.qrUrlPopOver').each(function () {
        var createElement = document.createElement('div');
        new QRCode(createElement, $(this).data('qr-url'));
        $(this).popover({
            'content': createElement,
            'placement': 'left',
            'html': true
        });
    });
});
