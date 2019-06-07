Feature: Community exports download

  Scenario: Accessing the export feature will download an excel file
    Given I am logged in as user "manager+lausanne"
    When I am on "/communities/1/dashboard/events/export"
    And I should see in the header "content-type":"application/vnd.ms-excel"

  Scenario: Accessing the export feature will download an excel file
    Given I am logged in as user "manager+lausanne"
    When I am on "/communities/1/dashboard/members/export"
    And I should see in the header "content-type":"application/vnd.ms-excel"
