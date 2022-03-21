jQuery(function(){
    /*>>>>>>>>>>> tags >>>>>>>>>>>>*/
    function getTagInput(name) {
        var item = $('.tags :input').filter(function() {
            return this.value == name
        })
        return item
    }
    function creatTag(name) {
        var list = $('.tags')
        var index = +$(list).attr('data-index')
        var prototype = $(list).attr('data-prototype').replace(/__name__/g, index)

        var item = $('<fieldset></fieldset>').html(prototype)
        $(item).find('input').val(name)
        $(list).append($(item))
        $(list).attr('data-index', index + 1)
    }
    function removeTag(name) {
        var input = getTagInput(name)
        var listItem = $(input).closest('fieldset')
        $(listItem).remove()
    }

    var s2 = $(".tags-input").select2({
        tags: true,
        theme: 'bootstrap-5',
        multiple: true,
        tokenSeparators: [',', ' '], 
        width: '100%',
    }).on('select2:select', e => {
        var data = e.params.data
        var input = $('.tags-input')
        var options = []
        $(input).find("option").each(function(){
            if($(this).text() == data.text){
                options.push(this)
            }
        })
        var searchingElement = $(options).first()
        data.id = +($(searchingElement).val())
        if (options.length == 2)
        {
            $(searchingElement).remove()
        } 
        if($(getTagInput(e.params.data.text)).length)
            return false
        creatTag(e.params.data.text)
        return false
    }).on('select2:unselect', e => {
        removeTag(e.params.data.text)
        return false
    })

    var tags = $('.tags :input').map((i,e) => {
        return $('.tags-input option')
            .filter(function() {
                return this.text == $(e).val()
            })
            .val()
    })
    s2.val(tags).trigger('change')
    /*<<<<<<<<<<< tags <<<<<<<<<<<<*/

    /*>>>>>>>>>>> review edit >>>>>>>>>>>>*/
    $('.review-create-button').click(function() {
        $(".tags-input").val('')
        $('.file-uploader').val('')
        saveImages = false
        for(var illustration of illustrations) {
            createIllustration(illustration)
        }
    })

    $('.review-remove-button').on('click', function(){
        var text = $(this).attr('message-text')
        return confirm(text)
    })
    /*<<<<<<<<<<< review edit <<<<<<<<<<<<*/

    /*>>>>>>>>>>> illustrations >>>>>>>>>>>>*/
    function createIllustration(name) {
        var list = $('.illustrations')
        var index = +$(list).attr('data-index')
        var prototype = $(list).attr('data-prototype').replace(/__name__/g, index)

        var item = $('<fieldset></fieldset>').html(prototype)
        $(item).find('input').val(name)
        $(list).append($(item))
        $(list).attr('data-index', index + 1)
    }
    function setIllustrations() {
        $('.illustrations :input').each(function() {
            illustrations.push($(this).val())
            $(this).closest('fieldset').remove()
        })
        $('.illustrations').attr('data-index', 0)
    }
    function getIllustrationsImages(){
        var imgs = []

        for(var illustration of illustrations) {
            imgs.push("<img src='" + cloudinaryPath + illustration + "' class='file-preview-image'>")
        }

        return imgs
    }
    function getIllustrationsConfigs(){
        var imgs = []

        for(var illustration of illustrations) {
            imgs.push({
                'fileId': illustration,
                'key': illustration,
            })
        }

        return imgs
    }

    var illustrations = []

    setIllustrations()
    var uploader = $('.file-uploader').fileinput({
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        initialPreview: getIllustrationsImages(),
        initialPreviewConfig: getIllustrationsConfigs(),
        language: getLocale(),
        maxFileSize:2000,
        overwriteInitial: false,
        showClose: false,
        showRemove: false,
        theme: 'bs5',
        uploadUrl: '/ajax/add_illustration',
        deleteUrl: '/ajax/remove_illustration',
        fileActionSettings: {
            showDrag: false,
        },
        resumableUploadOptions: {
            retainErrorHistory: false,
        },
    })
    .on('fileuploaded', function(event, previewId, index, fileId){
        var response = previewId.response
        var el = $('[id*="' + index + '"]')
        
        if(response.result) {
            $(el).attr('id', response.name)
            $(el).find('img').attr('src', cloudinaryPath + response.name)

            illustrations.push(response.name)
            saveImages = true
            return false
        }
    })
    .on('filesuccessremove', function(event, id){
        var uploader = $(this)
        
        $.ajax({
            url: '/ajax/remove_illustration',
            dataType: 'json',
            type: 'post',
            data: {
                'key': id
            },
            beforeSend: function(){
                $(uploader).fileinput('disable')
            },
            success: function(res){
                $('[id*="' + id + '"]').fadeOut(
                    300, 
                    function(){ 
                        $(this).remove()
                    }
                )
                illustrations.splice(illustrations.indexOf(ind), 1)
                saveImages = true
            },
            complete: function(){
                $(uploader).fileinput('enable')
            }
        })

        return false
    })
    .on('filedeleted', function(event, ind){
        illustrations.splice(illustrations.indexOf(ind), 1)
        saveImages = true
        return false
    })

    var saveImages = false
    window.onbeforeunload = function(){

        if(uploader.length && saveImages){

            saveImages = false

            if(/\/edit\//i.test(location)){
                $.ajax({
                    url: '/ajax/save-illustrations',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'reviewId': getReviewID(),
                        'illustrations': illustrations,
                    }
                })
            } else {
                $.ajax({
                    url: '/ajax/remove-temporary-illustrations',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'illustrations': illustrations,
                    }
                })
            }
        }
    }
    /*<<<<<<<<<<< illustrations <<<<<<<<<<<<*/
})