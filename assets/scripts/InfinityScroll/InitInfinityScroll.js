let lastId = {};
let xhr = {};
let isGeneration = {};
let isEnd = {};
let page = {};

function ajaxGenerate(type, param = -1){
    if(isGeneration[type]) {
        return;
    }
    isGeneration[type] = true;

    let data = {
        'param': param,
        'lastId': lastId[type],
        'page': page[type],
    };

    xhr[type] = $.ajax({
        url: Routing.generate(type + '_page', {'_locale': $('html').attr('lang')}),
        type: 'post',
        dataType: 'json',
        data: data,
        beforeSend: function () {
            $('#generation-status-' + type).removeClass('d-none');
        },
        success: function (res) {
            isEnd[type] = res.isEnd;
            $('.scrolling-block').append(res.html.content);
            page[type]++;
        },
        complete: function () {
            $('#generation-status-' + type).addClass('d-none');
            isGeneration[type] = false;
        }
    });
}

$('.scrolling-block').each((i, e) => {
    let type = $(e).data('scrolling-type');
    let param = $(e).data('scrolling-param');
    $(e).after('<div class="my-2 d-flex justify-content-center">'
        + '<div class="spinner-border text-primary" id="generation-status-'
        + type
        + '"><span class="sr-only"></span></div></div>');
    lastId[type] = $(e).data('scrolling-last-id');
    isEnd[type] = false;
    isGeneration[type] = false;
    page[type] = 1;
    ajaxGenerate(type, param);
})

$(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() >= $(document).height() - 10) {
        $('.scrolling-block').each((i, e) => {
            let type = $(e).data('scrolling-type');
            let param = $(e).data('scrolling-param');
            if (isEnd[type] || isGeneration[type]) {
                return;
            }
            ajaxGenerate(type, param);
        })
    }
})