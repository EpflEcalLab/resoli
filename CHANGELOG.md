# CHANGELOG

## NEXT RELEASE

## 1.5.5 (2020-11-19)
 - update Drupal from 8.8.4 to 8.9.3 with all dependencies
 - update Drupal from 8.9.3 to 8.9.7
 - update Drupal from 8.9.7 to 8.9.8 with all dependencies
 - update drupal/image_effects (2.3.0 => 3.0.0) with all dependencies
 - update to Node 10
 - improve the Event Export into Excel file styling #763
 - add a captcha on subscription form to avoid spam #768
 - update Drupal from 8.9.8 to 8.9.9 (SA-CORE-2020-012)

## 1.5.4 (2020-10-19)
 - Apply patch to be compatible with Apache 2.4 and avoid double compression with brotli

## 1.5.3 (2020-04-15)
 - update composer lock
 - update composer used in Alwaysdata from 1.5.5 -> 1.10.5

## 1.5.2 (2020-04-15)
 - update Drupal from 8.7.x to 8.8 - #753
 - update modules - #750
 - add NewRelic module

## 1.5.1 (2020-04-01)
 - fix end-of-year previous button - close #747
 - fix date pager bugged on february - close #758

## 1.5.0 (2020-01-08)
 - update to Drupal 8.7.11
 - fix dual step bug when multiple forms - close #748
 - rebuild New Relic for PHP 7.2 on Alwaysdata

## 1.4.4 (2019-10-11)
 - remove InlineErrorFormTrait to use Core Inline Form Error - close #702

## 1.4.3 (2019-09-25)
 - improve responsivness especially smartphones visual design - close #741

## 1.4.2 (2019-09-18)
 - fix fade-out of parent cards on ajax-submitted form - close #739

## 1.4.1 (2019-07-25)
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

## 1.4.0 (2019-06-29)
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

## 1.3.4 (2019-05-13)
 - fix mail layout paragraphs issue - close #691

## 1.3.3 (2019-05-02)
 - fix IE11 - close #687
 - add date of event on 'My subscriptions' page - close #688

## 1.3.2 (2019-03-27)
 - fix tests process on Codeship

## 1.3.1 (2019-03-27)
 - fix masquerade button on navigation - close #684
 - babelify the subscription.js which was uncompatible with IE or some Apple device - close #683
 - update phpunit workflow & scripts
 - add base for future PHPUnit tests
 - fix EventManager::getNext - close #647

## 1.3.0 (2019-02-18)
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

## 1.2.2 (2018-11-07)
 - add new tests bash files
 - get the true next events - close #647

## 1.2.1 (2018-11-07)
 - remove the baseline of "Pro Senectute" on sponsors.svg

## 1.2.0 (2018-08-21)
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

## 1.1.4 (2018-07-05)
 - add german (de) language
 - make Taxonomy "Themes" translatable with fallback in Default Site Lang (FR) - #627
 - update logo - #629

## 1.1.3 (2018-06-28)
 - add mailjet as SMTP mail provider.
 - update to Drupal 8.5.4

## 1.1.2 (2018-05-14)
 - update to Drupal 8.5.3
 - fix IE 11 regression, sticky polyfill crash #620

## 1.1.1 (2018-05-02)
 - add white border to btn-info buttons #598
 - add spacing below visibility buttons in ActivityAddForm #398

## 1.1.0 (2018-04-26)
 - add the sponsors on homepage
 - change confirmed icon for event registering #598
 - increase size of activity in activity user collection and in photos collection #545
 - fix card pill flag position on medium and small screens #588
 - make all cards close when opening one in calendar view #596
 - add Privilege's Badge on Activity by theme #318,  #315 & #316
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
 - add Subscription's Badge, using highest Privilege color,  on Calendar Weekly/Monthly Dots #386
 - update Subscripton's Badge, using hiest Privilege color, on Activity page #309 & #309
 - fix #590 - wrong badges on activities by Theme when activity has event in past with pending subscriptions
 - fix #207 - photos by months - sticky months are not translated
 - fix #584 - fix big_pipe google map autocomplete fields
 - add Masquerade module
 - fix pager #610 & #31 - following pagers wasn't working: Members of Activity, Members of Community & Form ActivityInlineAddMember

## 1.0.4 (2018-04-26)
 - Apply Remote Code Exectiion - SA-CORE-2018-004

## 1.0.3 (2018-03-28)
 - apply patch - Remote Code Execution - SA-CORE-2018-002

## 1.0.2 (2018-03-16)
 - setup pagination component #31

## 1.0.1 (2018-03-01)
 - fix images upload on past events - #576
 - update README.md badges

## 1.0.0 (2018-02-26)
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

## 0.1.1 (2018-02-21)
 - add Google Tag Manager

## 0.1.0 (2017-12-13)
 - production deployment

## 0.0.0 (2017-07-14)
 - init empty repo
