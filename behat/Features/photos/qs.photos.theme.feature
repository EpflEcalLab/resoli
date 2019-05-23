Feature: Activity photos by theme
  In order to make sure Activity photos by theme works
  As a bunch of users
  I want to make sure the correct activities are shown according ACL

  @api
  Scenario: Logged as Admin, I can see every activities photos
    Given I am logged in as user "admin"
    When I am on "/lausanne/photos/theme"
    And the response status code should be 200
    Then I should see 4 ".card" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne photos by theme of 2 Activities [N° 3 & 5]
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/photos/theme"
    And the response status code should be 200
    Then I should see 2 ".card" elements

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne photos by theme of 2 Activities [N° 3 & 2]
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/photos/theme"
    And the response status code should be 200
    Then I should see 2 ".card" elements

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne photos by theme of 1 Activities [N° 3]
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/photos/theme"
    And the response status code should be 200
    Then I should see 1 ".card" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, I can access to Lausanne photos by theme of 1 Activities [N° 3]
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/lausanne/photos/theme"
    And the response status code should be 200
    Then I should see 1 ".card" elements
