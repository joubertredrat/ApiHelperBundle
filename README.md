# Api Helper Bundle

[![Build Status](https://travis-ci.org/joubertredrat/ApiHelperBundle.svg?branch=master)](https://travis-ci.org/joubertredrat/ApiHelperBundle)
[![Maintainability](https://api.codeclimate.com/v1/badges/b45a7ebaf29e793ea918/maintainability)](https://codeclimate.com/github/joubertredrat/ApiHelperBundle/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b45a7ebaf29e793ea918/test_coverage)](https://codeclimate.com/github/joubertredrat/ApiHelperBundle/test_coverage)
[![Latest Stable Version](https://poser.pugx.org/redrat/api-helper-bundle/v)](https://packagist.org/packages/redrat/api-helper-bundle)
[![Total Downloads](https://poser.pugx.org/redrat/api-helper-bundle/downloads)](https://packagist.org/packages/redrat/api-helper-bundle/stats)
[![License](https://poser.pugx.org/redrat/api-helper-bundle/license)](https://packagist.org/packages/redrat/api-helper-bundle)


This Symfony bundle provides configuration to validate and convert data for API routes.

#### First question, why?

Because I needed a bundle with configurable url prefix to use as API routes. For default in Symfony, only requests with content type `multipart/form-data` and `application/x-www-form-urlencoded` will have Request class with parameters data easily accessible by `get()` method, as example below.

```php
class MyController
{
    public function handleForm(Request $request): Response
    {
        $myName = $request->get('myName'); // return Joubert RedRat
    }

    public function handleApi(Request $request): Response
    {
        $myName = $request->get('myName'); // return null
    }
}
```

With this bundle will be possible to configure which routes will work as API routes and any request with content type `application/json` will have Request class with parameters data easily accessible too.

#### Okay, how to install then?

Easy my friend, install with the composer.

```bash
composer require redrat/api-helper-bundle
```

#### Configure the bundle (if necessary)

If your composer don't like Symfony recipes contrib, don't worry, you can configure too.

* Open `config/bundles.php` and add the bundle like below.

```php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    RedRat\ApiHelperBundle\ApiHelperBundle::class => ['all' => true],
];
```

* Create `config/packages/redrat_api_helper.yaml` and set initial config like below.

```yaml
redrat_api_helper:
    paths:
```

#### How to bundle works?

This bundle works with a configuration defined in `config/packages/redrat_api_helper.yaml`. Into this file you will config which url path prefix will work as API url, like example below:

```yaml
redrat_api_helper:
    paths:
        my_amazing_api_v1:
            url_path_prefix: /api/v1
        other_api_v3:
            url_path_prefix: /api/v3
        old_not_cute_api:
            url_path_prefix: /legacy/api
```

You can configure one or more url path prefixes as you need.

After this, all url path that matches with a configuration will be acted by the bundle and will be validated and data putted into request class.

Look that only routes that matches will be acted by the bundle, like example below using configuration above.

```
GET /api/v1/accounts/users => matches
POST /api/login => doesn't matches
DELETE /api/v2/emails => doesn't matches
PUT /api/v1/accounts/users/46 => matches
PATCH /api/v1/accounts/users => matches
GET /about-us => doesn't matches
GET /legacy/api/v1/cities => matches
```

### Validations

This bundle performs 2 validations in a route matched with configuration.

##### Content-type

Route matched should have `application/json` as Content-Type with methods `POST`, `PUT` and `PATCH`.
If not pass by this validation, bundle will return error below.
Methods `GET` and `DELETE` normally don't contain body data, then these methods isn't validated.

```
< HTTP/2 400

{
  "error": "Invalid Content-Type, expected application\/json, got application\/x-www-form-urlencoded"
}
```
##### Valid json data

Route matched should have valid [RFC7159](http://www.faqs.org/rfcs/rfc7159.html) json data in body.
If not pass by this validation, bundle will return error below.

```
< HTTP/2 400

{
  "error": "Invalid json body data"
}
```

### Author

[Me](https://github.com/joubertredrat) and the [contributors](https://github.com/joubertredrat/ApiHelperBundle/graphs/contributors).

### License

The cute and amazing [MIT](https://github.com/joubertredrat/ApiHelperBundle/blob/master/LICENSE).