<?php
/**
* PockyPHP v1.0.0
* Copyright 2024, Morrison Development
*
* Licensed under The MIT License (http://www.opensource.org/licenses/MIT)
* Redistributions of files must retain the above copyright notice.
*/

class MySqlDatabaseService {

    protected $pdo;
    protected $queryHistory = [];

    public function __construct($connection_settings = []) {
        $cs = array_merge([
                'host'     => '',
                'port'     => '',
                'user'     => '',
                'password' => '',
                'database' => '',
            ], $connection_settings
        );

        $dsn = "mysql:host=$cs[host];";
        if (!empty($cs['port'])) $dsn .= "port=$cs[port];";
        $dsn .= "dbname=$cs[database];";
        try {
            $this->pdo = new \PDO($dsn, $cs['user'], $cs['password']);
        } catch(\Exception $e) {
            error_log('Error: Could not connect to PDO database: '. ($cs['database']?:'None defined'));
            throw($e);
        }
    }

    /**
    * @param string $sql  The SQL query to perform
    * @param array $values  The values to inject to the query
    * @return mixed  Returns false if the query failed, true if it succeeded, or an array of results if it is a successful SELECT or SHOW statement.
    */
    public function query($sql, $values = []) {
        $history = [
            'sql'     => $sql,
            'success' => null,
            'error'   => null,
            'rows'    => 0,
        ];
        $stmt = $this->pdo->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
        if ($stmt === false) {
            error_log('Error preparing query: '. $sql);
            $history['success'] = false;
            $history['error'] = $this->pdo->errorInfo()[1];
            $this->queryHistory[] = $history;
            return false;
        }
        $success = $stmt->execute($values);
        if ($success === false) {
            $errInfo = $stmt->errorInfo();
            error_log('Query failed: '. $sql. ';; Using '. json_encode($values). "\nError ". $errInfo[0]. ': '. $errInfo[2]);
            $history['success'] = false;
            $history['error'] = $this->pdo->errorInfo()[1];
            $this->queryHistory[] = $history;
            return false;
        }
        $history['success'] = true;
        if (strtoupper(substr(ltrim($sql), 0, 6)) !== 'SELECT' && strtoupper(substr(ltrim($sql), 0, 4)) !== 'SHOW') {
            $this->queryHistory[] = $history;
            return $success;
        }
        $history['rows'] = $stmt->rowCount();

        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        $stmt->closeCursor();
        $this->queryHistory[] = $history;
        return $result;
    }

    public function querySingleResult($sql, $values = []) {
        if (stripos($sql, ' limit ') === false) {
            $sql .= ' LIMIT 1';
        }
        $result = $this->query($sql, $values);
        if (!is_array($result)) {
            return $result;
        } elseif (empty($result)) {
            return [];
        } else {
            return $result[0];
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    public function rowsAffected() {
        $last = end($this->queryHistory);
        return $last['rows'] ?? 0;
    }

    public function getQueryHistory() {
        return $this->queryHistory;
    }

    public function close() {
        $this->pdo = null;
    }
}
