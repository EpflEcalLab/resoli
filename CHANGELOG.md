# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased2]
### Security
- upgrade drupal/core 9.5.11 => 10.0.11 - QS-50
- replace Mink Goutte in favor of Browserkit for Behat testing - QS-50

### Changed
- update phpstan.neon for Drupal 10 - QS-50
- remove shim class Drupal\Core\Http\RequestStack and use Symfony\Component\HttpFoundation\RequestStack - QS-50

### Added
- add drush alias on Docker container - QS-50

## [Unreleased]
### Security
- replace drupal/swiftmailer by drupal/symfony_mailer_lite - QS-61
- upgrade to PHP 8.1 - QS-52
- add module drupal/ckeditor (1.0.2)
- update module drupal/bamboo_twig (6.0.0 => 6.0.1)
- update module drupal/symfony_mailer_lite (1.0.2 => 1.0.6)
- update theme drupal/gin (3.0.0-rc7 => 3.0.0-rc9)
- update drupal/gin_toolbar (1.0.0-rc4 => 1.0.0-rc5)
- update drupal/loco_translate (3.0.0 => 3.0.1)
- update library dompdf/dompdf (v2.0.3 => v2.0.4)
- remove root dependency on webmozart/path-util but still present by another package
- update linters mglaman/phpstan-drupal (1.1.31 => 1.2.5) & phpstan/phpstan-deprecation-rules (1.1.3 => 1.1.4)
- update library phpoffice/phpspreadsheet (1.28.0 => 1.29.0)
- update dev-dependency module drupal/rules (3.0.0-alpha7 => 3.0.0-alpha8)
- update library behat/behat (v3.13.0 => v3.14.0)
- update module drupal/default_content (1.0.0-alpha9 => 2.0.0-alpha2)

### Added
- add Upgrade Status on CI - QS-49

### Changed
- change confirmation buttons waiting time from 2.5s to 4s - QS-62
- change deployment workflow - QS-47
- move Linters php-cs-fixer into own Tools/ComposerJson - QS-64
- move Linters php-deprecation-detector into own Tools/ComposerJson - QS-64
- rework Docker integration using new Docker images - QS-48
- rework the Github Actions PHPUnit & Behat integration - QS-48
- uninstall legacy base theme Stable for compliancy upgrade D9 -> D10 - QS-63
- update all Default Content to use Yaml format - QS-63
- add missing ->accessCheck() on Query - QS-63
- replace RedirectResponse::create by new RedirectResponse - QS-63
- twig replace deprecated spaceless by apply-spaceless - QS-63
- twig replace deprecated usage of if condition on for tag
- replace usage of deprecated function honeypot_add_form_protection() by honeypot service addFormProtection - QS-63
- replace deprecated GetResponseEvent event args by RequestEvent on subscribers - QS-63
- change visibility on $modules tests Class from public -> protected - QS-63
- replace usage of assertEqual() by assertEquals() - QS-63
- replace phpunit usage of deprecated withConsecutive - QS-63
- replace phpunit usage of deprecated setMethods - QS-63
- remove necessary @internal on FormBasic - QS-63
- replace deprecated usage of class Twig_Extension by Twig\Extension\AbstractExtension - QS-63
- replace usage of jQuery once in favor of Drupal core/once - QS-63
- replace usage of Symfony\Component\HttpFoundation\RequestStack by shim Drupal\Core\Http\RequestStack - QS-63
- replace deprecated method getCellByColumnAndRow() of class PhpOffice\PhpSpreadsheet\Worksheet\Worksheet by getCell() method with an array of \[, ] - QS-63

### Removed
- remove linter phpmd - QS-64
- uninstall legacy module RDF - QS-63
- remove jQuery Datepicker in favor of Browser native date picker - QS-63

## [2.3.7] - 2023-12-12
### Fixed
- fix order of photos by event then by creation date, then by ID, then by event date (follow-up QS-39) - QS-55 QS-57
- harmonize photos listing (teaser, calendar and activity) to follow the same order - QS-55 QS-57
- fix usage of restricted variable request and replace by node - QS-55 QS-57 QS-59
- fix bug populating mail on phone and vice-versa during offer edition - QS-59

