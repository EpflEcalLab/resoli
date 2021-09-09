Feature: Sharing manage Volunteerism Form

## Access
  @api
  Scenario Outline: Logged-in, I can access my own community(ies) volunteerisms form page. Accessing community in which I don't belongs should be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "/sharing/community/<community-id>/volunteerism/manage"
    Then the response status code should be <code>

    Examples:
      | user | community-id | code |
      | organizer+lausanne | 1 | 200 |
      | organizer+lausanne | 2 | 403 |
      | member+lausanne | 1  | 200 |
      | member+lausanne | 2  | 403 |
      | member+lausanne+organizer+fribourg | 1  | 200 |
      | member+lausanne+organizer+fribourg | 2  | 200 |
      | member+lausanne+organizer+fribourg | 3  | 403 |
      | manager+lausanne | 1  | 200 |
      | manager+lausanne | 2  | 403 |

## Form submits.
  @api @preserveDatabase
  Scenario: In the manage Volunteerism form, I should be able to submit valid volunteerisms and be redirected to my dashboard.
    Given I am logged in as user "admin"
    When I am on "/sharing/community/1/volunteerism/manage"
    And I check "edit-volunteerism-19"
    And I check "edit-volunteerism-20"
    And I press "edit-save-and-set-default-values"
    Then the url should match "/sharing/1/user/1/offers"
    And I should see "qs_sharing.volunteerisms.form.manage.success Lausanne" in the ".alert" element

  @api @preserveDatabase
  Scenario: In the manage Volunteerism form, I should be able to submit valid volunteerisms and be redirected to create an offer.
    Given I am logged in as user "admin"
    When I am on "/sharing/community/1/volunteerism/manage"
    And I check "edit-volunteerism-22"
    And I check "edit-volunteerism-25"
    And I press "edit-save-and-new-offer"
    Then the url should match "/sharing/1/offers/add?user=1"
    And I should see "qs_sharing.volunteerisms.form.manage.success Lausanne" in the ".alert" element

## Floating Button
  @api
  Scenario: In the Sharing volunteerism manage form page, I should see a floating button
    Given I am logged in as user "admin"
    When I am on "/sharing/community/1/volunteerism/manage"
    Then I should see 1 ".floating a" elements

# Back button.
  @api
  Scenario: In the Sharing volunteerism manage form page, I should see a back button pointing to my sharing dashboard
    Given I am logged in as user "admin"
    When I am on "/sharing/community/1/volunteerism/manage"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.previous.to_sharing_dashboard" link with href "/sharing/1/user/1/offers"
