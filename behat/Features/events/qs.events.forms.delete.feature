Feature: Event Delete Form

  @api
  Scenario: When reaching the Delete form of Event, I should see the confirmation form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/events/accueil-cafe-1/delete"
    Then I should see "qs_activity.events.form.delete.warning"
    And I should see "qs.form.cancel" link with href "/lausanne/activities/accueil-cafe/events/accueil-cafe-1/dashboard"
    Then I should see 1 "button[type=submit]" element

  @api @preserveDatabase @mail
  Scenario: When deleting an Event with subscribers, a mail should warn them
    Given I am logged in as user "admin"
    When I am on "/events/40/delete"
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe"
    And I should see "qs_event.events.form.delete.success Accueil Café"
    And 1 mail should be sent
    Then A mail as been sent to "organizer+lausanne@antistatique.net" with subject "qs.mail.event.deleted.subject Resoli Accueil Café"
