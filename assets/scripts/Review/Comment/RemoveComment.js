$('.comments').on('click', '.comment-remove-button', function(e, b) {
    let id = $(this).attr('data-comment-id');
    let comment = $(this).closest('.comment');
    let button = $(this);

    $.ajax({
        url: Routing.generate('comment_remove'),
        type: 'post',
        dataType: 'json',
        data: {
            'id': id,
        },
        beforeSend: function(){
            $(button).addClass('d-none');
        },
        success: function(res){
            if(res.result){
                $(comment).remove();
            }
        },
        complete: function(){
            if($(comment).length){
                $(button).removeClass('d-none');
            }
        }
    })
})