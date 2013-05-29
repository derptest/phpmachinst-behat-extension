Feature: Steps for single stores work correctly on SQLite

Given the following "sqlite_company" data exists:
| name  |
| pedro |

  Scenario: Wiping all data wipes all data
    When I wipe all "sqlite_company" data
    Then there is no "sqlite_company" data

