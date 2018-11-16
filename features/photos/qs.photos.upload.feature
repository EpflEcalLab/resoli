Feature: Upload photo via POST on Drupal\qs_photo\Controller\UploadController

  @api
  Scenario: I can't use the uploader whitout being connected
    Then create the private folder
    Then create "1" file "text" to be uploaded
    Then I send "POST" request to "/backend/photos/upload"
    And the response status code should be 404

  @api
  Scenario: I can upload file in an event I have access
    Given I am logged in as user "admin"
    Then create the private folder
    Then create "1" file "png" to be uploaded
    Then I send "POST" request to "/backend/photos/upload" with parameters:
      | event | 27 |
    And the response status code should be 200
    Then the JSON response should contain field "success"

  @api
  Scenario: The event POST field is mandatory
    Given I am logged in as user "admin"
    Then create the private folder
    Then create "1" file "png" to be uploaded
    Then I send "POST" request to "/backend/photos/upload"
    And the response status code should be 403

  @api
  Scenario: I can't upload tiff files
    Given I am logged in as user "admin"
    Then create the private folder
    Then create "1" file "tiff" to be uploaded
    Then I send "POST" request to "/backend/photos/upload" with parameters:
      | event | 27 |
    And the response status code should be 400
    Then the JSON response should contain field "error"
    Then the field "error" in the JSON response should contain "Only files with the following extensions are allowed"

  @api
  Scenario: I can't upload file in an event I can't access
    Given I am logged in as user "member+fribourg"
    Then create the private folder
    Then create "1" file "png" to be uploaded
    Then I send "POST" request to "/backend/photos/upload" with parameters:
      | event | 27 |
    And the response status code should be 403
