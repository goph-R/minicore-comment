<?php

class CommentTopics {

    /** @var Framework */
    protected $framework;

    /** @var Database */
    protected $db;

    protected $dbInstanceName = 'database';
    protected $tableName = 'comment_topic';
    protected $recordClass = 'CommentTopic';

    public function __construct(Framework $framework) {
        $this->framework = $framework;
        $this->db = $framework->get($this->dbInstanceName);
    }
    
    public function findByNameAndTargetId($name, $targetId) {
        $query = "SELECT * FROM {$this->tableName} WHERE name = :name AND target_id = :target_id LIMIT 1";
        return $this->db->fetch($this->recordClass, $query, [
            ':name' => $name,
            ':target_id' => $targetId
        ]);
    }
    
    public function findById($id) {
        $query = "SELECT * FROM {$this->tableName} WHERE id = :id LIMIT 1";
        return $this->db->fetch($this->recordClass, $query, ['id' => $id]);
    }
    
    private function isDuplicateEntry($e) {
        return $e->getCode() == 1062 || $e->getCode() == 23000;
    }
    
    public function findOrCreate($name, $targetId) {    
        $topic = $this->findByNameAndTargetId($name, $targetId);
        if ($topic) {
            return $topic;
        }
        $topic = $this->framework->create($this->recordClass);
        $topic->setName($name);
        $topic->setTargetId($targetId);
        try {
            $topic->save();
        } catch (PDOException $e) {
            if ($this->isDuplicateEntry($e)) {
                $topic = $this->findByNameAndTargetId($name, $targetId);
            } else {
                throw $e;
            }
        }
        return $topic;
    }    

}