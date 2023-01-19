Feature: Offers export by offer's type

  @api
  Scenario Outline: Access control to events export of Lausanne
    Given I am logged in as user "<user>"
    When I am on "/sharing/export/offer_type/<offer_type>/theme/<theme>/pdf"
    Then the response status code should be <code>
    Examples:
      | user | offer_type | theme | code |
      | member+lausanne | 66 | 19 | 200 |
      | manager+lausanne | 66 | 19 | 200 |
      | organizer+lausanne | 66 | 19 | 200 |
      | member+fribourg | 66 | 19 | 403 |
      | approval+lausanne | 66 | 19 | 403 |
      | member+lausanne+declined+organizer+lausanne | 66 | 19 | 200 |
      | member+lausanne+declined+organizer+lausanne | 65 | 21 |  403 |
      | declined+organizer+lausanne| 66 | 19 | 403 |
      | member+fribourg+declined+member+lausanne | 66 | 19 | 403 |
      | member+fribourg+declined+member+lausanne | 65 | 21 | 200 |
      | member+lausanne | 65 | 21 | 403 |
      | manager+lausanne | 65 | 21 | 403 |
      | organizer+lausanne | 65 | 21 | 403 |
      | manager+lausanne | 65 | 21 | 403 |

  @api
  Scenario: Accessing the export feature will download a PDF file.
    Given I am logged in as user "manager+lausanne"
    When I am on "/sharing/export/offer_type/66/theme/19/pdf"
    And I should see in the header "Content-Type":"application/pdf"
