# Laravel Core
More descriptions will be updated soon.

### FILTERS - SELECT PARTICULAR FIELDS
**k** is keys, **r** is relation, **cOnly** is flag to set count is needed or the relational data

**cOnly** flag can be used in **r** relations nestedly

    {
      "cOnly": true,
      "k": [
        "id",
        "firstName",
        "lastName"
      ],
      "r": {
        "designation": {
          "k": [
            "id",
            "name",
            "departmentId"
          ],
          "r": {
            "department": {
              "k": [
                "id",
                "name"
              ],
              "r": null
            }
          }
        },
        "salaryBreakups": {
          "k": [
            "id"
          ],
          "r": null
        },
        "salaryBreakup": {
          "k": [],
          "r": null
        }
      }
    }

## Search
Need to send key `search_key` via query params along with value.
In repo, define config along with models & column names to be searched on.
**Usage:**

    $searchConfig = [  
        'abc' => [  
            'model' => Abc::class,  
            'keys' => ['name', 'field']  
        ],
        'groups' => [  
            'model' => ProductGroup::class,  
            'keys' => ['name', 'code', 'hsn_code']  
        ]
    ];  
    return $this->search($searchConfig, $params);

## License
Laravel-core is released under the MIT License. See the bundled [LICENSE](LICENSE) for details.