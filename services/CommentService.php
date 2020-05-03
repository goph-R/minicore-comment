<?php

class CommentService {
    
    /** @var Request */
    protected $request;
    
    /** @var UserSession */
    protected $userSession;
    
    /** @var CommentTopics */
    protected $topics;
    
    /** @var Comments */
    protected $comments;
    
    /** @var View */
    protected $view;
    
    /** @var UserService */
    protected $userService;
    
    protected $topic;
    protected $addUrl;    
    protected $returnUrl;
    protected $adminPermission;
    protected $redirectTo;
    
    public function __construct() {
        $framework = Framework::instance();
        $this->request = $framework->get('request');
        $this->userSession = $framework->get('userSession');
        $this->topics = $framework->get('commentTopics');
        $this->comments = $framework->get('comments');
        $this->view = $framework->get('view');
        $this->userService = $framework->get('userService');
    }
    
    public function init($topicName, $targetId, $addUrl, $returnUrl, $adminPermission=null) {
        $this->topic = $this->topics->findOrCreate($topicName, $targetId);
        $this->addUrl = $addUrl;
        $this->returnUrl = $returnUrl;
        $this->adminPermission = $adminPermission;
    }
    
    public function fetch($path=':comment/layout') {
        $user = $this->userService->getCurrentUser();
        $hasAdminRight = $user->hasPermission($this->adminPermission);
        $options = [
            'replyText' => text('comment', 'reply'),
            'cancelText' => text('comment', 'cancel'),
            'addUrl' => $this->addUrl
        ];
        $comments = $this->findAll([]);
        return $this->view->fetch($path, [
            'comments' => $comments,
            'service' => $this,
            'user' => $user,
            'moreComments' => false, // TODO
            'hasAdminRight' => $hasAdminRight,
            'options' => $options,
            'addUrl' => $this->addUrl
        ]);
    }
    
    public function findAll(array $filter) {
        if (!$this->topic) {
            throw new RuntimeException("No topic was set.");
        }
        $filter['topic_id'] = $this->topic->getId();
        $comments = $this->comments->findAll($filter);
        $parent = null;
        $result = [];
        foreach ($comments as $comment) {
            if (!$comment->getParentId()) {
                $parent = $comment;
                $result[] = $comment;
            } else {
                $parent->addChild($comment);
            }
        }
        return $result;
    }
    
    public function add() {
        if (!$this->topic) {
            throw new RuntimeException("No topic was set.");
        }
        $framework = Framework::instance();
        $comment = $framework->create('Comment');
        $comment->setTopicId($this->topic->getId());        
        $text = trim($this->request->get('text'));
        if (!$text) {
            $this->redirectTo = $this->returnUrl;
            return null;
        }
        $replyTo = $this->request->get('reply_to');
        $replyToComment = $this->findValid($replyTo);
        if ($replyToComment) {
            $comment->setReplyTo($replyTo);
            $comment->setParentId($replyToComment->getGroupId());
        }
        $comment->setCreatedBy($this->userSession->getId());
        $comment->setCreatedOn(date('Y-m-d H:i:s'));
        $comment->setText($text);
        $comment->save();
        $this->comments->saveGroupUpdatedOn($comment->getGroupId());
        $postfix = '#comment_'.$comment->getId();
        $this->redirectTo = $this->returnUrl.$postfix;
        return $comment;
    }
    
    public function redirect() {
        $framework = Framework::instance();
        $framework->redirect($this->redirectTo);
    }
    
    private function findValid($id) {
        if (!$id) {
            return false;
        }
        $comment = $this->comments->findById($id);
        if (!$comment || $comment->getTopicId() != $this->topic->getId()) {
            return false;
        }
        return $comment;
    }
    
}
