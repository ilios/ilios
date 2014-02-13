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
     * @When /^I navigate to the "(.*?)" tab$/
     */
    public function iNavigateToTheTab ($tabName)
    {
        $this->getSession()->getPage()->find('css', '.tabs')->findLink($tabName)->click();
    }

    /**
     * @When /^I click the first "(.*?)"$/
     */
    public function iClickTheFirst ($linkText)
    {
        throw new PendingException();
    }

    /**
     * @When /^I click the first element with class "(.*?)"$/
     */
    public function iClickTheFirstElementWithClass ($elementText)
    {
        throw new PendingException();
    }

    /**
     * @When /^I log in as "(.*?)" with password "(.*?)"$/
     */
    public function iLogInAsWithPassword ($user, $login)
    {
        $this->clickLink("Login");
        $this->fillField("User Name", $user);
        $this->fillField("Password", $login);
        $this->pressButton("login_button");
        // @todo: fix this garbage hack
        $this->getSession()->wait(1000);
    }

    /**
     * @When /^I click the "(.*?)" link for "(.*?)"$/
     */
    public function iPressTheButtonFor ($buttonText, $section)
    {
        $session = $this->getSession();
        //find('.row', {:visible => true, :text => section}).click_link(button_text)
        $row = $session->getPage()->find(
            'xpath', "//*[contains(.,'{$section}') and contains(@class,'row')]");
        $row->clickLink($buttonText);
    }

    /**
     * @When /^I click "([^"]*)" tree picker item in "([^"]*)" dialog$/
     */
    public function iClickTreePickerItemInDialog ($itemText, $dialogId)
    {
        $dialog = $this->getSession()->getPage()->find('css', "#{$dialogId}");
        $node = $dialog->find('xpath', "//span[contains(.,'{$itemText}') and contains(@class,'ygtvlabel')]");
        $node->click();
    }

    /**
     * @When /^I press the "([^"]*)" button in "([^"]*)" dialog$/
     */
    public function iPressTheButtonInDialog ($buttonText, $dialogId)
    {
        $dialog = $this->getSession()->getPage()->find('css', "#{$dialogId}");
        $button = $dialog->find('xpath', "//button[contains(.,'{$buttonText}')]");
        $button->press();
    }

    /**
     * @When /^I press the element with id "([^"]*)"$/
     */
    public function iPressTheElementWithId ($id)
    {
        $element = $this->getSession()->getPage()->find('css', "#{$id}");
        $element->press();
    }

    /*
     * @When /^I click all expanded toggles$/
     */
    public function iClickAllExpandedToggles ()
    {
        $links = $this->getSession()->getPage()->findAll('css', '.expanded .toggle');
        foreach ($links as $link) {
            $link->click();
        }
    }

    /**
     * @When /^I click all collapsed toggles$/
     */
    public function iClickCollapsedToggles ()
    {
        $links = $this->getSession()->getPage()->findAll('css', '.collapsed .toggle');
        foreach ($links as $link) {
            $link->click();
        }
    }

    /**
     * @When /^I wait (\d+) second(?:s?)$/
     */
    public function iWaitSeconds($seconds)
    {
        $this->getSession()->wait($seconds * 1000);
    }


    /**
     * @When /^I set "([^"]*)" to "([^"]*)"$/
     */
    public function iSetTo($id, $text)
    {
        $this->getSession()->getPage()->find('css', "#{$id}")->setValue($text);
    }

    /**
     * @When /^I publish the (\d+)(?:st|nd|rd|th) program year$/
     */
    public function iPublishProgramYear ($cnumber)
    {
        $this->pressButton("{$cnumber}_child_publish");
    }

    /**
     * @Then /^I should see dirty state$/
     */
    public function iShouldSeeDirtyState ()
    {
        $this->assertElementOnPage('.dirty_state');
    }

    /**
     * @Then /^I should not see dirty state$/
     */
    public function iShouldNotSeeDirtyState ()
    {
        $this->assertElementNotOnPage('.dirty_state');
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
