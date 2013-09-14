Feature: Steps that affect all data work correctly

  Background:
    Given the following "mysql_company" data exists:
      | name  |
      | pedro |

    Given the following "sqlite_company" data exists:
      | name  |
      | pedro |

  Scenario: Wiping all data wipes all data
    When I wipe all data
    Then there is no "mysql_company" data
    Then there is no "sqlite_company" data

