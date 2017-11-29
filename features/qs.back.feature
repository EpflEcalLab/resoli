Feature: Back buttons
  In order to make sure back buttons are working
  As an Admin (bypass access)
  I want to make sure the correct links are shown

# Authentication routes
  Scenario: As anonymous, in the front page, I don't see any back button, but I see two shortcuts: register & login pages
    When I am on "/"
    And I should see "qs_auth.login" link with href "/authentication"
    And I should see "qs_auth.register" link with href "/authentication/register"

  Scenario: As logged user, in the front page, I don't see any back button, but I see two shortcuts: logout & communities pages
    Given I am logged in as user "admin"
    When I am on "/"
    And I should see "qs_auth.logout" link with href "/user/logout"
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  Scenario: In the 404 page, I don't see any back button, but I see two shortcuts: register & login pages
    When I am on "/404"
    And the response status code should be 404
    And I should see "qs_auth.login" link with href "/authentication"
    And I should see "qs_auth.register" link with href "/authentication/register"

  Scenario: As logged user, in the 404, I don't see any back button, but I see two shortcuts: logout & communities pages
    Given I am logged in as user "admin"
    When I am on "/"
    And I should see "qs_auth.logout" link with href "/user/logout"
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  Scenario: In the 403 page, I don't see any back button, but I see two shortcuts: register & login pages
    When I am on "/lausanne"
    And the response status code should be 403
    And I should see "qs_auth.login" link with href "/authentication"
    And I should see "qs_auth.register" link with href "/authentication/register"

  Scenario: In the 403 page, I don't see any back button, but I see two shortcuts: register & login pages
    Given I am logged in as user "member+lausanne"
    When I am on "/admin/content"
    And the response status code should be 403
    And I should see "qs_auth.logout" link with href "/user/logout"
    And I should see "qs_auth.communities" link with href "/authentication/communities"

  Scenario: In the login page, I don't see any back button, but I see 2 shortcuts: home page & register page
    When I am on "/authentication"
    And I should see "qs_auth.link.home" link with href "/"
    And I should see "qs_auth.link.register" link with href "/authentication/register"

  Scenario: In the register page, I don't see any back button, but I see 2 shortcuts: home page & login page
    When I am on "/authentication/register"
    And I should see "qs_auth.link.login" link with href "/authentication"
    And I should see "qs_auth.link.home" link with href "/"

  Scenario: In the forget password page, I don't see any back button, but I see 2 shortcuts: login page & home page
    When I am on "/authentication/password"
    And I should see "qs_auth.link.login" link with href "/authentication"
    And I should see "qs_auth.link.home" link with href "/"

  Scenario: In the forget password confirmation page, I don't see any back button, but I see a shortcut for login page
    When I am on "/authentication/password/confirmation"
    And I should see "qs_auth.login" link with href "/authentication"

  @api
  Scenario: In the Communities selection page, I see a back button for home
    Given I am logged in as user "admin"
    When I am on "/authentication/communities"
    And I should see "qs_auth.link.home" link with href "/"

# Welcome page
  @api
  Scenario: Logged a Member of Lausanne, I can't access the account dashboard of another user
    Given I am logged in as user "admin"
    Then I am on "/lausanne/welcome"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs_auth.link.home" link with href "/"

# Activities routes
  @api
  Scenario: In the Activities by dates page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/date"
    Then I should not see a "#block-previousnavigation a" element

  @api
  Scenario: In the Activities by themes page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/theme"
    Then I should not see a "#block-previousnavigation a" element

  @api
  Scenario: In the Activity page, I see a back button for Activities by dates
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/sorties-theatre"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activities_list" link with href "/lausanne/activities/date"

# Communities routes
  @api
  Scenario: In the Community dashboard page, I see a back button for Activities by dates
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/sorties-theatre"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activities_list" link with href "/lausanne/activities/date"

  @api
  Scenario: In the Community dashboard members page, I see a back button for Community dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/dashboard/members"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_community_dashboard" link with href "/lausanne/dashboard"

  @api
  Scenario: In the Community dashboard waiting-approval page, I see a back button for Community dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/dashboard/waiting-approval"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_community_dashboard" link with href "/lausanne/dashboard"

# Calendar routes
  @api
  Scenario: In the Calendar by week, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/lausanne/calendar/weekly"
    Then I should not see a "#block-previousnavigation a" element

  @api
  Scenario: In the Calendar by month, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/lausanne/calendar/monthly"
    Then I should not see a "#block-previousnavigation a" element

## Subscriptions Dashboard
  @api
  Scenario: In the My Subscriptions, I see a back button for Calendar by month
    Given I am logged in as user "admin"
    When I am on "/events/1/user/1"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_calendar" link with href "/lausanne/calendar/weekly"

