Feature: Dashboard my photos Access
  In order to make sure ACL is working for Dashboard my photos
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Admin, I can access to "My photos" to anyone
    Given I am logged in as user "admin"
    When I am on "/photos/1/user/1"
    And the response status code should be 200
    When I am on "/photos/1/user/2"
    And the response status code should be 200
    When I am on "/photos/1/user/3"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can access to "My photos"
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/1/user/2"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to "My photos" of someone else
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/1/user/3"
    And the response status code should be 403
