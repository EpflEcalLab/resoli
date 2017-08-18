Feature: Activitiy Access
  In order to make sure ACL is working for activities
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities
    Given I am logged in as user "member+lausanne"
    When I am on "/activities/lausanne/theme"
    And the response status code should be 200

  @api
  Scenario: Login as user waiting approval of Lausanne, I can't access to Lausanne activities
    Given I am logged in as user "approval+lausanne"
    When I am on "/activities/lausanne/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "member+lausanne"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "manager+lausanne"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "organizer+lausanne"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg I can't access to Lausanne & Fribourg activities
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 200
    When I am on "/activities/lausanne/theme"
    And the response status code should be 200

  # @api
  # Scenario: Logged as user whitout any previous appliances to communities redirect him on the apply form
  #   Given I am logged in as user "nobody"
  #   And the url should match "authentication/communities/apply"
  #   And the response status code should be 200

  # @api
  # Scenario: Login as Member with many communities redirect him on the communities page
  #   Given I am logged in as user "member+fribourg+member+lausanne"
  #   And the url should match "communities"
  #   And the response status code should be 200

  # @api
  # Scenario: Login as Member of Lausanne & Organizer of Fribourg redirect him on the communities page
  #   Given I am logged in as user "member+lausanne+organizer+fribourg"
  #   And the url should match "communities"
  #   And the response status code should be 200

  # @api
  # Scenario: Login as user waiting approval of his only community redirect him on the community approval page
  #   Given I am logged in as user "approval+lausanne"
  #   And the url should match "/authentication/approval/1"
  #   And the response status code should be 200

  # @api
  # Scenario: Login as user with multiple approval of communities redirect him on the community approval page
  #   Given I am logged in as user "approval+fribourg+approval+lausanne"
  #   And the url should match "/authentication/approval/1"
  #   And the response status code should be 200

  # @api
  # Scenario: Login as Member with 1 community & 1 approval redirect him on the community page
  #   Given I am logged in as user "member+fribourg+approval+lausanne"
  #   And the url should match "activities/fribourg/theme"
  #   And the response status code should be 200

  # @api
  # Scenario: Login as Member with multiple communities & 1 approval redirect him on the communities page
  #   Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
  #   And the url should match "communities"
  #   And the response status code should be 200


