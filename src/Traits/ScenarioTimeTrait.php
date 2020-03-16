<?php

namespace Genesis\Stats\Traits;

trait ScenarioTimeTrait
{
    private static $currentScenario = null;

    /**
     * @BeforeScenario
     */
    public static function beforeScenarioSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());
        $scenario = '(L' . $scope->getScenario()->getLine() . ') - ' . $scope->getScenario()->getTitle();
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
        $scenario = '(L' . $scope->getScenario()->getLine() . ') - ' . $scope->getScenario()->getTitle();

        $stats = self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario];
        $stats['end'] = microtime(true);
        $stats['final'] = self::getDiffInSeconds($stats['start'], $stats['end']);
        $location = $scope->getFeature()->getFile() . ':' . $scope->getScenario()->getLine();

        self::$snapshots[$suite]['features'][$feature]['scenarios'][$scenario] = $stats;
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario]['time'] = $stats['final'];
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario]['location'] = $location;
    }

    abstract public static function getDiffInSeconds($start, $end);
}
