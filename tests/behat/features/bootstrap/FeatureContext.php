<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Context\Step\When,
    Behat\Behat\Context\Step\Then,
    Behat\Behat\Context\Step\Given,
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
    public function spin ($lambda, $wait = 60)
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
    public function exceptionSpin ($lambda, $wait = 60)
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
     * Some of the tabs are not navigatable in some test drivers
     * so these are handled specially.
     *
     * @When /^I navigate to the "(.*?)" tab$/
     *
     * @param string $tabName
     */
    public function iNavigateToTheTab ($tabName)
    {
        switch($tabName){
            case 'Learner Groups':
                return new When('I go to "/ilios.php/group_management"');
                break;
            case 'Courses and Sessions':
                return new When('I go to "/ilios.php/course_management"');
                break;
            default:
                return new When("I click on the xpath \"//*[@id='topnav']//a[text()[normalize-space(.)='{$tabName}']]\"");
        }

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
        }, 5);
        $this->fillField("User Name", $user);
        $this->fillField("Password", $login);
        $this->pressButton("login_button");
        // Wait for login/logout link to reappear before continuing. Always id logout_link, yeah, misleading.
        $this->spin(function($context) {
            return ($context->getSession()->getPage()->findById('logout_link'));
        }, 5);
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
        $node = $this->findXpathElement("//*[@id='{$dialogId}']//span[contains(.,'{$itemText}') and contains(@class,'ygtvlabel')]");
        $node->click();
    }

    /**
     * The titles can not be clicked directly in tree pickers
     * instead we have to click the + button next to them
     *
     * @When /^I expand "([^"]*)" tree picker list in "([^"]*)" dialog$/
     *
     * @param string $itemText
     * @param string $dialogId
     */
    public function iExpandTreePickerListInDialog ($itemText, $dialogId)
    {
        $xpath = "//*[@id='{$dialogId}']" .
          "//tr[contains(.,'{$itemText}')]" .
          "//a[@class='ygtvspacer']";
        $node = $this->findXpathElement($xpath);
        $node->click();
    }

    /**
     * @When /^I press the "([^"]*)" button in "([^"]*)" dialog$/
     */
    public function iPressTheButtonInDialog ($buttonText, $dialogId)
    {
        $button = $this->findXpathElement("//*[@id='{$dialogId}']//button[contains(.,'{$buttonText}')]");
        $this->waitOnElementVisibility($button);
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
        $this->waitOnElementVisibility($element);
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
        });
    }

    /**
     * Click on text inside element
     *
     * @param string $test
     * @param string $elementId
     * @Given /^I click on the text "([^"]*)" in the "([^"]*)" element$/
     *
     */
    public function iClickOnTheTextInTheElement($text, $elementId)
    {
        $this->exceptionSpin(function($context) use ($text, $elementId) {
            $el = $context->getSession()->getPage()->find(
                'xpath',
                "//*[@id='{$elementId}']//*[normalize-space(text())='$text']"
            );
            if ($el === null) {
                throw new Exception(sprintf('Could not find text: "%s"', $text));
            }
            $el->click();
        });
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
        });

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
        });
    }

    /**
     * @Then /^I should not see dirty state$/
     */
    public function iShouldNotSeeDirtyState ()
    {
        $this->exceptionSpin(function($context) {
            $context->assertElementNotOnPage('.dirty_state');
        });
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
     * Set the browser window size
     * @Given /^I set the window size to "([^"]*)" x "([^"]*)"$/
     *
     * @param integer $x
     * @param integer $y
     */
    public function iSetTheWindowSizeTo($x, $y)
    {
        $this->getSession()->resizeWindow((int)$x, (int)$y, 'current');
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
        $userName = $this->findXpathElement("//*[@id='utility']/ul/li[1]")->getAttribute('title');
        $this->iClickOnTheXpath("//*[@id='permissions_autolist']//*[@title='${userName}']");
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
        $userName = $this->findXpathElement("//*[@id='utility']/ul/li[1]")->getAttribute('title');
        $this->visit('ilios.php/management_console/');
        $this->iClickOnTheText('Manage Permissions');
        $this->iClickOnTheXpath("//*[@id='permissions_autolist']//*[@title='${userName}']");
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
     * Check for a count of elements contained in another element
     *
     * @Then /^I should see (\d+) "([^"]*)" elements in the "([^"]*)" element$/
     * @param integer $num the numbers of elements we are looking for
     * @param string $countElement the element we are counting
     * @param string $containingElement the element we are searchign within
     */
    public function iShouldSeeElementsInTheElement($num, $countElement, $containingElement)
    {
        $el = $this->getSession()->getPage()->find('css', $containingElement);
        $this->assertSession()->elementsCount('css', $num, $countElement, $el);
    }

   /**
    * The YUI editors are in an iframe and the text area is inaccessible so
    * in order to fill them we have to access the JS object directly and use the
    * included setEditorHTML method
    *
    * @When /^I fill the editor "([^"]*)" with "([^"]*)"$/
    *
    * @param string $editorJsObject a fully qualified reference to yui editors object in
    *        the global namespace eg 'ilios.cm.editCourseObjectiveDialog.ecoEditor'
    * @param string $text
    */
    public function iFillTheEditorWith($editorJsObject, $text)
    {
        $this->getSession()->executeScript($editorJsObject . '.setEditorHTML("' . $text . '");');
    }

   /**
    * We have to use the JS directly in order to add events to a dhtmlx calendar
    *
    * @When /^I add a calendar event from "([^"]*)" to "([^"]*)"$/
    *
    * @param string $start a date time string
    * @param string $end a date time string
    */
    public function iAddAnEvent($start, $end)
    {
        $script = 'window.scheduler.addEvent({' .
            'start_date:"' . $start . '",' .
            'end_date:"' . $end . '"' .
            '});';
        $this->getSession()->executeScript($script);
    }

   /**
    * We have to use the JS directly in order to set yui calendar
    *
    * @When /^I set yui calendar "([^"]*)" to "([^"]*)"$/
    *
    * @param string $objectName the global reference to the YUI calendar object
    * @param string $date a date time string
    */
    public function iSetYUICalendar($objectName, $date)
    {
        $script = "{$objectName}.select('{$date}');";
        $this->getSession()->executeScript($script);
    }

    /**
     * Override the MinkContext::assertPageContainsText in order to add a
     * spin delay to the search
     * @param string $text
     */
    public function assertPageContainsText($text)
    {
        $text = $this->fixStepArgument($text);
        $this->exceptionSpin(function($context) use ($text) {
            $context->assertSession()->pageTextContains($text);
        });
    }


    /**
     * Override the MinkContext::assertElementContainsText in order to add a
     * spin delay to the search
     *
     * @param string $element
     * @param string $text
     */
    public function assertElementContainsText($element, $text)
    {
        $text = $this->fixStepArgument($text);
        $this->exceptionSpin(function($context) use ($element, $text) {
            $context->assertSession()->elementTextContains('css', $element, $text);
        });
    }

    /**
     * Override the MinkContext::selectOption in order to add a
     * spin delay to the search
     *
     * @param string $element
     * @param string $text
     */
    public function selectOption($select, $option)
    {
        $select = $this->fixStepArgument($select);
        $option = $this->fixStepArgument($option);
        $this->exceptionSpin(function($context) use ($select, $option) {
            $context->getSession()->getPage()->selectFieldOption($select, $option);
        });
    }

    /**
     * Override the MinkContext::pressButton in order to add a
     * spin delay to the search
     * @param string $locator
     */
    public function pressButton($locator)
    {
        $locator = $this->fixStepArgument($locator);
        $this->spin(function($context) use ($locator) {
            $el = $context->getSession()->getPage()->findButton($locator);
            if(is_null($el) or !$el->isVisible()){
                return false;
            }
            $el->press();
            return true;
        });
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
     * See if an element is visible, and spin and try again if not
     * This comes up with buttons and links that exist in dialogs before they can be
     * pressed.  So we need to wait for them to be visible first
     *
     * @param \Behat\Mink\Element $element
     */
    public function waitOnElementVisibility(\Behat\Mink\Element\NodeElement $element)
    {
        $this->exceptionSpin(function($context) use ($element) {
            if (!$element->isVisible()) {
                throw new Exception(
                    sprintf('The element at xpath "%s" is not visible', $element->getXpath())
                );
            }
        });
    }

    /**
     * Look for text in an element by its position on the page
     *
     * @Then /^I should see "([^"]*)" in the (\d+)(?:st|nd|rd|th) "([^"]*)" element$/
     *
     * @param $text string
     * @param $num integer
     * @param $css string
     */
    public function iShouldSeeInTheNumElement($text, $num, $css)
    {
        $this->exceptionSpin(function($context) use ($text, $num, $css) {
            $i = $num -1; //array index
            $elements = $context->getSession()->getPage()->findAll('css', $css);
            if(!array_key_exists($i, $elements)){
                throw new Exception(
                    sprintf('There %d element %s was not found on the page', $num, $css)
                );
            }
            $elementText = $elements[$i]->getText();
            if(strpos($elementText, $text) === FALSE){
                throw new Exception(
                    sprintf('The %d element %s does not contain %s', $num, $css, $text)
                );
            }
        });
    }

    /**
     * Checks, that text appears on the page exactly some number of times
     *
     * @Then /^I should see (?P<num>\d+) "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
     */
    public function iShouldSeeCountTextinElement($num, $text, $elementCss)
    {
        $element = $this->getSession()->getPage()->find('css', $elementCss);
        $count = substr_count($element->getText(), $text);
        if($count != $num){
            throw new Exception(
                sprintf(
                    'Expected to find %s ocurence of "%s" in "%s", but found %s instead',
                    $num, $text, $elementCss, $count
                )
            );
        }
    }

    /**
     * Create a test program for use in other features
     *
     * @todo - eventually this should be done by directly interacting with the DB
     * or models, for now just step through the process.
     * @Given /^I create a test program "([^"]*)"$/
     *
     * @param string $programName
     */
    public function iCreateATestProgram($programName)
    {
        $this->visit('/ilios.php/program_management');
        $this->clickLink('Search');
        $this->fillField('program_search_terms', $programName);
        $this->iClickOnTheXpath('//*[@id="program_search_picker"]//span[@class="search_icon_button"]');
        sleep(2);
        $searchResult = $this->getSession()->getPage()->find(
            'xpath',
            "//*[@id='program_search_results_list']/li/span[normalize-space(text()) = '{$programName}']"
        );
        if(!is_null($searchResult)){
            return true;
        }
        $shortProgramName = substr(strtolower(str_replace(' ', '', $programName)),0,10);
        return array(
            new When('I navigate to the "Programs" tab'),
            new When('I follow "Add Program"'),
            new When('I fill in "' . $programName . '" for "new_program_title"'),
            new When('I fill in "' . $shortProgramName . '" for "new_short_title"'),
            new When('I select "4" from "new_duration_selector"'),
            new When('I press "Done"'),
            new When('I wait for "add_new_program_year_link" to be visible'),
            new When('I should see "' . $shortProgramName . '"'),
            new When('I follow "show_more_or_less_link"'),
            new When('I press "Publish Now"'),
            new When('I should see "Published" in the "#parent_publish_status_text" element'),
            new When('I press "Add New Program Year"'),
            new When('I select "2013-2014" from "1_program_year_title"'),
            //fragile xpath link to the competencies edit button, which has no ID
            new When('I click on the xpath "//*[@id=\'1_collapser\']/form/div[3]/div[3]/a"'),
            new When('I expand "Medical Knowledge" tree picker list in "competency_pick_dialog" dialog'),
            new When('I click "Treatment" tree picker item in "competency_pick_dialog" dialog'),
            new When('I click "Inquiry and Discovery" tree picker item in "competency_pick_dialog" dialog'),
            new When('I press the "Done" button in "competency_pick_dialog" dialog'),
            new When('I follow "Add Objective"'),
            new When('I fill the editor "ilios.pm.eot.editObjectiveTextDialog.eotEditor" with "Test program objective 1"'),
            new When('I select "Treatment (Medical Knowledge)" from "eot_competency_pulldown"'),
            new When('I press the "Done" button in "edit_objective_text_dialog_c" dialog'),
            new When('I follow "Add Objective"'),
            new When('I fill the editor "ilios.pm.eot.editObjectiveTextDialog.eotEditor" with "Test program objective 2"'),
            new When('I select "Inquiry and Discovery (Medical Knowledge)" from "eot_competency_pulldown"'),
            new When('I press the "Done" button in "edit_objective_text_dialog_c" dialog'),
            new When('I publish the 1st program year'),
            new When('I should see "Published" in the "#1_child_draft_text" element')
        );
    }

    /**
     * Create a test program for use in other features
     *
     * @todo - eventually this should be done by directly interacting with the DB
     * or models, for now just step through the process.
     * @Given /^I create a test learner group for class of "(\d+)" in "([^"]*)"$/
     *
     * @param string $classYear
     * @param string $programName
     */
    public function iCreateATestLearnerGroupIn($classYear, $programName)
    {
        $table = new Behat\Gherkin\Node\TableNode(
        "\n| first  | last  | email | ucid |" .
        "\n| Test   | Student | first@example.com | 123456 |"
        );
        return array(
            new Given('the following learners exist in the "' . $classYear . '" "' . $programName . '" program:', $table),
            new When('I navigate to the "Learner Groups" tab'),
            new When('I follow "Select Program and Cohort"'),
            new When('I expand "' . $programName . '" tree picker list in "cohort_pick_dialog_c" dialog'),
            new When('I click "Class of ' . $classYear . '" tree picker item in "cohort_pick_dialog_c" dialog'),
            new When('I press "Add a New Student Group"'),
            new Then('I should see "Default Group Number 1"')
        );
    }

    /**
     * Create a test course for use in other features
     *
     * @todo - eventually this should be done by directly interacting with the DB
     * or models, for now just step through the process.
     * @Given /^I create a test course "([^"]*)" for class of "([^"]*)" in "([^"]*)"$/
     *
     * @param string $courseName
     * @param string $cohortYear
     * @param string $programName
     */
    public function iCreateATestCourseForClassOfIn($courseName, $cohortYear, $programName)
    {
        return array(
            new When('I navigate to the "Courses and Sessions" tab'),
            new When('I press "Add New Course"'),
            new When('I fill in "' . $courseName . '" for "new_course_title"'),
            new When('I press the "Done" button in "course_add_dialog" dialog'),
            new Then('I should see "' . $courseName . '"'),
            new When('I reload the page'), //necessary step to work around issue of the link not showing up all the time
            new When('I follow "show_more_or_less_link"'),
            new When('I follow "Select Program Cohorts for Course"'),
            new When('I expand "' . $programName . '" tree picker list in "cohort_pick_dialog_c" dialog'),
            new When('I click "Class of ' . $cohortYear . '" tree picker item in "cohort_pick_dialog_c" dialog'),
            new When('I press the "Done" button in "cohort_pick_dialog_c" dialog'),
            new When('I press "Save All as Draft"'),
            new When('I press the "Yes" button in "ilios_inform_panel" dialog')
        );
    }

    /**
     * Delete any existing learner groups in a program
     *
     * @todo - eventually this should be done by directly interacting with the DB
     * or models, for now just step through the process.
     * @Given /^I clear all learner groups in the "([^"]*)" "([^"]*)" program$/
     *
     * @param string $classYear
     * @param string $programName
     */
    public function iClearAllLearnerGroupsInTheProgram($classYear, $programName)
    {
        $this->visit('/ilios.php/group_management');
        $this->clickLink('Select Program and Cohort');
        $this->iExpandTreePickerListInDialog($programName, 'cohort_pick_dialog_c');
        $this->iClickTreePickerItemInDialog("Class of {$classYear}", 'cohort_pick_dialog_c');
        $this->iWaitForToBeVisible('program_cohort_title');
        while($link = $this->getSession()->getPage()->find(
            'xpath',
            "//*[@id='group_container']//div[contains(@class,'delete_widget')]"
        )){
                if($link->isVisible()){
                    $link->click();
                    $this->iPressTheButtonInDialog('Yes', 'ilios_inform_panel');
                    sleep(1); //prevent clicking the same link a few times
                }
        }
    }

    /**
     * Add new learners to a group, if they already exist thats ok.
     *
     * @Given /^the following learners exist in the "([^"]*)" "([^"]*)" program:$/
     *
     * @param string $classYear
     * @param string $programName
     * @param TableNode $learners
     */
    public function givenTheFollowingLearnersExistInTheProgram($classYear, $programName, TableNode $learners)
    {
        $this->visit('/ilios.php/group_management');
        $this->clickLink('Select Program and Cohort');
        $this->iExpandTreePickerListInDialog($programName, 'cohort_pick_dialog_c');
        $this->iClickTreePickerItemInDialog("Class of {$classYear}", 'cohort_pick_dialog_c');
        foreach($learners->getHash() as $learner){
            $this->pressButton('Add New Members to Cohort');
            $this->fillField('em_first_name', $learner['first']);
            $this->fillField('em_last_name', $learner['last']);
            $this->fillField('em_email', $learner['email']);
            $this->fillField('em_uc_id', $learner['ucid']);
            $this->pressButton("Add User");
            do{
                $done = true;
                $transactionStatus = $this->getSession()->getPage()->find(
                    'xpath',
                    '//*[@id="em_transaction_status" and contains(., "User has been added")]'
                );
                if ($transactionStatus === null) {
                    $done = false;
                    $alert = $this->getSession()->getPage()->find(
                        'xpath',
                        '//*[@id="ilios_alert_panel"]'
                    );
                    if(!is_null($alert) && $alert->isVisible()){
                        $this->iPressTheButtonInDialog('Ok', 'ilios_alert_panel');
                        $done = true;
                    }
                }
            } while(!$done);
            $this->iPressTheButtonInDialog('Done', 'add_new_members_dialog');
        }

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
     * When a step fails take a screen shot.
     * This should work with any of the selenium2 drivers incuding phantomjs
     *
     * It outputs the file location above the failed step.
     * @todo find a better way to techo the path to the console.
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep($event)
    {
        if ($event->getResult() == 4) {
            if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
                $stepText = $event->getStep()->getText();
                $fileTitle = preg_replace("#[^a-zA-Z0-9\._-]#", '', $stepText);
                $fileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileTitle . '.png';
                $screenshot = $this->getSession()->getDriver()->getScreenshot();
                file_put_contents($fileName, $screenshot);
                print "Screenshot for '{$stepText}' placed in {$fileName}\n";
            }
        }
    }
}
