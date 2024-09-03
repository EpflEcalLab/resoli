Feature: Event Subscriptions Inline Form

  @api
  Scenario: The Inline subscription form should allow all community member on Event's activity allowing all community to subscribe
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "fribourg/activities/ginguettes/events/ginguettes-party-de-vevey/dashboard/subscribers"
    Then I should see 1 "#qs-subscription-subscribe-member-form" element
    Then I should see 10 "#qs-subscription-subscribe-member-form select option" elements

  @api
  Scenario: The Inline subscription form should show no subscribable member when there is not member to subscribe
    Given I am logged in as user "organizer+lausanne"
    When I am on "lausanne/activities/accueil-cafe/events/accueil-cafe-1/dashboard/subscribers"
    Then I should see 1 "#qs-subscription-subscribe-member-form" element
    Then I should see 0 "#qs-subscription-subscribe-member-form select option" element

  @api
  Scenario: The Inline subscription form should allow only event member on Event's activity allowing only event member to subscribe
    Given I am logged in as user "manager+lausanne"
    When I am on "lausanne/activities/sorties-theatre/events/mariage-et-chatiment/dashboard/subscribers"
    Then I should see 1 "#qs-subscription-subscribe-member-form" element
    Then I should see 2 "#qs-subscription-subscribe-member-form select option" elements

  @api @preserveDatabase @mail
  Scenario: Manually subscribing someone should not send any mails
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "fribourg/activities/ginguettes/events/ginguettes-party-de-vevey/dashboard/subscribers"
    When I select "member2+fribourg@antistatique.net" from "edit-member"
    And I press "edit-submit"
    Then the url should match "/fr/fribourg/activities/ginguettes/events/ginguettes-party-de-vevey/dashboard/subscribers"
    And I should see "qs_subscription.subscription.form.subscribe.member.success Ginguettes Party de Vevey"
    And 1 mail should be sent
    Then A mail as been sent to "member2+fribourg@antistatique.net" with subject "qs.mail.subscription.waiting_approval.confirm.subject Resoli member2+fribourg@antistatique.net Abel Auboisdormant Fribourg Ginguettes Party de Vevey Ginguettes"
