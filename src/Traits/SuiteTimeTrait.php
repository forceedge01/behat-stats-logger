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
            'time' => null,
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

        $suiteReport = [];
        if (self::$suiteReport['suiteSummary']) {
            self::printLine('Suite summary report:');
            self::newline();
            foreach (self::$display as $suite => $stats) {
                $suiteReport[$suite] = $stats['time'];
                self::printLine('[' . self::colorCode($stats['time'], 'suite') . '] - ' . $suite);
            }
        }

        if (self::$filePath) {
            foreach (self::$display as $suite => $stats) {
                file_put_contents(self::$filePath . self::getName('suite-report', $suite), json_encode($stats));
            }
            file_put_contents(self::$filePath . self::getName('steps-report', $suite), json_encode(self::$steps));

            if (self::$suiteReport['suiteSummary']) {
                file_put_contents(self::$filePath . self::getName('suite', 'summary report'), json_encode($suiteReport));
            }
        }
    }

    private static function getName($type, $suiteName)
    {
        return '/' . $type . '-' . str_replace(' ', '-', $suiteName) . '.json';
    }

    public static function generateOutput(array $display)
    {
        foreach ($display as $suite => $suiteTimes) {
            if (!isset($suiteTimes['features'])) {
                continue;
            }
            self::printLine(sprintf('Suite >>> [%s] - %s', self::colorCode($suiteTimes['time'], 'suite'), $suite));
            foreach ($suiteTimes['features'] as $feature => $featureTimes) {
                if (!isset($featureTimes['scenarios'])) {
                    continue;
                }
                self::tab(1);
                self::printLine(sprintf('Feature >>> [%s] - %s', self::colorCode($featureTimes['time'], 'feature'), $feature));
                foreach ($featureTimes['scenarios'] as $scenario => $scenarioTimes) {
                    self::tab(2);
                    self::printLine(sprintf('Scenario >>> [%s] - %s', self::colorCode($scenarioTimes['time'], 'scenario'), $scenario));
                    if (self::$suiteReport['step'] && isset($scenarioTimes['steps'])) {
                        foreach ($scenarioTimes['steps'] as $step => $stepTimes) {
                            self::tab(3);
                            self::printLine(sprintf('Step: %s', $step));
                            foreach ($stepTimes as $index => $time) {
                                self::tab(4);
                                self::printLine(sprintf('%d: %s', $index + 1, self::colorCode($time, 'step')));
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

    private static function colorCode($string, $type)
    {
        if (!isset(self::$highlight[$type])) {
            return $string;
        }

        list($hours, $minutes, $seconds) = explode(':', $string);
        $inSeconds = ((int) $hours*24*60) + ((int) $minutes*60) + (float) $seconds;

        asort(self::$highlight[$type]);
        $color = null;
        foreach (self::$highlight[$type] as $colorName => $seconds) {
            if ($inSeconds > $seconds) {
                $color = $colorName;
            }
        }

        switch ($color) {
            case 'blue':
                return "\033[1;34m$string\033[0m";

            case 'red':
                return "\033[0;31m$string\033[0m";

            case 'brown':
                return "\033[0;33m$string\033[0m";

            case 'yellow':
                return "\033[1;33m$string\033[0m";

            case 'green':
                return "\033[0;32m$string\033[0m";

            case 'white':
                return "\033[1;37m$string\033[0m";

            default:
                return $string;
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
