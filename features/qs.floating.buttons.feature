Feature: Floating actions buttons
  In order to make sure ACL is working for floating actions buttons
  As a bunch of users
  I want to make sure the access & see this buttons works like a charm

## Activities by theme(s) page
  @api
  Scenario: Logged as Member of Lausanne, when reaching the Activities by theme(s) page of Lausanne, I don't have access to any floating actions buttons
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/theme"
    Then I should not see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Activities by theme(s) page of Lausanne, I can see the "Add activity" button
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Activities by theme(s) page of Lausanne, I can see the "Add activity" button & the "community dashboard" button
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Admin, when reaching the Activities by theme(s) page of Lausanne or Fribourg,  I can see the "Add activity" button & the "community dashboard" button
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element
    When I am on "/fribourg/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Activities by theme(s) page of Lausanne, I don't have access to any floating actions buttons
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/lausanne/activities/theme"
    Then I should not see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Activities by theme(s) page of Fribourg, I can see the "Add activity" button & the "community dashboard" button
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/fribourg/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, when reaching the Activities by theme(s) page of Lausanne, I don't have access to any floating actions buttons
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/theme"
    Then I should not see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, when reaching the Activities by theme(s) page of Fribourg, I can see the "Add activity" button
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/fribourg/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

  @api
  Scenario: Logged as Member of Fribourg & Organizer of Fribourg, when reaching the Activities by theme(s) page of Fribourg, I can see the "Add activity" button
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/theme"
    Then I should see "qs_activity.floating.add.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.community" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.dashboard.activity" in the "#block-floatingactionsbuttonsblock" element
    And I should not see "qs_activity.floating.add.event" in the "#block-floatingactionsbuttonsblock" element

## Activity 1: Activity 1 - Lausanne - Theme N°1.

## Activity 4: Activity 4 - Lausanne - Theme N°1.

## Activity 2: Activity 2 - Lausanne - Theme N°1.

## Activity 3: Activity 3 - Lausanne - Theme N°1.
