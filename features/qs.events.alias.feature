Feature: Event Alias
  In order to make sure Alias is working for events
  As a bunch of users
  I want to make sure the alias are working like a charm

## Dashboard
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard of Event 16, because I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard"
    And the response status code should be 200

## Edit Form - General Informations
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/edit"
    And the response status code should be 200

## Delete Form
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/delete"
    And the response status code should be 200

## Dashboard Subscribers
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard/subscribers"
    And the response status code should be 200

## Dashboard Waiting Approval
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard/waiting-approval"
    And the response status code should be 200
