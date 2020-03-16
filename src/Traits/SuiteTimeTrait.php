<?php

namespace Genesis\Stats\Traits;

trait SuiteTimeTrait
{
    /**
     * @BeforeSuite
     */
    public static function beforeTimeSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();

        self::$snapshots[$suite] = [
            'start' => microtime(true),
        ];
    }

    /**
     * @AfterSuite
     */
    public static function afterSuiteSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();

        $stats = self::$snapshots[$suite];
        $stats['end'] = microtime(true);
        $stats['final'] = self::getDiffInSeconds($stats['start'], $stats['end']);

        self::$snapshots[$suite] = $stats;
        self::$display[$suite]['time'] = $stats['final'];

        if (self::$printToScreen && self::$display) {
            self::generateOutput(self::$display);
        }

        if (self::$filePath) {
            file_put_contents(self::$filePath . '/stats.json', json_encode(self::$display));
        }
    }

    public static function generateOutput(array $display)
    {
        foreach ($display as $suite => $suiteTimes) {
            if (!isset($suiteTimes['features'])) {
                continue;
            }
            self::printLine(sprintf('Suite >>> [%s] - %s', $suiteTimes['time'], $suite));
            foreach ($suiteTimes['features'] as $feature => $featureTimes) {
                if (!isset($featureTimes['scenarios'])) {
                    continue;
                }
                self::tab(1);
                self::printLine(sprintf('Feature >>> [%s] - %s', $featureTimes['time'], $feature));
                foreach ($featureTimes['scenarios'] as $scenario => $scenarioTimes) {
                    self::tab(2);
                    self::printLine(sprintf('Scenario >>> [%s] - %s', $scenarioTimes['time'], $scenario));
                    if (self::$suiteReport['step'] && isset($scenarioTimes['steps'])) {
                        foreach ($scenarioTimes['steps'] as $step => $stepTimes) {
                            self::tab(3);
                            self::printLine(sprintf('Step: %s', $step));
                            foreach ($stepTimes as $index => $time) {
                                self::tab(4);
                                self::printLine(sprintf('%d: %s', $index + 1, $time));
                            }
                        }
                    }
                    self::tab(2);
                    self::printLine($scenarioTimes['location']);
                    self::newline();
                }
            }
        }
    }

    public static function newline()
    {
        echo PHP_EOL;
    }

    public static function printLine($line)
    {
        echo $line . PHP_EOL;
    }

    public static function tab($int)
    {
        echo str_repeat(' ', 4*$int);
    }
}
