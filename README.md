Installation
-------------

```
composer require --dev genesis/behat-stats-log-extension
```

Add to your behat.yml file

```
default:
    suites:
        default:
            contexts:
                - Genesis\Testing\Stats\StatsLoggerContext
```