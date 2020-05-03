<?php

class CommentModule extends Module {
    
    protected $id = 'minicore-comment';    
    
    public function __construct() {
        $framework = Framework::instance();
        $framework->add([
            'comments' => 'Comments',
            'commentTopics' => 'CommentTopics',
            'commentService' => 'CommentService',
        ]);
    }
    
    public function init() {
        parent::init();
        $framework = Framework::instance();
        /** @var Translation $translation */
        $translation = $framework->get('translation');
        $translation->add('comment', 'modules/minicore-comment/translations');
        /** @var View $view */
        $view = $framework->get('view');
        $view->addFolder(':comment', 'modules/minicore-comment/templates');        
    }
    
}