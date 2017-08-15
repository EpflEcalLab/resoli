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
    And I should see "Unrecognized username or password." in the ".alert-danger" element
    And I should see "Forgot your password?" in the ".alert-danger" element
    And the response status code should be 200

  @api
  Scenario: Login as Member of Lausanne redirect me on Lausanne activities
    Given I am on "/user/login"
    Then the url should match "authentication/login"
    When I fill in "edit-name" with "member+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "member+lausanne"
    And I press "edit-submit"
    And I should see "Activities by themes"
    And the url should match "activities/1/theme"

  @api
  Scenario: Login as Manager of Lausanne redirect me on Lausanne activities
    Given I am on "/user/login"
    Then the url should match "authentication/login"
    When I fill in "edit-name" with "manager+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "manager+lausanne"
    And I press "edit-submit"
    And I should see "Activities by themes"
    And the url should match "activities/1/theme"

  @api
  Scenario: Login as Organizer of Lausanne redirect me on Lausanne activities
    Given I am on "/user/login"
    Then the url should match "authentication/login"
    When I fill in "edit-name" with "organizer+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "organizer+lausanne"
    And I press "edit-submit"
    And I should see "Activities by themes"
    And the url should match "activities/1/theme"

  @api
  Scenario: Login as Member of Fribourg redirect me on Fribourg activities
    Given I am on "/user/login"
    Then the url should match "authentication/login"
    When I fill in "edit-name" with "member+fribourg@antistatique.net"
    Then I fill in "edit-pass" with "member+fribourg"
    And I press "edit-submit"
    And I should see "Activities by themes"
    And the url should match "activities/2/theme"

  # TODO
  # @api
  # Scenario: Login as Member whitout any previous appliances to communities redirect me on the apply form
  #   Given I am on "/user/login"
  #   Then the url should match "authentication/login"
  #   When I fill in "edit-name" with "member+fribourg@antistatique.net"
  #   Then I fill in "edit-pass" with "member+fribourg"
  #   And I press "edit-submit"
  #   And I should see "Activities by themes"
  #   And the url should match "activities/2/theme"

  # TODO
  # @api
  # Scenario: Login as Member with many communities redirect me on the communities page
  #   Given I am on "/user/login"
  #   Then the url should match "authentication/login"
  #   When I fill in "edit-name" with "member+fribourg@antistatique.net"
  #   Then I fill in "edit-pass" with "member+fribourg"
  #   And I press "edit-submit"
  #   And I should see "Activities by themes"
  #   And the url should match "activities/2/theme"

  # TODO
  # @api
  # Scenario: Login as user waiting approval of my only community redirect me on the community approval page
  #   Given I am on "/user/login"
  #   Then the url should match "authentication/login"
  #   When I fill in "edit-name" with "member+fribourg@antistatique.net"
  #   Then I fill in "edit-pass" with "member+fribourg"
  #   And I press "edit-submit"
  #   And I should see "Activities by themes"
  #   And the url should match "activities/2/theme"

  # TODO
  # @api
  # Scenario: Login as user with multiple approval of communities redirect me on the  first community approval page
  #   Given I am on "/user/login"
  #   Then the url should match "authentication/login"
  #   When I fill in "edit-name" with "member+fribourg@antistatique.net"
  #   Then I fill in "edit-pass" with "member+fribourg"
  #   And I press "edit-submit"
  #   And I should see "Activities by themes"
  #   And the url should match "activities/2/theme"

  # TODO
  # @api
  # Scenario: Login as Member with multiple communities & one approval redirect me on the communities page
  #   Given I am on "/user/login"
  #   Then the url should match "authentication/login"
  #   When I fill in "edit-name" with "member+fribourg@antistatique.net"
  #   Then I fill in "edit-pass" with "member+fribourg"
  #   And I press "edit-submit"
  #   And I should see "Activities by themes"
  #   And the url should match "activities/2/theme"
