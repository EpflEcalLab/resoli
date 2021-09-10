@debug
Feature: Collection of Requests
  Asserts the listing of Requests of one community display the correct number of items.

## Redirect
  @api
  Scenario: Accessing to a request canonical view should redirect to the requests collection page.
    Given I am logged in as user "member+lausanne"
    When I am on "/node/77"
    And the url should match "/sharing/1/requests"
    And the response status code should be 200

## Access
  @api
  Scenario Outline: As anonymous I should not be able to access any community offers collection.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /sharing/1/requests |
      | /sharing/2/requests |
      | /sharing/3/requests |

  @api
  Scenario Outline: Logged-in, I can access my own community(ies) request collection if I'm also Volunteer on that community. Accessing community in which I don't belongs should not be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
      | user | url | code |
      | admin | /sharing/1/requests | 200 |
      | admin | /sharing/2/requests | 200 |
      | member+lausanne | /sharing/1/requests | 200 |
      | member+lausanne | /sharing/2/requests | 403 |
      | approval+lausanne | /sharing/1/requests | 403 |
      | approval+lausanne | /sharing/2/requests | 403 |
      | manager+lausanne | /sharing/1/requests | 403 |
      | manager+lausanne | /sharing/2/requests | 403 |
      | member+lausanne+organizer+fribourg | /sharing/1/requests | 200 |
      | member+lausanne+organizer+fribourg | /sharing/2/requests | 200 |
      | member+fribourg+approval+organizer+fribourg | /sharing/1/requests | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/2/requests | 403 |
      | declined+organizer+lausanne | /sharing/1/requests | 403 |
      | declined+organizer+lausanne | /sharing/2/requests | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/1/requests | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/2/requests | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/1/requests | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/2/requests | 403 |
