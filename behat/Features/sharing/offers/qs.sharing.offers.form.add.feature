Feature: Sharing Offers add Form

## Access
  Scenario Outline: The add Offer form is only available to account being at least volunteering on one sharing theme.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
    | user | url | code |
    | admin | /sharing/1/offers/add | 200 |
    | admin | /sharing/2/offers/add | 200 |
    | admin | /sharing/3/offers/add | 200 |
    | member+lausanne | /sharing/1/offers/add | 200 |
    | member+lausanne | /sharing/2/offers/add | 403 |
    | member+lausanne | /sharing/3/offers/add | 403 |
    | approval+lausanne | /sharing/1/offers/add | 403 |
    | approval+lausanne | /sharing/2/offers/add | 403 |
    | approval+lausanne | /sharing/3/offers/add | 403 |
    | manager+lausanne | /sharing/1/offers/add | 403 |
    | manager+lausanne | /sharing/2/offers/add | 403 |
    | manager+lausanne | /sharing/3/offers/add | 403 |
    | organizer+lausanne | /sharing/1/offers/add | 403 |
    | organizer+lausanne | /sharing/2/offers/add | 403 |
    | organizer+lausanne | /sharing/3/offers/add | 403 |
    | member+lausanne+organizer+fribourg | /sharing/1/offers/add | 200 |
    | member+lausanne+organizer+fribourg | /sharing/2/offers/add | 200 |
    | member+lausanne+organizer+fribourg | /sharing/3/offers/add | 403 |
    | member+fribourg+approval+organizer+fribourg | /sharing/1/offers/add | 403 |
    | member+fribourg+approval+organizer+fribourg | /sharing/2/offers/add | 403 |
    | member+fribourg+approval+organizer+fribourg | /sharing/3/offers/add | 403 |
    | member+fribourg+organizer+fribourg | /sharing/1/offers/add | 403 |
    | member+fribourg+organizer+fribourg | /sharing/2/offers/add | 403 |
    | member+fribourg+organizer+fribourg | /sharing/3/offers/add | 403 |
    | approval+member+fribourg+approval+organizer+fribourg | /sharing/1/offers/add | 403 |
    | approval+member+fribourg+approval+organizer+fribourg | /sharing/2/offers/add | 403 |
    | approval+member+fribourg+approval+organizer+fribourg | /sharing/3/offers/add | 403 |
    | declined+organizer+lausanne | /sharing/1/offers/add | 403 |
    | declined+organizer+lausanne | /sharing/2/offers/add | 403 |
    | declined+organizer+lausanne | /sharing/3/offers/add | 403 |
    | member+lausanne+declined+organizer+lausanne | /sharing/1/offers/add | 403 |
    | member+lausanne+declined+organizer+lausanne | /sharing/2/offers/add | 403 |
    | member+lausanne+declined+organizer+lausanne | /sharing/3/offers/add | 403 |
    | member+fribourg+declined+member+lausanne | /sharing/1/offers/add | 403 |
    | member+fribourg+declined+member+lausanne | /sharing/2/offers/add | 403 |
    | member+fribourg+declined+member+lausanne | /sharing/3/offers/add | 403 |

## Floating Button
  Scenario: In the add Offer form, I should see the floating button pointing to the current form.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers/add"
    Then I should see 1 ".floating a" element
    And I should see "qs_sharing.add_offer" link with href "/sharing/1/offers/add"

## Back button.
  Scenario: In the add Offer form, I should see the back button pointing to my offers.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers/add"
    Then I should see a "#block-previousnavigation a" element
    And I should see "qs.previous.to_sharing_dashboard" link with href "/sharing/1/user/1/dashboard"

  @api @preserveDatabase
  Scenario Outline: In the add Offer form, the contact information must be prefilled using logged-in account data.
    Given I am volunteer on community <community> for theme 22 as user <user_id>
    Given I am logged in as user "<user>"
    When I am on "/sharing/<community>/offers/add"
    And the "edit-contact-firstname" field should contain "<firstname>"
    And the "edit-contact-lastname" field should contain "<lastname>"
    And the "edit-contact-phone" field should contain "<phone>"
    And the "edit-contact-mail" field should contain "<mail>"
    Examples:
      | user | user_id | community | firstname | lastname | phone | mail |
      | admin | 1 | 1 | | | | dev@antistatique.net |
      | member+lausanne | 2 | 1 | Sarah | Courci | 0211234567 | member+lausanne@antistatique.net |
      | manager+lausanne | 5 | 1 | Juda | Bricot | | manager+lausanne@antistatique.net |
      | organizer+lausanne | 6 | 1 | Gerard | Mensoif | | organizer+lausanne@antistatique.net |
      | member+lausanne+organizer+fribourg | 8 | 1 | Jerry | Kan | | member+lausanne+organizer+fribourg@antistatique.net |
      | member+lausanne+organizer+fribourg | 8 | 2 | Jerry | Kan | | member+lausanne+organizer+fribourg@antistatique.net |
      | member+fribourg+approval+organizer+fribourg | 14 | 2 | Paul | Honet | 0211234567 | member+fribourg+approval+organizer+fribourg@antistatique.net |

## Form submits.
  @api @preserveDatabase
  Scenario: In the add Offer form, I should be able to submit a valid offer in an existing offer type.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers/add"
    And I fill hidden field "offer_type_target_id" with "64"
    And I select "22" from "theme"
    And I fill in "J'échange diverses pièces de porcelaine, contre du matériel de cuisine." for "edit-body"
    And I fill in "A convenir" for "edit-availability"
    And I fill in "Sarah" for "edit-contact-firstname"
    And I fill in "Courci" for "edit-contact-lastname"
    And I fill in "0211234567" for "edit-contact-phone"
    And I fill in "member+lausanne@antistatique.net" for "edit-contact-mail"
    And I press "edit-submit"
    Then the url should match "/node/64#card82"
    And I should see "qs_sharing.offers.form.add.success Faire les courses | Sarah Courci Faire les courses" in the ".alert" element

  @api @preserveDatabase
  Scenario: In the add Offer form, I should be able to submit a valid offer in an new offer type.
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers/add"
    And I fill hidden field "offer_type_target_name" with "Echange ou don de matériel"
    And I select "22" from "theme"
    And I fill in "J'échange diverses pièces de porcelaine, contre du matériel de cuisine." for "edit-body"
    And I fill in "A convenir" for "edit-availability"
    And I fill in "Sarah" for "edit-contact-firstname"
    And I fill in "Courci" for "edit-contact-lastname"
    And I fill in "0211234567" for "edit-contact-phone"
    And I fill in "member+lausanne@antistatique.net" for "edit-contact-mail"
    And I press "edit-submit"
    Then the url should match "/node/82#card83"
    And I should see "qs_sharing.offers.form.add.success Echange ou don de matériel | Sarah Courci Echange ou don de matériel" in the ".alert" element
