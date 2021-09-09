Feature: Offer Moderate Form

## Access
  @api
  Scenario Outline: Logged-in, only a community manager or an admin can access the moderate form.
    Given I am logged in as user "<user>"
    When I am on "/sharing/community/<community-id>/<offer-id>/moderate"
    Then the response status code should be <code>

    Examples:
      | user | community-id | offer-id | code |
      | organizer+lausanne | 1 | 76 | 403 |
      | organizer+lausanne | 1 | 74 | 403 |
      | member+lausanne+manager+fribourg | 2 | 71  | 200 |
      | member+lausanne+manager+fribourg | 1 | 72  | 403 |
      | member+lausanne+manager+fribourg | 1 | 73  | 403 |
      | organizer+lausanne | 1 | 71 | 403 |
      | organizer+lausanne | 1 | 72 | 403 |
      | organizer+lausanne | 2 | 73 | 403 |
      | member+lausanne+manager+fribourg | 1 | 71  | 403 |
      | member+lausanne+manager+fribourg | 2 | 72  | 200 |
      | member+lausanne+manager+fribourg | 2 | 73  | 200 |
      | member+lausanne+manager+fribourg | 1 | 69  | 403 |
      | member+lausanne+manager+fribourg | 1 | 74  | 403 |
      | member+lausanne+manager+fribourg | 1 | 76  | 403 |
      | member+lausanne | 1 | 76  | 403 |
      | member+lausanne | 1 | 74  | 403 |
      | member+lausanne | 2 | 73  | 403 |
      | member+lausanne | 1 | 69  | 403 |
      | manager+lausanne | 1 | 70  | 200 |
      | manager+lausanne | 2 | 71  | 403 |
      | manager+lausanne | 1 | 72  | 200 |
      | manager+lausanne | 1 | 73  | 200 |

## Form visibility.
  @api
  Scenario Outline: When reaching the offer listing, only a community manager or an admin should see the moderation form
    Given I am logged in as user "<user>"
    When I am on "/node/65?theme=21"
    Then I should see <element> "form.moderate" element

    Examples:
      | user | element
      | admin | 1
      | member+lausanne+manager+fribourg | 1
      | member+lausanne | 0
      | organizer+lausanne | 0

## Form submits.
  @api @preserveDatabase @mail
  Scenario: When moderating an Offer, it should works, send an email and redirect me to the listing of offer type of the current community
    Given I am logged in as user "member+lausanne+manager+fribourg"
    When I am on "/node/65?theme=21"
    Then I should see 1 "form.offer73.moderate" element
    Then I follow the link ".offer73.moderate button[type='submit']" element
    Then A mail as been sent to "member+lausanne+organizer+fribourg@antistatique.net" with subject "qs.mail.offer.deactivated.subject Resoli Aide pour porter les courses"
    Then the url should match "/node/65?theme=21"
    And I should see 0 ".card.card-info " element
