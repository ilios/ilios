Feature: Calendar Feed
  In order to make the best use of calendar information
  Learners should be able incorporate the calendar data into their everyday calendar
  Which requires adding a feed to their external calendar applications

  Background:
    #
    # Log in
    #
    Given I am on the Ilios home page
    And I log in as "zero_user" with password "Ch4nge_m3"

  @javascript @insulated
  Scenario: Access calendar feed
    #
    # Press the calendar feed button
    #
    When I press the element with id "ical_feed_btn"

    #
    # Check that the like-a-password caution is displayed
    #
    And I should see "This URL is like a password. Anyone who knows it can view your calendar! If you wish to invalidate this URL and generate a new one, press Generate."

