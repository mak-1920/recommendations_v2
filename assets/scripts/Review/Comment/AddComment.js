import {URLInfo} from "../../URL/URLInfo";

let url = new URLInfo();

$('.add-comment').on('click', () => {
    let reviewId = url.getReviewID();
    $(this).attr('disabled', 'disabled');
    $.ajax({
        url: Routing.generate('comment_create'),
        type: 'post',
        data: {
            'text': $('.comment-text').val(),
            'reviewId': reviewId,
        },
        success: function(){
            $('.comment-text').val('');
        },
        complete: function(){
            $('.add-comment').removeAttr('disabled');
        },
    })
    return false;
})