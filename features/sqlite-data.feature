Feature: Steps for single stores work correctly on SQLite

  Background:
    Given the following "sqlite_company" data exists:
      | name      |
      | Coupla    |
      | LaunchKey |

    Given the following "sqlite_user" data exists:
      | username | sqlite_company   |
      | Adam     | name: Coupla    |
      | Devin    | name: LaunchKey |

  Scenario: The company data exists
    Then the following "sqlite_company" data is found:
      | name   |
      | Coupla |

  Scenario: Only the provided company data exists
    Then only the following "sqlite_company" data is found:
      | name      |
      | Coupla    |
      | LaunchKey |

  Scenario: The user data exists
    Then the following "sqlite_user" data is found:
      | username |
      | Adam     |

  Scenario: Only the provided user data exists
    Then only the following "sqlite_user" data is found:
      | username |
      | Adam     |
      | Devin    |

  Scenario: Find user data by company
    Then the following "sqlite_user" data is found:
      | sqlite_company |
      | name: Coupla  |

  Scenario: Wiping all data wipes all data
    When I wipe all "sqlite_company" data
    Then there is no "sqlite_company" data

  Scenario: Wiping all data wipes all data
    When I wipe all "sqlite_company" data
    And I wipe all "sqlite_user" data
    Then there is no "sqlite_company" data
    And there is no "sqlite_user" data
