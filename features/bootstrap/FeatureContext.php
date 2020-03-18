<?php

use Behat\Behat\Context\Context;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I am on a page
     */
    public function iAmOnAPage()
    {
        usleep(700000);
    }

    /**
     * @When I trigger some action
     */
    public function iTriggerSomeAction()
    {
        usleep(315000);
    }

    /**
     * @Then I should receive some output
     */
    public function iShouldReceiveSomeOutput()
    {
        usleep(270000);
    }
}
