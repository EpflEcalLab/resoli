Feature: Subscription User Collection
  In order to make sure activities user collection are working
  As a bunch of users
  I want to make sure the correct activities are shown

@api
Scenario: Logged as Manager of Lausanne, I can access my own subscriptions page in Lausanne
  Given I am logged in as user "manager+lausanne"
  When I am on "/events/1/user/5"
  And the response status code should be 200
  Then I should see 3 ".card-list-item" elements
  Then I should see a "#card36" element
  Then I should see a "#card54" element
  Then I should not see a "#card38" element

@api
Scenario: Logged as Member of Lausanne & Manager of Fribourg, I can access my own subscriptions page in Fribourg
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/2/user/13"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should see a "#card29" element
  Then I should not see a "#card54" element

@api
Scenario: Logged as Member of Lausanne & Manager of Fribourg, I can access my own subscriptions page in Lausanne
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/events/1/user/13"
  And the response status code should be 200
  Then I should see 1 ".card-list-item" elements
  Then I should not see a "#card29" element
  Then I should see a "#card54" element

@api
Scenario: Logged as Organizer of Lausanne, I can access my own subscriptions page in Lausanne
  Given I am logged in as user "organizer+lausanne"
  When I am on "/events/1/user/6"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see a "#card37" element
  Then I should see a "#card40" element
  Then I should not see a "#card54" element
