Feature: Community dashboard

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg I can't access to Fribourg dashboard
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/fribourg/dashboard"
    And the response status code should be 200
    Then I should see 3 ".modal-content a" elements
    And I should see "qs_community.dashboard.members" link with href "fr/fribourg/dashboard/members"
    And I should see "qs_community.dashboard.waiting_approval" link with href "fr/fribourg/dashboard/waiting-approval"
    And I should see "qs_community.events.export.download" link with href "fr/communities/2/dashboard/events/export"
