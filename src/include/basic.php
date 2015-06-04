<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 5:55 PM
 * Version: Beta 1
 */
$_SESSION['current_step'] = 0;
$_SESSION['message'] = " ";
if(isset($_GET['step'])) {
    if(isset($steps[$_GET['step']])) {
        $_SESSION['current_step'] = $_GET['step'];
    }
}
if(isset($_POST['back']) || isset($_POST['next'])) {
    if(isset($_POST['step']) && isset($steps[$_POST['step']])) {
        $execute = true;

        $step_number = $_POST['step'];
        $step = $steps[$step_number];
        if(isset($step['validation'])) {
            if(isset($step['validation']['inputs'])) {
                $invalid = array();
                foreach($step['validation']['inputs'] as &$validate) {
                    if(isset($validate['rules']['required']) && trim($_POST[$validate['name']]) == "") {
                        $execute = false;
                    }
                    if(isset($validate['rules']['value']) && trim($_POST[$validate['name']]) !== $validate['rules']['value']) {
                        $execute = false;
                    }
                    if(!$execute) {
                        $invalid[] = $validate['name'];
                    }
                }
                $_SESSION['invalid_inputs'] = $invalid;
                if(count($invalid) > 0) {
                    $_SESSION['message'] = "<p class=\"message\">There are some invalid values.</p>";
                }
            }
        }
        if(isset($_POST['db_host']) && isset($_POST['db_name']) && isset($_POST['db_username']) && isset($_POST['db_password'])) {
            try {
                new Connection($_POST['db_host'], $_POST['db_name'], $_POST['db_username'], $_POST['db_password']);
            } catch(PDOException $e) {
                $execute = false;
                $_SESSION['message'] = "<p class=\"message\">Invalid MySQL credentials!</p>";
            }
        }
        $next_step = $step_number;

        if($execute) {
            if (empty($_SESSION['executions'][$step_number]['after']) || !$_SESSION['executions'][$step_number]['after']) {
                $_SESSION['run'] = $step_number;
            }

            $next = $step_number + 1;
            if (isset($_POST['back'])) {
                $next = $step_number - 1;
            }

            if (isset($steps[$next])) {
                $next_step = $next;
            }

            foreach ($_POST as $key => $value) {
                if ($key != "back" && $key != "next") {
                    $_SESSION['values'][$key] = $value;
                }
            }
        }
        $_SESSION['current_step'] = $next_step;
    }
}