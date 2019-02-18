Feature: Dashboard my photos - Activity within the user can manage photos
  In order to make sure Dashboard my photos works
  As a bunch of users
  I want to make sure the correct activities writable are shown according ACL

  @api
  Scenario: Logged as Admin, When I access "My photos" in Lausanne
    Given I am logged in as user "admin"
    When I am on "/photos/1/user/1"
    And the response status code should be 200
    Then I should see 4 ".card-list .card-list-item" elements
    Then I should see a "#activity2" element
    Then I should see a "#activity5" element
    Then I should see a "#activity7" element
    Then I should see a "#activity3" element
    Then I should not see a "#activity4" element
    Then I should not see a "#activity6" element
    Then I should not see a "#activity8" element
    Then I should not see a "#activity12" element
    Then I should not see a "#activity13" element
    Then I should not see a "#activity14" element
    And I should see "qs.photos.add_photos"
    And I should not see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My photos" in Fribourg
    Given I am logged in as user "admin"
    When I am on "/photos/2/user/1"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    Then I should not see a "#activity11" element
    And I should see "qs.photos.add_photos"
    And I should not see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My photos" in Genève
    Given I am logged in as user "admin"
    When I am on "/photos/3/user/1"
    And the response status code should be 200
    Then I should see 1 ".card-list .card-list-item" elements
    Then I should see a "#activity9" element
    Then I should not see a "#activity10" element
    And I should see "qs.photos.add_photos"
    And I should not see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Member of Fribourg, When I access "My photos"
    Given I am logged in as user "member+fribourg+approval+lausanne"
    When I am on "/photos/2/user/11"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should not see "qs.photos.add_photos"
    And I should see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne, When I access "My photos"
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/1/user/2"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs.photos.add_photos"
    And I should not see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Manager of Lausanne, When I access "My photos"
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/1/user/5"
    And the response status code should be 200
    Then I should see 2 ".card-list .card-list-item" elements
    Then I should see a "#activity2" element
    Then I should see a "#activity3" element
    Then I should not see a "#activity5" element
    And I should see "qs.photos.add_photos" link with href "photos/1/add"
    And I should not see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Organizer of Lausanne, When I access "My photos"
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/1/user/6"
    And the response status code should be 200
    Then I should see 2 ".card-list .card-list-item" elements
    Then I should see a "#activity3" element
    Then I should see a "#activity5" element
    Then I should not see a "#activity2" element
    And I should see "qs.photos.add_photos" link with href "photos/1/add"
    And I should not see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, When I access "My photos"
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/photos/1/user/8"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    Then I should not see a "#activity2" element
    Then I should not see a "#activity3" element
    Then I should not see a "#activity5" element
    And I should not see "qs.photos.add_photos"
    And I should see "qs.photos.user.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne & Member of Fribourg, When I access "My photos"
    Given I am logged in as user "member+fribourg+member+lausanne"
    When I am on "/photos/1/user/9"
    And the response status code should be 200
    Then I should see 1 ".card-list .card-list-item" elements
    Then I should see a "#activity3" element
    Then I should not see a "#activity6" element
    And I should see "qs.photos.add_photos" link with href "photos/1/add"
    And I should not see "qs.photos.user.collection.empty"
