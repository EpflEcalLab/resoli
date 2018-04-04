Feature: Badges - Activities by Date
  Asserts the listing of Activities by Date show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Member of Fribourg, I should see 0 badges 'cause I have 0 subscriptions.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/date"
    Then I should see 2 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Manager of Lausanne, I see 2 badges of Subscriptions.
  The first one is Waiting in the Event(s) pill(s) with the color "warning" according my privilege on this events' activity. I can't see the Guests_Confirmed badge because I'm maintainer but I don't have a confirmed subscription on this event.
  The second one is Guests_Waiting in the Event(s) pill(s) with the color "warning" according my privilege on this events' activity. I can see the Guests_Confirmed badge because I'm maintainer & I have a confirmed subscription on this event.
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 10 ".card-list-item" elements
    Then I should see 1 "#event37 .flag" elements
    Then I should see 1 "#event37 .flag.flag-warning.flag-subscription-wait" elements
    # the </strong> is to avoid matching "qs.event.user.subscription.pendings_guests"
    And the "#card37" element should contain "qs.event.user.subscription.pending</strong>"
    And the "#card37" element should not contain "qs.event.user.subscription.confirmed</strong>"
    And I should see "qs.event.user.subscription.confirmed_guests 1" in the "#card37" element
    And I should not see "qs.event.user.subscription.pendings_guests" in the "#card37" element
    Then I should see 1 "#event54 .flag" elements
    Then I should see 1 "#event54 .flag.flag-outline-warning.flag-subscription-guests-wait.flag-shield" elements
    And I should see "qs.event.user.subscription.pendings_guests 1" in the "#card54" element
    And I should not see "qs.event.user.subscription.confirmed_guests" in the "#card54" element
    And the "#card54" element should not contain "qs.event.user.subscription.pending</strong>"
    And the "#card54" element should contain "qs.event.user.subscription.confirmed</strong>"

  @api
  Scenario: Logged as Manager of Lausanne, When I click on Next, I see 1 badge of Subscription.
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 10 ".card-list-item" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-item" elements
    Then I should see 1 "#event36 .flag" elements
    Then I should see 1 "#event36 .flag.flag-warning.flag-subscription-confirmed.flag-shield" elements
    And the "#card36" element should contain "qs.event.user.subscription.confirmed</strong>"
    And the "#card36" element should not contain "qs.event.user.subscription.pending</strong>"
    And I should not see "qs.event.user.subscription.pendings_guests 1" in the "#card36" element
    And I should not see "qs.event.user.subscription.confirmed_guests" in the "#card36" element

  @api
  Scenario: Logged as Organizer of Lausanne, I see the badge of Subscription(s) Guests_Waiting in the Event(s) pill(s) with the color "danger" according my privilege on this events' activity. I can see the Guests_Waiting badge because I'm organizer of this activity.
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/date"
    Then I should see 10 ".card-list-item" elements
    Then I should see 1 "#event37 .flag" elements
    Then I should see 1 "#event37 .flag.flag-outline-danger.flag-subscription-guests-wait.flag-shield" elements
    And I should see "qs.event.user.subscription.pendings_guests 1" in the "#card37" element
    And the "#card37" element should contain "qs.event.user.subscription.confirmed</strong>"
    And the "#card37" element should not contain "qs.event.user.subscription.pending</strong>"
    And I should not see "qs.event.user.subscription.confirmed_guests" in the "#card37" element
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-item" elements
    Then I should see 1 "#event36 .flag" elements
    Then I should see 1 "#event36 .flag.flag-outline-danger.flag-subscription-guests-confirmed.flag-shield" elements
    And I should see "qs.event.user.subscription.confirmed_guests 1" in the "#card36" element
    And the "#card36" element should not contain "qs.event.user.subscription.confirmed</strong>"
    And the "#card36" element should not contain "qs.event.user.subscription.pending</strong>"
    And I should not see "qs.event.user.subscription.pendings_guests" in the "#card36" element

  @api
    Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I should see 0 badges 'cause I have 0 subscriptions.
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/date"
    And the response status code should be 200
    Then I should see 10 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements
    Then I follow "qs.activity.date.link_next"
    Then I should see 1 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements
    When I am on "/fribourg/activities/date"
    Then I should see 2 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements

  @api
    Scenario: Logged as Member of Lausanne & Manager of Fribourg, I see my own badge of Subscription(s) in the Event(s) pill(s) with the color "info" according my privilege on this events' activity.
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/fribourg/activities/date"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see 1 "#event29 .flag" elements
    Then I should see 1 "#event29 .flag.flag-info.flag-subscription-confirmed.flag-default" elements
    And the "#card29" element should contain "qs.event.user.subscription.confirmed</strong>"
    And the "#card29" element should not contain "qs.event.user.subscription.pending</strong>"
    And I should not see "qs.event.user.subscription.pendings_guests 1" in the "#card29" element
    And I should not see "qs.event.user.subscription.confirmed_guests" in the "#card29" element
