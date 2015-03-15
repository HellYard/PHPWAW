<?php
/**
 * Created by Daniel Vidmar.
 * Date: 3/10/2015
 * Time: 10:37 PM
 * Version: Beta 1
 */

/**
 * Class ExecutionsCore
 */
class ExecutionsCore {

    public static function download($location, $save) {
        file_put_contents($save, fopen($location, 'r'));
    }
}