### Security
- update module drupal/admin_toolbar (3.3.2 => 3.4.2)
- update module drupal/gin (3.0.0-rc3 => 3.0.0-rc7)
- update module drupal/gin_toolbar (1.0.0-rc1 => 1.0.0-rc4)
- update module drupal/token (1.11.0 => 1.13.0)
- update module drupal/ctools (4.0.3 => 4.0.4)
- update module drupal/file_mdm (2.5.0 => 2.6.0)
- update module drupal/focal_point (2.0.0 => 2.0.2)
- update module drupal/redirect (1.8.0 => 1.9.0)
- update module drupal/pathauto (1.11.0 => 1.12.0)
- update module drupal/captcha (1.10.0 => 2.0.5)
- update module drupal/honeypot (2.1.2 => 2.1.3)
- update module drupal/masquerade (2.0.0-rc1 => 2.0.0-rc4)
- update module drupal/new_relic_rpm (2.1.0 => 2.1.1)
- update drupal/core (9.5.8 => 9.5.11)

### Removed
- uninstall module drupal/new_relic_rpm

## [2.3.6] - 2023-05-23
### Security
- update linter wapmorgan/php-deprecation-detector (2.0.29 => 2.0.33)
- update behat/behat (v3.12.0 => v3.13.0)
- update phpunit/phpunit (9.6.4 => 9.6.7)
- update phpstan/phpstan (1.10.5 => 1.10.14)
- update module drupal/jquery_ui_datepicker (1.4.0 => 2.0.0)
- update drupal/focal_point (1.5.0 => 2.0.0)
- update module drupal/backerymails (2.2.0 => 3.0.0)
- update theme drupal/gin (3.0.0-rc1 => 3.0.0-rc3)
- update module drupal/admin_toolbar (3.3.0 => 3.3.1)
- update module drupal/recaptcha (3.1.0 => 3.2.0)
- update module drupal/captcha (1.9.0 => 1.10.0)
- update module google/recaptcha (1.2.4 => 1.3.0)
- update module drupal/allowed_formats (1.5.0 => 2.0.0)
- update module drupal/schema_metatag (1.9.0 => 2.4.0)
- update module drupal/swiftmailer (2.3.0 => 2.4.0)
- update Core drupal/core (9.5.4 => 9.5.8)
- update all remaining minors library
- update library loco/loco (2.0.10 => 2.0.12)

## [2.3.5] - 2023-03-23
### Security
- update bundle capdrupal 3.0.2 (was 3.0.0)
- update drupal/core (9.5.1 => 9.5.4)
- update composer/installers (v1.12.0 => v2.2.0)
- update linter phpstan/phpstan (0.12.100 => 1.10.5)
- update linter mglaman/phpstan-drupal (0.12.15 => 1.1.29)
- update library dompdf/dompdf (v2.0.1 => v2.0.3)

### Fixed
- fix Github Actions build using uncompatible node version
- order the photos by date and by event ID - QS-39

### Removed
- remove drupal/console in favor of Drush

## [2.3.4] - 2023-01-19
### Security
- update dompdf/dompdf v1.2.2 => v2.0.1
- update module drupal/admin_toolbar (3.1.1 => 3.3.0)
- update module drupal/bamboo_twig (5.0.0 => 6.0.0)
- update module drupal/loco_translate (2.1.0 => 3.0.0)
- update module drupal/swiftmailer (2.2.0 => 2.3.0)
- update module drupal/ctools (4.0.0 => 4.0.3)
- update module drupal/jquery_ui (1.4.0 => 1.6.0)
- update module drupal/jquery_ui_datepicker (1.3.0 => 1.4.0)
- update module drupal/crop (2.2.0 => 2.3.0)
- update module drupal/gin_toolbar (1.0.0-beta22 => 1.0.0-rc1)
- update module drupal/gin (3.0.0-beta5 => 3.0.0-rc1)
- update module drupal/field_group (3.3.0 => 3.4.0)
- update module drupal/captcha (1.5.0 => 1.9.0)
- update module drupal/backerymails (2.1.0 => 2.2.0)
- update drupal/core-dev (9.4.8 => 9.5.1)
- update drupal/core (9.4.8 => 9.5.1)
- replace library wapmorgan/php-code-fixer by wapmorgan/php-deprecation-detector

