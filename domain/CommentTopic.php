<?php

class CommentTopic extends Record {
    
    protected $tableName = 'comment_topic';
    
    protected $id;
    protected $topic_name;
    protected $target_id;
    
}