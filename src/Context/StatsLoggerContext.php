<?php

namespace Genesis\Stats\Context;

use Behat\Behat\Context\Context;
use Genesis\Stats\Traits;

class StatsLoggerContext implements Context
{
    use Traits\SuiteTimeTrait;
    use Traits\FeatureTimeTrait;
    use Traits\ScenarioTimeTrait;
    use Traits\StepTimerTrait;

    private static $snapshots = [];
    private static $display = [];
    private static $printToScreen = false;
    private static $filePath = null;
    private static $top = [];
    private static $suiteReport = [];
    private static $suiteSummary = [];
    private static $highlight;

    public function __construct(
        $filePath,
        $printToScreen = false,
        array $suiteReport = [],
        array $topReport = [],
        array $suiteSummary = [],
        array $highlight = []
    ) {
        self::$filePath = $filePath;
        self::$printToScreen = $printToScreen;
        self::$suiteReport = array_merge([
            'step' => true,
            'enabled' => true
        ], $suiteReport);
        self::$top = array_merge([
            'count' => 10,
            'sortBy' => 'maxTime',
            'enabled' => true
        ], $topReport);
        self::$suiteSummary = array_merge([
            'enabled' => true,
        ], $suiteSummary);
        self::$highlight = $highlight;

        if (self::$filePath && !is_dir(self::$filePath)) {
            mkdir(self::$filePath, 0777, true);
        }
    }

    public static function getDiffInSeconds($start, $end)
    {
        $diff = $end - $start;
        $sec = intval($diff);
        $micro = $diff - $sec;

        return strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.2f', $micro));
    }

    public static function get($array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return $default;
    }
}
