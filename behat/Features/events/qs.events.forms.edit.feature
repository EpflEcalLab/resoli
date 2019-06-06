Feature: Event Edit Form

  @api
  Scenario: When reaching the Edit form of Event, the fields should be prefilled with entity values
    Given I am logged in as user "organizer+lausanne"
    When I am on "/events/21/edit"
    Then I should see 17 "#qs-activity-event-edit-form input" elements
    And the "edit-title" field should contain "Accueil Café"
    And the "#edit-date" field should match regex "/[0-9]{2}.[0-9]{2}.[0-9]{4}/"
    And the "#edit-start-at" field should match regex "/(([0-1][0-9])|([2][0-3])):([0-5][0-9])/"
    And the "#edit-end-at" field should match regex "/(([0-1][0-9])|([2][0-3])):([0-5][0-9])/"
    And the "edit-body" field should contain """
<p>Partagez un simple café entre habitants!</p>
      """
    And the "edit-venue" field should contain ""
    And the "edit-contact-name" field should contain ""
    And the "edit-contact-phone" field should contain ""
    And the "edit-contact-mail" field should contain ""
    And the "edit-contribution" field should contain ""
    Then the "edit-has-contribution-0" checkbox should be checked
    Then the "edit-has-contribution-1" checkbox should not be checked

  @api @preserveDatabase @mail
  Scenario: When editting the Edit form of Event, once saved I should be redirected on the Event dashboard
    Given I am logged in as user "organizer+lausanne"
    When I fill the Edit Event "accueil-cafe-1" form on activity "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café (edited) | now | 19:12 | 22:15 | Lorem Ipsum | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe/events/accueil-cafe-1/dashboard"
    And I should see "qs_activity.events.form.edit.success Accueil Café" in the ".alert" element

  @api @preserveDatabase @mail
  Scenario: When editting an Event, the values should be alterd & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I fill the Edit Event "accueil-cafe-1" form on activity "accueil-cafe" of "lausanne" with:
      | title | date| start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café (edited) | now | 19:12 | 22:15 | Lorem Ipsum | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-submit"
    When I am on "/lausanne/activities/accueil-cafe/events/accueil-cafe-1/edit"
    And the "edit-title" field should contain "Accueil Café (edited)"
    And the "#edit-date" field should match regex "/[0-9]{2}.[0-9]{2}.[0-9]{4}/"
    And the "edit-start-at" field should contain "19:12"
    And the "edit-end-at" field should contain "22:15"
    And the "edit-body" field should contain "Lorem Ipsum"
    And the "edit-venue" field should contain "Antistatique"
    And the "edit-contact-name" field should contain "John Doe"
    And the "edit-contact-phone" field should contain "+01 234 56 78"
    And the "edit-contact-mail" field should contain "john.doe@example.org"
    Then the "edit-has-contribution-0" checkbox should not be checked
    Then the "edit-has-contribution-1" checkbox should be checked
    And the "edit-contribution" field should contain "25 CHF"

  @api @preserveDatabase @mail
  Scenario: When editting an Event with some other Organizers, a mail should warn them of any changes (even me)
    Given I am logged in as user "manager+lausanne"
    When I fill the Edit Event "mariage-et-chatiment" form on activity "sorties-theatre" of "lausanne" with:
      | title | date| start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café (edited) | +1 week | 19:12 | 22:15 | Lorem Ipsum | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-submit"
    And 2 mails should be sent
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.event.updated.subject Resoli Accueil Café (edited)"
    Then A mail as been sent to "organizer+lausanne@antistatique.net" with subject "qs.mail.event.updated.subject Resoli Accueil Café (edited)"

  @api @preserveDatabase @mail
  Scenario: When editting an Event with some validated Subscribers, a mail should warn them of any changes
    Given I am logged in as user "admin"
    When I fill the Edit Event "prendre-des-photos-avec-son-smartphone" form on activity "cours-smartphone-tablette" of "fribourg" with:
      | title | date| start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Cours iOS (edited) | +1 week | 19:12 | 22:15 | Lorem Ipsum | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 250 CHF |
    And I press "edit-submit"
    And 1 mail should be sent
    Then A mail as been sent to "member+lausanne+manager+fribourg@antistatique.net" with subject "qs.mail.event.updated.subject Resoli Cours iOS (edited)"
