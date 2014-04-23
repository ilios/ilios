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
     * Helper function for slow loading pages. http://docs.behat.org/cookbook/using_spin_functions.html
     * Needed for Sauce Labs integration to work consistently probably because...
     * ...initial page load includes a lot of resources and may need some extra time to complete.
     * @todo: remove unnecessary elements, move elements that don't need to be in the critical path out of the critical path, and get rid of this
     */
    public function spin ($lambda, $wait = 20)
    {
        for ($i = 0; $i < $wait; $i++)
        {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        $backtrace = debug_backtrace();

        throw new Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()"
        );
    }
    
    /**
     * Duplicates FeatureContext::spin
     * Looks for exceptions instead of booleans
     * @param callback $lambda 
     * @param int $wait
     */
    public function exceptionSpin ($lambda, $wait = 20)
    {
        for ($i = 1; $i <= $wait; $i++)
        {
            try {
                $lambda($this);
                return true;
            } catch (Exception $e) {
                if($i == $wait){
                    throw $e;
                }
            }
            sleep(1);
        }
    }

    /**
     * @Given /^I am on the Ilios home page$/
     */
    public function iAmOnTheIliosHomePage ()
    {
        $this->visit("/");
        // Initial page load includes a lot of resources and may need some extra time to complete.
        // @todo: remove unnecessary elements, move elements that don't need to be in the critical path out of the critical path, and get rid of this
        $context = $this;
        $this->spin(function($context) {
            return (count($context->getSession()->getPage()->findById('content')) > 0);
        });
    }

    /**
     * @When /^I navigate to the "(.*?)" tab$/
     * 
     * @param string $tabName
     */
    public function iNavigateToTheTab ($tabName)
    {
        $this->iClickOnTheXpath("//*[@id='topnav']//a[text()[normalize-space(.)='{$tabName}']]");
    }

    /**
     * @When /^I log in as "(.*?)" with password "(.*?)"$/
     */
    public function iLogInAsWithPassword ($user, $login)
    {
        $this->clickLink("Login");
        $context = $this;
        $this->spin(function($context) {
            return (count($context->getSession()->getPage()->findField('username')) > 0);
        });
        $this->fillField("User Name", $user);
        $this->fillField("Password", $login);
        $this->pressButton("login_button");
        // Wait for login/logout link to reappear before continuing. Always id logout_link, yeah, misleading.
        $this->spin(function($context) {
            return ($context->getSession()->getPage()->findById('logout_link'));
        });
    }

    /**
     * @When /^I click the "(.*?)" link for "(.*?)"$/
     */
    public function iPressTheButtonFor ($buttonText, $section)
    {
        //find('.row', {:visible => true, :text => section}).click_link(button_text)
        $row = $this->findXpathElement(
            "//*[contains(.,'{$section}') and contains(@class,'row')]"
        );
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
        // Wait for element to appear before trying to press it.
        $this->spin(function ($context) use ($id) {
            return $context->getSession()->getPage()->find('css', "#{$id}");
        });

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
     * @Given /^I click on the text "([^"]*)"$/
     */
    public function iClickOnTheText($text)
    {
        $this->exceptionSpin(function($context) use ($text) {
            $el = $context->getSession()->getPage()->find('xpath', "//*[normalize-space(text())='$text']");
            if ($el === null) {
                throw new Exception(sprintf('Could not find text: "%s"', $text));
            }
            $el->click();
        }, 5);
    }

    /**
     * @Given /^I click on the xpath "([^"]*)"$/
     * 
     * @param string $xpath
     */
    public function iClickOnTheXpath($xpath)
    {
        $el = $this->findXpathElement($xpath);
        $el->click();
    }

    /**
     * @Given /^I click on the text starting with "([^"]*)"$/
     */
    public function iClickOnTheTextStartingWith($text)
    {
        $this->exceptionSpin(function($context) use ($text) {
            $el = $context->getSession()->getPage()->find('xpath', "//*[starts-with(normalize-space(text()),'$text')]");
            if ($el === null) {
                throw new Exception(sprintf('Could not find text: "%s"', $text));
            }
            $el->click();
        }, 5);
        
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
        $this->exceptionSpin(function($context) {
            $context->assertElementOnPage('.dirty_state');
        }, 5);   
    }

    /**
     * @Then /^I should not see dirty state$/
     */
    public function iShouldNotSeeDirtyState ()
    {
        $this->exceptionSpin(function($context) {
            $context->assertElementNotOnPage('.dirty_state');
        }, 5);
    }

    /**
     * @Given /^I wait for "([^"]*)" to be enabled$/
     */
    public function iWaitForToBeEnabled($id)
    {
        $this->spin(function($context) use ($id) {
            $el = $context->getSession()->getPage()->find('css', "#{$id}");
            if ($el) {
                return ! $el->hasAttribute('disabled');
            }
            return false;
        });
    }

    /**
     * @Given /^I wait for "([^"]*)" to be visible$/
     */
    public function iWaitForToBeVisible($id)
    {
        $this->spin(function($context) use ($id) {
            $el = $context->getSession()->getPage()->find('css', "#{$id}");
            if ($el) {
                return $el->isVisible();
            }
            return false;
        });
    }
    
    /**
     * Use the school selector to see if we already have permissions
     * If not then attempt to add them
     * @Given /^I have access in the "([^"]*)" school$/
     * 
     * @param string $school
     */
    public function iHaveAccessToTheSchool($school)
    {
        if($this->accessToSchool($school)){
            return true;
        }
        $this->visit('ilios.php/management_console/');
        $this->iClickOnTheText('Manage Permissions');
        $this->iClickOnTheXpath("//*[@id='permissions_autolist']//*[text()[contains(., 'Zero')]]");
        $this->pressButton('permissions_user_picker_continue_button');
        //we have to wait for the user permissions to load otherwise any previsouly
        //selected schools will be removed.
        $count = 0;
        do{
            sleep(1);
            $el = $this->getSession()->getPage()->find(
                'xpath', 
                "//*[@id='current_school_permissions_div']//*[text()='None']"
            );
            $count++;
        } while(count($el) > 0 and $count < 5);
        $this->iClickOnTheText('Change School Access');
        $this->iClickOnTheXpath("//*[@id='school_autolist']//*[normalize-space(text())='{$school}']");
        $this->iClickOnTheXpath("//*[@id='school_picker_dialog']//*[normalize-space(text())='Done']");
        $this->iClickOnTheText('Finished');
    }
    
    /**
     * Check to see if we are currently viewing a school by name
     * 
     * @Given /^I am in the "([^"]*)" school$/
     * @param string $school
     */
    public function iAmInTheSchool($school)
    {
        $this->exceptionSpin(function($context) use ($school) {
            if(!$context->inSchool($school)){
                throw new Exception(sprintf('Not in the school: "%s"', $school));
            }
        }, 5);
    }

    /**
     * Change to a school by name
     * 
     * @When /^I change to the \"([^\']*)\" school$/
     * @param string $school
     */
    public function iChangeToTheSchool($school)
    {
        if(!$this->accessToSchool($school)){
            throw new Exception(sprintf('No access to the school: "%s"', $school));
        }
        if($this->inSchool($school)){
            return true;
        }
        $this->iNavigateToTheTab('Home');
        $select = $this->findXpathElement("//*[@id='view-switch']");
        $select->selectOption($school);
    }
    
    /**
     * Check if we are in the school
     * First we use the school selector, but that is only present when we have
     * access to multiple schools.  If we are in a single school it could just be 
     * the default school, so we have to go and look at the permissions specifically
     * 
     * @param string $school
     * @return boolean
     */
    public function accessToSchool($school)
    {
        $this->iNavigateToTheTab('Home');
        $select = $this->getSession()->getPage()->findAll('css', '#view-switch');
        if($select){
            $options = $this->getSession()->getPage()->findAll('css', '#view-switch option');
            foreach($options as $option){
                if(trim($option->getText()) == $school){
                    return true;
                }
            }
        }
        $this->visit('ilios.php/management_console/');
        $this->iClickOnTheText('Manage Permissions');
        $this->iClickOnTheXpath("//*[@id='permissions_autolist']//*[text()[contains(., 'Zero')]]");
        $this->pressButton('permissions_user_picker_continue_button');
        
        //test for the school more than once since it can take a few moments to load
        $count = 0;
        do{
            sleep(1);
            $el = $this->getSession()->getPage()->find(
                'xpath', 
                "//*[@id='current_school_permissions_div']//*[text()[contains(.,'{$school}')]]"
            );
            $count++;
        } while(count($el) < 1 and $count < 5);

        return count($el) > 0;
    }
    
    /**
     * Check to see if we are currently in a school
     * Works be searchign for the school name in the header section 'view-current'
     *
     * @param type $school
     * @return boolean
     */
    public function inSchool($school)
    {
        $this->iNavigateToTheTab('Home');
        $el = $this->findXpathElement("//*[@id='view-current']");
        
        return strpos($el->getText(), $school) !== false;
    }
    
    /**
     * Override the MinkContext::assertPageContainsText in order to add a 
     * spin delay to the serach
     * @param string $text
     */
    public function assertPageContainsText($text)
    {
        $this->exceptionSpin(function($context) use ($text) {
            $context->assertSession()->pageTextContains($context->fixStepArgumentPublic($text));
        }, 5);
    }
    
    /**
     * Override the MinkContext::pressButton in order to add a 
     * spin delay to the search
     * @param string $locator
     */
    public function pressButton($locator)
    {
        $this->spin(function($context) use ($locator) {
            $button = $context->getSession()->getPage()->findButton($locator);
            return !is_null($button);
        });
        parent::pressButton($locator);
    }
    
    /**
     * Override the MinkContext::clickLink in order to add a 
     * spin delay to the search
     * @param string $locator
     */
    public function clickLink($locator)
    {
        $this->spin(function($context) use ($locator) {
            $link = $context->getSession()->getPage()->findLink($locator);
            return !is_null($link);
        });
        parent::clickLink($locator);
    }
    
    /**
     * MinkContext::reload to add a built delay to page reloads
     */
    public function reload()
    {
        $this->getSession()->reload();
        sleep(5);
    }
    
    /**
     * MinkContext::visit to add a built delay
     * @param string $page
     */
    public function visit($page)
    {
        $this->getSession()->visit($this->locatePath($page));
        sleep(5);
    }
    
    /**
     * Search for an element by an xpath search with a timeout
     * This allows other methods to easily ensure an element is present to avoid
     * Critical errors
     * 
     * @todo see if this can replace some of the existing spins
     * @param string $xpath
     * @return \Behat\Mink\Element
     */
    public function findXpathElement($xpath)
    {
        $element = null;
        $this->exceptionSpin(function($context) use (&$element, $xpath) {
            $element = $context->getSession()->getPage()->find('xpath', $xpath);
            if ($element === null) {
                throw new Exception(sprintf('Could not find xpath: "%s"', $xpath));
            }
        });
        
        return $element;
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

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    public function fixStepArgumentPublic($argument)
    {
        return parent::fixStepArgument($argument);
    }
}
