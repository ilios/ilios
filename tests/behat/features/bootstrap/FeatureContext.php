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
     * @var array configuration parameters
     *
     */
    protected $params;

    /**
     * Override the constructor to get access to the configuration params
     *
     */
    public function __construct(array $arr)
    {
        $this->params = $arr;
    }

    /**
     * Create and get a connection to the database
     * store it statically because this class gets instantiated on every
     * feature
     *
     * @return \PDO
     */
    protected function getDbConnection()
    {
        $db = new PDO(
            $this->params['database_dsn'],
            $this->params['database_user'],
            $this->params['database_password']
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }

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
        //we have to sleep after login to let the page load, otherwise we get
        //a transaction error
        sleep(3);
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
        $email = $this->findXpathElement("//*[@id='utility']/ul/li[1]")->getAttribute('title');
        $sql = 'INSERT INTO permission (user_id, table_row_id, table_name, can_read, can_write) VAlUES (' .
            '(SELECT user_id FROM user WHERE email=?),' .
            '(SELECT school_id from school WHERE title=?),"school",1,1)';
        $db = $this->getDbConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute(array($email, $school));
        $stmt = null;
        $db = null;
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
        $email = $this->findXpathElement("//*[@id='utility']/ul/li[1]")->getAttribute('title');
        $sql = 'SELECT permission_id FROM permission WHERE user_id = ' .
            '(SELECT user_id FROM user WHERE email=?) AND ' .
            'table_name = "school" AND ' .
            'table_row_id =(SELECT school_id from school WHERE title=?)';
        $db = $this->getDbConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute(array($email, $school));
        $bool = $stmt->rowCount() > 0;
        $stmt = null;
        $db = null;

        return $bool;
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
        try{
            $db = $this->getDbConnection();
            $results = $db->query("SELECT program_id FROM program WHERE title = '{$programName}'");
            if ($results->rowCount() > 0) {
                return true;
            }


            $db->beginTransaction();
            $shortProgramName = substr(strtolower(str_replace(' ', '', $programName)),0,10);

            $db->exec("INSERT INTO program (title, short_title, duration, owning_school_id) VALUES ('{$programName}','{$shortProgramName}',4,1)");
            $programId = $db->lastInsertId();

            $db->exec("INSERT INTO program_year (start_year, program_id) VALUES ('2013',{$programId})");
            $programYearId = $db->lastInsertId();

            $db->exec("INSERT INTO cohort (title, program_year_id) VALUES ('Class of 2017',{$programId})");

            $publishStmt = $db->prepare('INSERT INTO publish_event (administrator_id, machine_ip, table_name, table_row_id) VALUES (1,"10.10.10.10",?,?)');
            $publishStmt->execute(array('program', $programId));
            $programPublishEventId = $db->lastInsertId();

            $publishStmt->execute(array('program_year', $programYearId));
            $programYearPublishEventId = $db->lastInsertId();

            $publishStmt = null;

            $db->exec("UPDATE program SET publish_event_id = {$programPublishEventId} WHERE program_id = {$programId}");
            $db->exec("UPDATE program_year SET publish_event_id = {$programYearPublishEventId} WHERE program_year_id = {$programYearId}");

            $competencyStmt = $db->prepare('INSERT INTO program_year_x_competency (program_year_id, competency_id) VALUES (?,?)');
            $competencyStmt->execute(array($programYearId, 51));
            $competencyStmt->execute(array($programYearId, 52));
            $competencyStmt = null;

            $objectiveStmt = $db->prepare('INSERT INTO objective (title, competency_id) VALUES (?,?)');
            $objectiveStmt->execute(array("{$programName} objective 1", 51));
            $objective1Id = $db->lastInsertId();
            $objectiveStmt->execute(array("{$programName} objective 2", 52));
            $objective2Id = $db->lastInsertId();
            $objectiveStmt = null;

            $pyObjectiveStmt = $db->prepare('INSERT INTO program_year_x_objective (program_year_id, objective_id) VALUES (?,?)');
            $pyObjectiveStmt->execute(array($programYearId, $objective1Id));
            $pyObjectiveStmt->execute(array($programYearId, $objective2Id));
            $pyObjectiveStmt = null;

            $db->commit();
            $db = null;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
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
        $groupTitle = 'Default Group Number 1';
        $userEmail = 'lgteststudent@example.com';
        $table = new Behat\Gherkin\Node\TableNode(
        "| first  | last  | email | ucid |\n" .
        "| Test   | Student | {$userEmail} | 123456 |"
        );
        $this->givenTheFollowingLearnersExistInTheProgram($classYear,$programName, $table);
        try{
            $db = $this->getDbConnection();
            $db->beginTransaction();
            $arr = $this->getProgramAndCohort($classYear, $programName);
            $result = $db->query("SELECT group_id FROM `group` WHERE title = '{$groupTitle}' AND cohort_id = {$arr['cohort_id']}");
            if (!$groupId = $result->fetchColumn()) {
              $db->exec("INSERT INTO `group` (title,cohort_id) VALUES ('{$groupTitle}',{$arr['cohort_id']})");
              $groupId = $db->lastInsertId();
            }
            $sql = "INSERT IGNORE INTO group_x_user (group_id, user_id) VALUES ({$groupId}," .
                "(SELECT user_id FROM user WHERE email = '{$userEmail}'))";
            $db->exec($sql);
            $db->commit();
            $db = null;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
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
        try{
            $db = $this->getDbConnection();
            $db->beginTransaction();
            $sql = 'INSERT INTO course (title,year, start_date, end_date, owning_school_id) ' .
                "VALUES ('{$courseName}','2013', '2013-09-01','2013-12-31', 1)";
            $db->exec($sql);
            $courseId = $db->lastInsertId();

            $arr = $this->getProgramAndCohort($cohortYear, $programName);
            $sql = "INSERT INTO course_x_cohort (course_id, cohort_id) VALUES ({$courseId},{$arr['cohort_id']})";
            $db->exec($sql);
            $db->commit();
            $db = null;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
        return new When('I go to "/ilios.php/course_management?course_id=' . $courseId . '"');

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
        try{
            $db = $this->getDbConnection();
            $db->beginTransaction();
            $sql = 'DELETE FROM `group` WHERE group_id=?';
            $deleteGroupStmt = $db->prepare($sql);
            $sql = 'DELETE FROM group_x_user WHERE group_id=?';
            $deleteUserGroupStmt = $db->prepare($sql);
            $arr = $this->getProgramAndCohort($classYear, $programName);
            $sql = "SELECT group_id FROM `group` WHERE cohort_id={$arr['cohort_id']}";
            foreach ($db->query($sql) as $row) {
                $groupId = $row[0];
                //reverse the array so we get sub before sub parents
                $subgroups = array_reverse($this->getSubGroupsForGroup($groupId, $db));
                    foreach($subgroups as $subGroupId){
                        $deleteUserGroupStmt->execute(array($subGroupId));
                        $deleteGroupStmt->execute(array($subGroupId));
                    }
                $deleteUserGroupStmt->execute(array($groupId));
                $deleteGroupStmt->execute(array($groupId));
            }
            $deleteGroupStmt = null;
            $deleteUserGroupStmt = null;

            $db->commit();
            $db = null;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

        $this->visit('/ilios.php/group_management');
    }

    protected function getSubGroupsForGroup($groupId, PDO $db)
    {
        $arr = array();
        $sql = "SELECT group_id FROM `group` WHERE parent_group_id = {$groupId}";
        foreach($db->query($sql) as $row){
            $arr[] = $row[0];
            $arr = array_merge($arr, $this->getSubGroupsForGroup($row[0], $db));
        }

        return $arr;
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
        $programCohort = $this->getProgramAndCohort($classYear, $programName);
        try{
            $db = $this->getDbConnection();
            $db->beginTransaction();
            $sql = 'SELECT * FROM user u LEFT JOIN ' .
                'user_x_cohort uxc ON u.user_id = uxc.user_id LEFT JOIN ' .
                'cohort c ON uxc.cohort_id = c.cohort_id LEFT JOIN ' .
                'program_year py ON c.program_year_id= py.program_year_id LEFT JOIN ' .
                'program p ON py.program_id = p.program_id ' .
                'WHERE u.email = ?';
            $findUserStmt = $db->prepare($sql);

            $sql = 'INSERT INTO user (last_name, first_name, email, uc_uid, ' .
                'primary_school_id, middle_name) values (?,?,?,?,1,"")';
            $addUserStmt = $db->prepare($sql);

            $userCohortStmt = $db->prepare('INSERT INTO user_x_cohort (user_id, cohort_id, is_primary) values (?,?,1)');
            $userRoleStmt = $db->prepare('INSERT INTO user_x_user_role (user_id, user_role_id) VALUES (?,4) ON DUPLICATE KEY UPDATE user_role_id = 4');

            foreach($learners->getHash() as $learner){
                $findUserStmt->execute(array($learner['email']));
                if ($findUserStmt->rowCount()) {
                    $user = $findUserStmt->fetch(PDO::FETCH_ASSOC);
                    if($user['start_year'] == $programCohort['start_year'] and $user['title'] == $programName){
                        break;
                    }
                    $userId = $user['user_id'];
                } else {
                    $addUserStmt->execute(array($learner['last'],$learner['first'],$learner['email'],$learner['ucid']));
                    $userId = $db->lastInsertId();
                }
                $userCohortStmt->execute(array($userId, $programCohort['cohort_id']));
                $userRoleStmt->execute(array($userId));
            }
            $db->commit();

            $findUserStmt = null;
            $addUserStmt = null;
            $userCohortStmt = null;
            $userRoleStmt = null;
            $db = null;
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

    }

    protected function getProgramAndCohort($classYear, $programName)
    {
        $db = $this->getDbConnection();
        $results = $db->query("SELECT * FROM program WHERE title='{$programName}'");
        if (!$results->rowCount()) {
            throw new Exception("Unable to find the program {$programName}");
        }
        $program = $results->fetch(PDO::FETCH_ASSOC);
        $programStartYear = $classYear - $program['duration'];

        $sql = 'SELECT cohort_id FROM cohort WHERE program_year_id= ' .
            '(SELECT program_year_id FROM program_year ' .
            "WHERE start_year = '{$programStartYear}' AND program_id = {$program['program_id']})";
        $results = $db->query($sql);
        if (!$results->rowCount()) {
            throw new Exception("Unable to find the cohort {$classYear} for {$programName}");
        }
        $cohort = $results->fetch(PDO::FETCH_ASSOC);
        $return = array(
            'program_id' => $program['program_id'],
            'start_year'  => $programStartYear,
            'cohort_id'  => $cohort['cohort_id']
        );

        return $return;
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
