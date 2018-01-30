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

Feature: Badges - Subscription - My Subscriptions
  Asserts My Subscriptions show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Manager of Lausanne, I should see my 2 badges of subscriptions. I see my Confirmed/Maintainer badge on the Event N°36 (Victor Vatard - Dire l’inverse, penser le contraire et vice-versa) & my Wait/Maintainer badge on the Event N°37 (Macbeth)
  Given I am logged in as user "manager+lausanne"
  When I am on "/events/1/user/5"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see 2 ".card-list-item .flag" elements
  Then I should see 1 "#event36 .flag.flag-warning.flag-subscription-confirmed" elements
  Then I should see 1 "#event37 .flag.flag-warning.flag-subscription-wait" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I should see my 2 badges of subscriptions. I see my Confirmed/Organizer badge on the Event N°37 (Macbeth) & my Confirmed/Organizer badge on the Event N°40 (Accueil Café)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/events/1/user/6"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see 2 ".card-list-item .flag" elements
  Then I should see 1 "#event37 .flag.flag-danger.flag-subscription-confirmed" elements
  Then I should see 1 "#event40 .flag.flag-danger.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/1/user/13"
  And the response status code should be 200
  Then I should see 0 ".card-list-item" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, I should see my badge of subscriptions. I see my Member badge on the Event N°29 (Prendre des Photos avec son Smartphone)
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/2/user/13"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#event29 .flag.flag-info.flag-subscription-confirmed" elements

  @api
  Scenario: Logged as Member of Fribourg, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community
  Given I am logged in as user "member+fribourg"
  When I am on "/events/2/user/4"
  And the response status code should be 200
  Then I should see 0 ".card-list-item" elements