### Fixed
- fix Ajax Maximum call stack size exceeded since Drupal 9.5 update

## [2.3.3] - 2022-12-05
### Security
- update drupal/core (9.4.7 => 9.4.8)
- update engine from PHP 7.4 => 8.0

### Changed
- modernize the development environemnts to align with recent projects

### Fixed
- fix crash PHP 8 when Event does not have a content body - QS-36
- fix PHP8 new assertion triggering Warning - QS-36

## [2.3.2] - 2022-10-06
### Security
- Upgrade `drupal/core (9.4.5 => 9.4.7)` with all deps
- Upgrade `twig/twig (v2.15.2 => v2.15.3)`
- Upgrade module `drupal/captcha (1.4.0 => 1.5.0)`
- Upgrade module `drupal/console (1.9.8 => 1.9.10)`
- Upgrade module `drupal/field_group (3.2.0 => 3.3.0)`
- Upgrade module `drupal/file_mdm (2.4.0 => 2.5.0)`
- Upgrade module `drupal/file_mdm_exif (2.4.0 => 2.5.0)`
- Upgrade module `drupal/file_mdm_font (2.4.0 => 2.5.0)`
- Upgrade module `drupal/honeypot (2.1.1 => 2.1.2)`
- Upgrade module `drupal/image_effects (3.3.0 => 3.4.0)`
- Upgrade module `drupal/masquerade (2.0.0-beta4 => 2.0.0-rc1)`
- Upgrade module `drupal/metatag (1.21.0 => 1.22.0)`
- Upgrade module `drupal/recaptcha (3.0.0 => 3.1.0)`
- Upgrade module `drupal/redirect (1.7.0 => 1.8.0)`
- Upgrade module `phpoffice/phpspreadsheet (1.24.1 => 1.25.2)`

## [2.3.1] - 2022-09-13
### Security
- upgrade Drupal Core drupal/core (9.4.1 => 9.4.5) with all deps
- upgrade module behat/behat (v3.10.0 => v3.11.0)
- upgrade module drupal/admin_toolbar (3.1.0 => 3.1.1)
- upgrade module drupal/captcha (1.3.0 => 1.4.0)
- upgrade module drupal/jquery_ui_datepicker (1.2.0 => 1.3.0)
- upgrade module drupal/mailsystem (4.3.0 => 4.4.0)
- upgrade module drupal/metatag (1.19.0 => 1.21.0)
- upgrade module drupal/pathauto (1.10.0 => 1.11.0)
- upgrade module drupal/token (1.10.0 => 1.11.0)
- upgrade module myclabs/php-enum (1.8.3 => 1.8.4)
- upgrade module phpoffice/phpspreadsheet (1.23.0 => 1.24.1)
- upgrade module drupal/core-composer-scaffold (9.4.1 => 9.4.5)
- upgrade twig/twig (v2.15.1 => v2.15.2)

### Fixed
- fix demo enable & logout button visibility for admin

## [2.3.0] - 2022-08-11
### Security
- upgrade Drupal Core drupal/core (9.3.12 => 9.4.1) with all dependencies
- upgrade module drupal/loco_translate (2.0.0 => 2.1.0)
- upgrade module drupal/bamboo_twig (5.0.0-alpha1 => 5.0.0)
- upgrade theme drupal/gin (3.0.0-beta2 => 3.0.0-beta5)
- upgrade module drupal/pathauto (1.9.0 => 1.10.0)
- upgrade module drupal/honeypot (2.0.2 => 2.1.1)
- upgrade module drupal/image_effects (3.2.0 => 3.3.0)
- upgrade module drupal/ctools (3.7.0 => 4.0.0)
- upgrade module drupal/captcha (1.2.0 => 1.3.0)
- upgrade linter wapmorgan/php-code-fixer (2.0.26 => 2.0.29)
- upgrade library dompdf/dompdf (v1.2.1 => v1.2.2)
- upgrade behat extension drupal/drupal-extension (v4.1.0 => v4.2.1)
- upgrade behat driver drupal/drupal-driver (v2.1.1 => v2.2.0)
- replace behat/mink-extension by friends-of-behat/mink-extension
- upgrade library phpoffice/phpspreadsheet (1.22.0 => 1.23.0)

