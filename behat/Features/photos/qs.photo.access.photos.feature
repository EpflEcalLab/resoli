Feature: Activity photos by event Access
  In order to make sure ACL is working for Activity photos by event
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Admin, I can access to the any activities photos
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 200
    When I am on "/lausanne/activities/sorties-theatre/photos"
    And the response status code should be 200
    When I am on "/lausanne/activities/accueil-cafe/photos"
    And the response status code should be 200
    When I am on "/lausanne/activities/aperitif/photos"
    And the response status code should be 200

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Photos of Activity N°2 (Atelier Créatif) - Because Photos are only open to Members+
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Photos of Activity N°2 (Atelier Créatif)
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can access to Photos of Activity N°3 (Sorties Théàtre) - Because Photos are open to community
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/photos"
    And the response status code should be 200

  @api
  Scenario: Logged as Declined Organizer of Lausanne, I can't access to Photos of Activity N°2 (Atelier Créatif)
    Given I am logged in as user "declined+organizer+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Fribourg, I can't access to Photos of Activity N°2 (Atelier Créatif)
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Fribourg, I can't access to Photos of Activity N°3 (Sorties Théàtre)
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/sorties-theatre/photos"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Photos of Activity N°2 (Atelier Créatif)
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Photos of Activity N°2 (Atelier Créatif) - Because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Photos of Activity N°5 (Accueil Café) - Because I'm a pending member of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/photos"
    And the response status code should be 403
