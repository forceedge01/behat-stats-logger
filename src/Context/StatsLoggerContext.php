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

    public function __construct($filePath, $printToScreen = false)
    {
        self::$filePath = $filePath;
        self::$printToScreen = $printToScreen;

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
}
