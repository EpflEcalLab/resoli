Feature: Activity photos by event
  In order to make sure Activity photos by event works
  As a bunch of users
  I want to make sure the correct photos are shown

  @api
  Scenario: Logged as Member of Lausanne, When accessing the Activity 2 (Atelier Créatif), I see all photos of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 200
    Then I should see 1 ".gallery-photoswipe" elements
    Then I should see 7 ".gallery-item" elements

  @api
  Scenario: Logged as ORganizer of Lausanne, When accessing the Activity 5 (Accueil Café), I see all photos of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/photos"
    And the response status code should be 200
    Then I should see 1 ".gallery-photoswipe" elements
    Then I should see 1 ".gallery-item" elements

  @api
  Scenario: Logged as Member of Lausanne, When accessing the Activity 3 (Sortie Théàtre), I see all photos of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/photos"
    And the response status code should be 200
    Then I should see 1 ".gallery-photoswipe" elements
    Then I should see 3 ".gallery-item" elements

  @api
  Scenario: Logged as Member of Lausanne, When accessing the Activity 3 (Sortie Théàtre), I see all photos of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/photos"
    And the response status code should be 200
    Then I should see 1 ".gallery-photoswipe" elements
    Then I should see 3 ".gallery-item" elements
