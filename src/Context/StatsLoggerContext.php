<?php

namespace Genesis\Testing\Stats\Context;

use Behat\Behat\Context\Context;

/**
 * StatsLogger class.
 */
class StatsLoggerContext implements Context
{
    private static $snapshots = [];
    private static $display = [];

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

        print_r(self::$display);
    }

    /**
     * @BeforeFeature
     */
    public static function beforeFeatureSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());

        self::$snapshots[$suite]['features'][$feature] = [
            'start' => microtime(true),
        ];
    }

    /**
     * @AfterFeature
     */
    public static function afterFeatureSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());

        $stats = self::$snapshots[$suite]['features'][$feature];
        $stats['end'] = microtime(true);
        $stats['final'] = self::getDiffInSeconds($stats['start'], $stats['end']);

        self::$snapshots[$suite]['features'][$feature] = $stats;
        self::$display[$suite]['features'][$feature]['time'] = $stats['final'];
    }

    /**
     * @BeforeScenario
     */
    public static function beforeScenarioSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());
        $scenario = $scope->getScenario()->getTitle();

        self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario] = [
            'start' => microtime(true),
        ];
    }

    /**
     * @AfterScenario
     */
    public static function afterScenarioSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());
        $scenario = $scope->getScenario()->getTitle();

        $stats = self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario];
        $stats['end'] = microtime(true);
        $stats['final'] = self::getDiffInSeconds($stats['start'], $stats['end']);

        self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario] = $stats;
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario] = $stats['final'];
    }

    private static function getDiffInSeconds($start, $end)
    {
        $diff = $end - $start;
        $sec = intval($diff);
        $micro = $diff - $sec;

        return strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.2f', $micro));
    }
}
