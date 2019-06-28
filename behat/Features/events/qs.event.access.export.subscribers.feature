Feature: Event export subscribers access

  Scenario Outline: Access control to events export of Lausanne
    Given I am logged in as user "<user>"
    When I am on "/events/<event>/dashboard/subscribers/export"
    Then the response status code should be <code>

  Examples:
    | user | event | code |
    | manager+lausanne | 40 | 403 |
    | organizer+lausanne | 40 | 200 |
    | manager+lausanne | 54 | 200 |
    | organizer+lausanne | 54 | 200 |
    | member+fribourg+member+lausanne | 54 | 403 |
    | member+lausanne | 54 | 403 |
    | member+lausanne+declined+organizer+lausanne | 31 | 404 |
    | member+fribourg | 17 | 403 |
    | approval+lausanne | 17 | 403 |
