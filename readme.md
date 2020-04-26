# Laravel Core Package
Luezoid came up with a compact way to help one creating the APIs very fast & without much hassle. Using this package, one case easily create simple CRUD operations in Laravel framework in couple of minutes with just creating a few files & configuring the components. With a lot of efforts and deep analysis of day-to-day problems (we) developers faced in the past, we came up with a sub-framework of it's own kind to simplify & fasten up the REST APIs building process.

A few cool features of this package are:
 1. Simplest & fastest way to create [CRUD](#1-creating-crud)s.
 2. Pre-built support to define table columns which are to be [specifically excluded](#2-exclude-columns-for-default-post--put-requests) before creating/updating a record(in default CRUD).
 3. Pre-built [Search & Filters](#3-searching--filters) ready to use with just configuring components.
 4. Pre-built [Pagination & Ordering](#4-pagination--ordering) of records ready.
 5. [Relationship's data](#5-relationships-data) in the APIs(GET) is just a config thing.
 6. Better way to correctly [fire an event](#6-attach-event-on-an-action-success) upon successful completion of an action.
 7. [File uploads](#7-file-upload) has never been so easy before. Upload to local filesystem or AWS S3 bucket on the go.
 8. Pre-built feature rich Service classes eg. [EnvironmentService](src/services/EnvironmentService.php), [RequestService](src/services/RequestService.php), [UtilityService](src/services/UtilityService.php), etc.
 9. Nested Related models can be queried with simple config based approach from the code components.
 10. [On the go `SELECT` & Relation filters can be passed with JSON & object operator(`->`) in query params](#10--select--relation-filters---select--where-query-over-table-columns--nested-relations) to select particular columns from a table(and related objects defined in models) making the API's response with less garbage data instead of writing custom query every time a new endpoint is created.
 11. [Generic/Open Search](#11-genericopen-search) over a set of related objects(tables) with simple Array based config.
>**Note:** For a complete working example of all these feature with core package pre-configured is available on this repository [luezoidtechnologies/laravel-core-base-repo-example](https://github.com/luezoidtechnologies/laravel-core-base-repo-example "laravel-core-base-repo-example").

## Installation
We recommend using [Composer](https://getcomposer.org) to install this package. This package supports [Laravel](https://laravel.com) versions >= 5.x.

    composer require "luezoid/laravel-core"		# For latest version of Laravel (>=7.x)
    composer require "luezoid/laravel-core:^6.0"	# For Laravel version 6.x
    composer require "luezoid/laravel-core:^5.0"	# For Laravel version 5.x
Next, configure your `app/Exceptions/Handler.php` and extend it with `Luezoid\Laravelcore\Exceptions\Handler`. Sample file can be seen [here](/examples/Exceptions/Handler.php).

## 1. Creating CRUD
Using this packages adds an extra entity between the Controller & Model paradigm in a normal MVC architecture. This extra entity we are referring here is called **Repository**. A **Repository** is a complete class where your whole business logic resides from getting the processed data from a **Controller** and saving it into database using **Model**(s).
By using **Repository** as an intermediate between **Controller** & **Model**, we aim at maintaing clean code at **Controller's** end and making it a mediator which only receives data(from View, typically a REST route), validate it against the defined validation rules(if any, we uses **Request** class to define such rules), pre-process it(for eg. transform ***camelCased*** data from front-end into ***snake_case***) & sending business cooked data back the View.

Let's start with creating a simple **Minions** CRUD.

We have sample [migration](examples/migrations/2020_04_24_175321_create_minions_table.php) for table `minions`, model [`Minion`](/examples/Models/Minion.php), controller [`MinionController`](/examples/Controllers/MinionController.php) and repository [`MinionRepository`](/examples/Repositories/MinionRepository.php).
Add these files into your application & adjust the namespaces accordingly. Then create a Route resource as below in [`routes/api.php`](examples/routes/api.php) and we are all ready:

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
 3. DELETE /minions/1

	    curl -X DELETE \
	      http://localhost:7872/api/minions/1 \
	      -H 'cache-control: no-cache'
 4. GET /minions

	    curl -X GET \
	      http://localhost:7872/api/minions \
	      -H 'cache-control: no-cache'
 5. GET /minions/2
 
     	curl -X GET \
    	  http://localhost:7872/api/minions/2 \
    	  -H 'cache-control: no-cache'

## 2. Exclude columns for default POST & PUT request(s)
Refer the [`Minon`](examples/Models/Minion.php "Minon") model. We have the below public properties which can be used to define an array containing list of the columns to be specifically excluded for default **POST** & **PUT** requests of [CRUD](#1-creating-crud "CRUD"):

	// To exclude the key(s) if present in request body for default POST CRUD routes eg. POST /minions
    public $createExcept = [
    	'id'
    ];

	// To exclude the key(s) if present in request body for default PUT CRUD routes eg. PUT /minions/1
    public $updateExcept = [
    	'total_eyes',
    	'has_hairs'
    ];
The major advantage for using such config in the model in the first place is: to provide a clean & elegant way & to simply reduce the coding efforts to be done just before saving the whole data into table. Typical examples could be:
1. You don't want to save a column value say ***is_email_verified*** if an attacker sends it the request body of POST /users; just add it into `$createExcept`. You need not to exclude this specifically in the codes/or request rules.
2. You don't want to update a column say ***username*** if an attacker sends it the request body of PUT /users/{id}; just add it into `$updateExcept`. You need not to exclude this specifically in the codes/or request rules.

## 3. Searching & Filters
You can simply search over the list of available column(s) in the table for all GET requests. Let's begin with examples:
- **General Searching**

    By default all the available columns in the tables are ready to be queried over GET request just by passing the key(s)-value(s) pair(s) in the query params. But to specifically mention it in the Model itsef, just define a public property `$searchable` which is an array containing the columns allowed to be searched.
Let's say you want to search for all minions whose favourite sound is ***Pchhhh***.

		curl -X GET \
		  'http://localhost:7872/api/minions?favouriteSound=Pchhhh' \
		  -H 'cache-control: no-cache'
    Response should contain all minions having ***Pchhhh*** string available in the column `favourite_sound` in the table `minions`.
    > Searching is using `LIKE` operator as `'%-SEARCH-STRING%'`.
- **General Filtering**

    Similar to searching, you need to define public property `$filterable` which is again an array containing the columns allowed to be filtered.

		public $filterable = [
		    'id',
		    'total_eyes',
		    'has_hairs'
		];
    Exact match will performed against these columns if present in the query params.

    Example: To find all the minions having `totalEyes` equal to 1:

    	curl -X GET \
    	  'http://localhost:7872/api/minions?totalEyes=1' \
    	  -H 'cache-control: no-cache'
    > Filtering is using `=` operator as `'total_eyes'=1`.
- **Date Filters**

    Add the column which you want to be used for date filtering into `$filterable` property in the model class. Once done, you can now simply pass the query params **from** (AND/OR) **to** with the date(or datetime) values in standard MySQL format(`Y-m-d H:i:s`).
Example: We want to search for all the minions which are created after **2020-04-25  09:25:20**:

    	curl -X GET \
    	  'http://localhost:7872/api/minions?createdAt=2020-04-25%20%2009:25:20' \
    	  -H 'cache-control: no-cache'
    You can also pass the column name in the query params over which you want the date search to be applied for. Just pass the key **dateFilterColumn** with the column name you want to use date search on (but note that the **$filterable** property must have this column specified in order to make things work).
> **Notes:**
>1. You may specify multiple key-value pairs in the query params & all the conditions will be queried with `AND` operators.
>2. Pass all the variables in **camelCasing** & all will be transferred into **snake_casing** internally. You may configure this transformation **turning off** by **overriding properties** `$isCamelToSnake` & `$isSnakeToCamel` and setting them to `false` in [ApiCotroller](src/Http/Controllers/ApiController.php "ApiCotroller").

## 4. Pagination & Ordering
Did you notice the response of GET endpoint we just created above? Let's take a look in brief. Refer the [response](examples/Responses/get-minions-paginated-response.json "response") of **GET /minions** API.

    {
      "message": null,
      "data": {
        "items": [
          {},
          {},
          ...
        ],
        "page": 1,      // tells us about the current page
        "total": 6,     // tells us the total results available (matching all the query params for searching/filtering applied)
        "pages": 1,     // total pages in which the whole result set is distributed
        "perpage": 15   // total results per page
      },
      "type": null
    }
Pretty self explanatory, eh?

You can pass query param `perpage=5` (to limit the per page size). Similarly, the `page=2` will grab the results of page 2.

For ordering the result set by a particular column, just send query param key `orderby` with the name of column & a separate key `order` with value `ASC` for ascending (or) `DESC` for sorting in descending order. By default, results are sorted in descending order.

Paginating & Ordering the results has never been so easy before :)
> **Note:** Any GET(index) route retrieving results from a Repository (eg.[MinionRepository](src/examples/Repositories/MinionRepository.php "MinionRepository")) extending `\Luezoid\Laravelcore\Repositories\EloquentBaseRepository::getAll()` is all ready with such pagination & ordering. Make sure to use this pre-built feature & save time for manually implementing these features for every endpoint & grab a pint of beer to chill.

## 5. Relationship's data
Let's assume each **Minion** leads a mission operated by **Gru** i.e. there is one-to-one relationship exists between Minion & Missions. See the `missions` table migrations([1](examples/migrations/2020_04_25_193714_create_missions_table.php),[2](examples/migrations/2020_04_25_193715_add_foreign_keys_to_missions_table.php)) and Model [`Mission`](examples/Models/Mission.php). To retrieve the leading mission by each Minion in GET requests(index & show), just add the relationship name in the [`MinionController`](/examples/Controllers/MinionController.php) properties as follows:
- GET /minions

      protected $indexWith = [
          'leading_mission'	// name of the hasOne() relationship defined in the Minion model
      ];
- GET /minions/2

      protected $showWith = [
          'leading_mission'	// name of the hasOne() relationship defined in the Minion model
      ];

That's it. Just a config thingy & you can see in the response each **Minion** object contains another object **leadingMission** which is an instance of [`Mission`](examples/Models/Mission.php) model lead by respective Minion.

> **Note:** For nested relationships, you can define them appending dot(.) operator eg. `employee.designations`.

## 6. Attach Event on an action success
Let's arrange an [Event](#) to get triggered to bring a **Minion** to **Gru's** lab whenever a new [`Mission`](examples/Models/Mission.php) is created leading by the **Minion**. Create a POST route in [`routes/api.php`](examples/routes/api.php):

    Route::post('missions', 'MissionController@createMission')->name('missions.store');

We need to have [`MissionController`](examples/Controllers/MissionController.php), [`MissionRepository`](examples/Repositories/MissionRepository.php), [`MissionCreateRequest`](examples/Requests/MissionCreateRequest.php) and [`MissionCreateJob`](examples/Jobs/MissionCreateJob.php) ready for this route to work.
Also we need to have an Event say [`BringMinionToLabEvent`](examples/Events/BringMinionToLabEvent.php) ready to be triggered & the same to be configured into job [`MissionCreateJob`](examples/Jobs/MissionCreateJob.php) under property `public $event = BringMinionToLabEvent::class;`
Now try hitting the route POST /missions as follows:

    curl -X POST \
      http://localhost:7872/api/missions \
      -H 'Content-Type: application/json' \
      -H 'cache-control: no-cache' \
      -d '{
    	"name": "Steal the Moon! Part - 4",
    	"description": "The first moon landing happened in 1969. Felonius Gru watched this historic moment with his mother and was inspired by the landing to go to outer space just like his idol Neil Armstrong.",
    	"minionId": 2
    }'
You should be able to see a log entry under file `storage/logs/laravel.log` which is the action we had set in the event [`BringMinionToLabEvent`](examples/Events/BringMinionToLabEvent.php).
## 7. File Upload
With this package, file upload is just a config thing away.
Publish the [`file.php`](src/config/file.php) configuration file to the config directory with below command:

	php artisan vendor:publish --tag=luezoid-file-config
Configure the new `type` representing a specific module eg. **MINION_PROFILE_PICTURE** as per your requirement, define the `validation`(if any), `valid_file_types` allowed, `local_path`(for local filesystem), etc. A sample `type` named **EXAMPLE** is added by default for reference.
Next, add the below code in `AppServiceProvide`:

    $this->app->bind(\Luezoid\Laravelcore\Contracts\IFile::class, function ($app) {
        if (config('file.is_local')) {
            return $app->make(\Luezoid\Laravelcore\Files\Services\LocalFileUploadService::class);
        }
        return $app->make(\Luezoid\Laravelcore\Files\Services\SaveFileToS3Service::class);
    });
Next, create a route as below:

	Route::post('files', '\Luezoid\Laravelcore\Http\Controllers\FileController@store')->name('files.store');
Now, you are all set to upload files.

    curl -X POST \
      http://localhost:7872/api/files \
      -H 'cache-control: no-cache' \
      -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW' \
      -F type=EXAMPLE \
      -F file=@/home/choxx/Desktop/a61113f5afc5ad52eb59f98ce293c266.jpg
The response body will have an `id`, `url`, and a few other fields representing the storage location & other details for the uploaded file. See sample [here](examples/Responses/file-upload-response.json). This `id` field you could use to store in your table as foreign key column & make `hasOne()` relation with the records & use them as per need.

Moreover, in the config itself you could configure if local filesystem has to be used for uploads or on the go AWS S3 bucket. For using S3 bucket, you need to configure AWS credentials in `.env` file:

    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=
Isn't is awesome? :)

## 10.  Select & Relation Filters - `SELECT` & `WHERE` query over table columns (& nested relations)
This is one of the coolest feature of this package. Tired off writing query to shorten up the result set & applying filters over nested relation data? Apply such filters with simplicity with just sending simple query params. Let's see how:

- **Select Filters:**

    In the query params, a key-value pair can be passed with which you could simply restrict the number of columns to be present in the response for a particular model object & it's related model entities. The key to be sent is `selectFilters` & the value has to be minified JSON as explained below:

    **k** is keys, **r** is relation, **cOnly** is flag to set if only count is needed or the whole relational data.
**cOnly** flag can be used in **r** relations nestedly.

      {
        "cOnly": false,
        "k": ["id", "name", "favouriteSound"],
        "r": {
          "missions": {
            "k": ["name", "description"]
          }
        }
      }
    So, with the above JSON, a GET request like:

      curl -X GET \
        'http://localhost:7872/api/minions?selectFilters={%22cOnly%22:false,%22k%22:[%22id%22,%22name%22,%22favouriteSound%22],%22r%22:{%22missions%22:{%22k%22:[%22name%22,%22description%22]}}}' \
        -H 'cache-control: no-cache'
    would return only the columns `id`, `name` & `favourite_sound` of table `minions` and columns `name` & `description` of table `missions` in the [response](examples/Responses/json-filters-applied-on-listing-api.json). Rest redundant columns which are not needed are eleminated from the response causing substantial reduction in the size of overall response & speeding up the response time of APIs.
    > **Note:** both the columns the '**local key**' & the '**foreign key**' must be present in the main & the related relation(s). This whole config can go as deeper as needed in the same way as the first relation goes.

- **Relation Filters:**

    Let's assume we wan to find all the minions whose **Leading Mission** name is **"Steal the Moon!"**. Using `Eloquent` queries we can retrieve such results as:

      $query = Minion::whereHas('missions', function ($q) {
          $q->where('name', 'Steal the Moon!');
      });
    Seems easy right? But wait, for this to be dynamic, you need to customarily pass the query param holding this `missions.name` column & manage yourself by writing custom logic to make such filtering work. But by using this package, you can simply do it with just sending the query params as we have seen in [Searching & Filters](#3-searching--filters). You just need to send the query params as **relation-name->column-name={string-to-be-filtered}**. See the below example:

      curl -X GET \
        'http://localhost:7872/api/minions?missions-%3Ename=Steal%20the%20Moon%21' \
        -H 'cache-control: no-cache'
    Notice that we have passed query param `missions-%3Ename=Steal%20the%20Moon%21` & hence reducing our effort of writing the above custom logic altogether.
    
    So, do we deserve a **star** rating? :)
    >**Note:** These Select & Relation filters can be combined together to reduce the response size as well as the size of bacck-end code. Make use of these two features combinely & make wonderful applications.

## 11. Generic/Open Search  
Need to send key `searchKey` via query params along with value.  
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
