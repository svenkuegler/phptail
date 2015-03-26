<?php

class config {
    
}

class configData {
    public $maxsizetoload = 1024000;
    public $reloadinterval = 3000;
    public $logfiles = array();
    public $grid = 0;
    public $autorefresh = false;
}

class logfileData {
    public $pos = 0;
    public $name = "";
    public $filePath = "";
    public $showLines = 50;
}