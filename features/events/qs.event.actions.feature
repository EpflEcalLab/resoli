Feature: Event Actions Buttons
  In order to make sure ACL is working for event pills
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

## Subscribe button - on detail page of activity
  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°2 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element
    And I should see "qs.event.register" in the "#collapse-17 .card-actions" element

  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    # @todo Change as pending
    And I should see "qs.event.register.pending" in the "#collapse-37 .card-actions" element
    Then I should see a "#collapse-35" element
    And I should see "qs.event.register" in the "#collapse-35 .card-actions" element
    Then I should see a "#collapse-36" element
    # @todo Change as confirmed
    And I should see "qs.event.register.confirmed" in the "#collapse-36 .card-actions" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a not member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element
    And I should not see "qs.event.register" in the "#collapse-17 .card-actions" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    # @todo Change as confirmed
    And I should see "qs.event.register.confirmed" in the "#collapse-37 .card-actions" element
    Then I should see a "#collapse-35" element
    And I should see "qs.event.register" in the "#collapse-35 .card-actions" element
    Then I should see a "#collapse-36" element
    And I should see "qs.event.register" in the "#collapse-36 .card-actions" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    And I should see "qs.event.register" in the "#collapse-22 .card-actions" element
    Then I should see a "#collapse-18" element
    And I should see "qs.event.register" in the "#collapse-18 .card-actions" element

  @api
  Scenario: Logged as Member of Lausanne, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    And I should see "qs.event.register" in the "#collapse-22 .card-actions" element
    Then I should see a "#collapse-18" element
    And I should see "qs.event.register" in the "#collapse-18 .card-actions" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    And I should see "qs.event.register" in the "#collapse-22 .card-actions" element
    Then I should see a "#collapse-18" element
    And I should see "qs.event.register" in the "#collapse-18 .card-actions" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because this is not a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    And I should not see "qs.event.register" in the "#collapse-37 .card-actions" element
    Then I should see a "#collapse-35" element
    And I should not see "qs.event.register" in the "#collapse-35 .card-actions" element
    Then I should see a "#collapse-36" element
    And I should not see "qs.event.register" in the "#collapse-36 .card-actions" element
