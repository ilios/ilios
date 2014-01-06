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
    public function iNavigateToTheTab ($tabName)
    {
        $this->getSession()->getPage()->find('css', '.tabs')->findLink($tabName)->click();
    }

    /**
     * @Given /^I click the "(.*?)" link$/
     */
    public function iClickTheLink ($linkText)
    {
        $this->clickLink($linkText);
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
     * @Given /^I click the first element with class "(.*?)"$/
     */
    public function iClickTheFirstElementWithClass ($elementText)
    {
        throw new PendingException();
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
        $this->fillField("User Name", $user);
        $this->fillField("Password", $login);
        $this->pressButton("login_button");
    }

    /**
     * @Then /^I click the "(.*?)" button for "(.*?)"$/
     */
    public function iPressTheButtonFor ($buttonText, $section)
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
        $links = $this->getSession()->getPage()->findAll('css', '.expanded .toggle');
        foreach ($links as $link) {
            $link->click();
        }
    }

    /**
     * @Given /^I wait (\d+) seconds$/
     */
    public function iWaitSeconds($seconds)
    {
        $this->getSession()->wait($seconds * 1000);
    }

    /**
     * @AfterScenario
     *
     * PhantomJS does not clear the session properly, so we must
     * implicitly do so.
     * @see http://stackoverflow.com/a/17306831/307333
     */
    public function after ($event)
    {
        $this->getSession()->reset();
    }
}
