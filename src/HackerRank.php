<?php
class HackerRank extends TestRunner
{
    private
        $_sTestPath,
        $_rProcess,
        $_aPipes;

    public function loadTest($sTestPath)
    {
        $this->_rProcess = proc_open(
            'php -d xdebug.remote_autostart=1 ' . $sTestPath,
            [['pipe', 'r'], ['pipe', 'w']],
            $this->_aPipes);

        if(!is_resource($this->_rProcess)) {
            throw new RuntimeException('Failed to load the test');
        }
    }

    /**
     * HackerRank passes input to the test as discrete lines of input over STDIN
     */
    protected function _execute(array $aTestArgs)
    {
        // Pass each input to the test script as line
        foreach($aTestArgs as $mArg) {
            fwrite($this->_aPipes[0],  "$mArg\n");
        }
        fclose($this->_aPipes[0]);

        $sOutput = stream_get_contents($this->_aPipes[1]);

        return $sOutput;
    }

    protected function _cleanup()
    {
        fclose($this->_aPipes[1]);

        proc_close($this->_rProcess);
    }
}