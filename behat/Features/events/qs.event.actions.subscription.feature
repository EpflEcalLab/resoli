Feature: Subscription on Event

  @api @preserveDatabase @mail
  Scenario: Requesting a subscription to an elligible event should send multiple mails to:
    - One mail per activity Organizer(s)
    - One mail per activity Manager(s)
    Given I am logged in as user "member+fribourg+member+lausanne"
    When I am on "lausanne/activities/sorties-theatre"
    And the response status code should be 200
    And I follow the link "#qs-subscription-request-form-37 button" element
    And I should see "qs_subscription.request.form.success"
    And 2 mails should be sent
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.request_organizers.subject Resoli member+fribourg+member+lausanne@antistatique.net Larry Bambelle Lausanne Macbeth Sorties Théâtre"
    Then A mail as been sent to "organizer+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.request_organizers.subject Resoli member+fribourg+member+lausanne@antistatique.net Larry Bambelle Lausanne Macbeth Sorties Théâtre"
