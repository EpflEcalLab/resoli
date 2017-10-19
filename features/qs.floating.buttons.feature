Feature: Floating actions buttons
  In order to make sure ACL is working for floating actions buttons
  As a bunch of users
  I want to make sure the access & see this buttons works like a charm

## Activities by theme(s) page
  @api
  Scenario: Logged as Member of Lausanne, when reaching the Activities by theme(s) page of Lausanne, I must see the "My activities" button
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.my_activities" link with href "/activities/1/user/2"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, when reaching the Activities by theme(s) page of Lausanne, I must see the "My activities" button
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.my_activities" link with href "/activities/1/user/8"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, when reaching the Activities by theme(s) page of Fribourg, I must see the "Add activity" button
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.add.activity" link with href "/fribourg/activities/add"

## Activities by date(s) page
  @api
  Scenario: Logged as Member of Lausanne, when reaching the Activities by date page of Lausanne, I must see the "My activities" button
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.my_activities" link with href "/activities/1/user/2"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, when reaching the Activities by date page of Lausanne, I must see the "My activities" button
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.my_activities" link with href "/activities/1/user/8"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, when reaching the Activities by date page of Fribourg, I must see the "Add activity" button
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/fribourg/activities/date"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.add.activity" link with href "/fribourg/activities/add"

## Activity detail page
  @api
  Scenario: Logged as Member of Lausanne, when reaching the Activity N°2, I don't see any floating button
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 0 "#block-floatingactionsbuttonsblock a" elements

  @api
  Scenario: Logged as Member of Lausanne, when reaching the Activity N°4, I don't see any floating button
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 0 "#block-floatingactionsbuttonsblock a" elements

  @api
  Scenario: Logged as Member of Lausanne, when reaching the Activity N°5, I don't see any floating button
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/accueil-cafe"
    And the response status code should be 200
    Then I should see 0 "#block-floatingactionsbuttonsblock a" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Activity N°2, I must see the "Activity Dashboard" button
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.dashboard.activity" link with href "/lausanne/activities/accueil-cafe/dashboard"

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Activity N°5, I must see the "Activity Dashboard" button
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.dashboard.activity" link with href "/lausanne/activities/atelier-creatif/dashboard"

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Activity N°3, I must see the "Activity Dashboard" button
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 1 "#block-floatingactionsbuttonsblock a" elements
    And I should see "qs_activity.floating.add.event" link with href "/lausanne/activities/sorties-theatre/events/add"
