Feature: Collection of Requests
  Asserts the listing of Requests of one community display the correct number of items.

## Access
  Scenario Outline: As anonymous I should not be able to access any request confirmation page.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /sharing/requests/77/confirmation |
      | /sharing/requests/78/confirmation |
      | /sharing/requests/71/confirmation |

  Scenario Outline: Logged-in, I can access my own request confirmation on my community. Accessing request I'm not the author should not be authorized.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
      | user | url | code |
      | admin | /sharing/requests/77/confirmation | 200 |
      | admin | /sharing/requests/79/confirmation | 200 |
      | member+lausanne | /sharing/requests/77/confirmation | 200 |
      | member+lausanne | /sharing/requests/79/confirmation | 403 |
      | approval+lausanne | /sharing/requests/77/confirmation | 403 |
      | approval+lausanne | /sharing/requests/79/confirmation | 403 |
      | manager+lausanne | /sharing/requests/77/confirmation | 403 |
      | manager+lausanne | /sharing/requests/79/confirmation | 200 |
      | member+lausanne+organizer+fribourg | /sharing/requests/77/confirmation | 403 |
      | member+lausanne+organizer+fribourg | /sharing/requests/79/confirmation | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/requests/77/confirmation | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/requests/79/confirmation | 403 |
      | declined+organizer+lausanne | /sharing/requests/77/confirmation | 403 |
      | declined+organizer+lausanne | /sharing/requests/79/confirmation | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/requests/77/confirmation | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/requests/79/confirmation | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/requests/77/confirmation | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/requests/79/confirmation | 403 |

## Element listed
  Scenario: On the "Demande d'entraide N°77 de Sara Courci" confirmation page, I should see the "submit another request" button.
    Given I am logged in as user "admin"
    When I am on "/sharing/requests/77/confirmation"
    And I should see "qs_sharing.requests.confirmation.add_request" link with href "/sharing/1/requests/add"

## Floating Button
  Scenario: In the Sharing requests confirmation page, I should see the floating button point to my dashboard.
    Given I am logged in as user "admin"
    When I am on "/sharing/requests/77/confirmation"
    Then I should see 1 ".floating a" elements
    And I should see "qs_sharing.floating.dashboard" link with href "/sharing/1/user/1/dashboard"

## Back button.
  Scenario: In the Sharing requests confirmation page, I should not see any back button.
    Given I am logged in as user "admin"
    When I am on "/sharing/requests/77/confirmation"
    Then I should not see a "#block-previousnavigation a" element
