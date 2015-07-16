<?php
/**
 * Class phpTail
 */
class phpTail {

    /**
     * Map Message Types to CSS Styles
     */
    const SUCCESS   = "alert-success";
    const INFO      = "alert-info";
    const WARNING   = "alert-warning";
    const DANGER    = "alert-danger";

    /**
     * @var string
     */
    private $version = "v0.1.0-pre";

    /**
     * @var array
     */
    private $logiles = array();

    /**
     * @var array
     */
    private $messages = array();

    /**
     * @var int
     */
    private $maxSizeToLoad = 0;

    /**
     * @var int
     */
    private $invert = 0;
    
    /**
     * @var int
     */
    private $reloadInterval = 0;

    /**
     * Contruct
     */
    function __construct() {
        $this->_getSettings();
        $this->_setLocale();
    }

    /**
     * Set Locale
     */
    private function _setLocale() {
        if (function_exists("gettext")) {
            putenv('LC_ALL=de_DE');
            setlocale(LC_ALL, 'de_DE');
            bindtextdomain("messages", "./locale");
            bind_textdomain_codeset("messages", 'UTF-8'); 
            textdomain("messages");    
        } else {
            $this->_setMessage(_("Translation not available on this System!"), phpTail::WARNING);
        }
    }
    
    /**
     * Get Settings
     */
    private function _getSettings() {
        if(file_exists("config.json")) {
            $r = @file_get_contents("config.json");

            if($r !== false) {
                $obj = json_decode($r);

                $this->maxSizeToLoad = $obj->maxsizetoload;
                $this->logiles = $this->_checkFiles($obj->logfiles);
                $this->reloadInterval = $obj->reloadinterval;
            } else {
                $this->_setMessage(_("Could not read Settings!"), phpTail::DANGER);
            }
        } else {
            $this->_setMessage(_("No Settings found!"), phpTail::WARNING);
        }
    }


    /**
     * Check if File exist
     *
     * @param array $arr
     * @return array
     */
    private function _checkFiles($arr   ) {
        $files = array();
        foreach($arr as $file) {
            if(file_exists($file->file)) {
                $files[] = $file;
            } else {
                $this->_setMessage(_("Could not find " . $file->file), phpTail::DANGER);
            }
        }
        return $files;
    }

    /**
     * Set Message
     *
     * @param string $message
     * @param int $type
     */
    private function _setMessage($message, $type) {
        $this->messages[] = array("text" => $message, "type" => $type);
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Set Settings
     */
    private function _setSettings() {

    }

    /**
     * @return array
     */
    public function getLogfiles() {
        return $this->logiles;
    }

    /**
     * @return string
     */
    public function getVersionInfo() {
        return $this->version;
    }

    /**
     * @return int
     */
    public function getReloadInterval() {
        return $this->reloadInterval;
    }

    /**
     * 
     * @return string
     */
    public function getLogContent() {
        $result = "";
        if(self::get('file', FILTER_VALIDATE_INT) != false) {

            foreach($this->getLogfiles() as $row) {
                if(self::get('file') == $row->pos) {
                    $result = $this->_readFile($row->file, self::get('lastsize', FILTER_VALIDATE_INT), self::get('filter'));
                }
            }
        }

        return $result;
    }

    /**
     * 
     * @param string    $file
     * @param int       $lastFetchedSize
     * @param string    $filter
     * 
     * @return string
     */
    private function _readFile($file, $lastFetchedSize=0, $filter=null) {

        clearstatcache();

        $fsize = filesize($file);
        $maxLength = ($fsize - $lastFetchedSize);

        /**
         * Verify that we don't load more data then allowed.
         */
        if($maxLength > $this->maxSizeToLoad) {
            return json_encode(array("lastsize" => $fsize, "data" => array("ERROR: PHPTail attempted to load more (".round(($maxLength / 1048576), 2)."MB) then the maximum size (".round(($this->maxSizeToLoad / 1048576), 2)."MB) of bytes into memory. You should lower the defaultUpdateTime to prevent this from happening. ")));
        }

        // Load new Lines
        $data = array();
        if($maxLength > 0) {

            $fp = fopen($file, 'r');
            fseek($fp, -$maxLength , SEEK_END);
            $data = explode("\n", fread($fp, $maxLength));
        }
        
        if($filter != null) {
            // Run the grep function to return only the lines we're interested in.
            if($this->invert == 0) {
                $data = preg_grep("/$filter/",$data);
            } else {
                $data = preg_grep("/$filter/",$data, PREG_GREP_INVERT);
            }

            // Highlight Filter
            $data = preg_replace("/$filter/", "<span style=\"color:red\">$0</span>", $data);
        }
        
        // Remove Last Line if empty
        if(end($data) == "") {
            array_pop($data);
        }
        return json_encode(array("lastsize" => $fsize, "data" => $data));
    }
    
    /**
     * 
     * @param string $name
     * @param $filter
     * @return type
     */
    public static function get($name, $filter = FILTER_DEFAULT)
    {
        return filter_input(INPUT_GET, $name, $filter);
    }
}