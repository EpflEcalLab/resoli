Feature: Login
  In order to make sure Authentication - Login is working
  As a bunch of users
  I want to make sure the login redirect on the correct pages

  @api
  Scenario: Log in redirection
    Given I am on "/user/login"
    Then the url should match "authentication/login"
    And the response status code should be 200

  @api
  Scenario: Login as Member of Lausanne redirect him on Lausanne activities
    Given I am logged in as user "member+lausanne"
    And the url should match "lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Manager of Lausanne redirect him on Lausanne activities
    Given I am logged in as user "manager+lausanne"
    And the url should match "lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Organizer of Lausanne redirect him on Lausanne activities
    Given I am logged in as user "organizer+lausanne"
    And the url should match "lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Member of Fribourg redirect him on Fribourg activities
    Given I am logged in as user "member+fribourg"
    And the url should match "fribourg/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as user whitout any previous appliances to communities redirect him on the apply form
    Given I am logged in as user "nobody"
    And the url should match "authentication/communities/apply"
    And the response status code should be 200

  @api
  Scenario: Login as Member with many communities redirect him on the communities page
    Given I am logged in as user "member+fribourg+member+lausanne"
    And the url should match "communities"
    And the response status code should be 200

  @api
  Scenario: Login as Member of Lausanne & Organizer of Fribourg redirect him on the communities page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    And the url should match "communities"
    And the response status code should be 200

  @api
  Scenario: Login as user waiting approval of his only community redirect him on the community approval page
    Given I am logged in as user "approval+lausanne"
    And the url should match "/authentication/approval/1"
    And the response status code should be 200

  @api
  Scenario: Login as user with multiple approval of communities redirect him on the community approval page
    Given I am logged in as user "approval+fribourg+approval+lausanne"
    And the url should match "/authentication/approval/1"
    And the response status code should be 200

  @api
  Scenario: Login as Member with 1 community & 1 approval redirect him on the community page
    Given I am logged in as user "member+fribourg+approval+lausanne"
    And the url should match "fribourg/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Member with multiple communities & 1 approval redirect him on the communities page
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    And the url should match "communities"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple Privileges (Member of Fribourg & waiting approval Organizer for Fribourg) in the same community redirect him on the community page
    Given I am logged in as user "member+fribourg+approval+organizer+fribourg"
    And the url should match "fribourg/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple waiting approval (Member & Organizer for Fribourg) in the same community redirect him on the community approval page
    Given I am logged in as user "approval+member+fribourg+approval+organizer+fribourg"
    And the url should match "/authentication/approval/2"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple Privileges (Member & Organizer of Fribourg) in the same community redirect him on the community page
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    And the url should match "fribourg/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Declined Organizer of Lausanne redirect him on the apply form
    Given I am logged in as user "declined+organizer+lausanne"
    And the url should match "authentication/communities/apply"
    And the response status code should be 200

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Lausanne redirect him on the community page
    Given I am logged in as user "member+lausanne+declined+organizer+lausanne"
    And the url should match "lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Fribourg, redirect him on the community page
    Given I am logged in as user "member+fribourg+declined+member+lausanne"
    And the url should match "fribourg/activities/theme"
    And the response status code should be 200

  # @api
  # Scenario: Login as Beginner redirect him on the onboarding page
  #   Given I am logged in as a user with the "beginner" role
  #   And the url should match "onboarding"
  #   And the response status code should be 200
