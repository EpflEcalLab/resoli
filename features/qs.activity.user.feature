Feature: Activitiy User Collection
  In order to make sure activities user collection are working
  As a bunch of users
  I want to make sure the correct activities are shown

@api
Scenario: Logged as Member of Lausanne, I can access my own activities page in Lausanne
  Given I am logged in as user "member+lausanne"
  When I am on "/activities/1/user/2"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see a "#activity5" element
  Then I should see a "#activity4" element

@api
Scenario: Logged as Manager of Lausanne, I can access my own activities page in Lausanne
  Given I am logged in as user "manager+lausanne"
  When I am on "/activities/1/user/5"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see a "#activity2" element
  Then I should see a "#activity3" element

@api
Scenario: Logged as Organizer of Lausanne, I can access my own activities page in Lausanne
  Given I am logged in as user "organizer+lausanne"
  When I am on "/activities/1/user/6"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see a "#activity5" element
  Then I should see a "#activity3" element
