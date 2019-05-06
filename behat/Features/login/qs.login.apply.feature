Feature: Communities Apply
  In order to make sure Authentication - Apply is working
  As a bunch of users with multiple approval or membership
  I want to make sure the communities panel for applying is working

  @api
  Scenario: Login as Member with 2 communities, show him only those whitout application
    Given I am logged in as user "member+fribourg+member+lausanne"
    And I am on "/authentication/communities/apply"
    Then I should see 1 "input[type=radio]" elements
    And I should not see a "#edit-community-1" element
    And I should not see a "#edit-community-2" element
    And I should see a "#edit-community-3" element

  @api
  Scenario: Login as Member with 1 community & 1 approval, show him only those whitout application
    Given I am logged in as user "member+fribourg+approval+lausanne"
    And I am on "/authentication/communities/apply"
    Then I should see 1 "input[type=radio]" elements
    And I should not see a "#edit-community-1" element
    And I should not see a "#edit-community-2" element
    And I should see a "#edit-community-3" element

  @api
  Scenario: Login as user whitout any previous appliances to communities, show them all
    Given I am logged in as user "nobody"
    And I am on "/authentication/communities/apply"
    Then I should see 3 "input[type=radio]" elements
    And I should see a "#edit-community-1" element
    And I should see a "#edit-community-2" element
    And I should see a "#edit-community-3" element

  @api
  Scenario: Login as Member whith all communities, show him a special message instead of radios
    Given I am logged in as user "approved+all"
    And I am on "/authentication/communities/apply"
    Then I should see 0 "input[type=radio]" elements
    And I should not see a "#edit-community-1" element
    And I should not see a "#edit-community-2" element
    And I should not see a "#edit-community-3" element
    And I should see "qs_auth.form.communities_apply.no_community" in the "form" element

 @api
  Scenario: Login as Member with 2 communities & 1 approval, show him a special message
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    And I am on "/authentication/communities/apply"
    Then I should see 0 "input[type=radio]" elements
    And I should not see a "#edit-community-1" element
    And I should not see a "#edit-community-2" element
    And I should not see a "#edit-community-3" element
    And I should see "qs_auth.form.communities_apply.no_community" in the "form" element

  @api
  Scenario: Login as user with declined appliances to communities, show them again
    Given I am logged in as user "declined+all"
    And I am on "/authentication/communities/apply"
    Then I should see 3 "input[type=radio]" elements
    And I should see a "#edit-community-1" element
    And I should see a "#edit-community-2" element
    And I should see a "#edit-community-3" element
