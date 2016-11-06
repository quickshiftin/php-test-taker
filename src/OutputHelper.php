<?php
class OutputHelper {


    public function __construct() {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors() {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names
    public function getBackgroundColors() {
        return array_keys($this->background_colors);
    }

    public function testBanner($s) {
        echo $this->getColoredString('================================================================================', "light_cyan", "black");
        echo "\n";
        $iPad = (80 - strlen($s)) / 2;
        echo $this->getColoredString(str_pad('', round($iPad), ' ') . $s, 'light_cyan', 'black') . "\n";
        echo $this->getColoredString('================================================================================', "light_cyan", "black");
        echo "\n\n";
    }

    public function errorBanner($s) {
        echo $this->getColoredString("  ERROR: " . strtoupper($s) . ': ', "black", "red");
    }

    public function errorDetail($s) {
        echo $this->getColoredString($s, "black", "red");
        echo "\n";
    }

    public function successBanner($iTime) {
        echo $this->getColoredString("  OK: Ran in " . round($iTime, 2), "black", "green");
        echo "\n";
    }

    public function summaryBanner($sResult, $s) {
        $aColors = ['ok' => 'green', 'warn' => 'yellow', 'error' => 'red'];
        $sColor  = $aColors[$sResult];
        echo $this->getColoredString("********************************************************************************", "black", $sColor);
        echo "\n";
        $iLength = strlen($s);
        $iPad = round((80 - $iLength) / 2);
        $iAfter = 80 - ($iPad + $iLength);
        echo $this->getColoredString(str_pad('', round($iPad), ' ') . strtoupper($s) . str_pad('', $iAfter, ' '), 'black', $sColor);
        echo "\n";
        echo $this->getColoredString("********************************************************************************", "black", $sColor);
        echo "\n";
    }

    public function warningResult($s) {
        $this->summaryBanner('warn', $s);
    }

    public function errorResult($s) {
        $this->summaryBanner('error', $s);
    }

    public function successResult($s) {
        $this->summaryBanner('ok', $s);
    }
}