### Fixed
- fix icon 'object' of sharing theme - QS-25

## [2.2.3] - 2022-04-21
### Security
- update Drupal 9.3.7 => 9.3.12 with all dependencies
- update module drupal/gin (3.0.0-beta1 => 3.0.0-beta2)
- update module drupal/gin_toolbar (1.0.0-beta21 => 1.0.0-beta22)
- update module drupal/allowed_formats (1.4.0 => 1.5.0)

## [2.2.2] - 2022-03-15
### Security
- update Drupal 9.3.0-beta3 => 9.3.7 with all dependencies

## [2.2.1] - 2022-03-08
### Added
- add a translation if no option into the autocomplet - QS-17

## [2.2.0] - 2022-02-14
### Security
- update Drupal 9.2.10 => 9.3.3 with all dependencies

## [2.1.1] - 2021-12-17
### Fixed
- fix display Sharing Themes translated names on volunteerism form

### Removed
- remove satackey/action-docker-layer-caching on Github Actions

## [2.1.0] - 2021-12-16
### Added
- add core architecture for Entraide
- Add new Sharing entity 'Offer's Type'
- Add new Sharing entity 'Offer'
- Add new Sharing entity 'Request'
- Add new Sharing entity 'Volunteerism'
- Add PDF exporter service and base template #796
- Add dashboard 'Offers'
- Add templates for listing offers
- Add moderation for the offers
- add offer edit form
- add Request collection for volunteers - #831 #832
- add Request Archive form - #832
- add Request Solve form - #830
- add Request Add form - #833
- add filter by moderation state on requests admin Views UI - #943
- add fragment #card upon offer desactivation & re-activation - #978
- add solved_by lead text on listing of offers - #964
- add bg-pink-darker variante and use it on request confirmation form - #969
- add active sharing link on request confirmation page - #969
- add Loco Translate integration to ease translations
- add new Javascript library node-autocomplete - QS-3
- integrate node-autocomplete - QS-7 QS-4 QS-5 QS-6

### Changed
- update all Javascript Dependencies
- Update drupal/core (9.2.1 => 9.2.4)
- Update drupal/admin_toolbar (3.0.1 => 3.0.2)
- Update drupal/field_group (3.1.0 => 3.2.0)
- move from Codeship to Github Actions
- security update drupal/core-dev (9.2.4 => 9.2.6)
- show the offer_type title instead of offer title upon deactivate, delete and reactive actions on offers - #947
- remove quill from Offer Availability field - #1001
- change order of offers collection listing to always list last changed on top - #1010
- remove notification mail when someone archive Requests of others - #968
- increase image WxH limitation from 5000x5000 => 10000x10000 - #940
- update all PHP dependencies with config export
- update all Javascript dependencies
- update autocomplete order of elements to be sorted alpha - QS-12
- update friendsofphp/php-cs-fixer (v2.19.3 => v3.3.1)

### Fixed
- fix pager of Events
- fix un-peristed contact firstname & lasname upon offer creation - #944
- fix un-peristed contact firstname & lasname upon request creation
- fix wrong persistence of e-mail & phone number upon request creation
- fix missing translations strings - #960
- fix counter of offers by types - #981
- strip HTML markup on PDF export - #974
- increase body width of request confirmation form to make the btn readable - #969
- volunteering preferences form should redirect to my dashboard - #961
- fix persistance of solved_at date when solving a request - #964
- apply fullpage modal template to edit offer form - #977 #957
- remove box-shadow on checkboxes on Create offer Form - #951
- use Large sharing icon size on collection pages - #947
- update 'divers' sharing theme icon - #967
- update request add form to have margin on mobile - #1006
- update offer add form themes responsivness - #1005
- fix none-working Moderate offer action when multiple form on the same page
- fix Daylight Saving offsetting events's hours over repetitions - #920
- user's offers collection responsivness - #1003
- community's requests collection responsivness - #1004
- fix button past event active state - QS-10

