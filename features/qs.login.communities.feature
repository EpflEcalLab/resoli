Feature: Login Communities
  In order to make sure Authentication - Login is working
  As a bunch of users with multiple approval or membership
  I want to make sure the communities selection after login is working

  @api
  Scenario: Login as Member with many communities redirect him on the communities page
    Given I am logged in as user "member+fribourg+member+lausanne"
    And the url should match "communities"
    And I should see "Fribourg" link with href "activities/fribourg/theme"
    And I should see "Lausanne" link with href "activities/lausanne/theme"

  @api
  Scenario: Login as Member of Lausanne & Organizer of Fribourg redirect him on the communities page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    And the url should match "communities"
    And I should see "Fribourg" link with href "activities/fribourg/theme"
    And I should see "Lausanne" link with href "activities/lausanne/theme"

  @api
  Scenario: Login as Member with multiple communities & 1 approval redirect him on the communities page
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    And the url should match "communities"
    And I should see "Fribourg" link with href "activities/fribourg/theme"
    And I should see "Lausanne" link with href "authentication/approval/1"
    And I should see "Genève" link with href "activities/geneve/theme"
