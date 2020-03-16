<?php

namespace Genesis\Stats\Traits;

trait FeatureTimeTrait
{
    /**
     * @BeforeFeature
     */
    public static function beforeFeatureSnapshot($scope)
    {
        $suite = $scope->getSuite()->getName();
        $feature = basename($scope->getFeature()->getFile());

        self::$snapshots[$suite]['features'][$feature] = [
            'time' => null,
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

    abstract public static function getDiffInSeconds($start, $end);
}
