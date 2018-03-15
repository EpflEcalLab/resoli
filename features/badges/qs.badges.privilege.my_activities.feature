Feature: Badges - Privilege - My Activities
  Asserts the listing of My Activities show badges
  according current users highest privilege on this activity.

  @api
  Scenario: Logged as Manager of Lausanne, I should see my 2 badges of privileges. I see my Organizer badge on the Activity N°2 (Atelier Créatif) & my Maintainer badge on the Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "manager+lausanne"
  When I am on "/activities/1/user/5"
  And the response status code should be 200
  Then I should see 2 ".card-list-item" elements
  Then I should see 2 ".card-list-item .flag" elements
  Then I should see 1 "#activity2 .flag.flag-danger.flag-privilege-organizers" elements
  And I should see "qs.activity.user.you_are qs.roles.activity_organizer" in the "#card2" element
  Then I should see 1 "#activity3 .flag.flag-warning.flag-privilege-maintainers" elements
  And I should see "qs.activity.user.you_are qs.roles.activity_maintainer" in the "#card3" element

  @api
  Scenario: Logged as Organizer of Lausanne, I should see my 2 badges of privileges. I see my Organizer badge on the Activity N°5 (Accueil Café) & my Organizer badge on the Activity N°3 (Sorties Théâtre)
  Given I am logged in as user "organizer+lausanne"
  When I am on "/activities/1/user/6"
  Then I should see 2 ".card-list-item" elements
  Then I should see 2 ".card-list-item .flag" elements
  Then I should see 1 "#activity5 .flag.flag-danger.flag-privilege-organizers" elements
  And I should see "qs.activity.user.you_are qs.roles.activity_organizer" in the "#card5" element
  Then I should see 1 "#activity3 .flag.flag-danger.flag-privilege-organizers" elements
  And I should see "qs.activity.user.you_are qs.roles.activity_organizer" in the "#card3" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I should see my badge of privileges. I see my Member badge on the Activity N°2 (Atelier Créatif)
  Given I am logged in as user "member+lausanne+organizer+fribourg"
  When I am on "/activities/1/user/8"
  Then I should see 1 ".card-list-item" elements
  Then I should see 1 ".card-list-item .flag" elements
  Then I should see 1 "#activity2 .flag.flag-info.flag-privilege-members" elements
  And I should see "qs.activity.user.you_are qs.roles.activity_member" in the "#card2" element

  @api
  Scenario: Logged as Member of Fribourg, I see no badges cause I have 0 privilege.
  Given I am logged in as user "member+fribourg"
  When I am on "/activities/2/user/4"
  And the response status code should be 200
  Then I should see 0 ".card-list-item" elements
  Then I should see 0 ".card-list-item .flag" elements

  @api
  Scenario: Logged as Member of Lausanne & Manager of Fribourg, I see no badges cause I have 0 privilege.
  Given I am logged in as user "member+lausanne+manager+fribourg"
  When I am on "/activities/2/user/13"
  And the response status code should be 200
  Then I should see 0 ".card-list-item" elements
  Then I should see 0 ".card-list-item .flag" elements
