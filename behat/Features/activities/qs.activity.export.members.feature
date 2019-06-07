Feature: Activity export members download

  Scenario: Accessing the export feature will download an excel file
    Given I am logged in as user "manager+lausanne"
    When I am on "/activities/2/dashboard/members/export"
    And I should see in the header "content-type":"application/vnd.ms-excel"