### Added
- add Docker credentials for Codeship to prevent Pull Rate Limit
- node-autocomplete library

### Security
- update drupal/core (9.2.7 => 9.2.10) - QS-14
- update dependencies (others than drupal/core) - QS-14

## [2.0.0] - 2021-08-26
### Added
- add Gin Admin theme
- fix Gin custom QS Toolbar icon

### Changed
- update drupal/core-recommended (8.9.17 => 9.2.3)
- update to the new age the way Behat tests are run in Docker

### Fixed
- fix unit tests by removing install schema system.router in tests
- fix behat tests by updating default content date format stored
- fix twig missing include of macros

### Removed
- remove mglaman/drupal-check
- remove drupal/upgrade_status

## 1.7.1 - 2021-08-19
### Added
- add new "base theme" key on themes
- add drupal/upgrade_status to prepare Drupal 9 update
- add drupal/jquery_ui_datepicker as deprecated from Drupal Core

### Changed
- update drupal/core (8.9.14 => 8.9.17) with all dependencies (39 updates)
- update drupal/honeypot (1.30.0 => 2.0.1)
- update drupal/admin_toolbar (2.4.0 => 3.0.1)
- update drupal/new_relic_rpm (1.3.0 => 2.1.0)
- update drupal/backerymails (1.3.0 => 2.0.0)
- update drupal/bamboo_twig (4.1.0 => 5.0.0-alpha1)
- update config system.action using deprecated plugin
- update changelog form to follow keepachangelog format

### Fixed
- fix themes (customs) D9 compatibility declaration
- fix modules (customs) D9 compatibility declaration
- fix behat scalar usage without quotes
- fix spacing issue for ENG hours in events #874
- fix quilljs link popover #880

### Removed
- remove drupal/devel (1.2.0)
- remove drupal/block_exclude_pages (1.7.0)

