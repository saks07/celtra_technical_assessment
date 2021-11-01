<?php

class User {
    private $userId = 0;
    private $dbConnection = null;

    public function __constructor($id, $connection) {
        $this->userId = $id;
        $this->dbConnection = $connection;
    }

    private function get() {
        
    }
}