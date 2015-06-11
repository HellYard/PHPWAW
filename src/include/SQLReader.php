<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 5:30 PM
 * Version: Beta 1
 */

/**
 * Class SQLReader
 */
class SQLReader {

    private $file;

    public function __construct($file) {
        $this->file = $file;
    }

    public function parse_queries() {
        $queries = array();

        $content = file_get_contents($this->file);
        $content = $this->remove_comments($content);
        $lines = explode("\n", $content);

        $query_statement = '';

        foreach($lines as &$line) {
            $l = trim($line);
            if(empty($l)) { continue; }

            $chars = substr($l, 0, 2);
            if($chars == '--' || substr($chars, 0, 1) == '#') {
                continue;
            }

            if(empty($query_statement)) {
                $query_statement = $l;
            } else {
                $query_statement .= $l;
            }

            if(substr($l, -strlen(';')) == ';') {
                $queries[] = $query_statement;
                $query_statement = "";
            }
        }
        return $queries;
    }

    private function remove_comments($string) {
        $return = preg_replace("/\\/\\*([^}]+)\\*\\//", "", $string);
        return $return;
    }
}