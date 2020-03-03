<?php

class CommentService {
    
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
    protected $adminPermission;
    
    public function __construct(Framework $framework) {
        $this->userSession = $framework->get('userSession');
        $this->topics = $framework->get('commentTopics');
        $this->comments = $framework->get('comments');
        $this->view = $framework->get('view');
        $this->userService = $framework->get('userService');
        
    }
    
    public function add($topicName, $targetId) {
        $commentData = $this->request->get('comment');
        
        $topic = $this->topics->findOrCreate($topicName, $targetId);
        $comment = $this->framework->create('Comment');
        $comment->setTopicId($topic->getId());
        $comment->setParentId($parent->getId());
        $comment->setCreatedBy($this->userSession->getId());
        $comment->setCreatedOn(date('Y-m-d H:i:s'));
        $comment->setText($text);
        $comment->save();
        return $comment;
    }
    
    public function setTopic($topicName, $targetId, $addUrl, $adminPermission=null) {
        $this->topic = $this->topics->findByNameAndTargetId($topicName, $targetId);
        $this->addUrl = $addUrl;
        $this->adminPermission = $adminPermission;
    }
    
    public function findAll(array $filter) {
        if (!$this->topic) {
            return [];
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
    
    public function fetch($path=':comment/list') {
        $comments = $this->findAll([]);
        return $this->view->fetch($path, [
            'comments' => $comments,
            'service' => $this,
            'addUrl' => $this->addUrl,
            'userService' => $this->userService,
            'moreComments' => false,
            'adminPermission' => $this->adminPermission
        ]);
    }
    
}
