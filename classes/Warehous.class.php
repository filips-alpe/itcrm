<?php

class Warehous extends DBObject {
    function Load() {
        if (isset(self::$url[2]) && self::$url[2] === 'slieder') {
            if ($_SESSION['menu'] == 1) {
                $_SESSION['menu'] = 0;
            } else {
                $_SESSION['menu'] = 1;
            }
        }
    }
}
