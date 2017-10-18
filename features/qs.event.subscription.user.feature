Feature: Subscription User Collection
  In order to make sure activities user collection are working
  As a bunch of users
  I want to make sure the correct activities are shown

@api
Scenario: Logged as Manager of Lausanne, I can access my own subscriptions page in Lausanne
  Given I am logged in as user "manager+lausanne"
  When I am on "/events/1/user/5"
  And the response status code should be 200
  Then I should see 2 ".card-list-simple-item" elements
  Then I should see a "#event36" element
  Then I should see a "#event36 .flag" element
  Then I should see a "#event36 .flag .flag-subscription-confirmed" element
  Then I should see a "#event37" element
  Then I should see a "#event37 .flag" element
  Then I should see a "#event37 .flag .flag-subscription-wait" element
  Then I should not see a "#event38" element

@api
Scenario: Logged as Member of Lausanne & Manager of Fribourg, I can access my own subscriptions page in Fribourg
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/2/user/13"
  And the response status code should be 200
  Then I should see 1 ".card-list-simple-item" elements
  Then I should see a "#event29" element
  Then I should see a "#event29 .flag" element
  Then I should see a "#event29 .flag .flag-subscription-confirmed" element

@api
Scenario: Logged as Member of Lausanne & Manager of Fribourg, I can access my own subscriptions page in Lausanne
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/1/user/13"
  And the response status code should be 200
  Then I should see 0 ".card-list-simple-item" elements
  Then I should not see a "#event29" element

@api
Scenario: Logged as Organizer of Lausanne, I can access my own subscriptions page in Lausanne
  Given I am logged in as user "organizer+lausanne"
  When I am on "/events/1/user/6"
  And the response status code should be 200
  Then I should see 2 ".card-list-simple-item" elements
  Then I should see a "#event37" element
  Then I should see a "#event37 .flag" element
  Then I should see a "#event37 .flag .flag-subscription-confirmed" element
  Then I should see a "#event40" element
  Then I should see a "#event40 .flag" element
  Then I should see a "#event40 .flag .flag-subscription-confirmed" element
