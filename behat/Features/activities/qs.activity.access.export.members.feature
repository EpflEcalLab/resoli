Feature: Activity export members access

  @api
  Scenario Outline: Access control to events export of Lausanne
    Given I am logged in as user "<user>"
    When I am on "/activities/<activity>/dashboard/members/export"
    Then the response status code should be <code>

  Examples:
    | user | activity | code |
    | member+lausanne | 2 | 403 |
    | manager+lausanne | 2 | 200 |
    | organizer+lausanne | 2 | 403 |
    | member+fribourg | 2 | 403 |
    | approval+lausanne | 2 | 403 |
    | member+lausanne+declined+organizer+lausanne | 2 | 403 |
    | member+lausanne+declined+organizer+lausanne | 8 | 404 |
    | member+lausanne | 3 | 403 |
    | manager+lausanne | 3 | 403 |
    | organizer+lausanne | 3 | 200 |
    | manager+lausanne | 5 | 403 |
