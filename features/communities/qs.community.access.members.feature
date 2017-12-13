Feature: Community Members Access
  In order to make sure ACL is working for event forms
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Lausanne list of members
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Login as user waiting approval of Lausanne, I can't access to Lausanne list of members
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Lausanne list of members
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Lausanne list of members
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg list of members
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne list of members
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg list of members
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg I can't access to Fribourg list of members
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/fribourg/dashboard/members"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg I can't access to Lausanne list of members
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Declined Organizer of Lausanne, I can't access to Lausanne list of members
    Given I am logged in as user "declined+organizer+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Lausanne, I can't access to Lausanne list of members
    Given I am logged in as user "member+lausanne+declined+organizer+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Fribourg, I can't access to Lausanne list of members neither Fribourg list of members
    Given I am logged in as user "member+fribourg+declined+member+lausanne"
    When I am on "/lausanne/dashboard/members"
    And the response status code should be 403
    When I am on "/fribourg/dashboard/members"
    And the response status code should be 403
