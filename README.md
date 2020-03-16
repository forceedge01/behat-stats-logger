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
```

Example project available in features folder.
