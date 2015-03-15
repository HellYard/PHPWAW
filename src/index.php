<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 2:03 AM
 * Version: Beta 1
 */
session_start();
define('base_directory', rtrim(realpath(__DIR__), '/').'/');
$_SESSION['current_step'] = 0;
require_once(base_directory."include/ExecutionsCore.php");
require_once(base_directory."include/Connection.php");
require_once(base_directory."include/PHPWAW.php");
require_once(base_directory."include/SQLReader.php");
require_once(base_directory."include/StepBuilder.php");
require_once(base_directory."include/SimpleTemplate.php");
require_once(base_directory."Executions.php");

include_once(base_directory."steps.php");
include_once(base_directory."include/basic.php");

$configurations = array(
    'copyright' => 'Copyright &copy; 2015 <a href="http://creatorfromhell.com">Daniel "creatorfromhell" Vidmar.</a>.',
);
$setup = new PHPWAW($steps, $configurations);
$setup->run($_SESSION['current_step']);
