Feature: Request Solve Form

## Access
  Scenario Outline: As anonymous I should not be able to access any request solve form.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /sharing/requests/79/solve |
      | /sharing/requests/80/solve |
      | /sharing/requests/81/solve |

  Scenario Outline: Logged-in, only a volunteer or an admin can access the request solve form.
    Given I am logged in as user "<user>"
    When I am on "/sharing/requests/<request-id>/solve"
    Then the response status code should be <code>

    Examples:
      | user | request-id | code |
      | admin | 79 | 403 |
      | admin | 80 | 200 |
      | admin | 81 | 200 |
      | organizer+lausanne | 79 | 403 |
      | organizer+lausanne | 80 | 403 |
      | organizer+lausanne | 81 | 403 |
      | member+lausanne+manager+fribourg | 79  | 403 |
      | member+lausanne+manager+fribourg | 80  | 403 |
      | member+lausanne+manager+fribourg | 81  | 403 |
      | member+lausanne | 79 | 403 |
      | member+lausanne | 80 | 200 |
      | member+lausanne | 81 | 403 |
      | manager+lausanne | 79 | 403 |
      | manager+lausanne | 80 | 403 |
      | manager+lausanne | 81 | 403 |
      | member+lausanne+organizer+fribourg | 79  | 403 |
      | member+lausanne+organizer+fribourg | 80  | 200 |
      | member+lausanne+organizer+fribourg | 81  | 200 |

## Form visibility.
  Scenario Outline: When reaching the request listing, only a volunteer or an admin should see the request solve form
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then I should see <element> "form.solve" element
    Examples:
      | user | url | element |
      | admin | /sharing/1/requests | 2 |
      | admin | /sharing/2/requests | 1 |
      | member+lausanne+manager+fribourg | /sharing/1/requests | 0 |
      | member+lausanne+manager+fribourg | /sharing/2/requests | 0 |
      | member+lausanne | /sharing/1/requests | 2 |
      | member+lausanne | /sharing/2/requests | 0 |
      | organizer+lausanne | /sharing/1/requests | 0 |
      | organizer+lausanne | /sharing/2/requests | 0 |
      | manager+lausanne | /sharing/1/requests | 0 |
      | manager+lausanne | /sharing/2/requests | 0 |
      | member+lausanne+organizer+fribourg | /sharing/1/requests | 2 |
      | member+lausanne+organizer+fribourg | /sharing/2/requests | 1 |

## Form submits.
  @api @preserveDatabase @mail
  Scenario: When solving a request, an email should be sent to the request author and redirect the solver user to the listing of request
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see 0 "#requests-accordion .card-list-item#card80.solved" element
    Then I should see 1 "form.request80.solve" element
    Then I follow the link ".request80.solve button[type='submit']" element
    And 1 mail should be sent
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.request.solved.subject Resoli Sarah Courci Lausanne Mobilité 9 September 2021"
    Then the url should match "/sharing/1/requests#card80"
    And I should see "qs_sharing.collection.request.solve.success" in the ".alert" element
    Then I should see 1 "#requests-accordion .card-list-item#card80.solved" element
