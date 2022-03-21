$(document).on({
    mouseenter: function(){
        let buttons = $(this).parent()
        let val = $(this).html()
        $(buttons).find('button').each((i, e) => {
            $(e).removeClass('btn-primary').addClass('btn-secondary')
        })
        for(i=1; i<=val; i++){
            $('.review-rating-button-' + i)
                .addClass('btn-primary')
                .removeClass('btn-secondary')
        }
    },
    mouseleave: function(){
        $(this).parent().find('button')
            .removeClass('btn-primary')
            .addClass('btn-secondary')
    }
}, '.review-rating-buttons:not(.appreciated) button')