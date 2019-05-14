Feature: Activity Alias
  In order to make sure Alias is working for activities
  As a bunch of users
  I want to make sure the alias are working like a charm

## Communities, Activities by Theme & default fallback
  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities"
    And the url should match "lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne welcome
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne"
    And the url should match "lausanne/welcome"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Fribourg, I can access to Fribourg activities
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Genève, I can access to Genève activities
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    When I am on "/geneve/activities/theme"
    And the response status code should be 200

## Activity detail page
  @api
  Scenario: Logged as Member of Lausanne, I can access to the Activity N°2 (Activity - Lausanne - Theme N°1)
    Given I am logged in as user "member+lausanne"
    When I am on "/node/2"
    And the url should match "lausanne/activities/atelier-creatif"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, when I access to an Event of Activity N°2 (Activity - Lausanne - Theme N°1) I am redirected on the activity page
    Given I am logged in as user "member+lausanne"
    When I am on "/node/15"
    And the url should match "lausanne/activities/atelier-creatif"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can access to the Activity N°2 (Activity - Lausanne - Theme N°1)
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events"
    And the url should match "lausanne/activities/atelier-creatif"
    And the response status code should be 200

## Activity Edit Form
  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

## Event Add Form
  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne events add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/add"
    And the response status code should be 200

## Activity Dashboard
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/dashboard"
    And the response status code should be 200

## Activity Dashboard Members
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard Members of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/dashboard/members"
    And the response status code should be 200
