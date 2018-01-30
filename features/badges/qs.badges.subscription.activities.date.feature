Feature: Badges - Activities by Date
  Asserts the listing of Activities by Date show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Member of Fribourg, I see no badges cause I have no subscription.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/date"
    Then I should see 2 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements

  @api
  Scenario: Logged as Manager of Lausanne, I see my own badge of Subscription(s) in the Event(s) pill(s) with the color "warning" according my privilege on this events' activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 9 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-warning.flag-subscription-wait" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-warning.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I see my own badge of Subscription(s) in the Event(s) pill(s) with the color "danger" according my privilege on this events' activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/date"
    Then I should see 9 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-danger.flag-subscription-confirmed" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-simple-item" elements
    Then I should see 0 ".card-list-simple-item .flag" elements

  @api
    Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I see no badges on cause I have no subscription
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

  @api
    Scenario: Logged as Member of Lausanne & Manager of Fribourg, I see my own badge of Subscription(s) in the Event(s) pill(s) with the color "info" according my privilege on this events' activity
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/fribourg/activities/date"
    And the response status code should be 200
    Then I should see 2 ".card-list-simple-item" elements
    Then I should see 1 ".card-list-simple-item .flag" elements
    Then I should see 1 ".card-list-simple-item .flag.flag-info.flag-subscription-confirmed" elements
