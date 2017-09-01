Feature: Community Waiting Approval Access
  In order to make sure ACL is working for event forms
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Login as user waiting approval of Lausanne, I can't access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg list of accounts waiting for approval
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/waiting-approval"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg list of accounts waiting for approval
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg I can't access to Fribourg list of accounts waiting for approval
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/fribourg/waiting-approval"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg I can't access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/lausanne/waiting-approval"
    And the response status code should be 403

