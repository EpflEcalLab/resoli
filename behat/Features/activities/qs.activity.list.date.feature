Feature: Activities by Date list
  Asserts the listing of Activities by Date show correct number of items and the
  corresponding next/previous buttons.

  @api
  Scenario: On the Fribourg listing, I should see the correct elements and buttons.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/date"
    Then I should see 5 ".card-list-item" elements
    And I should see "qs.activity.date.link_next"
    But I should not see "qs.activity.date.link_prev"

  @api
  Scenario: On the Fribourg listing second page, I should see the correct elements and buttons.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/date"
    And I follow "qs.activity.date.link_next"
    Then I should see 0 ".card-list-item" element
    And I should see "qs.activity.date.link_next"
    And I should see "qs.activity.date.link_prev"

  @api
  Scenario: On the Fribourg listing page with a custom old date, I should see the correct elements and buttons.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/date?date=2016-08-10"
    And I should see "qs.activity.date.link_next"
    And I should see "qs.activity.date.link_prev"
    And I should see "qs.activity.date.title_empty Fribourg 8 August 2016 4 September 2016"
    And I follow "qs.activity.date.link_next"
    And I should see "qs.activity.date.title_empty Fribourg 5 September 2016 2 October 2016"

  @api
  Scenario: On the Lausanne listing, I should see the correct elements and buttons.
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/date"
    Then I should see 10 ".card-list-item" elements
    And I should see "qs.activity.date.link_next"
    But I should not see "qs.activity.date.link_prev"

  @api
  Scenario: On the Lausanne listing second page, I should see the correct elements and buttons.
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/date"
    And I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-item" element
    And I should see "qs.activity.date.link_next"
    And I should see "qs.activity.date.link_prev"
