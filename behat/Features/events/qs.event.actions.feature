Feature: Event Actions Buttons
  In order to make sure ACL is working for event pills
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

# Subscribe button - on detail page of activity
  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°2 (Atelier Scooby-Doo), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element
    Then I should see 4 "#collapse-17 .card-actions .col-sm-6" elements
    And I should see "qs.event.register" in the "#collapse-17 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-17 .card-actions" element
    And I should see "qs.event.dashboard" in the "#collapse-17 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-17 .card-actions" element
    And I should see 0 "#collapse-17 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-17 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 4 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see 7 "#collapse-37 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.register.pending" in the "#collapse-37 .card-actions" element
    And the "#collapse-37 .card-actions .btn.btn-outline-info.btn-white[data-status-show='pending']" element should contain "qs.event.register.pending"
    And I should see "qs.event.dashboard" in the "#collapse-37 .card-actions" element
    And I should see 1 "#collapse-37 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-37 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='pendings_guests']" elements
    And the "#collapse-37 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" element should contain "qs.event.dashboard.shortcut.confirmed 1"
    Then I should see a "#collapse-35" element
    Then I should see 4 "#collapse-35 .card-actions .col-sm-6" elements
    And I should not see "qs.event.contact" in the "#collapse-35 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-35 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-35 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-35 .card-actions" element
    And the "#collapse-35 .card-actions .btn.btn-outline-secondary.btn-white[data-status-show='default']" element should contain "qs.event.register"
    And I should see "qs.event.dashboard" in the "#collapse-35 .card-actions" element
    And I should see 0 "#collapse-35 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-35 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-36" element
    Then I should see 5 "#collapse-36 .card-actions .col-sm-6" elements
    And I should see "qs.event.register.confirmed" in the "#collapse-36 .card-actions" element
    And the "#collapse-36 .card-actions .btn.btn-info[data-status-show='confirmed']" element should contain "qs.event.register.confirmed"
    And I should see "qs.event.dashboard" in the "#collapse-36 .card-actions" element
    And I should see 0 "#collapse-36 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-36 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-54" element
    Then I should see 6 "#collapse-54 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.register.confirmed" in the "#collapse-54 .card-actions" element
    And the "#collapse-54 .card-actions .btn.btn-info[data-status-show='confirmed']" element should contain "qs.event.register.confirmed"
    And I should see "qs.event.dashboard" in the "#collapse-54 .card-actions" element
    And the "#collapse-54 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='pendings_guests']" element should contain "qs.event.dashboard.shortcut.waiting_approval 1"
    And I should see 0 "#collapse-54 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a not member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-creatif"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element
    Then I should see 1 "#collapse-17 .card-actions .col-sm-6" elements
    And I should see "qs.event.calendar" in the "#collapse-17 .card-actions" element
    And I should not see "qs.event.contact" in the "#collapse-17 .card-actions" element
    And I should not see "qs.event.location" in the "#collapse-17 .card-actions" element
    And I should not see "qs.event.register.confirmed" in the "#collapse-17 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-17 .card-actions" element
    And I should not see "qs.event.register.pending" in the "#collapse-17 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-17 .card-actions" element
    And I should see 0 "#collapse-17 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-17 .card-actions .btn.btn-outline-warning.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 4 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see 7 "#collapse-37 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.register.confirmed" in the "#collapse-37 .card-actions" element
    And the "#collapse-37 .card-actions .btn.btn-info[data-status-show='confirmed']" element should contain "qs.event.register.confirmed"
    And I should see "qs.event.dashboard" in the "#collapse-37 .card-actions" element
    And the "#collapse-37 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" element should contain "qs.event.dashboard.shortcut.waiting_approval 1"
    And I should see 0 "#collapse-37 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    Then I should see a "#collapse-35" element
    Then I should see 4 "#collapse-35 .card-actions .col-sm-6" elements
    And I should see "qs.event.register" in the "#collapse-35 .card-actions" element
    And the "#collapse-35 .card-actions .btn.btn-outline-secondary.btn-white[data-status-show='default']" element should contain "qs.event.register"
    And I should see "qs.event.dashboard" in the "#collapse-35 .card-actions" element
    And I should see 0 "#collapse-35 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-35 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-36" element
    And I should see "qs.event.register" in the "#collapse-36 .card-actions" element
    Then I should see 6 "#collapse-36 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.dashboard" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.dashboard.shortcut.confirmed 1" in the "#collapse-36 .card-actions" element
    And the "#collapse-36 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And the "#collapse-36 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" element should contain "qs.event.dashboard.shortcut.confirmed 1"
    And I should see 0 "#collapse-36 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-54" element
    Then I should see 6 "#collapse-54 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.dashboard" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.dashboard.shortcut.waiting_approval 1" in the "#collapse-54 .card-actions" element
    And the "#collapse-54 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And the "#collapse-54 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" element should contain "qs.event.dashboard.shortcut.waiting_approval 1"
    And I should see 0 "#collapse-54 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should see 4 "#collapse-22 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-22 .card-actions" element
    And I should not see "qs.event.view.photos" in the "#collapse-22 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-22 .card-actions" element
    And the "#collapse-22 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see 0 "#collapse-22 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-22 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-18" element
    Then I should see 3 "#collapse-18 .card-actions .col-sm-6" elements
    And I should not see "qs.event.contact" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-18 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-18 .card-actions" element
    And the "#collapse-18 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see 0 "#collapse-18 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-18 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Member of Lausanne, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should see 5 "#collapse-22 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-22 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-22 .card-actions" element
    And the "#collapse-22 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see 0 "#collapse-22 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-22 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-18" element
    Then I should see 3 "#collapse-18 .card-actions .col-sm-6" elements
    And I should not see "qs.event.contact" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-18 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-18 .card-actions" element
    And the "#collapse-18 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see 0 "#collapse-18 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-18 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/atelier-bougies"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should see 4 "#collapse-22 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-22 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-22 .card-actions" element
    And I should not see "qs.event.view.photos" in the "#collapse-22 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-22 .card-actions" element
    And the "#collapse-22 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see 0 "#collapse-22 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-22 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-18" element
    Then I should see 3 "#collapse-18 .card-actions .col-sm-6" elements
    And I should not see "qs.event.contact" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-18 .card-actions" element
    And I should see "qs.event.register" in the "#collapse-18 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-18 .card-actions" element
    And the "#collapse-18 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see 0 "#collapse-18 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-18 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because this is not a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/sorties-theatre"
    And the response status code should be 200
    Then I should see 4 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see 4 "#collapse-37 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-37 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-37 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-37 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-37 .card-actions" element
    And I should see 0 "#collapse-37 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-37 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-35" element
    Then I should see 2 "#collapse-35 .card-actions .col-sm-6" elements
    And I should see "qs.event.calendar" in the "#collapse-35 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-35 .card-actions" element
    And I should not see "qs.event.contact" in the "#collapse-35 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-35 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-35 .card-actions" element
    And I should see 0 "#collapse-35 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-35 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    And I should not see "qs.event.register" in the "#collapse-35 .card-actions" element
    Then I should see a "#collapse-36" element
    Then I should see 3 "#collapse-36 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-36 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-36 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-36 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-36 .card-actions" element
    And I should see 0 "#collapse-36 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-36 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
    Then I should see a "#collapse-54" element
    Then I should see 3 "#collapse-54 .card-actions .col-sm-6" elements
    And I should see "qs.event.contact" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-54 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-54 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-54 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-54 .card-actions" element
    And I should see 0 "#collapse-54 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-54 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Member & Organizer of Fribourg, I should see all the CTA buttons in the Events of the Activity N°56 (Escalade), because I'm a Organizer of this activity.
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/escalade"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-59" element
    Then I should see 7 "#collapse-59 .card-actions .col-sm-6" elements
    And the "#collapse-59 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see "qs.event.location" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.dashboard" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.contact" in the "#collapse-59 .card-actions" element
    And the "#collapse-59 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" element should contain "qs.event.dashboard.shortcut.confirmed 1"

  @api
  Scenario: Logged as Member N°2 of Fribourg, I should see all some CTA buttons in the Events of the Activity N°56 (Escalade), because I'm a the Member of this activity.
    Given I am logged in as user "member2+fribourg"
    When I am on "/fribourg/activities/escalade"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-59" element
    Then I should see 5 "#collapse-59 .card-actions .col-sm-6" elements
    And the "#collapse-59 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see "qs.event.contact" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-59 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-59 .card-actions" element
    And I should see 0 "#collapse-59 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-59 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Member of Fribourg, I should see only 2 CTA buttons in the Events of the Activity N°56 (Escalade), because I'm not a the Member of this activity.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/escalade"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-59" element
    Then I should see 3 "#collapse-59 .card-actions .col-sm-6" elements
    And I should see "qs.event.calendar" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-59 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-59 .card-actions" element
    And I should not see "qs.event.contact" in the "#collapse-59 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-59 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-59 .card-actions" element
    And I should see 0 "#collapse-59 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-59 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  # @todo: Fix according - #517
  @api
  Scenario: Logged as Member & Organizer of Fribourg, I should see all the CTA buttons in the Events of the Activity N°57 (Monopoly), because I'm a Organizer of this activity.
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/monopoly"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-61" element
    Then I should see 7 "#collapse-61 .card-actions .col-sm-6" elements
    And the "#collapse-61 .card-actions .btn.btn-outline-secondary[data-status-show='default']" element should contain "qs.event.register"
    And I should see "qs.event.location" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.dashboard" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.contact" in the "#collapse-61 .card-actions" element
    And the "#collapse-61 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" element should contain "qs.event.dashboard.shortcut.confirmed 1"

  @api
  Scenario: Logged as Member N°2 of Fribourg, I should see all some CTA buttons in the Events of the Activity N°57 (Monopoly), because I'm a the Member of this activity.
    Given I am logged in as user "member2+fribourg"
    When I am on "/fribourg/activities/monopoly"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-61" element
    Then I should see 6 "#collapse-61 .card-actions .col-sm-6" elements
    And the "#collapse-61 .card-actions .btn.btn-info[data-status-show='confirmed']" element should contain "qs.event.register.confirmed"
    And I should see "qs.event.dashboard" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.calendar" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.contact" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.location" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-61 .card-actions" element
    And I should see 0 "#collapse-61 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-61 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements

  @api
  Scenario: Logged as Member of Fribourg, I should see only 2 CTA buttons in the Events of the Activity N°57 (Monopoly), because I'm not a the Member of this activity.
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/monopoly"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-61" element
    Then I should see 2 "#collapse-61 .card-actions .col-sm-6" elements
    And I should see "qs.event.calendar" in the "#collapse-61 .card-actions" element
    And I should see "qs.event.view.photos" in the "#collapse-61 .card-actions" element
    And I should not see "qs.event.location" in the "#collapse-61 .card-actions" element
    And I should not see "qs.event.contact" in the "#collapse-61 .card-actions" element
    And I should not see "qs.event.register" in the "#collapse-61 .card-actions" element
    And I should not see "qs.event.dashboard" in the "#collapse-61 .card-actions" element
    And I should see 0 "#collapse-61 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='confirmed_guests']" elements
    And I should see 0 "#collapse-61 .card-actions .btn.btn-outline-danger.btn-white[data-status-guest-show='pendings_guests']" elements
