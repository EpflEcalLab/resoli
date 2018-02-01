# Given I am logged in as user "manager+lausanne"
# Given I am logged in as user "organizer+lausanne"
# Given I am logged in as user "member+lausanne+organizer+fribourg"
# Given I am logged in as user "member+fribourg"
# Given I am logged in as user "member+lausanne+manager+fribourg"

# When I am on "/activities/1/user/5"
# When I am on "/activities/1/user/6"
# When I am on "/activities/1/user/8"
# When I am on "/activities/2/user/4"
# When I am on "/activities/2/user/13"

Feature: Badges - Subscription - Calendar - List
  AssertsCalendar List (below weekly and monthly dots) show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Waiting/Maintainer badge on the Event N°37 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 4 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event37 .flag.flag-warning.flag-subscription-wait" elements

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Waiting/Maintainer badge on the Event N°37 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 4 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event37 .flag.flag-warning.flag-subscription-wait" elements

  @api
  Scenario: Logged as Manager of Lausanne, when reaching the Calendar Monthly (next months), I should see my 2 badges of subscriptions. I see my Confirmed/Maintainer badge on the Event N°36 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/calendar/monthly"
  Then I follow "calendar-monthly-next"
  And the response status code should be 200
  Then I should see 1 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event36 .flag.flag-warning.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Confirmed/Organizer badge on the Event N°37 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 4 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event37 .flag.flag-danger.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Confirmed/Organizer badge on the Event N°37 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 4 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event37 .flag.flag-danger.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Weekly, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/lausanne/calendar/weekly"
  And the response status code should be 200
  Then I should see 4 ".card-list-simple-item" elements
  Then I should see 0 ".card-list-simple-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Monthly, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/lausanne/calendar/monthly"
  And the response status code should be 200
  Then I should see 4 ".card-list-simple-item" elements
  Then I should see 0 ".card-list-simple-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Monthly, I should see my 1 badge of subscriptions. I see my Member badge on the Event N°29 (Cours Smartphone & Tablette)
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/fribourg/calendar/monthly"
  And the response status code should be 200
  Then I should see 1 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event29 .flag.flag-info.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, when reaching the Calendar Weekly, I should see my 1 badge of subscriptions. I see my Member badge on the Event N°29 (Cours Smartphone & Tablette)
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/fribourg/calendar/weekly"
  And the response status code should be 200
  Then I should see 1 ".card-list-simple-item" elements
  Then I should see 1 ".card-list-simple-item .flag" elements
  Then I should see 1 "#event29 .flag.flag-info.flag-subscription-confirmed" elements
