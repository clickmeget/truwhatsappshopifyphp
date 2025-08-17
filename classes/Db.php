<?php

class Database {
    private $host = 'localhost';
    private $username = 'truw_truwa';
    private $password = 'HIu@*sM5bhCHlrbu';
    private $database = 'truw_truwa';
    private $connection;

    public function __construct() {
        $this->connect();
    }

    // Establish database connection
    private function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    // Close the connection
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    // Execute a query and return the result
    public function query($sql) {
        $result = $this->connection->query($sql);
        if ($result === FALSE) {
            die("Error: " . $this->connection->error);
        }
        return $result;
    }

    // Fetch a single row
    public function fetch($result) {
        return $result->fetch_assoc();
    }

    // Fetch all rows
    public function fetchAll($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function numRows($result) {
        return $result->num_rows;
    }

    // Prepare and bind a prepared statement
    public function prepare($sql) {
        $stmt = $this->connection->prepare($sql);
        if ($stmt === FALSE) {
            die("Error: " . $this->connection->error);
        }
        return $stmt;
    }

    // Get the last inserted ID
    public function insertId() {
        return $this->connection->insert_id;
    }

    // Escape a string to prevent SQL injection
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    // Start a transaction
    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    // Commit a transaction
    public function commit() {
        $this->connection->commit();
    }

    // Rollback a transaction
    public function rollback() {
        $this->connection->rollback();
    }
}

?>