## 1.7.0 - 2021-06-30
### Changed
- add Quill lib for rich-text edition #791
- add link to the event card on Photo's activity list - #799
- add feature to show Past & Future events on Activity canonical page - #806 #804
- add standard (numeric) pager on Activity canonical page listing of past events - #805
- add an Inline form autocomplete to subscribe member manually - #800
- update action btn displayed on past event cards - #839
- add a 3rd Step on Event creation to choose between One event or Weekly 12 repeated events - #803
- send mail when organizer manually subscribe a user (#800) to an event - #801
- update action btn displayed on past event cards - #839
- add QuillJS to Event add/edit & Activity Defaults - #807
- add QuillJS link capability - #849
- fix Excel future events export date timezone - #802
- fix navigation button next months on Photos by Month, Icon position left instead of right - #759
- add fragment on past-event button "access event's photo" to scroll-down to the event - #855
- on multiple events creation (#803) add the URL fragment to the first created event - #854
- add Description field to Activity - #859
- add Description field to Activity create/edit form - #859
- show Description field to Activity Canonical view - #860
- show button "View Photos" on Past event & event finishined today - #839
- add custom QuillJS Link component to prepend missing http(s) schemes - #849

## 1.6.1 - 2021-04-23
### Changed
- update drupal/core (8.9.13 => 8.9.14)

## 1.6.0 - 2021-04-19
### Changed
- update Docker to PHP7.4, Node 10 & MariaDB 10.4
- update Drupal from 8.9.7 to 8.9.13 with all dependencies
- modernize the Code Styles integration
- disable Code Styles PHPMD & PHPCDP for now
- move Code Styles checking from Codeship to Github Actions
- update Bundle from 1.16.0 => 2.1.4
- update Capistrano deployment steps
- masive codebase update to remove deprecation notices
- update Node from 10 => 12

## 1.5.6 - 2020-11-19
### Changed
- fix Event Export into Excel file which trigger crash on empty column "Contact"

## 1.5.5 - 2020-11-19
### Changed
- update Drupal from 8.8.4 to 8.9.3 with all dependencies
- update Drupal from 8.9.3 to 8.9.7
- update Drupal from 8.9.7 to 8.9.8 with all dependencies
- update drupal/image_effects (2.3.0 => 3.0.0) with all dependencies
- update to Node 10
- improve the Event Export into Excel file styling #763
- add a captcha on subscription form to avoid spam #768
- update Drupal from 8.9.8 to 8.9.9 (SA-CORE-2020-012)

## 1.5.4 - 2020-10-19
### Changed
- Apply patch to be compatible with Apache 2.4 and avoid double compression with brotli

## 1.5.3 - 2020-04-15
### Changed
- update composer lock
- update composer used in Alwaysdata from 1.5.5 -> 1.10.5

## 1.5.2 - 2020-04-15
### Changed
- update Drupal from 8.7.x to 8.8 - #753
- update modules - #750
- add NewRelic module

## 1.5.1 - 2020-04-01
### Changed
- fix end-of-year previous button - close #747
- fix date pager bugged on february - close #758

## 1.5.0 - 2020-01-08
### Changed
- update to Drupal 8.7.11
- fix dual step bug when multiple forms - close #748
- rebuild New Relic for PHP 7.2 on Alwaysdata

## 1.4.4 - 2019-10-11
### Changed
- remove InlineErrorFormTrait to use Core Inline Form Error - close #702

## 1.4.3 - 2019-09-25
### Changed
- improve responsivness especially smartphones visual design - close #741

## 1.4.2 - 2019-09-18
### Changed
- fix fade-out of parent cards on ajax-submitted form - close #739

## 1.4.1 - 2019-07-25
### Changed
- fix encoding issues in photos section, close #152
- fix some modal issues in themes filter and in event/activity add forms #722
- fix IE and Edge issues, fix some other alignment problems #733 #735
- fix scroll in modals on iOS? #734
- fixes refactoring frontend (#716)
- fix menu spacing, close #714
- fix dates of activities by date - close #674
- fix #729 - Add photos button has no more outline
- fix wrong dates in activities view (#726)
- force rendering in GPU to mitigate scrolling issue #721
- fix card not showing on first load, close #718
- fix unclickable dots of calendar - close #719
- fix export of members by community - close #727 #728
- fix z-index dropdown selector on modal - close #711
- fix button contact-all on subscription waiting approvals - close #717

## 1.4.0 - 2019-06-29
### Changed
- refactoring of Floating buttons
- refactoring of Cards HTML markup & skeletton
- refactor global pages markup & skeletton
- refactor radio buttons for privileges
- refactor twig calendars
- refactor all trans to |t in twig templates
- refactore hidden form field - #703,#705
- add search on members of community - close #45i,#704
- add export of futur events of community #686
- add export of member by activity #678
- add export of member by community #678
- add export of subscriber user by event #678
- add gradient scroll to long pages #661
- add export of #699
- add hours on 'My Subscriptions' pill - close #688
- add new rules when organizer/co-organizer create an event - #675
- fix hiding of caption when photoswipe is closed #677
- fix auto-subscriptions of organizers/co-organizers - #700
- fix community dashboards - #710
- fix minor frontend flaws #707, #686
- tests process improved with Base Class & Trait
- tests export/download of Excel files #708
- tests Forms on Behat - close #696
- tests improved on BadgeManager::countEventsByDates
- setup Codeship Pro
- setup Docker

## 1.3.4 - 2019-05-13
### Changed
- fix mail layout paragraphs issue - close #691

## 1.3.3 - 2019-05-02
### Changed
- fix IE11 - close #687
- add date of event on 'My subscriptions' page - close #688

## 1.3.2 - 2019-03-27
### Changed
- fix tests process on Codeship

## 1.3.1 - 2019-03-27
### Changed
- fix masquerade button on navigation - close #684
- babelify the subscription.js which was uncompatible with IE or some Apple device - close #683
- update phpunit workflow & scripts
- add base for future PHPUnit tests
- fix EventManager::getNext - close #647

## 1.3.0 - 2019-02-18
### Changed
- update to Drupal 8.6.9
- responsivness all the way
- change icon in "Mes informations" button on welcome dashboard #651
- fade out activity card teasers if they have no events #653
- allow upload of today's photos #578
- add Photos Dashboard – Activity's name in strings #429
- calendar – Today/Tomorrow
- photos by date – Newest to Oldest
- improve label of activity's page, contact manager #321
- create/edit event – Contribution placeholder is missing #630
- create event – Floating button icon #360
- add possibility to have multiple buttons in floating action buttons #651
- add contrast to toggle buttons #652
- add confirmation step to subscription button #651
- fix date display for weeks with more than 4 weeks #674
- hide uppy until we select an event #671
- fix a lot of issues in responsive mode #664
- change photos order by date – Newest to Oldest #519
- add calendar – Today/Tomorrow #384

## 1.2.2 - 2018-11-07
### Changed
- add new tests bash files
- get the true next events - close #647

## 1.2.1 - 2018-11-07
### Changed
- remove the baseline of "Pro Senectute" on sponsors.svg

## 1.2.0 - 2018-08-21
### Changed
- fix mail body encoded quote #152
- add module "Image Effects" which allow Image Styles to Automatically use EXIF Image Orientation #606
- improve members listing by ordering them using Lastname instead of Name (mail) #570
- fix Activity CTA buttons visibilities #517
- improve organizers subscriptions by removing "waiting approval" mail to others organizers #613
- enable English #635
- add language switcher #634
- add warning screen on mobile #481
- add phone button in confirmend subscriptions list #397
- add Image Effects for Exif Orientation #606
- fix mail body encoded quote #152
- improve members listing by ordering them using Lastname instead of Name (mail) #570
- add "email to all" button in listings (activity members, event subscribers, event pending subscribers) #477
- add gradient behind modal-footer in modal-forms #390
- fix scrolling issue in Firefox with scrollable flex inside flex container #539
- update to Drupal 8.5.6
- fix hours display in all languages #628 #643

## 1.1.4 - 2018-07-05
### Changed
- add german (de) language
- make Taxonomy "Themes" translatable with fallback in Default Site Lang (FR) - #627
- update logo - #629

## 1.1.3 - 2018-06-28
### Changed
- add mailjet as SMTP mail provider.
- update to Drupal 8.5.4

## 1.1.2 - 2018-05-14
### Changed
- update to Drupal 8.5.3
- fix IE 11 regression, sticky polyfill crash #620

## 1.1.1 - 2018-05-02
### Changed
- add white border to btn-info buttons #598
- add spacing below visibility buttons in ActivityAddForm #398

## 1.1.0 - 2018-04-26
### Changed
- add the sponsors on homepage
- change confirmed icon for event registering #598
- increase size of activity in activity user collection and in photos collection #545
- fix card pill flag position on medium and small screens #588
- make all cards close when opening one in calendar view #596
- add Privilege's Badge on Activity by theme #318, #315 & #316
- add Subscription's Badge, using highest Privilege color, on Activity by date #308 & #309
- add Subscription's Badge, using highest Privilege color, on My Activities #304
- update Subscription's Badge, using highest Privilege color, on Activity page #543
- update Subscription's Badge, using highest Privilege color, on Calendar Weekly/Monthly Cards - below Calendar Dots - #387
- add Privilege's Badge on My Photos #412
- add Privilege's Badge on Photos by theme #441, #442 & #443
- add Privilege's Badge on Members of Activity #349
- add 'Direct Subscription' whitout needing request for Organizer of activity #313
- add subscription to the author Maintainer of Activity when creating an Event #311
- add collapsable event card on 'My subscriptions' page
- add subscription textual information on 'My Subscription' card event body
- add Subscription's Badge, using Count of Guests Pending & Confirmed on events pill #310, #311, #312 & #313
- add Subscription's Badge, using Count of Guests Pending on Activitiy Teaser Card #317 & #319
- switch the Event Dashboard button in Activity detail page when Guests Pending
- add Subscription's Badge, using highest Privilege color, on Calendar Weekly/Monthly Dots #386
- update Subscripton's Badge, using hiest Privilege color, on Activity page #309 & #309
- fix #590 - wrong badges on activities by Theme when activity has event in past with pending subscriptions
- fix #207 - photos by months - sticky months are not translated
- fix #584 - fix big_pipe google map autocomplete fields
- add Masquerade module
- fix pager #610 & #31 - following pagers wasn't working: Members of Activity, Members of Community & Form ActivityInlineAddMember

## 1.0.4 - 2018-04-26
### Changed
- Apply Remote Code Exectiion - SA-CORE-2018-004

## 1.0.3 - 2018-03-28
### Changed
- apply patch - Remote Code Execution - SA-CORE-2018-002

## 1.0.2 - 2018-03-16
### Changed
- setup pagination component #31

## 1.0.1 - 2018-03-01
### Changed
- fix images upload on past events - #576
- update README.md badges

## 1.0.0 - 2018-02-26
### Changed
- fix #500 - Remove privilegies & subscriptions when deleting entities
- improve user supervisor dashboard #202
- fix click propagation in photoswipe gallery #497
- add some missing empty states in photos section #466
- change focus ring width to 2px instead of .2rem
- fix small shift when clicking on menu toggle button
- set event card link to weekly calendar instead of monthly #383
- setup favicon! #391
- add title to Community Apply form #297
- remove sentence in password recovery page #285
- set title in purple on Activity and Activiy Photos pages #320
- make invalid feedback readable on backgrounds #219
- ensure cleaning of redirects when entities are deleted #496 #470 #39
- send emails to user when communities approval is confirmed/declined #270
- close #411 - improve community dashboard return button
- add classes to toggle the flag and button visibility in event cards #234
- refactoring the subscription workflow to use Drupal Ajax form #234
- send email to user when subscription to event is confirmed #234
- fix broken image ratio in photoswipe gallery in view by month #444
- send mail to community managers when new user(s) request access to community #264
- send mail to community managers when new user(s) request access to community #264 - via communityApply form
- send mail to community managers when new user(s) request access to community #264 - via register form
- set the modal-open wrapper to `position: fixed` to remove scroll inertia on Safari #408
- send mail to subscribers of events when event is deleted #381
- send mail to activity organizers when events is deleted #381
- send mail to activity maintainers when events is deleted #381
- send mail to subscribers of events when event is updated #381
- send mail to activity organizers when events is updated #381
- send mail to activity maintainers when events is updated #381
- add the role in communities listing of supervisor dashboard #202
- flip the card back when using the browser back button #520
- remove confirm state text in ajax submit buttons #224
- fix translation extractor code and update translations
- add shadow to copy button #491
- fix empty state messages in photos collection by theme #466
- send mail to user when subscription to event is declined #377
- send mail to organizers & maintainers when subscription to event is confirmed #377
- fix Firefox glitch when flipping a card #255
- remove padding hacks to mitigate Firefox bugs... #255
- use jQuery UI Calendar by default on all browsers to avoid issues with date format on Chrome
- fix #507 - Google Autocomplete doesn't works when user chose a place & then change to a custom one
- fix #501 - Manage community Floating buttons
- fix IE specific cases #534
- fix Edge specific cases #566

## 0.1.1 - 2018-02-21
### Changed
- add Google Tag Manager

## 0.1.0 - 2017-12-13
### Changed
- production deployment

## 0.0.0 - 2017-07-14
### Changed
- init empty repo

[Unreleased]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.7...HEAD
[2.3.7]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.6...2.3.7
[2.3.6]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.5...2.3.6
[2.3.5]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.4...2.3.5
[2.3.4]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.3...2.3.4
[2.3.3]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.2...2.3.3
[2.3.2]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/antistatique/quartiers-solidaires/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/antistatique/quartiers-solidaires/compare/2.2.3...2.3.0
[2.2.3]: https://github.com/antistatique/quartiers-solidaires/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/antistatique/quartiers-solidaires/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/antistatique/quartiers-solidaires/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/antistatique/quartiers-solidaires/compare/2.1.1...2.2.0
[2.1.1]: https://github.com/antistatique/quartiers-solidaires/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/antistatique/quartiers-solidaires/compare/2.0.0...2.1.0
[2.0.0]: https://github.com/antistatique/quartiers-solidaires/compare/v1.7.1...v2.0.0
