<?php
/**
 * Created by Daniel Vidmar.
 * Date: 1/18/15
 * Time: 10:40 PM
 * Version: 1.0
 * Last Modified: 1/20/15 at 8:30 PM
 * Last Modified by Daniel Vidmar.
 */
class SimpleTemplate {
    /*
    * The location of the template file.
    * Correct format: path/to/template.tpl
    */
    public $template = null;
    public $base_path = null;
    /*
    * The templating rules that are going to be used to parse the template file.
    */
    public $rules = array();
    /*
    * The class constructor.
    * $template: The template file(including path) that's going to be parsed - required.
    * $rules: The templating rules to be used for parsing the template file.
    * $auto: Whether or not the template should be parsed automatically when this class is initiated.
    * $base_path: The base location to be added onto the beginning any includes or template location.
    */
    public function __construct($template, $rules = array(), $auto = false, $base_path = "") {
        $this->template = $template;
        $this->rules = $rules;
        $this->base_path = $base_path;
        if($auto) {
            $this->template();
        }
    }
    public function template($return = false, $name = null) {
        $name = ($name == null) ? $this->base_path.$this->template : $name;
        $lines = $this->parse_template($name);
        $template = "";
        if(is_array($lines)) {
            foreach ($lines as &$line) {
                $template .= $line;
            }
        }
        if ($return) {
            return $template;
        }
        echo $template;
        return "";
    }
    /*
    * Reads every line in a .tpl file.
    */
    private function read_template($name) {
        $lines = array();
        $file = fopen($name, 'r');
        while(!feof($file)) {
            $lines[] = stream_get_line($file, 30000, "\n");
        }
        return $lines;
    }
    private function parse_template($name = null) {
        $name = ($name == null) ? $this->base_path.$this->template : $this->base_path.$name;
        if(file_exists($name)) {
            $lines = $this->read_template($name);
            $filtered = array();
            foreach($lines as &$line) {
                $filtered[] = $this->filter_rules($line);
            }
            return $filtered;
        }
        return "Failed to parse template ".$name.".";
    }
    private function filter_rules($string) {
        $matched = array();
        preg_match_all("/\\{([^}]+)\\}/", $string, $matched, PREG_SET_ORDER);
        if(!empty($matched)) {
            foreach ($matched as &$rule) {
                $string = str_replace($rule[0], $this->parse_rule(trim($rule[1], " \t\n\r\0\x0B{}")), $string);
            }
        }
        return $string;
    }
    private function parse_rule($rule) {
        $special_rules = array("include", "function");
        $rule_check = explode("->", $rule);
        if(in_array($rule_check[0], $special_rules)) {
            switch($rule_check[0]) {
                case "include":
                    return $this->include_template($rule);
                case "function":
                    return $this->call_function($rule);
            }
        }
        if(strpos($rule_check[0], "&") !== false && in_array(trim($rule_check[0], "&"), $special_rules)) {
            $rule_check[0] = trim($rule_check[0], "&");
        }
        /*
        * This allows the parsing of include rules passed through the rules array without going over the nesting
        * limit for web servers that use xdebug.
        */
        $rule_value = $this->get_rule(implode("->", $rule_check));
        if(strpos($rule_value, "include->") !== false) {
            $rule_value = trim($rule_value, " \t\n\r\0\x0B{}");
            $location = explode("->", $rule_value)[1];
            if(strpos($location, ".tpl") === false) {
                $location = $location.".tpl";
            }
            $include = new SimpleTemplate($location, $this->rules, false, $this->base_path);
            return $include->template(true);
        }
        return $this->get_rule(implode("->", $rule_check));
    }
    private function include_template($rule) {
        $location = str_ireplace("include->", "", $rule);
        if(strpos($location, ".tpl") === false) {
            $location = $location.".tpl";
        }
        return $this->template(true, $location);
    }
    private function call_function($rule) {
        $rule_parts = explode("->", $rule);
        if(function_exists($rule_parts[1])) {
            $parameters = array();
            if(count($rule_parts) > 2) {
                $parameters = explode(":", $rule_parts[2]);
            }
            $value = call_user_func_array($rule_parts[1], $parameters);
            if($value != null) {
                return $value;
            }
            return "";
        }
        return "{ ".$rule." }";
    }
    private function get_rule($rule) {
        $value = $this->array_path($this->rules, $rule);
        if($value != null) {
            return $value;
        }
        return "{ ".$rule." }";
    }
    private function array_path($array, $path, $delimiter = "->") {
        $path_array = explode($delimiter, $path);
        $tmp = $array;
        foreach($path_array as $p) {
            if(empty($tmp[$p])) {
                return null;
            }
            $tmp = $tmp[$p];
        }
        return $tmp;
    }
}