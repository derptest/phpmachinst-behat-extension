<?php

use Behat\Behat\Context\BehatContext;
use DerpTest\Behat\MachinistExtension\Context\MachinistAwareInterface;
use DerpTest\Behat\MachinistExtension\Context\MachinistContext;
use DerpTest\Machinist\Machinist;

/**
 * Features context.
 */
class FeatureContext extends BehatContext implements MachinistAwareInterface
{
    /**
     * @var \DerpTest\Machinist\Machinist
     */
    private $machinist;

    /**
     * @var array
     */
    private $machinistParameters;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext('machinst', new MachinistContext());
    }

    /**
     * Set Machinist
     *
     * @param Machinist $machinist
     * @return void
     */
    public function setMachinist(Machinist $machinist)
    {
        $this->machinist = $machinist;
    }

    /**
     * Set the Machinist parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setMachinistParameters(array $parameters)
    {
        $this->machinistParameters = $parameters;
    }


    /**
     * @BeforeScenario
     */
    public function clearAllData()
    {
        $this->machinist->wipeAll($this->machinistParameters['truncate_on_wipe']);
    }

    /**
     * @Then /^"(?P<property>(?:[^"]|\\")*)" is embedded object in "(?P<blueprint>(?:[^"]|\\")*)" for:$/
     */
    public function isEmbeddedInFor($property, $arg2, TableNode $table)
    {
        throw new PendingException();
    }

}
