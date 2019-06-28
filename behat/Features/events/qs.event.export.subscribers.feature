Feature: Event export subscribers download

  Scenario: Accessing the export feature will download an excel file
    Given I am logged in as user "manager+lausanne"
    When I am on "/events/54/dashboard/subscribers/export"
    And I should see in the header "content-type":"application/vnd.ms-excel"
