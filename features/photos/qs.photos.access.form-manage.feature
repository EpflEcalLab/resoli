Feature: Photos form manage Access
  In order to make sure ACL is working for Photos form manage
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Admin, I can access "Manage photos" of any Activities of any User
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/user/1"
    And the response status code should be 200
    When I am on "/photos/activity/4/user/1"
    And the response status code should be 200
    When I am on "/photos/activity/4/user/2"
    And the response status code should be 200

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to "Manage photos" of Activity N°2 (Atelier Créatif) - Because Photos are writable by Maintainer+ & I'm not even a Member
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/2/user/6"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to "Manage photos" of Activity N°5 (Accueil Café) - Because Photos are writable by Member+ & I'm Organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/5/user/6"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to "Manage photos" of Activity N°2 (Atelier Créatif) - Because Photos are writable by Maintainer+ & I'm Organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/activity/2/user/5"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to "Manage photos" of Activity N°3 (Sorties Théàtre) - Because Photos are writable by Member+ & I'm not even a Member
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/activity/3/user/2"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can access to "Manage photos" of Activity N°4 (Atelier Bougies) - Because Photos are writable by Member+ & I'm a Member of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/activity/4/user/2"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to "Manage photos" of Activity N°5 (Accueil Café) - Because Photos are writable by Member+ & I'm a pending member of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/activity/5/user/2"
    And the response status code should be 403
