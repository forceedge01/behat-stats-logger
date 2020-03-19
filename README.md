Overview
--------

Got a slow running pack and want to decipher what is going on but not sure where to start? Look no further. This package will give you all the reports you need to identify issues with your long running behat pack. With features such as sorting and highlighting long time consuming steps, scenarios, features or suites you'll be able to scan through those massive reports in no time.

Installation
-------------

![After](https://github.com/forceedge01/behat-stats-logger/blob/master/assets/stats.png?raw=true#version=1)

```
composer require --dev genesis/behat-stats-logger
```

Add to your behat.yml file

```yml
default:
    suites:
        default:
            contexts:
                - Genesis\Stats\Context\StatsLoggerContext:
                    filePath: test/report/
                    printToScreen: true
                    topReport:
                        count: 5
                        sortBy: maxTime
                    suiteReport:
                        step: true
                    highlight:
                        scenario:
                            red: 7
                            yellow: 3
                        step:
                            red: 3
                            yellow: 2
                            brown: 1
                        suite:
                            red: 80
                            brown: 70
                            yellow: 50
```

Example project available in features folder.

filePath (string): Set where the reports are to be generated.
printToScreen (boolean): Whether to produce console output or not.
topReport (array):
    count (int): Number of step summaries to show in the top report.
    sortyBy (count|maxTime|cumulativeTime): Sort the output and file report by metrics.
suiteReport (array):
    step (boolean): Whether to output step details or not.
highlight (array):
    <type> (array):
        <color> (int): Number of seconds as the limit. Anything above the limit will be highlighted by the color.

type in (suite, feature, scenario, step)
color in (red, brow, blue, yellow, green, white)

Suite report:
-------------

This report gives you a detailed view with full breakdown, you'll be able to follow the time taken step by step.

Top report:
-------------

This report gives you the top most time consuming steps based on the configuration you set. You can focus on the most time consuming steps in no time.

Suite summary:
-------------

This report gives you a summary breakdown of the suite execution with number of features, scenarios and steps executed in each. You can easily find time consumed by methods other than steps using this report such as hooks.

Reports produced:
---------------

All stats produce files for you to analyse later by suite, based on the filePath you set. These contain all information gathered (disregards limits) but sorting is still applied.
