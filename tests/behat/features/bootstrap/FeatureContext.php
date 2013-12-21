<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * @Given /^I am on the Ilios home page$/
     */
    public function iAmOnTheIliosHomePage ()
    {
        $this->visit("/");
    }


    /**
     * @Then /^"(.*?)" should contain "(.*?)"/
     */
    public function shouldContain ($id, $content)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I navigate to the "(.*?)" tab$/
     */
    public function iNavigateToTheTab ($tabName) {
        throw new PendingException();
    }

    /**
     * @Given /^I click the "(.*?)" link$/
     */
    public function iClickTheLink ($linkText)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I click "(.*?)"$/
     */
    public function iClick ($linkText)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I click the first "(.*?)"$/
     */
    public function iClickTheFirst ($linkText)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I click the "(.*?)" button$/
     */
    public function iClickTheButton ($buttonText)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I click the first element with class "(.*?)"$/
     */
    public function iClickTheFirstElementWithClass ($elementText)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I enter "(.*?)" into "(.*?)"$/
     */
    public function iEnterInto ($content, $field)
    {
        $this->fillField($field, $content);
    }

    /**
     * @Given /^I set "(.*?)" to "(.*?)"$/
     */
    public function iSetTo ($content, $field)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I log in as "(.*?)" with password "(.*?)"$/
     */
    public function iLogInAsWithPassword ($user, $login)
    {
        $this->clickLink("Login");
        $this->iEnterInto($user, "User Name");
        $this->iEnterInto($login, "Password");
        $this->pressButton("Login");
    }

    /**
     * @Then /^there is a "(.*?)" class$/
     */
    public function thereIsAClass ($class)
    {
        throw new PendingException();
    }

    /**
     * @Then /^there is no "(.*?)" class$/
     */
    public function thereIsNoClass ($class)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I click the "(.*?)" button for "(.*?)"$/
     */
    public function iClickTheButtonFor ($buttonText, $section)
    {
        throw new PendingException();
    }

    /**
     * @Then /^I select "(.*?)" from "(.*?)"$/
     */
    public function iSelectFrom ($arg1, $arg2)
    {
       throw new PendingException();
    }

    /**
     * @Then /^I click all expanded toggles$/
     */
    public function iClickAllExpandedToggles ()
    {
        throw new PendingException();
    }
}
