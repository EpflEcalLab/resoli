Feature: Event Forms Access
  In order to make sure ACL is working for event forms
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1) Event add form because I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Activity N°3 (Activity 3 - Lausanne - Theme N°1) Event add form because I'm one of the maintainers of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Activity N°5 (Activity 5 - Lausanne - Theme N°1) Event add form because I'm not an organizer or maintainers of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/events/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Activity N°3 (Activity 3 - Lausanne - Theme N°1) Event add form because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Activity N°3 (Activity 3 - Lausanne - Theme N°1) Event add form because I'm not an organizer or maintainers of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1) Event add form, even If I'm member of this activity because, I'm not an organizer or maintainers of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/add"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer or maintainers privilege of Fribourg but not a member of community Fribourg could access to the form. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1) Event add form because I'm not an organizer or maintainers of this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/add"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1) Event add form because I'm not an organizer or maintainers of this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/add"
    And the response status code should be 403

## Dashboard
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard of Event 16, because I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard of Event 35, because I'm one of the maintainers of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/event-activity-3-2-days-16h00/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/events/event-activity-5-10-days-ago-23h20/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Dashboard of Event 35, because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/event-activity-3-2-days-16h00/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard of Event 35, because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/event-activity-3-2-days-16h00/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard of Event 16, even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the dashboard. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Dashboard of Event 16, because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Dashboard of Event 16, because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/dashboard"
    And the response status code should be 403

## Edit Form - General Informations
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/edit"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Form of Event 35, because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/event-activity-3-2-days-16h00/edit"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Form of Event 20, because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/events/event-activity-5-10-days-ago-23h20/edit"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Edit Form of Event 35, because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/event-activity-3-2-days-16h00/edit"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Form of Event 35, because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/events/event-activity-3-2-days-16h00/edit"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Form of Event 16, even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/edit"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the dashboard. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Edit Form of Event 16, because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/edit"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Edit Form of Event 16, because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/events/event-activity-2-1-day-ago-15h00/edit"
    And the response status code should be 403

## Delete Form
