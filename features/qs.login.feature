Feature: Login
  In order to make sure Authentication - Login is working
  As a bunch of users
  I want to make sure the login is working like a charm

  @api
  Scenario: Log in redirection
    Given I am on "/user/login"
    Then the url should match "authentication/login"
    And the response status code should be 200

  @api
  Scenario: Login show message(s) on error & forget password link
    Given I am on "/user/login"
    When I fill in "edit-name" with "Batman"
    Then I fill in "edit-pass" with "RobinMyLove"
    And I press "edit-submit"
    And I should see "Unrecognized username or password." in the ".alert" element
    And I should see "Forgot your password?" in the ".alert" element
    And the response status code should be 200

  @api
  Scenario: Login as Member of Lausanne redirect me on Lausanne activities
    Given I am logged in as user "member+lausanne"
    And I should see "Activities by themes"
    And the url should match "activities/lausanne/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Manager of Lausanne redirect me on Lausanne activities
    Given I am logged in as user "manager+lausanne"
    And I should see "Activities by themes"
    And the url should match "activities/lausanne/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Organizer of Lausanne redirect me on Lausanne activities
    Given I am logged in as user "organizer+lausanne"
    And I should see "Activities by themes"
    And the url should match "activities/lausanne/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Member of Fribourg redirect me on Fribourg activities
    Given I am logged in as user "member+fribourg"
    And I should see "Activities by themes"
    And the url should match "activities/fribourg/theme"
    And the response status code should be 200

  @api
  Scenario: Login as user whitout any previous appliances to communities redirect me on the apply form
    Given I am logged in as user "nobody"
    And the url should match "authentication/communities/apply"
    And the response status code should be 200

  @api
  Scenario: Login as Member with many communities redirect me on the communities page
    Given I am logged in as user "member+fribourg+member+lausanne"
    And the url should match "communities"
    And the response status code should be 200

  @api
  Scenario: Login as Member of Lausanne & Organizer of Fribourg redirect me on the communities page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    And the url should match "communities"
    And the response status code should be 200

  @api
  Scenario: Login as user waiting approval of his only community redirect him on the community approval page
    Given I am logged in as user "approval+lausanne"
    And the url should match "/authentication/approval/1"
    And the response status code should be 200

  @api
  Scenario: Login as user with multiple approval of communities redirect me on the community approval page
    Given I am logged in as user "approval+fribourg+approval+lausanne"
    And the url should match "/authentication/approval/1"
    And the response status code should be 200

  @api
  Scenario: Login as Member with 1 community & 1 approval redirect me on the community page
    Given I am logged in as user "member+fribourg+approval+lausanne"
    And the url should match "activities/fribourg/theme"
    And the response status code should be 200

  @api
  Scenario: Login as Member with multiple communities & 1 approval redirect me on the communities page
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    And the url should match "communities"
    And the response status code should be 200

  # @api
  # Scenario: Login as Beginner redirect me on the onboarding page
  #   Given I am logged in as a user with the "beginner" role
  #   And the url should match "onboarding"
  #   And the response status code should be 200
