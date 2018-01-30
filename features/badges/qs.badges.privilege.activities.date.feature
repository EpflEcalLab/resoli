Feature: Badges - Privilege - Activities by Date
  Asserts the listing of Activities by Date show badges
  according current loggedin users highest privilege on this activity.

  @api
  Scenario: Logged as Manager of Lausanne, I can see my 2 badges of privileges. I see my Organizer badge on the  Activity N°2 (Atelier Créatif) & my Maintainer badge on the  Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/lausanne/activities/date"
  And the response status code should be 200

  @api
  Scenario: Logged as Organizer of Lausanne, I can see my 2 badges of privileges. I see my Organizer badge on the Activity N°5 (Accueil Café) & my Organizer badge on the Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/lausanne/activities/date"
  And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can see my badge of privileges. I see my Member badge on the Activity N°2 (Atelier Créatif)
  Given I am logged in as user "member+lausanne+organizer+fribourg"
  When I am on "/lausanne/activities/date"
  And the response status code should be 200

  @api
  Scenario: Logged as Member of Fribourg, I see no badges cause I have 0 privilege.
  Given I am logged in as user "member+fribourg"
  When I am on "/fribourg/activities/date"
