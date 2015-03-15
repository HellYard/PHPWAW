<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 11:58 PM
 * Version: Beta 1
 */

/**
 * Class StepBuilder
 */
class StepBuilder {

    private $step_number;
    private $step = array(
        'header' => 'PHP Web Application Wizard',
        'name' => 'Setup',
        'buttons' => array(
            'left' => array(
                'text' => 'Back',
                'location' => '#',
                'show' => false
            ),
            'right' => array(
                'text' => 'Continue',
                'location' => '?step=1',
                'show' => true
            )
        )
    );

    public function __construct($step, $step_number) {
        $this->step_number = $step_number;
        if(is_array($step)) {
            $this->step = array_replace($this->step, $step);
        }
    }

    public function build() {
        $build_string = "<h2>".$this->step['header']."</h2>";
        $build_string .= "<div class=\"holder\">";
        $build_string .= "<fieldset class=\"parts\">";
        $build_string .= "<input type=\"hidden\" value=\"".$this->step_number."\" name=\"step\">";
        $build_string .= $this->build_parts();
        $build_string .= "</fieldset>";
        $build_string .= "</div>";
        $build_string .= "<fieldset class=\"buttons\">";
        $build_string .= $this->build_buttons($this->step['buttons']);
        $build_string .= "</fieldset>";

        return $build_string;
    }

    private function build_buttons($buttons) {
        $left_text = (isset($buttons['left']['text'])) ? $buttons['left']['text'] : "Back";
        $left_disabled = (isset($buttons['left']['show']) && !$buttons['left']['show']) ? " disabled" : "";
        $right_text = (isset($buttons['right']['text'])) ? $buttons['right']['text'] : "Next";
        $right_disabled = (isset($buttons['right']['show']) && !$buttons['right']['show']) ? " disabled" : "";
        $left_button = "<input type=\"submit\" name=\"back\" value=\"".$left_text."\"".$left_disabled.">";
        $right_button = "<input type=\"submit\" name=\"next\" class=\"right\" value=\"".$right_text."\"".$right_disabled.">";
        return $left_button.$right_button;
    }

    private function build_parts() {
        $part_string = "";
        if(isset($this->step['step_parts']) && is_array($this->step['step_parts'])) {
            foreach ($this->step['step_parts'] as &$part) {
                $name = $part['name'];
                $value = (isset($part['value'])) ? $part['value'] : "";
                $placeholder = (isset($part['placeholder'])) ? $part['placeholder'] : "";
                $newline = isset($part['newline']) ? $part['newline'] : "";

                if(isset($part['label'])) {
                    if($part['type'] != "checks") {
                        $part_string .= "<label for=\"".$name."\">".$part['label']."</label>";
                    } else {
                        $part_string .= "<h4>".$part['label']."</h4>";
                    }
                    if($newline === "both" || $newline === "label") {
                        $part_string .= "<br>";
                    }
                }
                switch($part['type']) {
                    case "select":
                        $part_string .= $this->build_select($name, $part['options'], $value);
                        break;
                    case "textarea":
                        $rows = (isset($part['rows'])) ? $part['rows'] : 8;
                        $columns = (isset($part['cols'])) ? $part['cols'] : 50;
                        $part_string .= "<textarea rows=\"".$rows."\" cols=\"".$columns."\">".$value."</textarea>";
                        break;
                    case "description":
                        $part_string .= "<p class=\"description\">".$value."</p>";
                        break;
                    case "checks":
                        $part_string .= $this->build_check($part);
                        break;
                    default:
                        $part_string .= "<input type=\"".$part['type']."\" name=\"".$name."\" value=\"".$value."\" placeholder=\"".$placeholder."\" />";
                        break;
                }
                if($newline === "both" || $newline === "input") {
                    $part_string .= "<br>";
                }
            }
        }
        return $part_string;
    }

    private function build_check($checks) {
        $return = "<table><tbody>";
        if(isset($checks['checks']) && is_array($checks['checks'])) {
            $i = 0;
            foreach($checks['checks'] as &$check) {
                $value = "";
                switch($check['type']) {
                    case "php-config":
                        $value .= $this->check_configuration($check);
                        break;
                    case "php-extension":
                        $value .= $this->check_extension($check);
                        break;
                    case "file":
                        $value .= $this->check_file($check);
                        break;
                }
                $special = ($i % 2 == 0) ? " class=\"special\"" : "";
                $return .= "<tr".$special.">".$value."</tr>";
                $i++;
            }
        }
        $return .= "</tbody></table>";
        return $return;
    }

    private function check_configuration($check) {
        $correct = false;
        $value = "";
        switch($check['name']) {
            case "php-version":
                $correct = version_compare(phpversion(), $check['check'][0], $check['check'][1]);
                $value = phpversion();
                break;
            default:
                $correct = ini_get($check['name']) == $check['check'];
                $value = ini_get($check['name']);
                break;
        }
        $file = base_directory."resources/img/".(($correct) ? "yes.png" : "no.png");
        $return = "<td><img src=\"".$file."\" alt=\"".(($correct) ? "yes" : "no")."\" /></td><td>".$check['name']."</td><td>".$value."</td>";
        return $return;
    }

    private function check_extension($check) {
        $correct = false;
        switch($check['name']) {
            default:
                $correct = extension_loaded($check['check']);
                break;
        }
        $file = base_directory."resources/img/".(($correct) ? "yes.png" : "no.png");
        $return = "<td><img src=\"".$file."\" alt=\"".(($correct) ? "yes" : "no")."\" /></td><td>".$check['check']."</td><td>extension loaded: ".$correct."</td>";
        return $return;
    }

    private function check_file($check) {
        $correct = false;
        switch($check['check']) {
            case "writable":
                $correct = is_writable($check['file']);
                break;
            case "readable":
                $correct = is_readable($check['file']);
                break;
            case "exists":
                $correct = file_exists($check['file']);
                break;
        }
        $file = base_directory."resources/img/".(($correct) ? "yes.png" : "no.png");
        $return = "<tr><img src=\"".$file."\" alt=\"".(($correct) ? "yes" : "no")."\" /></td><td>".$check['file']."</td><td>".$check['check'].": ".$correct."</td>";
        return $return;
    }

    private function build_select($name, $options, $value) {
        $return = "<select name=\"".$name."\">";

        foreach($options as $key => $v) {
            $selected = ($key === $value) ? " selected" : "";
            $return .= "<option value=\"".$key."\"".$selected.">".$v."</option>";
        }

        $return .= "</select>";
        return $return;
    }
}