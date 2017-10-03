
Feature: Activities by Date Access
  In order to make sure ACL is working for Activities by date page
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities by date
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200

  @api
  Scenario: Login as user waiting approval of Lausanne, I can't access to Lausanne activities by date
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities by date
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities by date
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities by date
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg I can access to Lausanne & Fribourg activities by date
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/fribourg/activities/date"
    And the response status code should be 200
    When I am on "/lausanne/activities/date"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple Privileges (Member of Fribourg & waiting approval Organizer for Fribourg), I can access to Fribourg activities by date
    Given I am logged in as user "member+fribourg+approval+organizer+fribourg"
    Then I am on "/fribourg/activities/date"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple Privileges (Member & Organizer of Fribourg), I can access to Fribourg activities by date
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    Then I am on "/fribourg/activities/date"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple waiting approval (Member & Organizer for Fribourg), I can't access to Fribourg activities by date
    Given I am logged in as user "approval+member+fribourg+approval+organizer+fribourg"
    Then I am on "/fribourg/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Declined Organizer of Lausanne, I can't access to Lausanne activities by date
    Given I am logged in as user "declined+organizer+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Lausanne, I can access to Lausanne activities by date
    Given I am logged in as user "member+lausanne+declined+organizer+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Fribourg, I can't access to Lausanne activities by date
    Given I am logged in as user "member+fribourg+declined+member+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 403

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Fribourg, I can access to Fribourg activities by date
    Given I am logged in as user "member+fribourg+declined+member+lausanne"
    When I am on "/fribourg/activities/date"
    And the response status code should be 200
