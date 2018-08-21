Feature: Form add photos
  In order to make sure form Add Photos works
  As a bunch of users
  I want to make sure the correct activities writable are shown in Selectize Step 1

  @api
  Scenario: Logged as Admin, When I access "Form add photos" in Lausanne
    Given I am logged in as user "admin"
    When I am on "/photos/1/add"
    And the response status code should be 200
    Then I should see 10 "select#edit-activity option" elements
    And I should see a "option[value='2']" element
    And I should see a "option[value='5']" element
    And I should see a "option[value='7']" element
    And I should see a "option[value='3']" element
    And I should see a "option[value='4']" element
    And I should see a "option[value='6']" element
    And I should see a "option[value='8']" element
    And I should see a "option[value='12']" element
    And I should see a "option[value='13']" element
    And I should see a "option[value='14']" element

  @api
  Scenario: Logged as Admin, When I access "Form add photos" in Fribourg
    Given I am logged in as user "admin"
    When I am on "/photos/2/add"
    And the response status code should be 200
    Then I should see 4 "select#edit-activity option" elements
    And I should see a "option[value='11']" element
    And I should see a "option[value='56']" element
    And I should see a "option[value='57']" element
    And I should see a "option[value='62']" element

  @api
  Scenario: Logged as Admin, When I access "Form add photos" in Genève
    Given I am logged in as user "admin"
    When I am on "/photos/3/add"
    And the response status code should be 200
    Then I should see 2 "select#edit-activity option" elements
    And I should see a "option[value='9']" element
    And I should see a "option[value='10']" element

  @api
  Scenario: Logged as Member of Fribourg, When I access "Form add photos"
    Given I am logged in as user "member+fribourg+approval+lausanne"
    When I am on "/photos/2/add"
    And the response status code should be 200
    Then I should see 0 "select#edit-activity option" elements

  @api
  Scenario: Logged as Member of Lausanne, When I access "Form add photos"
    Given I am logged in as user "member+lausanne"
    When I am on "/photos/1/add"
    And the response status code should be 200
    Then I should see 0 "select#edit-activity option" elements

  @api
  Scenario: Logged as Manager of Lausanne, When I access "Form add photos"
    Given I am logged in as user "manager+lausanne"
    When I am on "/photos/1/add"
    And the response status code should be 200
    Then I should see 3 "select#edit-activity option" elements
    And I should see a "option[value='2']" element
    And I should see a "option[value='3']" element
    And I should see a "option[value='13']" element

  @api
  Scenario: Logged as Organizer of Lausanne, When I access "Form add photos"
    Given I am logged in as user "organizer+lausanne"
    When I am on "/photos/1/add"
    And the response status code should be 200
    Then I should see 2 "select#edit-activity option" elements
    And I should see a "option[value='5']" element
    And I should see a "option[value='3']" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg ,When I access "Add Photos"
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/photos/1/add"
    And the response status code should be 200
    Then I should see 0 "select#edit-activity option" elements

  @api
  Scenario: Logged as Member of Lausanne & Member of Fribourg, When I access "Form add photos"
    Given I am logged in as user "member+fribourg+member+lausanne"
    When I am on "/photos/1/add"
    And the response status code should be 200
    Then I should see 2 "select#edit-activity option" elements
    And I should see a "option[value='6']" element
    And I should see a "option[value='3']" element

