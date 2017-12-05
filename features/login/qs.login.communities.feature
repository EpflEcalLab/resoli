Feature: Login Communities
  In order to make sure Authentication - Login is working
  As a bunch of users with multiple approval or membership
  I want to make sure the communities selection after login is working

  @api
  Scenario: Login as Member with many communities redirect him on the communities page
    Given I am logged in as user "member+fribourg+member+lausanne"
    And the url should match "communities"
    And I should see "Fribourg" link with href "fribourg/welcome"
    And I should see "Lausanne" link with href "lausanne/welcome"

  @api
  Scenario: Login as Member of Lausanne & Organizer of Fribourg redirect him on the communities page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    And the url should match "communities"
    And I should see "Fribourg" link with href "fribourg/welcome"
    And I should see "Lausanne" link with href "lausanne/welcome"

  @api
  Scenario: Login as Member with multiple communities & 1 approval redirect him on the communities page
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    And the url should match "communities"
    And I should see "Fribourg" link with href "fribourg/welcome"
    And I should see "Lausanne" link with href "authentication/approval/1"
    And I should see "Genève" link with href "geneve/welcome"

  @api
  Scenario: Login as user with declined appliances to communities, show him a special message instead of communities links
    Given I am logged in as user "declined+all"
    And I am on "/authentication/communities"
    And I should see "qs_auth.form.communities.no_community"

  @api
  Scenario: Login as user whitout any previous appliances to communities, show him a special message instead of communities links
    Given I am logged in as user "nobody"
    And I am on "/authentication/communities"
    And I should see "qs_auth.form.communities.no_community"
