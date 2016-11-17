PHP Test Taker
--------------
Write and test your solutions locally with you favorite editory and debugger.

Currently supports [HackerRank](https://www.hackerrank.com) and [Codility](https://codility.com/programmers/).

How to use it?
--------------

* Clone the repo
* Create a directory for a test
* Create a config file for the test
* Write solutions
* Test, Refine, Paste your solution

Config Files
------------

Config files define what platform the test is on and test cases.

* `type` - One of the supported test types, currently HackerRank or Codility
* `name` - A name to identify the test
* `cases` - One or more test cases

Test cases allow you to provide input for the test and expectations for the input. Both input and expectations can be arrays indicating there are multiple inputs or outputs for the algorithm.

```php
<?php
return [
    'type' => 'HackerRank',
    'name' => 'Steady Gene',
    'cases'    => [
        ['t' => [4, 'GATC'], 'e' => '0'],
        ['t' => [8, 'GATCGATC'], 'e' => 0],
        ['t' => [8, 'AGACAGTT'], 'e' => 1],
    ]];
```

Test Scripts
------------
php-test-taker abstracts the differences between platforms, however your tests will need to be platform specific. The idea is you paste your solution right into the site you're working on.

Codility Example - Practice test

```php
<?php
function solution($aTest)
{
    $iLength = count($aTest);

    if($iLength == 1) {
        return 0;
    }

    $iBottom = $iTop = 0;
    for($p=0; $p<$iLength; $p++) {

        // Prime the top slice
        if($p == 0) {
            $iTop = array_sum(array_slice($aTest, $p + 1));
        }
        else {
            $iBottom += $aTest[$p - 1];
            $iTop    -= $aTest[$p];
        }

        if($iBottom == $iTop) {
            return $p;
        }
    }

    return -1;
}
```

HackerRank Example - Sherlock and Anagrams
```php
<?php
$aTestStrings = file('php://stdin', FILE_IGNORE_NEW_LINES);
array_shift($aTestStrings);

foreach($aTestStrings as $sTestString) {
    $iLength = strlen($sTestString);

    $iAnagramPairs = 0;
    for($iAnagramLength=1; $iAnagramLength<$iLength; $iAnagramLength++) {
        $jMax = $iLength - $iAnagramLength;
        $iMax = $jMax - 1;
        for($i=0; $i<=$iMax; $i++) {
            for($j=$i+1; $j<=$jMax; $j++) {
                $sLeftChar  = $sTestString[$i];
                $sRightChar = $sTestString[$j];
                if($iAnagramLength == 1) {
                    if($sLeftChar == $sRightChar) {
                        $iAnagramPairs++;
                    }
                } else {
                    $sLeft      = substr($sTestString, $i, $iAnagramLength);
                    $sRight     = substr($sTestString, $j, $iAnagramLength);
                    $aLeft      = str_split($sLeft);
                    $aRight     = str_split($sRight);
                    $aLeftHist  = array_count_values($aLeft);
                    $aRightHist = array_count_values($aRight);

                    if(count($aLeftHist) == count($aRightHist)) {
                        $bMatch = true;
                        foreach($aLeftHist as $k => $v) {
                            if(!isset($aRightHist[$k]) || $aRightHist[$k] != $v) {
                                $bMatch = false;
                            }
                        }
                        if($bMatch) {
                            $iAnagramPairs++;
                        }
                    }
                }
            }
        }
    }
    echo "$iAnagramPairs\n";
}
```

Writing and Running Tests
-------------------------

When you clone the project, you will see a *tests* directory, this is where you put test files. You can organize tests however you wish. What I do is create a directory for each test, then put the config and solutions for the test in that directory. Here's what my tests directory looks like

tests/steady-gene
tests/steady-gene/solution1.php
tests/steady-gene/solution2.php
tests/steady-gene/solution3.php
tests/steady-gene/config.php
tests/practice
tests/practice/solution1.php
tests/practice/solution2.php
tests/practice/config.php

You might want another subdirectory for each site like tests/hacker-rank/steady-gene etc.

To run a test use the `run-test` script, passing it the path to a test config and solution.

`./run-test tests/sherlock-and-anagrams/config.php tests/sherlock-and-anagrams/solution1.php
```
================================================================================
                  Running HackerRank Sherlock and Anagrams Test
================================================================================

Testing case: (2, abba, abcd)
  OK: Ran in 17.05

Testing case: (5, ifailuhkqq, hucpoltgty, ovarjsnrbf, pvmupwjjjf, iwwhrlkpek)
  OK: Ran in 29.13

********************************************************************************
                               YOU PASSED THE TEST                              
********************************************************************************
```
