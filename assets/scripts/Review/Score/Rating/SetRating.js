import {URLInfo} from "../../../URL/URLInfo";

$('.review-rating-buttons button').on('click', function(){
    let url = new URLInfo();
    let buttons = $(this).parent()
    let value = $(this).html()

    $.ajax({
        url: Routing.generate('review_set_rating', {'id': url.getReviewID()}),
        type: 'post',
        data: {
            'value': value
        },
        dataType: 'json',
        beforeSend: function(){
            $(buttons).children().attr('disabled', 'disabled')
        },
        success: function(res){
            if(!res.add){
                $(buttons).removeClass('appreciated')
                $(buttons).children()
                    .removeClass('btn-success')
                    .addClass('btn-secondary')
            } else {
                $(buttons).addClass('appreciated')
                for(let i=1; i<=value; i++){
                    $('.review-rating-button-' + i)
                        .addClass('btn-success')
                        .removeClass('btn-secondary')
                }
            }
        },
        complete: function(){
            $(buttons).children().removeAttr('disabled')
        }
    })
})