# Laravel Core Package
Luezoid came up with a compact way to help one creating the APIs very fast & without much hassle. Using this package, one case easily create simple CRUD operations in Laravel framework in couple of minutes with just creating a few files & configuring the components. With a lot of efforts and deep analysis of day-to-day problems (our) developers faced in the past, we came up with a sub-framework of it's own kind to simplify & fasten up the REST APIs creating process.
A few cool features of this package are:
 1. Simplest & fastest way to create [CRUD](#creating-crud)s.
 2. Pre-built support to define table columns which are to be specifically excluded before creating/updating a record.
 3. Pre-built Search & Filter queries ready to use with just configuring components.
 4. Pre-built Pagination ready.
 5. Relationship's data in the APIs is just a config thing.
 6. Better way to correctly fire an event upon successful completion of an action.
 7. File uploads has never been easy before.
 8. Pre-built feature rich Service classes eg. [EnvironmentService](src/services/EnvironmentService.php), [RequestService](src/services/RequestService.php), [UtilityService](src/services/UtilityService.php), etc.
 9. Nested Related models can be queried with simple config based approach from the code components.
 10. On the go filters can be passed as JSON in query params to select particular columns from a table(and related objects defined in models) making the API's response with less garbage data instead of writing custom query every time a new endpoint is created.
 11. On the go searching over the related objects with simple Array based config. Much more effective when a generic search has to be made over a couple of related tables.

## Installation
We recommend using [Composer](https://getcomposer.org) to install this package. This package supports [Laravel](https://laravel.com) versions >= 5.x.

    composer require "luezoid/laravel-core"		# For latest version of Laravel (>=7.x)
    composer require "luezoid/laravel-core:^6.0"	# For Laravel version 6.x
    composer require "luezoid/laravel-core:^5.0"	# For Laravel version 5.x
Next, configure your `app/Exceptions/Handler.php` and extend it with `Luezoid\Laravelcore\Exceptions\Handler`. Sample file can be seen [here](/examples/Exceptions/Handler.php).

## Creating CRUD
Using this packages adds an extra entity between the Controller & Model paradigm in a normal MVC architecture. This extra entity we are referring here is called **Repository**. A **Repository** is a complete class where your whole business logic resides from getting the processed data from a **Controller** and saving it into database using **Model**(s).
By using **Repository** as an intermediate between **Controller** & **Model**, we aim at maintaing clean code at **Controller's** end and making it a mediator which only receives data(from View, typically a REST route), validate it against the defined validation rules(if any, we uses **Request** class to define such rules), pre-process it(for eg. transform ***camelCased*** data from front-end into ***snake_case***) & sending business cooked data back the View.

Let's start with creating a simple **Minions** CRUD.

We have sample [migration](examples/migrations/2020_04_24_175321_create_minions_table.php) for table `minions`, model [`Minion`](/examples/Models/Minion.php), controller [`MinionController`](/examples/Controllers/MinionController.php) and repository [`MinionRepository`](/examples/Repositories/MinionRepository.php).
Create a Route resource as below and we are all ready:

    Route::resource('minions', 'MinionController', ['parameters' => ['minions' => 'id']]);
Assuming your local server is running on port: 7872, try hitting REST endpoints as below:

 1. POST /minions

	     curl -X POST \
	      http://localhost:7872/api/minions \
	      -H 'Content-Type: application/json' \
	      -H 'cache-control: no-cache' \
	      -d '{
	    	"name": "Stuart",
	    	"totalEyes": 2,
	    	"favouriteSound": "Grrrrrrrrrrr",
	    	"hasHairs": true
	    }'
You should see the response like:
		{
			"message": "Resource Created successfully",
			"data": {
				"name": "Stuart",
				"total_eyes": 2,
				"favourite_sound": "Grrrrrrrrrrr",
				"has_hairs": true,
				"updated_at": "2020-04-25T09:15:18.000000Z",
				"created_at": "2020-04-25T09:15:18.000000Z",
				"id": 1
			},
			"type": null
		}

 2. PUT /minions/1

	    curl -X PUT \
	      http://localhost:7872/api/minions/1 \
	      -H 'Content-Type: application/json' \
	      -H 'cache-control: no-cache' \
	      -d '{
	    	"name": "Stuart - The Pfff",
	    	"totalEyes": 2,
	    	"favouriteSound": "Grrrrrrrrrrr Pffffff",
	    	"hasHairs": false
	    }'
You should see the response like:
		{
			"message": "Resource Updated successfully",
			"data": {
				"id": 2,
				"name": "Stuart - The Pfff",
				"total_eyes": 2,
				"favourite_sound": "Grrrrrrrrrrr Pffffff",
				"has_hairs": false,
				"created_at": "2020-04-24T18:58:41.000000Z",
				"updated_at": "2020-04-24T19:04:16.000000Z"
			},
			"type": null
		}

 3. DELETE /minions/1

	    curl -X DELETE \
	      http://localhost:7872/api/minions/1 \
	      -H 'cache-control: no-cache'
You should see the response like:
		{
			"message": "Resource deleted successfully",
			"data": {
				"id": 1,
				"name": "Stuart - The Pfff",
				"total_eyes": 2,
				"favourite_sound": "Grrrrrrrrrrr Pffffff",
				"has_hairs": true,
				"created_at": "2020-04-25T09:24:38.000000Z",
				"updated_at": "2020-04-25T09:24:42.000000Z"
			},
			"type": null
		}
 4. GET /minions

	    curl -X GET \
	      http://localhost:7872/api/minions \
	      -H 'cache-control: no-cache'
You should see the response like:
		{
			"message": null,
			"data": {
				"items": [
					{
						"id": 3,
						"name": "Hector",
						"totalEyes": 1,
						"favouriteSound": "Shhhhhhhhhhh",
						"hasHairs": false,
						"createdAt": "2020-04-25T09:25:26.000000Z",
						"updatedAt": "2020-04-25T09:25:26.000000Z"
					},
					{
						"id": 2,
						"name": "Dave",
						"totalEyes": 1,
						"favouriteSound": "Hmmmmmmmmmmmmm Pchhhhhh",
						"hasHairs": true,
						"createdAt": "2020-04-25T09:25:04.000000Z",
						"updatedAt": "2020-04-25T09:25:04.000000Z"
					}
				],
				"page": 1,
				"total": 2,
				"pages": 1,
				"perpage": 15
			},
			"type": null
		}

 5. GET /minions/2

	    curl -X GET \
	      http://localhost:7872/api/minions/2 \
	      -H 'cache-control: no-cache'
You should see the response like:
		{
			"message": null,
			"data": {
				"id": 2,
				"name": "Dave",
				"total_eyes": 1,
				"favourite_sound": "Hmmmmmmmmmmmmm Pchhhhhh",
				"has_hairs": true,
				"created_at": "2020-04-25T09:25:04.000000Z",
				"updated_at": "2020-04-25T09:25:04.000000Z"
			},
			"type": null
		}

>**Note:** For the working complete example of this CRUD with core package pre-configured is available on this repository [luezoidtechnologies/laravel-core-base-repo](https://github.com/luezoidtechnologies/laravel-core-base-repo "laravel-core-base-repo").
  
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
      'groups' => ['model' => ProductGroup::class,  
      'keys' => ['name', 'code', 'hsn_code']  
     ]];  
    return $this->search($searchConfig, $params);

  
## License  
Laravel-core is released under the MIT License. See the bundled [LICENSE](LICENSE) for details.
