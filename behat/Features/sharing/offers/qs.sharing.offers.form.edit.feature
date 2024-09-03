Feature: Sharing Offers edit Form

## Access
  Scenario Outline: The edit Offer form is only available to author of the offer.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
    | user | url | code |
    | admin | /sharing/offers/69/edit | 200 |
    | admin | /sharing/offers/70/edit | 200 |
    | admin | /sharing/offers/71/edit | 200 |
    | member+lausanne | /sharing/offers/69/edit | 200 |
    | member+lausanne | /sharing/offers/70/edit | 403 |
    | member+lausanne | /sharing/offers/71/edit | 403 |
    | manager+lausanne | /sharing/offers/69/edit | 403 |
    | organizer+lausanne | /sharing/offers/69/edit | 403 |
    | organizer+lausanne | /sharing/offers/70/edit | 403 |
    | organizer+lausanne | /sharing/offers/71/edit | 403 |
    | member+lausanne+organizer+fribourg | /sharing/offers/69/edit | 403 |
    | member+lausanne+organizer+fribourg | /sharing/offers/70/edit | 403 |
    | member+lausanne+organizer+fribourg | /sharing/offers/71/edit | 200 |

## Floating Button
  Scenario: In the edit Offer form, I should see the floating button pointing to the current form.
    Given I am logged in as user "admin"
    When I am on "/sharing/offers/69/edit"
    Then I should see 1 ".floating a" element
    And I should see "qs_sharing.edit_offer" link with href "/sharing/offers/69/edit"

## Back button.
  Scenario: In the edit Offer form, I should see the back button pointing to my offers.
    Given I am logged in as user "admin"
    When I am on "/sharing/offers/69/edit"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_sharing_dashboard" link with href "/sharing/1/user/1/dashboard"

## Form pre-filled values.
  Scenario: In the edit Offer form, the fields should be prefilled with entity values.
    Given I am logged in as user "admin"
    When I am on "/sharing/offers/69/edit"
    And I fill hidden field "offer_type_target_id" with "67"
    And the "edit-theme" field should contain "20"
    And the "edit-body" field should contain "Je parle volontiers de tous les sujets, j'aime particulièrement l'art et le design, et me passionne pour la cuisine et le hockey sur glace."
    And the "edit-availability" field should contain "Disponible tout les jours de la semaine, l'après-midi."
    And the "edit-contact-name" field should contain "Sarah Courci"
    And the "edit-contact-mail" field should contain "sara.courci@example.org"
    And the "edit-contact-phone" field should contain "+41 21 123 45 67"

## Form submits.
  @api @preserveDatabase
  Scenario: In the edit Offer form, I should be able to submit a valid offer in an existing offer type.
    Given I am logged in as user "admin"
    When I am on "/sharing/offers/69/edit"
    And I fill hidden field "offer_type_target_id" with "64"
    And I select "22" from "theme"
    And I fill in "J'échange diverses pièces de porcelaine, contre du matériel de cuisine." for "edit-body"
    And I fill in "A convenir" for "edit-availability"
    And I fill in "Sarah Courci" for "edit-contact-name"
    And I fill in "0211234567" for "edit-contact-phone"
    And I fill in "member+lausanne@antistatique.net" for "edit-contact-mail"
    And I press "edit-submit"
    Then the url should match "/node/64#card69"
    And I should see "qs_sharing.offers.form.edit.success"

  @api @preserveDatabase
  Scenario: In the edit Offer form, I should be able to submit new values for an offer and those one should be persisted.
    Given I am logged in as user "admin"
    When I am on "/sharing/offers/69/edit"
    And I fill hidden field "offer_type_target_name" with "Echange ou don de matériel"
    And I select "22" from "theme"
    And I fill in "Jéchange diverses pièces de porcelaine, contre du matériel de cuisine." for "edit-body"
    And I fill in "A convenir" for "edit-availability"
    And I fill in "Sarah Courci" for "edit-contact-name"
    And I fill in "0211234567" for "edit-contact-phone"
    And I fill in "member+lausanne@antistatique.net" for "edit-contact-mail"
    And I press "edit-submit"
    Then the url should match "/fr/node/82#card69"
    And the url should match "/fr/node/82#card69" with parameters:
      | theme |
      | 22 |
    And I should see "qs_sharing.offers.form.edit.success"
