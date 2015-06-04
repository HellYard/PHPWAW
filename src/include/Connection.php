<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 5:30 PM
 * Version: Beta 1
 */

/**
 * Class Connection
 */
class Connection {

    public $pdo;

    public function __construct($host, $database, $username, $password) {
        $this->pdo = new PDO('mysql:dbname='.$database.';host='.$host, $username, $password);
    }

    public function query_file($file) {
        $reader = new SQLReader($file);
        $queries = $reader->parse_queries();
        foreach($queries as &$query) {
            $this->query($query);
        }
    }

    public function query($query, $parameters = array()) {
        $statement = $this->pdo->prepare($query);
        return $statement->execute($parameters);
    }

    public function value($table, $column, $extra = '', $params = array()) {
        $statement = $this->pdo->prepare("SELECT ".$column." FROM `".$table."`".$extra);
        $statement->execute($params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result[$column];
    }

    public function values($table, $column, $extra = '', $params = array()) {
        $statement = $this->pdo->prepare("SELECT ".$column." FROM `".$table."`".$extra);
        $statement->execute($params);
        $result = $statement->fetchAll(PDO::FETCH_COLUMN);
        $values = array();
        foreach($result as &$r) {
            $values[] = $r;
        }
        return $values;
    }

    public function has_values($table, $extra = '', $params = array()) {
        if($this->count_columns($table, $extra, $params) > 0) {
            return true;
        }
        return false;
    }

    public function count_columns($table, $extra = '', $params = array()) {
        $statement = $this->pdo->prepare("SELECT * FROM `".$table."`".$extra);
        $statement->execute($params);
        return $statement->fetch(PDO::FETCH_NUM);
    }
}