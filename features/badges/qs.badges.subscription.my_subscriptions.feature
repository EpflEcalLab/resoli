Feature: Badges - Subscription - My Subscriptions
  Asserts My Subscriptions show subscriptions's badges
  according current users state of subscriptions on the event pill
  and use the highest privilege on this event's activity to get the color.

  @api
  Scenario: Logged as Manager of Lausanne, I should see my 3 badges of subscriptions. I see my Confirmed/Maintainer badge on the Event N°36 (Victor Vatard - Dire l’inverse, penser le contraire et vice-versa), my Pending/Maintainer badge on the Event N°37 (Macbeth) & the Guests_Wait/Maintainer badge on the Event 54 (Mariage et châtiment)
  Given I am logged in as user "manager+lausanne"
  When I am on "/events/1/user/5"
  And the response status code should be 200
  Then I should see 3 ".card-list-item" elements
  Then I should see 3 ".card-list-item .flag" elements
  Then I should see 1 "#card36 .flag.flag-warning.flag-subscription-confirmed.flag-shield" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card36" element
  Then I should see 1 "#card37 .flag.flag-warning.flag-subscription-wait.flag-shield" elements
  And I should see "qs.event.user.subscription.pending" in the "#card37" element
  Then I should see 1 "#card54 .flag.flag-outline-warning.flag-subscription-guests-wait.flag-shield" elements
  And I should see "qs.event.user.subscription.pendings_guests 1" in the "#card54" element

  @api
  Scenario: Logged as Organizer of Lausanne, I should see my 2 badges of subscriptions. I see the Guests_Waiting/Organizer badge on the Event N°37 (Macbeth) & my Confirmed/Organizer badge on the Event N°40 (Accueil Café)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/events/1/user/6"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see 2 ".card-list-item .flag" elements
  Then I should see 1 "#card37 .flag.flag-outline-danger.flag-subscription-guests-wait.flag-shield" elements
  Then I should see 1 "#card40 .flag.flag-danger.flag-subscription-confirmed.flag-shield" elements
  And I should see "qs.event.user.subscription.pendings_guests 1" in the "#card37" element
  And I should see "qs.event.user.subscription.confirmed" in the "#card40" element

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/1/user/13"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should see 1 "#card54 .flag.flag-info.flag-subscription-wait.flag-default" elements
  And I should see "qs.event.user.subscription.pending" in the "#card54" element

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, I should see my badge of subscriptions. I see my Member badge on the Event N°29 (Prendre des Photos avec son Smartphone)
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/2/user/13"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#card29 .flag.flag-info.flag-subscription-confirmed.flag-default" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card29" element

  @api
  Scenario: Logged as Member of Fribourg, I should see 0 badges 'cause I have 0 subscriptions in Lausanne community
  Given I am logged in as user "member+fribourg"
  When I am on "/events/2/user/4"
  And the response status code should be 200
  Then I should see 0 ".card-list-item" elements

  @api
  Scenario: Logged as Member N°2 of Fribourg, I see my Organizer badge on the Activity N°57 (Monopoly) & my 2 Member badges on the Activity N°56 (Escalade) & Activity N°61 (Ginguettes)
  Given I am logged in as user "member2+fribourg"
  When I am on "/events/2/user/23"
  And the response status code should be 200
  Then I should see 3 ".card-list-item" elements
  Then I should see 3 ".card-list-item .flag" elements
  Then I should see 1 "#card59 .flag.flag-info.flag-subscription-confirmed.flag-default" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card59" element
  Then I should see 1 "#card61 .flag.flag-warning.flag-subscription-confirmed.flag-shield" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card61" element
  Then I should see 1 "#card63 .flag.flag-info.flag-subscription-confirmed.flag-default" elements
  And I should see "qs.event.user.subscription.confirmed" in the "#card63" element

  @api
  Scenario: Logged as Member & Organizer of Fribourg, I should see 0 badges 'cause I have 0 subscriptions in Fribourg community
  Given I am logged in as user "member+fribourg+organizer+fribourg"
  When I am on "/events/2/user/16"
  And the response status code should be 200
  Then I should see 0 ".card-list-item" elements

