Feature: Event Dashboard Access

  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard of Event 16, because I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard of Event 35, because I'm one of the maintainers of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no privilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/events/accueil-cafe/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Dashboard of Event 35, because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard of Event 35, because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/events/un-obus-dans-le-coeur/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard of Event 16, even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the dashboard. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Dashboard of Event 16, because I have no privilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Dashboard of Event 16, because I have no privilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"
    And the response status code should be 403
