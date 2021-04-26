Feature: Activity Members Form

  @api
  Scenario: When reaching the Members form of Activity, the selector should contain every member of the community which are not member of this Activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/dashboard/members"
    Then I should see 7 "#qs-activity-add-member-form select option" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=13]" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=8]" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=5]" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=9]" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=22]" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=18]" elements
    Then I should see 1 "#qs-activity-add-member-form select option[value=2]" elements

  @api @preserveDatabase
  Scenario: When adding a Member to the Activity, once saved I should be redirected on the Activity Member page
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/dashboard/members"
    And I select "manager+lausanne@antistatique.net" from "edit-member"
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe/dashboard/members#card5"
    And I should see "qs_activity.activities.form.add.member.success Accueil Café" in the ".alert" element

  @api @preserveDatabase @mail
  Scenario: When adding a Member to the Activity, no mail should be sent
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/dashboard/members"
    And I select "member+fribourg+member+lausanne@antistatique.net" from "edit-member"
    And I press "edit-submit"
    And 0 mail should be sent

  @api @preserveDatabase
  Scenario: When adding a Member to the Activity, the new member should be shown on the liste
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/dashboard/members"
    Then I should see 0 ".card-list-item" elements
    And I select "manager+lausanne@antistatique.net" from "edit-member"
    And I press "edit-submit"
    Then I should see 1 ".card-list-item" elements
