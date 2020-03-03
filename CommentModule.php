<?php

class CommentModule extends Module {
    
    protected $id = 'minicore-comment';    
    
    public function __construct(Framework $framework) {
        parent::__construct($framework);
        $framework->add([
            'comments' => 'Comments',
            'commentTopics' => 'CommentTopics',
            'commentService' => 'CommentService',
        ]);
    }
    
    public function init() {
        parent::init();
        /** @var Translation $translation */
        $translation = $this->framework->get('translation');
        $translation->add('comment', 'modules/minicore-comment/translations');
        /** @var View $view */
        $view = $this->framework->get('view');
        $view->addFolder(':comment', 'modules/minicore-comment/templates');        
    }
    
}