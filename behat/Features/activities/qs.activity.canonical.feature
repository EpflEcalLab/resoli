Feature: Activity Canonical
  In order to make sure listing of events works
  As a bunch of users
  I want to make sure the canonical view of one activity list proper past/futures events

## Communities, Activities by Theme & default fallback
  @api
  Scenario: Logged as Member of Lausanne, accessing the canonical view of Activity N°2 (Atelier Scooby-Doo) list future events of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element

  @api
  Scenario: Logged as Member of Lausanne, accessing the canonical view (with past parameter) of Activity N°2 (Atelier Scooby-Doo) list past events of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif?view=past"
    Then I should see 4 ".card-list-item" elements
