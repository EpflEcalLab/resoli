Feature: Collection of Offers Access
  In order to make sure ACL is working for Collection of Offers page
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario Outline: Logged as Member of Lausanne, I can access to any Lausanne offers collection
    Given I am logged in as user "member+lausanne"
    When I am on "<url>"
    And the response status code should be 200
    Examples:
      | url |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |

  @api
  Scenario Outline: Login as user waiting approval of Lausanne, I can't access to any Lausanne offers collection
    Given I am logged in as user "approval+lausanne"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |

  @api
  Scenario Outline: Logged as Member of Lausanne, I can't access to any Fribourg offers collection
    Given I am logged in as user "member+lausanne"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Logged as Manager of Lausanne, I can't access to any Fribourg offers collection
    Given I am logged in as user "manager+lausanne"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Logged as Organizer of Lausanne, I can't access to any Fribourg offers collection
    Given I am logged in as user "organizer+lausanne"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Logged as Member of Lausanne & Organizer of Fribourg I can access to any Lausanne & Fribourg offers collection
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "<url>"
    And the response status code should be 200
    Examples:
      | url |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Login as Multiple Privileges (Member of Fribourg & waiting approval Organizer for Fribourg), I can access to any Fribourg offers collection
    Given I am logged in as user "member+fribourg+approval+organizer+fribourg"
    When I am on "<url>"
    And the response status code should be 200
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Login as Multiple Privileges (Member & Organizer of Fribourg), I can access to any Fribourg offers collection
    Given I am logged in as user "member+fribourg+organizer+fribourg"
    When I am on "<url>"
    And the response status code should be 200
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Login as Multiple waiting approval (Member & Organizer for Fribourg), I can't access to any Fribourg offers collection
    Given I am logged in as user "approval+member+fribourg+approval+organizer+fribourg"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |

  @api
  Scenario Outline: Logged as Declined Organizer of Lausanne, I can't access to any Lausanne offers collection
    Given I am logged in as user "declined+organizer+lausanne"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |

  @api
  Scenario Outline: Logged as Declined Organizer of Lausanne but still a Member of Lausanne, I can access to any Lausanne offers collection
    Given I am logged in as user "member+lausanne+declined+organizer+lausanne"
    When I am on "<url>"
    And the response status code should be 200
    Examples:
      | url |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |

  @api
  Scenario Outline: Logged as Declined Organizer of Lausanne but still a Member of Fribourg, I can't access to any Lausanne offers collection
    Given I am logged in as user "member+fribourg+declined+member+lausanne"
    When I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |

  @api
  Scenario Outline: Logged as Declined Organizer of Lausanne but still a Member of Fribourg, I can access to any Fribourg offers collection
    Given I am logged in as user "member+fribourg+declined+member+lausanne"
    When I am on "<url>"
    And the response status code should be 200
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |
