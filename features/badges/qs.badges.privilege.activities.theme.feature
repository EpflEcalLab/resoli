Feature: Badges - Privilege - Activities by Theme
  Asserts the listing of Activities by Theme show badges
  according current loggedin users highest privilege on this activity.

  @api
  Scenario: Logged as Manager of Lausanne, I can see my 2 badges of privileges. I see my Organizer badge on the  Activity N°2 (Atelier Créatif) & my Maintainer badge on the  Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/activities/theme"
  And the response status code should be 200
  Then I should see 10 ".card" elements
  Then I should see 2 ".card .flag" elements
  Then I should see 1 "#card-activity2 .flag.flag-privilege-organizers" elements
  Then I should see 1 "#card-activity3 .flag.flag-privilege-maintainers" elements

  @api
  Scenario: Logged as Organizer of Lausanne, I can see my 2 badges of privileges. I see my Organizer badge on the Activity N°5 (Accueil Café) & my Organizer badge on the Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/activities/theme"
  And the response status code should be 200
  Then I should see 10 ".card" elements
  Then I should see 2 ".card .flag" elements
  Then I should see 1 "#card-activity5 .flag.flag-privilege-organizers" elements
  Then I should see 1 "#card-activity3 .flag.flag-privilege-organizers" elements

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can see my badge of privileges. I see my Member badge on the Activity N°2 (Atelier Créatif)
  Given I am logged in as user "member+lausanne+organizer+fribourg"
  When I am on "/lausanne/activities/theme"
  And the response status code should be 200
  Then I should see 10 ".card" elements
  Then I should see 1 ".card .flag" elements
  Then I should see 1 "#card-activity2 .flag.flag-privilege-members" elements

  @api
  Scenario: Logged as Member of Fribourg, I see no badges cause I have 0 privilege.
  Given I am logged in as user "member+fribourg"
  When I am on "/fribourg/activities/theme"
  And the response status code should be 200
  Then I should see 1 ".card" elements
  Then I should see 0 ".card .flag" elements
