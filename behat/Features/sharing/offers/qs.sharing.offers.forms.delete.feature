Feature: Offer Delete Form

## Access
  @api
  Scenario Outline: Logged-in, I can access my own offers delete forms. Accessing other people offers delete form page should be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "/sharing/<offer-id>/delete"
    Then the response status code should be <code>

    Examples:
      | user | offer-id | code |
      | organizer+lausanne | 76 | 200 |
      | organizer+lausanne | 74 | 200 |
      | member+lausanne | 69  | 200 |
      | member+lausanne+organizer+fribourg | 71  | 200 |
      | member+lausanne+organizer+fribourg | 72  | 200 |
      | member+lausanne+organizer+fribourg | 73  | 200 |
      | manager+lausanne | 70  | 200 |
      | organizer+lausanne | 71 | 403 |
      | organizer+lausanne | 72 | 403 |
      | organizer+lausanne | 73 | 403 |
      | member+lausanne | 76  | 403 |
      | member+lausanne | 74  | 403 |
      | member+lausanne | 73  | 403 |
      | member+lausanne+organizer+fribourg | 69  | 403 |
      | member+lausanne+organizer+fribourg | 74  | 403 |
      | member+lausanne+organizer+fribourg | 76  | 403 |
      | manager+lausanne | 71  | 403 |
      | manager+lausanne | 72  | 403 |
      | manager+lausanne | 73  | 403 |

## Form visibility.
  @api
  Scenario: When reaching the offer dashboard, I should see the delete form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 1 "form.delete" element

## Form submits.
  @api @preserveDatabase
  Scenario: When deleting an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 1 "form.offer75.delete" element
    Then I follow the link ".offer75.delete button[type='submit']" element
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.delete.success Discussion à l'achat d'un nouvelle ordinateur" in the ".alert" element
    And I should see 0 "form.offer75.delete" element
