Feature: Event Dashboard Waiting Approval
  In order to make sure ACL is working for event dashboard subscribers
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

## Dashboard Waiting Approval
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard waiting Approval of Event 16, because I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/waiting-approval"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard waiting Approval of Event 35, because I'm one of the maintainers of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/dashboard/waiting-approval"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard waiting Approval of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no privilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/events/accueil-cafe/dashboard/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Dashboard waiting Approval of Event 35, because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/dashboard/waiting-approval"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard waiting Approval of Event 35, because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/dashboard/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard waiting Approval of Event 16, even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/waiting-approval"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the dashboard. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Dashboard waiting Approval of Event 16, because I have no privilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/waiting-approval"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Dashboard waiting Approval of Event 16, because I have no privilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/waiting-approval"
    And the response status code should be 403

## Edit Form - General Informations
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Form of Event 16, I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/edit"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Form of Event 35, because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/edit"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Form of Event 20, because I have no privilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/events/accueil-cafe/edit"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Edit Form of Event 35, because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/edit"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Form of Event 35, because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/edit"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Form of Event 16, even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/edit"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the dashboard. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Edit Form of Event 16, because I have no privilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/edit"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Edit Form of Event 16, because I have no privilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/edit"
    And the response status code should be 403

