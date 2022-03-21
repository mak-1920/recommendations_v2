import {URLInfo} from "../../../URL/URLInfo";

$('.review-like-button').click(function(){
    let url = new URLInfo();
    let button = $(this)

    $.ajax({
        url: Routing.generate('review_like', {'id': url.getReviewID()}),
        type: 'post',
        dataType: 'json',
        beforeSend: function(){
            $(button).attr('disabled', 'disabled')
        },
        success: function(res){
            if(res.result === 'add'){
                $(button)
                    .removeClass('btn-secondary')
                    .addClass('btn-success')
            } else if(res.result === 'remove') {
                $(button)
                    .addClass('btn-secondary')
                    .removeClass('btn-success')
            }
            $('.review-likes-count').html(res.likesCount)
        },
        complete: function(){
            $(button).removeAttr('disabled')
        }
    })
})