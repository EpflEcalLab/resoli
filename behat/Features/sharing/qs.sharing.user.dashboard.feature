Feature: Dashboard sharing
  Asserts the sharing dashboard display the right actions.

## Access
  @api
  Scenario Outline: As anonymous I should not be able to access any sharing dashboard.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /sharing/1/user/1/dashboard |
      | /sharing/2/user/1/dashboard |
      | /sharing/1/user/2/dashboard |
      | /sharing/1/user/3/dashboard |
      | /sharing/2/user/3/dashboard |
      | /sharing/1/user/5/dashboard |

  @api
  Scenario Outline: Logged-in, I can access my own community(ies) sharing dashboard. Accessing community in which I don't belongs should be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
      | user | url | code |
      | admin | /sharing/1/user/1/dashboard | 200 |
      | admin | /sharing/2/user/1/dashboard | 200 |
      | admin | /sharing/1/user/2/dashboard | 200 |
      | admin | /sharing/1/user/3/dashboard | 200 |
      | admin | /sharing/2/user/3/dashboard | 200 |
      | admin | /sharing/1/user/5/dashboard | 200 |
      | member+lausanne | /sharing/1/user/2/dashboard | 200 |
      | member+lausanne | /sharing/1/user/3/dashboard | 403 |
      | approval+lausanne | /sharing/1/user/2/dashboard | 403 |
      | approval+lausanne | /sharing/1/user/3/dashboard | 403 |
      | manager+lausanne | /sharing/1/user/2/dashboard | 403 |
      | manager+lausanne | /sharing/1/user/5/dashboard | 200 |
      | member+lausanne+organizer+fribourg | /sharing/1/user/8/dashboard | 200 |
      | member+lausanne+organizer+fribourg | /sharing/2/user/8/dashboard | 200 |
      | member+lausanne+organizer+fribourg | /sharing/1/user/5/dashboard | 403 |
      | member+lausanne+organizer+fribourg | /sharing/2/user/13/dashboard | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/1/user/14/dashboard | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/2/user/14/dashboard | 200 |
      | member+fribourg+approval+organizer+fribourg | /sharing/1/user/5/dashboard | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/2/user/13/dashboard | 403 |
      | declined+organizer+lausanne | /sharing/1/user/17/dashboard | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/1/user/18/dashboard | 200 |
      | member+lausanne+declined+organizer+lausanne | /sharing/2/user/18/dashboard | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/1/user/17/dashboard | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/2/user/19/dashboard | 200 |
      | member+fribourg+declined+member+lausanne | /sharing/1/user/19/dashboard | 403 |

## Element listed
  @api
  Scenario: On any sharing dashboard page, it should have a "Share request" button.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/user/1/dashboard"
    And I should see "qs_sharing.share_request" link with href "/sharing/1/request/add"

  @api
  Scenario: On the sharing dashboard page, as a member with no volunteerism, it should have a "Become volunteer" button and no "Add offer"", "Manage volunteerism" and "See requests" buttons.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/user/2/dashboard"
    ## Update test with correct url once created
    And I should see "qs_sharing.become_volunteer" link with href "/sharing/1/offers/add?user=2"
    And I should not see "qs_sharing.add_offer"
    And I should not see "qs_sharing.see_requests"
    And I should not see "qs_sharing.manager_volunteerism"

  @api
  Scenario: On the sharing dashboard page, as a member with volunteerism, it should have an "Add Offer", an "Manage volunteerism" and a "See requests" button and no "Become volunteer" button.
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/dashboard"
    ## Update test with correct url once created
    And I should see "qs_sharing.add_offer" link with href "/sharing/1/offers/add?user=8"
    And I should see "qs_sharing.see_requests" link with href "/sharing/1/user/8/offers"
    And I should see "qs_sharing.manager_volunteerism" link with href "/sharing/1/user/8/offers"
    And I should not see "qs_sharing.become_volunteer"

  @api
  Scenario: On the sharing dashboard page, as a member with no offers, it should not have a "Manager Offer" button.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/user/1/dashboard"
    And I should not see "qs_sharing.manage_offers"

  @api
  Scenario: On the sharing dashboard page, as a member with at least one offer, it should have a "Manager Offer" button.
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/dashboard"
    And I should see "qs_sharing.manage_offers" link with href "/sharing/1/user/8/offers"


## Floating Button
  @api
  Scenario Outline: Logged-in on the sharing dashboard, I should see the floating button point to my dashboard
    Given I am logged in as user "<user>"
    When I am on "/sharing/1/user/<user-id>/dashboard"
    Then I should see 1 ".floating a" elements
    And I should see "qs_sharing.floating.dashboard" link with href "/sharing/1/user/<user-id>/dashboard"
    Examples:
      | user | user-id |
      | member+lausanne | 2 |
      | manager+lausanne | 5 |
      | organizer+lausanne | 6 |
      | member+lausanne+manager+fribourg | 13 |

# Back button.
  @api
  Scenario: In the Sharing dashboard page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/user/1/dashboard"
    Then I should not see a "#block-previousnavigation a" element
