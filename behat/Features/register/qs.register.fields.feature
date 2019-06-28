Feature: Register Fields

  Scenario: Register Form - Step 1 - Should see 3 communities radios
    Given I am on "/authentication/register"
    Then I should see "qs_auth.register_form.step1.description"
    Then I should see "qs_auth.register_form.step1.sub_description"
    Then I should see "qs_auth.form.register.community"
    Then I should see 3 "fieldset#edit-step-1 input" elements
    Then I should see 1 "fieldset#edit-step-1 input[type=radio]#edit-community-1" elements
    Then I should see 1 "fieldset#edit-step-1 input[type=radio]#edit-community-2" elements
    Then I should see 1 "fieldset#edit-step-1 input[type=radio]#edit-community-3" elements

  Scenario: Register Form - Step 2 - Should see the firstname & lastname fields
    Given I am on "/authentication/register"
    Then I should see "qs_auth.register_form.step2.description"
    Then I should see "qs_auth.register_form.step2.sub_description"
    Then I should see "qs_auth.form.register.community"
    Then I should see 2 "fieldset#edit-step-2 input" elements
    Then I should see 1 "fieldset#edit-step-2 input[type=text][name=firstname]" elements
    Then I should see 1 "fieldset#edit-step-2 input[type=text][name=lastname]" elements

  Scenario: Register Form - Step 3 - Should see the mail & phone fields
    Given I am on "/authentication/register"
    Then I should see "qs_auth.register_form.step3.description"
    Then I should see "qs_auth.register_form.step3.sub_description"
    Then I should see "qs_auth.form.register.community"
    Then I should see 2 "fieldset#edit-step-3 input" elements
    Then I should see 1 "fieldset#edit-step-3 input[type=email][name=mail]" elements
    Then I should see 1 "fieldset#edit-step-3 input[type=tel][name=phone]" elements

  Scenario: Register Form - Step 4 - Should see the password & password verification fields
    Given I am on "/authentication/register"
    Then I should see "qs_auth.register_form.step4.description"
    Then I should see "qs_auth.register_form.step4.sub_description"
    Then I should see "qs_auth.form.register.community"
    Then I should see 2 "fieldset#edit-step-4 input" elements
    Then I should see 1 "fieldset#edit-step-4 input[type=password][name=password]" elements
    Then I should see 1 "fieldset#edit-step-4 input[type=password][name=password_verification]" elements
