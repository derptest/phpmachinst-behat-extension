Feature: Steps for single stores work correctly on MySQL

  Background:
    Given the following "mysql_company" data exists:
      | name      |
      | Coupla    |
      | LaunchKey |

    Given the following "mysql_user" data exists:
      | username | mysql_company   |
      | Adam     | name: Coupla    |
      | Devin    | name: LaunchKey |

  Scenario: The company data exists
    Then the following "mysql_company" data is found:
      | name   |
      | Coupla |

  Scenario: Only the provided company data exists
    Then only the following "mysql_company" data is found:
      | name      |
      | Coupla    |
      | LaunchKey |

  Scenario: The user data exists
    Then the following "mysql_user" data is found:
      | username |
      | Adam     |

  Scenario: Only the provided user data exists
    Then only the following "mysql_user" data is found:
      | username |
      | Adam     |
      | Devin    |

  Scenario: Find user data by company
    Then the following "mysql_user" data is found:
      | mysql_company |
      | name: Coupla  |

  Scenario: Wiping all data wipes all data
    When I wipe all "mysql_company" data
    And I wipe all "mysql_user" data
    Then there is no "mysql_company" data
    And there is no "mysql_user" data

