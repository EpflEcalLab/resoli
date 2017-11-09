Feature: Event Alias
  In order to make sure Alias is working for events
  As a bunch of users
  I want to make sure the alias are working like a charm

## Dashboard
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard of Event 16, because I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"
    And the response status code should be 200

## Edit Form - General Information
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/edit"
    And the response status code should be 200

## Delete Form
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/delete"
    And the response status code should be 200

## Dashboard Subscribers
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/subscribers"
    And the response status code should be 200

## Dashboard Waiting Approval
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/waiting-approval"
    And the response status code should be 200
