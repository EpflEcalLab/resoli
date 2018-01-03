Feature: Badges
  In order to make sure Badges are working for event pills & activities
  As a bunch of users
  I want to make sure they are shown like a charm

## Activity Detail Page
  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°2 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#card-event17" element
    Then I should see a "#card-event17[data-status='default']" element
    Then I should not see a "#card-event17[data-status='pending']" element
    Then I should not see a "#card-event17[data-status='confirmed']" element

  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see a "#card-event37[data-status='pending']" element
    Then I should see a "#collapse-35" element
    Then I should see a "#card-event35[data-status='default']" element
    Then I should see a "#collapse-36" element
    Then I should see a "#card-event36[data-status='confirmed']" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a not member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element
    Then I should see a "#card-event17[data-status='default']" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see a "#card-event37[data-status='confirmed']" element
    Then I should see a "#collapse-35" element
    Then I should see a "#card-event35[data-status='default']" element
    Then I should see a "#collapse-36" element
    Then I should see a "#card-event36[data-status='default']" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should see a "#card-event22[data-status='default']" element
    Then I should see a "#collapse-18" element
    Then I should see a "#card-event18[data-status='default']" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should see a "#card-event22[data-status='default']" element
    Then I should see a "#collapse-18" element
    Then I should see a "#card-event18[data-status='default']" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see a "#card-event37[data-status='default']" element
    Then I should see a "#collapse-35" element
    Then I should see a "#card-event35[data-status='default']" element
    Then I should see a "#collapse-36" element
    Then I should see a "#card-event36[data-status='default']" element

## Events by Date lausanne/activities/date
  @api
  Scenario: Logged as Member of Fribourg, I see my own badge of Subscription(s) in the Event(s) pill(s)
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/date"
    Then I should see 2 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements

  @api
  Scenario: Logged as Manager of Lausanne, I see my own badge of Subscription(s) in the Event(s) pill(s)
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 9 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-subscription-wait" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I see my own badge of Subscription(s) in the Event(s) pill(s)
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/date"
    Then I should see 9 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-subscription-confirmed" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements

  @api
    Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I see my own badge of Subscription(s) in the Event(s) pill(s)
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 9 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements
    When I am on "/fribourg/activities/date"
    Then I should see 2 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements

## Calendar by Month

## Calendar by Week

## My activities

## My subscriptions