# Activity Dashboard
  @api
  Scenario: In the Activity Add Form, I see a back button for My Activities
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/add"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_my_activities" link with href "/activities/1/user/1"

  @api
  Scenario: In the My Activities, I see a back button for Activities by dates
    Given I am logged in as user "admin"
    When I am on "/activities/1/user/1"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activities_list" link with href "/lausanne/activities/date"

  @api
  Scenario: In the Activity Dashboard, I see a back button for Activity
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/dashboard"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity" link with href "/lausanne/activities/atelier-creatif"

  @api
  Scenario: In the Activity Info Edit Form, I see a back button for Activity Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/edit/info"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity_dashboard" link with href "/lausanne/activities/atelier-creatif/dashboard"

  @api
  Scenario: In the Activity Visibility Edit Form, I see a back button for Activity Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/edit/info"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity_dashboard" link with href "/lausanne/activities/atelier-creatif/dashboard"

  @api
  Scenario: In the Activity Default Values Edit Form, I see a back button for Activity Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/edit/defaults"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity_dashboard" link with href "/lausanne/activities/atelier-creatif/dashboard"

  @api
  Scenario: In the Activity Add Event Form, I see a back button for Activity Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/add"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity_dashboard" link with href "/lausanne/activities/atelier-creatif/dashboard"

  @api
  Scenario: In the Activity Dashboard Members, I see a back button for Activity Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/dashboard/members"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity_dashboard" link with href "/lausanne/activities/atelier-creatif/dashboard"

  @api
  Scenario: In the Activity Delete Form, I don't see a back button, but I see a Cancel action
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/delete"
    Then I should not see a "#block-previousnavigation a" element
    And I should see "qs.form.cancel" link with href "/lausanne/activities/atelier-creatif/dashboard"

# Event Dashboard
  @api
  Scenario: In the Event Add Form, I see a back button for Activity
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/add"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activity" link with href "/lausanne/activities/atelier-creatif"

  @api
  Scenario: In the My Activities, I see a back button for Activities by dates
    Given I am logged in as user "admin"
    When I am on "/activities/1/user/1"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_activities_list" link with href "/lausanne/activities/date"

  @api
  Scenario: In the Event Dashboard, I see a back button for Activity
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_event" link with href "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo#card17"

  @api
  Scenario: In the Event Edit Form, I see a back button for Event Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/edit"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_event_dashboard" link with href "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"

  @api
  Scenario: In the Event Subscribers Dashboard, I see a back button for Event Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/subscribers"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_event_dashboard" link with href "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"

  @api
  Scenario: In the Event Waiting Approval Dashboard, I see a back button for Event Dashboard
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard/waiting-approval"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_event_dashboard" link with href "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"

  @api
  Scenario: In the Event Delete Form, I don't see a back button, but I see a Cancel action
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/delete"
    Then I should not see a "#block-previousnavigation a" element
    And I should see "qs.form.cancel" link with href "/lausanne/activities/atelier-creatif/events/atelier-scooby-doo/dashboard"

# Supervisor account
  @api
  Scenario: Logged a Member of Lausanne, I can't access the account dashboard of another user
    Given I am logged in as user "admin"
    Then I am on "/account/1/dashboard"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs_auth.link.home" link with href "/"

# Photos by Activity
  Scenario: In the Activity's Photos, I see a back button for Photos by Date
    Given I am logged in as user "admin"
    When I am on "/lausanne/activities/atelier-creatif/photos"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_photos_list" link with href "/lausanne/photos/theme"

# My Photos
  Scenario: In the My Activities, I see a back button for Photos by dates
    Given I am logged in as user "admin"
    When I am on "/photos/1/user/1"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_photos_month" link with href "/lausanne/photos/month"

# Form add Photo
  Scenario: In the Form add Photo, I see a back button for Photos by dates
    Given I am logged in as user "admin"
    When I am on "/photos/1/user/1"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_photos_month" link with href "/lausanne/photos/month"

# Form manage Photos
  Scenario: In the Form add Photo, I see a back button for My Photos
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/user/1"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_my_photos" link with href "/photos/1/user/1"

# Form delete Photos
  Scenario: In the Form add Photo, I see a back button for My Photos
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/delete?photos[42]=42&photos[45]=45"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_manage_photos" link with href "/photos/activity/2/user/1"

# Form comments Photos
  Scenario: In the Form add Photo, I see a back button for My Photos
    Given I am logged in as user "admin"
    When I am on "/photos/activity/2/comment?photos[42]=42&photos[45]=45"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_manage_photos" link with href "/photos/activity/2/user/1"
