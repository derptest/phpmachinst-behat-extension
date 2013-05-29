Feature: Steps for single stores work correctly on MySQL

Given the following "mysql_company" data exists:
| name  |
| pedro |

  Scenario: Wiping all data wipes all data
    When I wipe all "mysql_company" data
    Then there is no "mysql_company" data

