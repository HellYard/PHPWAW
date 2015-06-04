<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/9/2015
 * Time: 10:07 PM
 * Version: Beta 1
 */

/**
 * Class PHPWAW
 */
class PHPWAW {

    public $steps;

    public $db;

    private static $powered_by = "Powered by <a href=\"https://github.com/creatorfromhell/PHPWAW\">PHP Web Application Wizard</a>.";

    public $configurations = array(
        'sql_enabled' => false,
        'db_host' => 'localhost',
        'db_name' => 'database',
        'db_username' => 'username',
        'db_password' => 'password',
        'copyright' => 'Copyright &copy; 2015 <a href="http://creatorfromhell.com">Daniel "creatorfromhell" Vidmar</a>.',
    );

    public function __construct($steps, $configurations = array()) {
        $this->steps = $steps;

        if(isset($_SESSION['values']['db_host']) && isset($_SESSION['values']['db_name']) && isset($_SESSION['values']['db_username']) && isset($_SESSION['values']['db_password'])) {
            $this->configurations['sql_enabled'] = true;
            $this->configurations['db_host'] = $_SESSION['values']['db_host'];
            $this->configurations['db_name'] = $_SESSION['values']['db_name'];
            $this->configurations['db_username'] = $_SESSION['values']['db_username'];
            $this->configurations['db_password'] = $_SESSION['values']['db_password'];
        }

        $this->configurations = array_replace($this->configurations, $configurations);

        if($this->configurations['sql_enabled']) {
            $this->connect(
                $this->configurations['db_host'],
                $this->configurations['db_name'],
                $this->configurations['db_username'],
                $this->configurations['db_password']
            );
        }
    }

    public function connect($host, $db, $username, $password) {
        try {
            $this->db = new Connection($host, $db, $username, $password);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    private function build_step($step) {
        $step_string = "";
        if(isset($this->steps[$step])) {
            $builder = new StepBuilder($this->steps[$step], $step);
            $step_string = $builder->build();
        }
        return $step_string;
    }
    
    private function pre_run() {
        if(isset($_SESSION['run']) && isset($this->steps[$_SESSION['run']])) {
            $this->run_executions($_SESSION['run'], false);
            $_SESSION['executions'][$_SESSION['run']]['after'] = true;
        }
    }

    public function run($step) {
        $this->pre_run();
        if(empty($_SESSION['executions'][$step]['before']) || !$_SESSION['executions'][$step]['before']) {
            $this->run_executions($step);
            $_SESSION['executions'][$step]['before'] = true;
        }
        $rules = array(
            'message' => $_SESSION['message'],
            'step' => $this->build_step($step),
            'copyright' => self::$powered_by."<br>".$this->configurations['copyright']
        );
        new SimpleTemplate(base_directory."resources/templates/wizard.tpl", $rules, true);
    }

    public function run_executions($step, $before = true) {
        if(isset($this->steps[$step]) && isset($this->steps[$step]['executions'])) {
            $order = ($before) ? "before" : "after";
            foreach($this->steps[$step]['executions'] as &$execution) {
                $execution_order = (isset($execution['order'])) ? $execution['order'] : "before";
                if($execution_order === $order) {
                    $type = $execution['type'];

                    switch($type) {
                        case "download":
                            Executions::download($execution['location'], $execution['save']);
                            break;
                        case "function":
                            $parameters = (isset($execution['parameters']) && is_array($execution['parameters'])) ? $execution['parameters'] : array();
                            call_user_func_array(array("Executions", $execution['name']), $parameters);
                            break;
                        case "sql_query":
                            if($this->configurations['sql_enabled']) {
                                foreach ($execution['queries'] as &$query) {
                                    $parameters = (isset($query['parameters']) && is_array($query['parameters'])) ? $query['parameters'] : array();
                                    $this->db->query($query['query'], $parameters);
                                }
                            }
                            break;
                        case "sql_file":
                            if($this->configurations['sql_enabled']) {
                                $this->db->query_file($execution['name']);
                            }
                            break;
                    }
                }
            }
        }
    }
}