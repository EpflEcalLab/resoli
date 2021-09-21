Feature: Collection of Requests
  Asserts the listing of Requests of one community display the correct number of items.

## Redirect
  Scenario: Accessing to a request canonical view should redirect to the requests collection page.
    Given I am logged in as user "member+lausanne"
    When I am on "/node/77"
    And the url should match "/sharing/1/requests"
    And the response status code should be 200

## Access
  Scenario Outline: As anonymous I should not be able to access any community offers collection.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /sharing/1/requests |
      | /sharing/2/requests |
      | /sharing/3/requests |

  Scenario Outline: Logged-in, I can access my own community(ies) request collection if I'm also Volunteer on that community. Accessing community in which I don't belongs should not be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
      | user | url | code |
      | admin | /sharing/1/requests | 200 |
      | admin | /sharing/2/requests | 200 |
      | member+lausanne | /sharing/1/requests | 200 |
      | member+lausanne | /sharing/2/requests | 403 |
      | approval+lausanne | /sharing/1/requests | 403 |
      | approval+lausanne | /sharing/2/requests | 403 |
      | manager+lausanne | /sharing/1/requests | 403 |
      | manager+lausanne | /sharing/2/requests | 403 |
      | member+lausanne+organizer+fribourg | /sharing/1/requests | 200 |
      | member+lausanne+organizer+fribourg | /sharing/2/requests | 200 |
      | member+fribourg+approval+organizer+fribourg | /sharing/1/requests | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/2/requests | 403 |
      | declined+organizer+lausanne | /sharing/1/requests | 403 |
      | declined+organizer+lausanne | /sharing/2/requests | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/1/requests | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/2/requests | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/1/requests | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/2/requests | 403 |

## Element listed
  Scenario: On the "Lausanne" listing of requests, as a regular volunteer, I should see 3 requests.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/requests"
    And I should see "qs_sharing.collection.request.title Lausanne"
    And I should not see "qs_sharing.collection.request.empty"
    Then I should see 3 "#requests-accordion .card-list-item" elements
    Then I should see 1 "#requests-accordion .card-list-item#card79.solved" element

  @api @preserveDatabase
  Scenario: On the "Genève" listing of requests, it should display no requests.
    Given I am volunteer on community 3 for theme 22 as user 1
    And I am logged in as user "admin"
    When I am on "/sharing/3/requests"
    And I should see "qs_sharing.collection.request.title Genève"
    And I should see "qs_sharing.collection.request.empty"
    Then I should see 0 "#requests-accordion .card-list-item" element

## Visible actions per element listed
  Scenario: On the "Lausanne" listing of requests, as a regular volunteer, I should see specific actions to my account.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see 1 "#requests-accordion .card-list-item#card80 .card-actions .btn" element
    Then I should see 1 "#requests-accordion .card-list-item#card80 .card-actions form#qs-sharing-request-solve-form" element
    Then I should see 4 "#requests-accordion .card-list-item#card77 .card-actions .btn" elements
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions a[href='tel:+41 021 987 47 22']" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions a[href='mailto:sara.courci@example.org']" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions form#qs-sharing-request-solve-form--2" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions form#qs-sharing-request-archive-form" element
    Then I should see 1 "#requests-accordion .card-list-item#card79 .card-actions .btn" element
    Then I should see 1 "#requests-accordion .card-list-item#card79 .card-actions a[href='mailto:manager+lausanne@antistatique.net']" element

  @api @preserveDatabase
  Scenario: On the "Lausanne" listing of requests, as a regular volunteer and author of some requests, I should see specific actions to my account.
    Given I am volunteer on community 1 for theme 22 as user 5
    Given I am logged in as user "manager+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see 2 "#requests-accordion .card-list-item#card80 .card-actions .btn" element
    Then I should see 1 "#requests-accordion .card-list-item#card80 .card-actions form#qs-sharing-request-solve-form" element
    Then I should see 1 "#requests-accordion .card-list-item#card80 .card-actions form#qs-sharing-request-archive-form" element
    Then I should see 4 "#requests-accordion .card-list-item#card77 .card-actions .btn" elements
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions a[href='tel:+41 021 987 47 22']" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions a[href='mailto:sara.courci@example.org']" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions form#qs-sharing-request-solve-form--2" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions form#qs-sharing-request-archive-form--2" element
    Then I should see 2 "#requests-accordion .card-list-item#card79 .card-actions .btn" element
    Then I should see 1 "#requests-accordion .card-list-item#card79 .card-actions a[href='mailto:manager+lausanne@antistatique.net']" element
    Then I should see 1 "#requests-accordion .card-list-item#card79 .card-actions form#qs-sharing-request-archive-form--3" element

  Scenario: On the "Lausanne" listing of requests, as Admin, I should see specific actions to my account.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/requests"
    Then I should see 2 "#requests-accordion .card-list-item#card80 .card-actions .btn" element
    Then I should see 1 "#requests-accordion .card-list-item#card80 .card-actions form#qs-sharing-request-solve-form" element
    Then I should see 1 "#requests-accordion .card-list-item#card80 .card-actions form#qs-sharing-request-archive-form" element
    Then I should see 4 "#requests-accordion .card-list-item#card77 .card-actions .btn" elements
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions a[href='tel:+41 021 987 47 22']" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions a[href='mailto:sara.courci@example.org']" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions form#qs-sharing-request-solve-form--2" element
    Then I should see 1 "#requests-accordion .card-list-item#card77 .card-actions form#qs-sharing-request-archive-form--2" element
    Then I should see 2 "#requests-accordion .card-list-item#card79 .card-actions .btn" element
    Then I should see 1 "#requests-accordion .card-list-item#card79 .card-actions a[href='mailto:manager+lausanne@antistatique.net']" element
    Then I should see 1 "#requests-accordion .card-list-item#card79 .card-actions form#qs-sharing-request-archive-form--3" element

## Floating Button
  Scenario: In the Sharing requests collection page, I should see the floating button point to this page.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see 1 ".floating a" elements
    And I should see "qs_sharing.floating.requests" link with href "/sharing/1/requests"

## Back button.
  Scenario: In the Sharing requests collection page, I should see a back button pointing to my sharing dashboard.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/requests"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_sharing_dashboard" link with href "/sharing/1/user/2/dashboard"
