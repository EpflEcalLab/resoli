Feature: Photos form comments Access
  In order to make sure ACL is working for Photos form comments
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Admin, I can access "Comments photos" of any Activities
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/comment?photos[42]=42&photos[43]=43"
    And the response status code should be 200
    When I am on "/photos/activity/5/comment?photos[48]=48"
    And the response status code should be 200

  @api
  Scenario: Logged as Admin, I can access "Comments photos" of any Activities whitout Photos parameter in URL
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/comment"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to "Comments photos" of Activity N°2 (Atelier Créatif) - Because Photos are deletable by Maintainer+ & I'm not even a Member
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/2/comment?photos[42]=42&photos[43]=43"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to "Comments photos" of Activity N°5 (Accueil Café) - Because Photos are deletable by Maintainer+ & I'm Organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/5/comment?photos[48]=48"
    And the response status code should be 200

 @api
  Scenario: Logged as Organizer of Lausanne, I can't access to "Comments photos" when I trick URL to accessible activity with others photos
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/2/comment?photos[48]=48"
    And the response status code should be 403

 @api
  Scenario: Logged as Organizer of Lausanne, I can't access to "Comments photos" when I trick URL to accessible activity with others photos
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/activity/3/comment?photos[42]=42"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can access to "Comments photos" of Activity N°2 (Atelier Créatif) - Because Photos are deletable by Maintainer+ & I'm Organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/activity/2/comment?photos[42]=42&photos[43]=43&photos[45]=45&photos[46]=46"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to "Comments photos" of Activity N°3 (Sorties Théàtre) - Because Photos are deletable by Maintainer+ & I'm not even a Member
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/activity/3/comment?photos[50]=50"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to "Comments photos" of Activity N°5 (Accueil Café) - Because Photos are deletable by Maintainer+ & I'm a pending member of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/activity/5/comment?photos[48]=48"
    And the response status code should be 403
