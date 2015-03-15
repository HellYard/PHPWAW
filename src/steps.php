<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/14/2015
 * Time: 4:54 PM
 * Version: Beta 2
 */
$steps = array(
    array(
        'header' => 'PHPWAW Initial Requirements',
        'buttons' => array(
            'left' => array(
                'text' => 'Back',
                'show' => false
            ),
            'right' => array(
                'text' => 'Next',
                'show' => true
            )
        ),
        'executions' => array(
            array(
                'type' => 'download',
                'order' => 'before',
                'location' => 'http://creatorfromhell.com/projects/trackr/latest.zip',
                'save' => 'temp.zip'
            )
        ),
        'step_parts' => array(
            array(
                'type' => 'checks',
                'label' => 'Server Requirements',
                'checks' => array(
                    array(
                        'type' => 'php-config',
                        'name' => 'php-version',
                        'check' => array('5.3.1', '>=')
                    ),
                    array(
                        'type' => 'php-extension',
                        'name' => 'loaded',
                        'check' => 'PDO_MYSQL'
                    )
                )
            ),
            array(
                'label' => 'Language: ',
                'name' => 'language',
                'type' => 'select',
                'value' => 'English',
                'newline' => 'input', //accepted values: label, input, both
                'options' => array(
                    'en' => 'English',
                    'de' => 'Dutch',
                    'es' => 'Spanish'
                )
            ),
        ),
    ),
    array(
        'header' => 'PHPWAW Secondary Step',
        'buttons' => array(
            'left' => array(
                'text' => 'Back',
                'show' => true
            ),
            'right' => array(
                'text' => 'Finish',
                'show' => true
            )
        ),
        'step_parts' => array(
            array(
                'label' => 'MySQL Host: ',
                'name' => 'db_host',
                'type' => 'text',
                'newline' => 'input',
            ),
            array(
                'label' => 'MySQL Database: ',
                'name' => 'db_name',
                'type' => 'text',
                'newline' => 'input',
            ),
            array(
                'label' => 'MySQL Username: ',
                'name' => 'db_username',
                'type' => 'text',
                'newline' => 'input',
            ),
            array(
                'label' => 'MySQL Password: ',
                'name' => 'db_password',
                'type' => 'text',
                'newline' => 'input',
            ),
        ),
    ),
);