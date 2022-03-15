Feature: Demo mode
  In order to make sure the demo mode is active
  I want to make sure the logout button is hidden

  Scenario: As the admin, on the front page I should see the logout button
    Given I am logged in as user "admin"
    When I am on "/"
    Then I should see "qs_auth.logout" link with href "/user/logout"
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  @qs_demo
  Scenario: As a member, on the front page I should not see the logout button
    Given I am logged in as user "member+lausanne"
    When I am on "/"
    Then I should not see "qs_auth.logout" link
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  @qs_demo
  Scenario: As the admin, on the front page I should see the logout button
    Given I am logged in as user "admin"
    When I am on "/"
    Then I should see "qs_auth.logout" link with href "/user/logout"
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  @qs_demo
  Scenario: As a member, on my account page I should not see the logout button
    Given I am logged in as user "member+lausanne"
    When I am on "/fr/account/2/dashboard"
    Then I should not see "qs_supervisor.account.dashboard.logout" link
    And I should see "qs_supervisor.account.dashboard.edit" link with href "/fr/account/2/edit"

  @qs_demo
  Scenario: As a member, on a 404 page I should not see the logout button
    Given I am logged in as user "member+lausanne"
    When I am on "/fr/jolie404"
    Then I should not see "qs_auth.logout" link
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  @qs_demo
  Scenario: As a member, on a 403 page I should not see the logout button
    Given I am logged in as user "member+lausanne"
    When I am on "/fr/system/403"
    Then I should not see "qs_auth.logout" link
    And I should see "qs_auth.communities" link with href "/authentication/communities"
