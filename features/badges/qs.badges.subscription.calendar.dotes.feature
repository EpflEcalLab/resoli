Feature: Badges - Subscription - Calendar - List
  AssertsCalendar List (below weekly and monthly dots) show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Confirmed/Maintainer badge.
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 7 ".calendar-item" elements
  Then I should see 1 ".calendar-item .flag" elements
  Then I should see 1 ".calendar-item .flag.flag-warning.flag-subscription-confirmed.flag-shield" elements

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Confirmed/Maintainer badge.
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 1 ".calendar-item .flag" elements
  Then I should see 1 ".calendar-item .flag.flag-warning.flag-subscription-confirmed.flag-shield" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Confirmed/Organizer badge.
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 7 ".calendar-item" elements
  Then I should see 1 ".calendar-item .flag" elements
  Then I should see 1 ".calendar-item .flag.flag-danger.flag-subscription-confirmed.flag-shield" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Confirmed/Organizer badge.
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 1 ".calendar-item .flag" elements
  Then I should see 1 ".calendar-item .flag.flag-danger.flag-subscription-confirmed.flag-shield" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Weekly, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 7 ".calendar-item" elements
  Then I should see 0 ".calendar-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Monthly, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 0 ".calendar-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Confirmed/Member badge.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/fribourg/calendar/weekly"
  And the response status code should be 200
  Then I should see 7 ".calendar-item" elements
  Then I should see 1 ".calendar-item .flag" elements
  Then I should see 1 ".calendar-item .flag.flag-info.flag-subscription-confirmed.flag-default" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Confirmed/Member badge.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/fribourg/calendar/monthly"
  And the response status code should be 200
  Then I should see 1 ".calendar-item .flag" elements
  Then I should see 1 ".calendar-item .flag.flag-info.flag-subscription-confirmed.flag-default" elements

