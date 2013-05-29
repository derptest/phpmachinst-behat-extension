Feature: Steps for single stores work correctly on Mongo

  Background:
    Given the following "mongo_company" data exists:
      | name     |
      | pedro    |
      | napoleon |

  Scenario: The data exists
    Then the following "mongo_company" data is found:
      | name  |
      | pedro |

  Scenario: The data exists
    Then only the following "mongo_company" data is found:
      | name  |
      | pedro |

  Scenario: Wiping all data wipes all data
    When I wipe all "mongo_company" data
    Then there is no "mongo_company" data

