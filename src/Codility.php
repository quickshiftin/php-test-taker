<?php
class Codility extends TestRunner
{
    public function loadTest($sTestPath)
    {
        require $sTestPath;
        if(!function_exists('solution')) {
            throw new InvalidArgumentException("Found no solution\n");
        }
    }

    /**
     * HackerRank passes input to the test as discrete lines of input over STDIN
     */
    protected function _execute(array $aArgs)
    {
        return call_user_func_array('solution', $aArgs);
    }
}