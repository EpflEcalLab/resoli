@debug
Feature: Sharing Request add Form

## Access
  Scenario Outline: The add Request form is only available to member of the community.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
      | user | url | code |
      | admin | /sharing/1/requests/add | 200 |
      | admin | /sharing/2/requests/add | 200 |
      | admin | /sharing/3/requests/add | 200 |
      | member+lausanne | /sharing/1/requests/add | 200 |
      | member+lausanne | /sharing/2/requests/add | 403 |
      | member+lausanne | /sharing/3/requests/add | 403 |
      | approval+lausanne | /sharing/1/requests/add | 403 |
      | approval+lausanne | /sharing/2/requests/add | 403 |
      | approval+lausanne | /sharing/3/requests/add | 403 |
      | manager+lausanne | /sharing/1/requests/add | 200 |
      | manager+lausanne | /sharing/2/requests/add | 403 |
      | manager+lausanne | /sharing/3/requests/add | 403 |
      | organizer+lausanne | /sharing/1/requests/add | 200 |
      | organizer+lausanne | /sharing/2/requests/add | 403 |
      | organizer+lausanne | /sharing/3/requests/add | 403 |
      | member+lausanne+organizer+fribourg | /sharing/1/requests/add | 200 |
      | member+lausanne+organizer+fribourg | /sharing/2/requests/add | 200 |
      | member+lausanne+organizer+fribourg | /sharing/3/requests/add | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/1/requests/add | 403 |
      | member+fribourg+approval+organizer+fribourg | /sharing/2/requests/add | 200 |
      | member+fribourg+approval+organizer+fribourg | /sharing/3/requests/add | 403 |
      | member+fribourg+organizer+fribourg | /sharing/1/requests/add | 403 |
      | member+fribourg+organizer+fribourg | /sharing/2/requests/add | 200 |
      | member+fribourg+organizer+fribourg | /sharing/3/requests/add | 403 |
      | approval+member+fribourg+approval+organizer+fribourg | /sharing/1/requests/add | 403 |
      | approval+member+fribourg+approval+organizer+fribourg | /sharing/2/requests/add | 403 |
      | approval+member+fribourg+approval+organizer+fribourg | /sharing/3/requests/add | 403 |
      | declined+organizer+lausanne | /sharing/1/requests/add | 403 |
      | declined+organizer+lausanne | /sharing/2/requests/add | 403 |
      | declined+organizer+lausanne | /sharing/3/requests/add | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/1/requests/add | 200 |
      | member+lausanne+declined+organizer+lausanne | /sharing/2/requests/add | 403 |
      | member+lausanne+declined+organizer+lausanne | /sharing/3/requests/add | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/1/requests/add | 403 |
      | member+fribourg+declined+member+lausanne | /sharing/2/requests/add | 200 |
      | member+fribourg+declined+member+lausanne | /sharing/3/requests/add | 403 |

## Floating Button
  Scenario Outline: In the Sharing request form page, I should see the floating button pointing to my dashboard.
    Given I am logged in as user "<user>"
    When I am on "/sharing/1/requests/add"
    Then I should see 1 ".floating a" elements
    And I should see "qs_sharing.floating.dashboard" link with href "/sharing/1/user/<user-id>/dashboard"
    Examples:
      | user | user-id |
      | member+lausanne | 2 |
      | manager+lausanne | 5 |
      | organizer+lausanne | 6 |
      | member+lausanne+manager+fribourg | 13 |

# Back button.
  Scenario: In the Sharing request form page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/requests/add"
    Then I should not see a "#block-previousnavigation a" element

## Form pre-filled values.
  Scenario Outline: In the add Request form, the contact information must be prefilled using logged-in account data.
    Given I am volunteer on community <community> for theme 22 as user <user_id>
    Given I am logged in as user "<user>"
    When I am on "/sharing/<community>/requests/add"
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
