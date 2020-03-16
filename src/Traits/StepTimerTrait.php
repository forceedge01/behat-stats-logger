<?php

namespace Genesis\Stats\Traits;

use InvalidArgumentException;

trait StepTimerTrait
{
    public static $steps = [];

    /**
     * @BeforeStep
     */
    public function beforeStepSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());
        $scenario = self::$currentScenario;
        $step = $scope->getStep()->getText();

        self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step] = [
            'start' => microtime(true),
        ];
    }

    /**
     * @AfterStep
     */
    public function afterStepSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());
        $scenario = self::$currentScenario;
        $step = $scope->getStep()->getText();

        $stats = self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step];
        $stats['end'] = microtime(true);
        $stats['final'] = self::getDiffInSeconds($stats['start'], $stats['end']);

        if (!isset(self::$steps[$step])) {
            self::$steps[$step] = [
                'count' => 1,
                'maxTime' => $stats['end'] - $stats['start'],
                'cumulativeTime' => $stats['end'] - $stats['start'],
                'times' => [$stats['final']]
            ];
        } else {
            self::$steps[$step]['count'] += 1;
            array_push(self::$steps[$step]['times'], $stats['final']);
            $time = $stats['end'] - $stats['start'];
            self::$steps[$step]['cumulativeTime'] += $time;
            if (self::$steps[$step]['maxTime'] < $time) {
                self::$steps[$step]['maxTime'] = $time;
            }
        }

        self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step] = $stats;
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step][] = $stats['final'];
    }

    /**
     * @BeforeSuite
     */
    public static function resetSteps()
    {
        self::$steps = [];
    }

    /**
     * @AfterSuite
     */
    public static function topTenTimeIntensiveSteps($scope)
    {
        if (self::$steps) {
            $suite = $scope->getSuite()->getName();
            echo 'Top ' .
                self::$top['count'] .
                ' time intensive step definitions for suite: ' .
                $suite .
                PHP_EOL .
                PHP_EOL;

            if (!in_array(self::$top['sortBy'], ['cumulativeTime', 'maxTime', 'count'])) {
                throw new InvalidArgumentException('Invalid sort by param provided, allowed are: maxTime, cumulativeTime, count');
            }

            $maxTimes = array_column(self::$steps, self::$top['sortBy']);
            arsort($maxTimes);

            $counter = 1;
            foreach ($maxTimes as $key => $time) {
                if ($counter > self::$top['count']) {
                    break;
                }
                $step = array_slice(self::$steps, $key, 1);
                self::printStep($step);
                $counter++;
            }
        }
    }

    private static function printStep(array $step)
    {
        $stepDefinition = key($step);
        echo 'Step: ' . $stepDefinition . PHP_EOL;
        echo 'Count: ' . $step[$stepDefinition]['count'] . PHP_EOL;
        echo 'Max Time: ' . $step[$stepDefinition]['maxTime'] . PHP_EOL;
        echo 'Cumulative Time: ' . $step[$stepDefinition]['cumulativeTime'] . PHP_EOL;

        foreach ($step[$stepDefinition]['times'] as $index => $time) {
            echo str_repeat(' ', 4) . ($index+1) . ': ' . self::colorCode($time, 'step') . PHP_EOL;
        }

        echo PHP_EOL;
    }

    abstract public static function getDiffInSeconds($start, $end);
}
