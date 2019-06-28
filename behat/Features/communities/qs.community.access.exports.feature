Feature: Community exports access

  Scenario Outline: Access control to events export of Lausanne
    Given I am logged in as user "<user>"
    When I am on "/communities/1/dashboard/events/export"
    Then the response status code should be <code>

  Examples:
    | user | code |
    | member+lausanne | 403 |
    | approval+lausanne | 403 |
    | organizer+lausanne | 403 |
    | manager+lausanne | 200 |
    | member+lausanne+manager+fribourg | 403 |
    | declined+organizer+lausanne | 403 |
    | member+lausanne+declined+organizer+lausanne | 403 |
    | member+fribourg+declined+member+lausanne | 403 |

  Scenario Outline: Access control to events export of Fribourg
    Given I am logged in as user "<user>"
    When I am on "/communities/2/dashboard/events/export"
    Then the response status code should be <code>

  Examples:
    | user | code |
    | organizer+lausanne | 403 |
    | manager+lausanne | 403 |
    | member+lausanne+manager+fribourg | 200 |
    | member+fribourg+declined+member+lausanne | 403 |

  Scenario Outline: Access control to members export of Lausanne
    Given I am logged in as user "<user>"
    When I am on "/communities/1/dashboard/members/export"
    Then the response status code should be <code>

  Examples:
    | user | code |
    | member+lausanne | 403 |
    | approval+lausanne | 403 |
    | organizer+lausanne | 403 |
    | manager+lausanne | 200 |
    | member+lausanne+manager+fribourg | 403 |
    | declined+organizer+lausanne | 403 |
    | member+lausanne+declined+organizer+lausanne | 403 |
    | member+fribourg+declined+member+lausanne | 403 |

  Scenario Outline: Access control to members export of Fribourg
    Given I am logged in as user "<user>"
    When I am on "/communities/2/dashboard/members/export"
    Then the response status code should be <code>

  Examples:
    | user | code |
    | organizer+lausanne | 403 |
    | manager+lausanne | 403 |
    | member+lausanne+manager+fribourg | 200 |
    | member+fribourg+declined+member+lausanne | 403 |
