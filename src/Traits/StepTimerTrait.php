<?php

namespace Genesis\Stats\Traits;

trait StepTimerTrait
{
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
        self::$display[$suite]['features'][$feature]['scenarios'][$scenario]['steps'][$step][] = $stats['final'];
    }

    abstract public static function getDiffInSeconds($start, $end);
}
