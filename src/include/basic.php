<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 5:55 PM
 * Version: Beta 1
 */
if(isset($_GET['step'])) {
    if(isset($steps[$_GET['step']])) {
        $_SESSION['current_step'] = $_GET['step'];
    }
}

if(isset($_POST['back']) || isset($_POST['next'])) {
    if(isset($_POST['step']) && isset($steps[$_POST['step']])) {
        if(empty($_SESSION['executions'][$_POST['step']]['after']) || !$_SESSION['executions'][$_POST['step']]['after']) {
            $_SESSION['run'] = $_POST['step'];
        }
        $next = $_POST['step'] + 1;

        if(isset($_POST['back'])) {
            $next  = $_POST['step'] - 1;
        }

        if(isset($steps[$next])) {
            $_SESSION['current_step'] = $next;
        } else {
            $_SESSION['current_step'] = $_POST['step'];
        }

        foreach($_POST as $key => $value) {
            if($key != "back" && $key != "next") {
                $_SESSION['values'][$key] = $value;
            }
        }
    }
}