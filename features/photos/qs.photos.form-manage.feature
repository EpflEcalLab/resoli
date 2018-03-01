Feature: Photos form manage
  In order to make sure Dashboard Photos form manage works
  As a bunch of users
  I want to make sure the correct writable photos are shown according ACL

  @api
  Scenario: Logged as Admin, When I access "Manage photos" of any Activities of any User, I should see every photos
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/user/1"
    And the response status code should be 200
    Then I should see 7 "form .input-checkbox" elements
    Then I should see 1 ".checkbox-image .card-btn-corner" elements

  @api
  Scenario: Logged as Organizer of Lausanne, When I access to "Manage photos" of Activity N°5 (Accueil Café) - Because Photos are writable by Member+ & I'm Organizer of this activity, I should see every photos
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/5/user/6"
    And the response status code should be 200
    Then I should see 1 "form .input-checkbox" elements

  @api
  Scenario: Logged as Manager of Lausanne, When I access to "Manage photos" of Activity N°2 (Atelier Créatif) - Because Photos are writable by Maintainer+ & I'm Organizer of this activity, I should see every photos
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/activity/2/user/5"
    And the response status code should be 200
    Then I should see 7 "form .input-checkbox" elements

  @api
  Scenario: Logged as Member of Lausanne, When I access to "Manage photos" of Activity N°4 (Atelier Bougies) - Because Photos are writable by Member+ & I'm a Member of this activity, I should see only my photos
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/activity/4/user/2"
    And the response status code should be 200
    Then I should see 0 "form .input-checkbox" elements

  @api
  Scenario: Logged as Member of Lausanne & Member of Fribourg, When I access to "Manage photos" of Activity N°3 (Sorties Théâtre) - Because Photos are writable by Member+ & I'm a Member of this activity, I should see only my photos
    Given I am logged in as user "member+fribourg+member+lausanne"
    When I am on "/photos/activity/3/user/9"
    And the response status code should be 200
    Then I should see 2 "form .input-checkbox" elements

  @api
  Scenario: Logged as Manager of Lausanne & Member of Fribourg, When I access to "Manage photos" of Activity N°3 (Sorties Théâtre) - Because Photos are writable by Member+ & I'm a Maintainer of this activity, I should see all photos
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/activity/3/user/5"
    And the response status code should be 200
    Then I should see 3 "form .input-checkbox" elements

  @api
  Scenario: Logged as Admin, When I access "Manage photos" of Activity N°3 (Sorties Théâtre), I should see every photos
    Given I am logged in as user "admin"
    When I am on "/photos/activity/3/user/1"
    And the response status code should be 200
    Then I should see 3 "form .input-checkbox" elements

