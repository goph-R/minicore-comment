<?php

class Comments {
    
    /** @var Framework */
    protected $framework;

    /** @var Database */
    protected $db;
    
    protected $cache = [];

    protected $dbInstanceName = 'database';
    protected $tableName = 'comment';
    protected $recordClass = 'Comment';

    public function __construct(Framework $framework) {
        $this->framework = $framework;
        $this->db = $framework->get($this->dbInstanceName);
    }
    
    public function getRecordClass() {
        return $this->recordClass;
    }
    
    public function findById($id) {
        $id = (int)$id;
        if (isset($this->cache[$id])) {
            return $this->cache[$id];
        }
        $query = "SELECT * FROM {$this->tableName} WHERE id = :id LIMIT 1";
        $params = [':id' => $id];
        $result = $this->db->fetch($this->recordClass, $query, $params);
        $this->cache[$id] = $result;
        return $result;
    }
    
    public function findAll(array $filter) {
        if (!isset($filter['topic_id'])) {
            return [];
        }
        $query = "SELECT * FROM {$this->tableName}";
        $query .= " WHERE topic_id = :topic_id";
        $query .= " ORDER BY group_updated_on DESC, created_on ASC";
        if (isset($filter['offset']) && isset($filter['limit'])) {
            $query .= " LIMIT ".(int)$filter['offset'].", ".(int)$filter['limit'];
        }
        $params = [':topic_id' => $filter['topic_id']];
        $result = $this->db->fetchAll($this->recordClass, $query, $params);
        foreach ($result as $r) {
            $this->cache[$r->getId()] = $r;
        }
        return $result;
    }
}