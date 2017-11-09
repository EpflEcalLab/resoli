Feature: Dashboard my photos - Activity within the user can manage photos
  In order to make sure Dashboard my photos works
  As a bunch of users
  I want to make sure the correct activities writable are shown according ACL

  @api
  Scenario: Logged as Member of Lausanne, When I access "My photos
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/1/user/2"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements

  @api
  Scenario: Logged as Manager of Lausanne, When I access "My photos
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/1/user/5"
    And the response status code should be 200
    Then I should see 2 ".card-list .card-list-item" elements
    Then I should see a "#activity2" element
    Then I should see a "#activity3" element
    Then I should not see a "#activity5" element

  @api
  Scenario: Logged as Organizer of Lausanne, When I access "My photos
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/1/user/6"
    And the response status code should be 200
    Then I should see 2 ".card-list .card-list-item" elements
    Then I should see a "#activity3" element
    Then I should see a "#activity5" element
    Then I should not see a "#activity2" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, When I access "My photos
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/photos/1/user/8"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    Then I should not see a "#activity2" element
    Then I should not see a "#activity3" element
    Then I should not see a "#activity5" element
