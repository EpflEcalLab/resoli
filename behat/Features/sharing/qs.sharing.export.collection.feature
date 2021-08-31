Feature: Offers export by community

  @api
  Scenario Outline: Access control to events export of Lausanne
    Given I am logged in as user "<user>"
    When I am on "/sharing/export/community/<community>/pdf"
    Then the response status code should be <code>
    Examples:
      | user | community | code |
      | member+lausanne | 1 | 200 |
      | manager+lausanne | 1 | 200 |
      | organizer+lausanne | 1 | 200 |
      | member+fribourg | 1 | 403 |
      | approval+lausanne | 1 | 403 |
      | member+lausanne+declined+organizer+lausanne | 1 | 200 |
      | member+lausanne+declined+organizer+lausanne | 2 | 403 |
      | declined+organizer+lausanne| 1 | 403 |
      | member+fribourg+declined+member+lausanne | 1 | 403 |
      | member+fribourg+declined+member+lausanne | 2 | 200 |
      | member+lausanne | 2 | 403 |
      | manager+lausanne | 2 | 403 |
      | organizer+lausanne | 2 | 403 |
      | manager+lausanne | 2 | 403 |

#  @todo add tests on header to ensure a pdf is downloaded.
#  @api
#  Scenario: Accessing the export feature will download an pdf file
#    Given I am logged in as user "manager+lausanne"
#    When I am on "/sharing/export/community/1/pdf"
#    And I should see in the header "Content-Type":"application/pdf"
