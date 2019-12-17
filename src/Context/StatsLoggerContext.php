<?php

namespace Genesis\Testing\Stats\Context;

use Behat\Behat\Context\Context;

class StatsLogContext implements Context
{
    private static $snapshots = [];
    private static $display = [];
    private static $filePath = '/../../report/timestats.json';
    private static $currentScenario = null;

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
        file_put_contents(__DIR__ . self::$filePath, json_encode(self::$display));
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
        self::$currentScenario = $scenario;

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
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario]['time'] = $stats['final'];
    }

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

        self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step] = $stats;
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step] = $stats['final'];
    }

    private static function getDiffInSeconds($start, $end)
    {
        $diff = $end - $start;
        $sec = intval($diff);
        $micro = $diff - $sec;

        return strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.2f', $micro));
    }
}
