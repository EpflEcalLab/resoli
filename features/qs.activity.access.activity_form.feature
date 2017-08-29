Feature: Activitiy Forms Access
  In order to make sure ACL is working for activity forms
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

## Add Form
  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

## Dashboard
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Dashboard of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Dashboard of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Dashboard of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Dashboard of Activity N°2 (Activity 2 - Lausanne - Theme N°1) even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the dashboard. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Dashboard of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Dashboard of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/dashboard"
    And the response status code should be 403

## Edit Form - General Informations
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Info Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Info Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Info Form of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Edit Info Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Info Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Info Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the Edit Info Form. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Edit Info Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Edit Info Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/info"
    And the response status code should be 403

## Edit Form - Visibility
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Visibility Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Visibility Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Visibility Form of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Edit Visibility Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Visibility Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Visibility Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the Edit Visibility Form. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Edit Visibility Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Edit Visibility Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/visibility"
    And the response status code should be 403

## Edit Form - Default values
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Edit Defaults Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Defaults Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Edit Defaults Form of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Edit Defaults Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Defaults Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Edit Defaults Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the Edit Defaults Form. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Edit Defaults Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Edit Defaults Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/edit/defaults"
    And the response status code should be 403

## Delete Form
  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Delete Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) I'm the organizer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/delete"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Delete Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm only a maintainer of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/delete"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Delete Form of Activity N°5 (Activity 5 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-5-lausanne-theme-ndeg1/delete"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Delete Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm the organizer of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/delete"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Delete Form of Activity N°3 (Activity 3 - Lausanne - Theme N°1) because I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1/delete"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Delete Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) even If I'm member of this activity because, I'm not an organizer of this activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/delete"
    And the response status code should be 403

  # Shoud I tests access of community & privilege of activity ? If I don't a member with organizer privilege of Fribourg but not a member of community Fribourg could access to the Delete Form. What should I do ?
  @api
  Scenario: Logged as Member of Fribourg, I can't access to the Delete Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "member+fribourg"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/delete"
    And the response status code should be 403

  @api
  Scenario: Logged as approval of Lausanne, I can't access to the Edit Defaults Form of Activity N°2 (Activity 2 - Lausanne - Theme N°1) because I have no provilege on this activity
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1/delete"
    And the response status code should be 403
