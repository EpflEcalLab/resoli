Feature: Activity Delete Form

  @api
  Scenario: When reaching the Delete form of Activity, I should see the confirmation form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/delete"
    Then I should see "qs_activity.activities.form.delete.warning"
    And I should see "qs.form.cancel" link with href "/fr/lausanne/activities/accueil-cafe/"
    Then I should see 1 "button[type=submit]" element

  @api @preserveDatabase
  Scenario: When deleting an Activity with event, I should be stopped as this Activity has event
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/delete"
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe/delete"
    And I should see "qs_activity.activities.form.delete.error.has_events Accueil Café" in the ".alert" element

  @api @preserveDatabase
  Scenario: When deleting an Activity whitout event, it should works and redirect me on on the community by theme page & no mail should be sent
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/rencontres-reseaux-solidaires/delete"
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/theme"
    And I should see "qs_activity.activities.form.delete.success Rencontres Réseaux Solidaires" in the ".alert" element
    And 0 mail should be sent
