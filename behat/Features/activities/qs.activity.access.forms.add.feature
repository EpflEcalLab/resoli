Feature: Add Activity Form Access

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Declined Organizer of Lausanne, I can't access to Lausanne activities add form
    Given I am logged in as user "declined+organizer+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Login as Multiple Privileges (Member of Fribourg & waiting approval Organizer for Fribourg), I can't access to Fribourg activities add form
    Given I am logged in as user "member+fribourg+approval+organizer+fribourg"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Login as Multiple Privileges (Member & Organizer of Fribourg), I can access to Fribourg activities add form
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/add"
    And the response status code should be 200

  @api
  Scenario: Login as Multiple waiting approval (Member & Organizer for Fribourg), I can't access to Fribourg activities add form
    Given I am logged in as user "approval+member+fribourg+approval+organizer+fribourg"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403
