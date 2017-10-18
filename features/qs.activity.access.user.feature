Feature: Activitiy User Access
  In order to make sure ACL is working for activity user collection
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

@api
Scenario: Logged as Member of Lausanne, I can access my own activities page in Lausanne
  Given I am logged in as user "member+lausanne"
  When I am on "/activities/1/user/2"
  And the response status code should be 200

@api
Scenario: Logged as Member of Lausanne, I can't access my own activities page in Fribourg, because I don't have access to Fribourg community
  Given I am logged in as user "member+lausanne"
  When I am on "/activities/2/user/2"
  And the response status code should be 403

@api
Scenario: Logged as Member of Lausanne, I can't access to someone activities page
  Given I am logged in as user "member+lausanne"
  When I am on "/activities/2/user/4"
  And the response status code should be 403

@api
Scenario: Logged as Admin, When I can access to anybody activities page in any communities
  Given I am logged in as user "admin"
  When I am on "/activities/1/user/1"
  And the response status code should be 200
  When I am on "/activities/1/user/2"
  And the response status code should be 200
  When I am on "/activities/2/user/2"
  And the response status code should be 200
  When I am on "/activities/2/user/4"
  And the response status code should be 200
