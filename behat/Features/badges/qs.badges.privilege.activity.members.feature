Feature: Badges - Privilege - Activity Members
  Asserts the listing of members in Activity show privilege's badges
  according member highest privilege on this activity.

  @api
  Scenario: Logged as Manager of Lausanne, when I access the Dashboard Members of Activity N°2 (Atelier Créatif), I should see 1 badge to the only member - excepted me.
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/atelier-creatif/dashboard/members"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see 1 ".card-list-item .flag" elements
    Then I should see 1 "#card8 .flag.flag-info.flag-privilege-members.flag-default" elements

  @api
  Scenario: Logged as Declined Organizer of Lausanne but still a Member of Lausanne, when I access the Dashboard Members of Activity N°8 (Causeries & Conférences), I should see 0 badge because I'm the only member of this activity & I'm exclude from list.
    Given I am logged in as user "member+lausanne+declined+organizer+lausanne"
    When I am on "/lausanne/activities/causeries-conferences/dashboard/members"
    And the response status code should be 200
    Then I should see 0 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when I access the Dashboard Members of Activity N°3 (Sorties Théâtre), I should see 2 badges - excepted me.
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/sorties-theatre/dashboard/members"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see 2 ".card-list-item .flag" elements
    Then I should see 1 "#card5 .flag.flag-warning.flag-privilege-maintainers.flag-shield" elements
    Then I should see 1 "#card9 .flag.flag-info.flag-privilege-members.flag-default" elements

  @api
  Scenario: Logged as Organizer of Lausanne, when I access the Dashboard Members of Activity N°5 (Accueil Café), I should see 0 badge because I'm the only member of this activity & I'm exclude from list.
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/dashboard/members"
    And the response status code should be 200
    Then I should see 0 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Member & Organizer of Fribourg, when I access the Dashboard Members of Activity N°56 (Escalade), I should see 1 badge to the only member - excepted me.
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/escalade/dashboard/members"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see 1 ".card-list-item .flag" elements
    Then I should see 1 "#card23 .flag.flag-info.flag-privilege-members.flag-default" elements

  @api
  Scenario: Logged as Member & Organizer of Fribourg, when I access the Dashboard Members of Activity N°62 (Ginguettes), I should see 0 badge because I'm the only member of this activity & I'm exclude from list.
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/ginguettes/dashboard/members"
    And the response status code should be 200
    Then I should see 0 ".card-list-item" elements
    Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Member & Organizer of Fribourg, when I access the Dashboard Members of Activity N°57 (Monopoly), I should see 1 badge to the only member - excepted me.
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "/fribourg/activities/monopoly/dashboard/members"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see 1 ".card-list-item .flag" elements
    Then I should see 1 "#card23 .flag.flag-warning.flag-privilege-maintainers.flag-shield" elements
