const CommentService = new function() {

    let addUrl;
    let replyText;
    let cancelText;
    let createReplyFormCallback;

    this.init = function(options) {
        const replyLinks = document.querySelectorAll('.comment-reply-link');
        const postCommentButton = document.getElementById('post_comment_button');
        const postCommentTextarea = document.getElementById('post_comment_textarea');
        addUrl = options.addUrl;
        replyText = options.replyText || 'Reply';
        cancelText = options.cancelText || 'Cancel';
        createReplyFormCallback = options.createReplyFormCallback || CommentService.createReplyForm;
        replyLinks.forEach(function(e) {
            e.addEventListener('click', CommentService.showReplyForm);
        });
    };        
    
    this.removeReplyFormContainers = function() {
        const containers = document.querySelectorAll('.reply-form-container');
        containers.forEach(function (element) {
            element.parentNode.removeChild(element);
        });
    };

    this.showReplyForm = function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-comment-id');
        const actionsContainer = document.querySelector('#comment_' + id + ' .comment-actions');        
        if (document.getElementById('reply_form_container_' + id)) {
            return;
        }
        actionsContainer.after(createReplyFormCallback(id));
    };

    this.createReplyForm = function(id) {
        const form = document.createElement('form');
        const textarea = document.createElement('textarea');
        const hidden = document.createElement('input');
        const submit = document.createElement('button');
        const cancel = document.createElement('button');        
        CommentService.removeReplyFormContainers();
        textarea.setAttribute('name', 'text');
        textarea.classList.add('textarea');
        hidden.setAttribute('name', 'reply_to');
        hidden.setAttribute('type', 'hidden');
        hidden.value = id;
        submit.innerText = replyText;
        submit.classList.add('button');
        submit.classList.add('is-primary');
        submit.setAttribute('type', 'submit');
        cancel.classList.add('button');
        cancel.innerText = cancelText;
        cancel.addEventListener('click', CommentService.removeReplyFormContainers);
        form.classList.add('reply-form-container');
        form.setAttribute('id', 'reply_form_container_' + id);
        form.setAttribute('action', addUrl);
        form.setAttribute('method', 'POST');
        form.appendChild(hidden);
        form.appendChild(textarea);
        form.appendChild(submit);
        form.appendChild(cancel);        
        return form;
    };    
    
};


