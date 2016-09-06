$(document).ready(function(){
    $('.aboutPlugin').click(function(){
        var content = $(this).next('span').html();
        TINY.box.show({html:content});
    });
});