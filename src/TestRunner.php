<?php
abstract class TestRunner
{
    private
        $_sName = '',
        $_iCurTest = 0,
        $_iNumCases = 0,
        $_aTestCases,
        $_oOutputHelper,
        $_bErrors = false,
        $_bTooSlow = false,
        $_bIncorrect = false;

    public function __construct($sName='')
    {
        if($sName != '') {
            $this->_sName = " {$sName}";
        }
        $this->_oOutputHelper = new OutputHelper();
    }

    public function hasMoreCases() { return $this->_iCurTest < $this->_iNumCases; }

    public function loadConfig(array $aCases)
    {
        $this->_aTestCases = $aCases;
        $this->_iNumCases  = count($aCases);
    }

    public function displayTestBanner()
    {
        $this->_oOutputHelper->testBanner("Running " . get_class($this) . $this->_sName . " Test");
    }

    final public function run()
    {
        //--------------------------------------------------
        // Determine if there are any expectations
        //--------------------------------------------------
        $mTest = $this->_aTestCases[$this->_iCurTest];

        // If a test has an expectation, the arguments have to be specified separately
        if(!is_array($mTest)) {
            throw new InvalidArgument("Bogus test case\n");
        }
        if(isset($mTest['e']) && isset($mTest['t'])) {
            $bHasExpectation  = true;
            $aTestArgs = $mTest['t'];
            $mExpectation     = $mTest['e'];
        // Otherwise the supplied value is the first test arg
        } else {
            $aTestArgs = $mTest;
        }

        //--------------------------------------------------
        // Print test overview
        //--------------------------------------------------
        if($this->_iCurTest > 0) {
            echo "\n";
        }

        echo 'Testing case: (';
        foreach($aTestArgs as $i => $mTestArg) {
            $this->_printTestArg($mTestArg, $i == 0);
        }
        echo ")\n";

        // Run the test
        $iStart  = microtime(true);
        $mResult = $this->_execute($aTestArgs);
        $iEnd    = microtime(true);
        $iTime   = ($iEnd - $iStart) * 1000;

        // Evaluate
        $bError = $bTooSlow = $bIncorrect = false;
        if($iTime > 900) {
            $bError = true;
            $this->_bTooSlow = true;
            $this->_oOutputHelper->errorBanner('took too long');
            $this->_oOutputHelper->errorDetail('Ran in ' . round($iTime, 2));
        }

        if($bHasExpectation && $mResult != $mExpectation) {
            $bError = true;
            $this->_bIncorrect = true;
            $this->_oOutputHelper->errorBanner('wrong result');
            $this->_oOutputHelper->errorDetail(
                'Expected ' . print_r($mExpectation, true) . ' got ' . print_r($mResult, true));
        }

        if(!$bError) {
            $this->_oOutputHelper->successBanner($iTime);
        }
        // Indicate there was an error in at least one test
        else {
            $this->_bErrors = true;
        }

        $this->_iCurTest++;
    }

    public function displayResults()
    {
        echo "\n";

        if($this->_bErrors) {
            if($this->_bIncorrect && !$this->_bTooSlow) {
                $this->_oOutputHelper->warningResult('you have logic issues but speed is ok');
            } elseif(!$this->_bIncorrect && $this->_bTooSlow) {
                $this->_oOutputHelper->warningResult('logic is ok but runs too slow');
            } else {
                $this->_oOutputHelper->errorResult('you have logic & speed issues check them out!!');
            }
        } else {
            $this->_oOutputHelper->successResult('you passed the test');
        }
    }

    private function _printTestArg($mTestArg, $bIsFirst)
    {
        if(!$bIsFirst) {
            echo ', ';
        }

        if(is_array($mTestArg)) {
            $bLongInput = false;
            if(count($mTestArg) > 10) {
                $bLongInput = true;
                $mTestArg = array_slice($mTestArg, 0, 10);
            }
            echo '[' . implode(', ', $mTestArg);
            if($bLongInput) {
                echo ', ...]';
            } else {
                echo ']';
            }
        } elseif(is_string($mTestArg) && strlen($mTestArg) > 10) {
            echo substr($mTestArg, 0, 10) . '...';
        } elseif(is_bool($mTestArg)) {
            if($mTestArg) {
                echo 'true';
            } else {
                echo 'false';
            }
        } else {
            echo $mTestArg;
        }
    }

    /**
     * Subclass is given the path to the test script.
     * Do whatever you need to in order to be able to use the test script
     * with the current test case when _execute() is called later.
     */
    abstract public function loadTest($sTestPath);

    /**
     * Run the underlying test against the current test case and return the result.
     */
    abstract protected function _execute(array $aTestArgs);
}
