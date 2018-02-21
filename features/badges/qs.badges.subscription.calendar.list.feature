Feature: Badges - Subscription - Calendar - List
  AssertsCalendar List (below weekly and monthly dots) show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Waiting/Maintainer badge on the Event N°37 (Sorties Théâtre). I can't see the Guests_Waiting badge because I'm maintainer but I don't have a confirmed subscription on this event.
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 4 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#card37 .flag.flag-warning.flag-subscription-wait" elements
  And I should see "qs.event.user.subscription.pending" in the "#card37" element

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see the Guests_Waiting/Maintainer badge on the Event N°37 (Sorties Théâtre). I can't see the Guests_Waiting badge because I'm maintainer but I don't have a confirmed subscription on this event.
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 4 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#card37 .flag.flag-warning.flag-subscription-wait" elements
  And I should see "qs.event.user.subscription.pending" in the "#card37" element

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see the Guests_Waiting/Organizer badge on the Event N°37 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 4 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#card37 .flag.flag-outline-danger.flag-subscription-guests-wait" elements
  And I should see "qs.event.user.subscription.pendings_guests 1" in the "#card37" element

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see the Guests_Waiting/Organizer badge on the Event N°37 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 4 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#card37 .flag.flag-outline-danger.flag-subscription-guests-wait" elements
  And I should see "qs.event.user.subscription.pendings_guests 1" in the "#card37" element

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Weekly, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 4 ".card-list-item" elements
  Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Monthly, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 4 ".card-list-item" elements
  Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Member badge on the Event N°29 (Cours Smartphone & Tablette)
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/fribourg/calendar/monthly"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#event29 .flag.flag-info.flag-subscription-confirmed" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card29" element

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Member badge on the Event N°29 (Cours Smartphone & Tablette)
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/fribourg/calendar/weekly"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#event29 .flag.flag-info.flag-subscription-confirmed" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card29" element
