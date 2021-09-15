Feature: Request Archive Form

## Access
  Scenario Outline: As anonymous I should not be able to access any request archive form.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /sharing/requests/79/archive |
      | /sharing/requests/80/archive |
      | /sharing/requests/81/archive |

  Scenario Outline: Logged-in, only the request author, a community manager or an admin can access the request archive form.
    Given I am logged in as user "<user>"
    When I am on "/sharing/requests/<request-id>/archive"
    Then the response status code should be <code>

    Examples:
      | user | request-id | code |
      | admin | 78 | 403 |
      | admin | 79 | 200 |
      | admin | 80 | 200 |
      | admin | 81 | 200 |
      | organizer+lausanne | 78 | 403 |
      | organizer+lausanne | 79 | 200 |
      | organizer+lausanne | 80 | 200 |
      | organizer+lausanne | 81 | 403 |
      | member+lausanne+manager+fribourg | 78  | 403 |
      | member+lausanne+manager+fribourg | 79  | 403 |
      | member+lausanne+manager+fribourg | 80  | 403 |
      | member+lausanne+manager+fribourg | 81  | 200 |
      | member+lausanne | 78  | 403 |
      | member+lausanne | 79 | 403 |
      | member+lausanne | 80 | 403 |
      | member+lausanne | 81 | 403 |
      | manager+lausanne | 78 | 403 |
      | manager+lausanne | 79 | 200 |
      | manager+lausanne | 80 | 200 |
      | manager+lausanne | 81 | 403 |
      | member+lausanne+organizer+fribourg | 78  | 403 |
      | member+lausanne+organizer+fribourg | 79  | 403 |
      | member+lausanne+organizer+fribourg | 80  | 403 |
      | member+lausanne+organizer+fribourg | 81  | 200 |

## Form visibility.
  Scenario Outline: When reaching the request listing, only the request author, a community manager or an admin should see the request archive form
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then I should see <element> "form.archive" element
    Examples:
      | user | url | element |
      | admin | /sharing/1/requests | 3 |
      | admin | /sharing/2/requests | 1 |
      | member+lausanne+manager+fribourg | /sharing/1/requests | 0 |
      | member+lausanne+manager+fribourg | /sharing/2/requests | 0 |
      | member+lausanne | /sharing/1/requests | 1 |
      | member+lausanne | /sharing/2/requests | 0 |
      | organizer+lausanne | /sharing/1/requests | 0 |
      | organizer+lausanne | /sharing/2/requests | 0 |
      | manager+lausanne | /sharing/1/requests | 0 |
      | manager+lausanne | /sharing/2/requests | 0 |
      | member+lausanne+organizer+fribourg | /sharing/1/requests | 0 |
      | member+lausanne+organizer+fribourg | /sharing/2/requests | 0 |

## Form submits.
  @api @preserveDatabase @mail
  Scenario: When archiving one of my request, no email should be sent and I should be redirect to the listing of requests.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see 3 "#requests-accordion .card-list-item" elements
    Then I follow the link ".request77.archive button[type='submit']" element
    And 0 mail should be sent
    Then the url should match "/sharing/1/requests"
    And I should see "qs_sharing.collection.request.archive.success" in the ".alert" element
    Then I should see 2 "#requests-accordion .card-list-item" elements

  @api @preserveDatabase @mail
  Scenario: When archiving someone else requests, a email should be sent to the request's author and I should be redirect to the listing of requests.
    Given I am volunteer on community 1 for theme 22 as user 5
    Given I am logged in as user "manager+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see 3 "#requests-accordion .card-list-item" elements
    Then I follow the link ".request77.archive button[type='submit']" element
    And 1 mail should be sent
    Then A mail as been sent to "member+lausanne@antistatique.net" with subject "qs.mail.request.archived.subject Resoli Juda Bricot Lausanne Convivialité 9 September 2021"
    Then the url should match "/sharing/1/requests"
    And I should see "qs_sharing.collection.request.archive.success" in the ".alert" element
    Then I should see 2 "#requests-accordion .card-list-item" elements
