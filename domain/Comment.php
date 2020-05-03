<?php

class Comment extends Record {
    
    protected $tableName = 'comment';    
    protected $protectedList = ['userService', 'user', 'comments', 'children'];
    
    /** @var UserService */
    protected $userService;
    protected $user = null;
    
    /** @var Comments */
    protected $comments;
    protected $children = [];
    
    protected $id;
    protected $topic_id;
    protected $parent_id = null;
    protected $reply_to = null;
    protected $created_by;
    protected $created_on;
    protected $group_updated_on = '';
    protected $text;
    
    public function __construct($dbInstanceName=null) {
        parent::__construct($dbInstanceName);
        $framework = Framework::instance();
        $this->userService = $framework->get('userService');
        $this->comments = $framework->get('comments');
    }
    
    public function addChild(Comment $comment) {
        $this->children[] = $comment;
    }
    
    public function getChildren() {
        return $this->children;
    }
    
    public function getUser() {
        return $this->userService->findById($this->created_by);
    }
    
    public function getReplyToUser() {
        $comment = $this->comments->findById($this->reply_to);
        return $comment->getUser();
    }
    
    public function getGroupId() {
        return $this->parent_id ? $this->parent_id : $this->id;
    }
    
}
