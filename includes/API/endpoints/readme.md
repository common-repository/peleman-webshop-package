# endpoints
folder containing endpoint classes for the REST API

a standard REST API allows for four operations:

* `GET` - get data from the API without changing it.
    in this context, `GET` is handled by two abstract classes:
    * `PWP_Abstract_READ_Endpoint`: will get multiple entries from the database
    * `PWP_Abstract_FIND_Endpoint`: will try to find one specific entry in the database

* `POST` - upload data to the API, to create a new entry. In this context handled by `PWP_Abstract__CREATE_Endpoint`

* `PUT` - try to alter the data of an existing entry in the API system. handled by `PWP_Abstract_UPDATE_Endpoint`

* `DELETE` - remove an existing entry in the system. handled by `PWP_Abstract_DELETE_Endpoint`


## PWP_I_Endpoint
interface for API endpoints

## PWP_Endpoint
abstract class for API endpoints. Contains necessary functionality for the proper operation of all endpoints.

Endpoints are intended to work with `PWP_I_Handler` classes, but there is no concrete requirement for this; all logic could easily be handled within the endpoint itself. This is not recommended.

API Endpoints can have up to five endpoints:
### __GET__
retrieve multiple or a single item at the endpoint

```php
    get_item(WP_REST_Request $request): WP_REST_Response
```
```php
    get_items(WP_REST_Request $request): WP_REST_Response
```

### __POST__
create a new item at the endpoint

```php
    create_item(WP_REST_Request $request): WP_REST_Response
```

### __PUT__/__PATCH__
update an existing item at the endpoint

```php
    update_item(WP_REST_Request $request): WP_REST_Response
```

### __DELETE__
delete an existing item at the endpoint

```php
    delete_item(WP_REST_Request $request): WP_REST_Response
```

### __BATCH__
batch items to post/update/delete. goes up to 100 items max

```php
    batch_items(WP_REST_Request $request): WP_REST_Response
```
to batch a series of functions, call a `POST` request with an array in the body containing 3 sub-arrays:

* __create__ (matches `POST` request)
* __update__ (matches `PUT`| `PATCH` request)
* __delete__ (matches `DELETE` request)

    _each of these should follow the parameter structure of their respective request._
___
# abstract endpoints

## PWP_Abstract_CREATE_Endpoint
- Tries to create a single item. Matches `POST`
___
## PWP_Abstract_FIND_Endpoint
- Tries to retrieve a single item based on an identifier. Matches `GET`
___
## PWP_Abstract_READ_Endpoint
- Tries to return multiple products based on a series of arguments. Matches `GET`
___
## PWP_Abstract_UPDATE_Endpoint
- Tries to update a single item based on an identifier. Matches `PUT`, `UPDATE`, and `POST`
___
## PWP_Abstract_DELETE_Endpoint
- Tries to delete a single item based on an identifier. Matches `DELETE`


___
## PWP_Images_Endpoint
- Returns one or multiple products.

        {{domain}}/wp-json/pwp/v1/Images

    `GET` | `POST`


___
### PWP_Menus_Endpoint
- Returns menus/mega menus

        {{domain}}/wp-json/pwp/v1/menus

    `GET`

