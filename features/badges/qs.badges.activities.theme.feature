Feature: Badges - Privilege - Activities by Theme
  Asserts the listing of Activities by Theme show privilege's badges or guest's badges
  according current users highest privilege on this activity.

  @api
  Scenario: Logged as Manager of Lausanne, I should see my 2 badges of privileges. I see my Organizer badge on the Activity N°2 (Atelier Créatif) & my Maintainer badge on the Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/activities/theme"
  And the response status code should be 200
  Then I should see 10 ".card" elements
  Then I should see 3 ".card .flag" elements
  Then I should see 1 "#card-activity2 .flag.flag-danger.flag-privilege-organizers" elements
  Then I should see 1 "#card-activity3 .flag.flag-outline-warning.flag-subscription-guests-wait" elements
  Then I should see 1 "#card-activity13 .flag.flag-warning.flag-privilege-maintainers" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I should see my 2 badges of privileges. I see my Organizer badge on the Activity N°5 (Accueil Café) & my Organizer badge on the Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/activities/theme"
  And the response status code should be 200
  Then I should see 10 ".card" elements
  Then I should see 2 ".card .flag" elements
  Then I should see 1 "#card-activity5 .flag.flag-danger.flag-privilege-organizers" elements
  Then I should see 1 "#card-activity3 .flag.flag-outline-danger.flag-subscription-guests-wait" elements

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I should see my badge of privileges. I see my Member badge on the Activity N°2 (Atelier Créatif)
  Given I am logged in as user "member+lausanne+organizer+fribourg"
  When I am on "/lausanne/activities/theme"
  And the response status code should be 200
  Then I should see 10 ".card" elements
  Then I should see 1 ".card .flag" elements
  Then I should see 1 "#card-activity2 .flag.flag-info.flag-privilege-members" elements

  @api
  Scenario: Logged as Member of Fribourg, I see no badges cause I have 0 privilege.
  Given I am logged in as user "member+fribourg"
  When I am on "/fribourg/activities/theme"
  And the response status code should be 200
  Then I should see 1 ".card" elements
  Then I should see 0 ".card .flag" elements
