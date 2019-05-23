Feature: Activity Visibility Form

  @api
  Scenario: When reaching the Visibility form of Activity, the fields should be prefilled with entity values
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/edit/visibility"
    Then I should see 11 "#qs-activity-edit-visibility-form input" elements
    Then the "edit-community-can-subscribe" checkbox should not be checked
    Then the "edit-community-access-contact" checkbox should be checked
    Then the "edit-community-access-detail" checkbox should be checked
    Then the "edit-community-access-story" checkbox should not be checked
    Then the "edit-member-create-story" checkbox should be checked
    Then the "edit-community-access-gallery" checkbox should not be checked
    Then the "edit-member-create-gallery" checkbox should be checked

  @api @preserveDatabase
  Scenario: When editting the Visibility form of Activity, once saved I should be redirected on the Activity dashboard
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Visibility form "accueil-cafe" of "lausanne" with:
      | community-can-subscribe | community-access-contact | community-access-detail | community-access-story | member-create-story | community-access-gallery | member-create-gallery |
      | 1 | 1 | 1 | 1 | 1 | 1 | 1 |
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe/dashboard"

  @api @preserveDatabase
  Scenario: When editting the Visibility form of Activity, no mail should be sent
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Visibility form "accueil-cafe" of "lausanne" with:
      | community-can-subscribe | community-access-contact | community-access-detail | community-access-story | member-create-story | community-access-gallery | member-create-gallery |
      | 1 | 1 | 1 | 1 | 1 | 1 | 1 |
    And I press "edit-submit"
    And 0 mails should be sent

  @api @preserveDatabase
  Scenario: When editting the Visibility form of Activity, the values should be alterd & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Visibility form "accueil-cafe" of "lausanne" with:
      | community-can-subscribe | community-access-contact | community-access-detail | community-access-story | member-create-story | community-access-gallery | member-create-gallery |
      | 1 | 1 | 1 | 1 | 1 | 1 | 1 |
    And I press "edit-submit"
    When I am on "/lausanne/activities/accueil-cafe/edit/visibility"
    Then the "edit-community-can-subscribe" checkbox should be checked
    Then the "edit-community-access-contact" checkbox should be checked
    Then the "edit-community-access-detail" checkbox should be checked
    Then the "edit-community-access-story" checkbox should be checked
    Then the "edit-member-create-story" checkbox should be checked
    Then the "edit-community-access-gallery" checkbox should be checked
    Then the "edit-member-create-gallery" checkbox should be checked
