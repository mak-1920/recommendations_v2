import {SetEventSource} from "../../Mercure/SetEventSource";
import {URLInfo} from "../../URL/URLInfo";

const template = $('#comment-block-template').html();
const url = new URLInfo();
const commentsBlock = $('.comments');
const lastId = commentsBlock.attr('data-scrolling-last-id');

SetEventSource('#comment-edit-listener', response => {
    let comment = JSON.parse(response.data);

    if(comment.comment.id > lastId) {
        addComment(comment);
    } else {
        removeComment(comment);
    }
})

function addComment(comment) {
    let commentBlock = initBlock(comment);
    commentsBlock.prepend(commentBlock);
}

function removeComment(comment) {
    $('.comment-' + comment.comment.id).remove();
}

function initBlock(comment) {
    let tpl = $(template).clone();
    return setInfo(comment, tpl);
}

function setInfo(comment, block) {
    let html = $(block).html();
    html = html.replace('id0', comment.author_id)
        .replace('___author_name___', comment.author_name)
        .replace('___author_likes___', comment.author_likes)
        .replace('___comment_id___', comment.comment.id)
        .replace('___comment_text___', comment.comment.text)
        .replace('___comment_time___', getCurrentTime(comment.comment.time))
    ;
    html = setRemoveSupport(comment, $(html));
    return html;
}

function getCurrentTime(timeISO) {
    let time = new Date(timeISO);
    return Intl.DateTimeFormat(url.getLocale(), {dateStyle: 'short', timeStyle: 'short'}).format(time);
}

function setRemoveSupport(comment, block) {
    let userId = parseInt($(block).find('[name=user-id]').val());
    let userAdmin = $(block).find('[name=user-admin]').val();

    if(userId === comment.author_id || userAdmin) {
        $(block).find('.user-info').remove();
    } else {
        $(block).find('.comment-remove-block').remove();
    }
    return block;
}
