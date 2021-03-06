# Schema Validator for PHP [![Build Status](https://img.shields.io/travis/cmpayments/json-schema-validator.svg)](https://travis-ci.org/cmpayments/json-schema-validator)

![License](https://img.shields.io/packagist/l/cmpayments/schemavalidator.svg)
[![Latest Stable Version](https://img.shields.io/packagist/v/cmpayments/schemavalidator.svg)](https://packagist.org/packages/cmpayments/schemavalidator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cmpayments/json-schema-validator/badges/quality-score.png)](https://scrutinizer-ci.com/g/cmpayments/json-schema-validator/)
[![Total Downloads](https://img.shields.io/packagist/dt/cmpayments/schemavalidator.svg)](https://packagist.org/packages/cmpayments/schemavalidator)
[![Reference Status](https://www.versioneye.com/php/cmpayments:schemavalidator/reference_badge.svg)](https://www.versioneye.com/php/cmpayments:schemavalidator/references)

SchemaValidator is a PHP implementation for validating JSON against a Schema (also a string), the JSON and Schema are both linted with https://github.com/cmpayments/jsonlint.
This library is optimized for speed and performance.

Installation
------------
For a quick install with Composer use:

    $ composer require cmpayments/schemavalidator

Schema Validator for PHP can easily be used within another app if you have a
[PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
autoloader, or it can be installed through [Composer](https://getcomposer.org/).

Usage
-----

```
// use the required classes

use CMPayments\Cache\Cache;
use CMPayments\Json\Json;
use CMPayments\SchemaValidator\SchemaValidator;;
```

### Example #1 - simple; validate JSON input for syntax errors

```
$data = '{"testProperty": {"length": 7.0, "superfluousProperty": 12.0}}';

// the constructor only accepts Strings as input
$json = new Json($data);

// validate() returns a boolean whether the $data input is valid JSON or not
$isValid = $json->validate(); // $isValid = true since we only want to know if $data is valid JSON or not (we are not validating any schema's yet)

var_dump('Example 1:', $isValid); // true
```

### Example #2 - advanced; validate JSON input for syntax errors and validate the JSON against a schema

```
$data   = '{"testProperty": {"length": 7, "superfluousProperty": 12.0}}';
$schema = '{"type": "object","properties": {"testProperty": {"type": "object","properties": {"length": {"type": "number"}}}},"additionalProperties": true}';

$json = new Json($data);

// validate()' first argument only accepts Strings , the second parameter is a passthru parameter which picks up error along the way (if any)
$isValid = $json->validate($schema, $errors);

if ($isValid) { // $isValid = true since additional properties are now allowed

    // Get the decoded version of the $input (which was a String when inputted)
    var_dump('Example 2:', $json->getDecodedJSON()); // returns:

//    object(stdClass)[11]
//      public 'testProperty' =>
//        object(stdClass)[10]
//          public 'length' => float 7
//          public 'superfluousProperty' => float 12
} else {

    // in case $isValid should be false, $errors is the passthru variable and it now contains an array with all the errors that occurred.
    var_dump($errors);
}

// example data for example 3 & 4
$data   = '{"testProperty": {"length": 7.0, "superfluousProperty": 12.0}}';
$schema = '{"type": "object","properties": {"testProperty": {"type": "object","properties": {"length": {"type": "number"}}}},"additionalProperties": false}';
```

### Example #3 - Simple SchemaValidator example; if you are not interested if the JSON (string) is valid or not (since the input is an object)

```
// SchemaValidator constructor:
// first argument is the JSON input and only accepts it when it is an object (mandatory)
// second argument is the Schema input and only accepts it when it is an object (mandatory)
// third argument must be an instance of Cache() (optional)
try {

    $validator = new SchemaValidator(json_decode($data), json_decode($schema));

    if (!$validator->isValid()) { // returns false again additional properties are not allowed

        var_dump('Example 3:', $validator->getErrors()); // returns:

//        array(size = 1)
//          0 =>
//            array(size = 3)
//              'code' => int 101
//              'args' => string '/testProperty/superfluousProperty' (length = 33)
//              'message' => string 'The Data property ' / testProperty / superfluousProperty' is not an allowed property' (length = 80)
    }
} catch (\Exception $e) {

    var_dump('Example 3:', $e->getMessage());
}
```

### Example #4 - Advanced SchemaValidator example; if you are not interested if the JSON (string) is valid or not (since the input is an object) but you want to specify some caching options

```
try {

    // create new Cache object
    $cache = new Cache();

    // There are currently 2 cache options that can be set
    // 'debug' (boolean), if true you'll be notified if your cache directory is writable or not (any validation will be considered false when the cache directory is not writable and debug is true).
    // 'directory' (string), the absolute location where the cached schema's should be stored, by default this is '/src/CMPayments/SchemaValidator/cache/'
    $cache->setOptions(['debug' => true, 'directory' => 'absolute/path/to/your/cache/directory']); // currently this does not exist yet

    $validator = new SchemaValidator(json_decode($data), json_decode($schema), $cache);

    if (!$validator->isValid()) { // returns false again additional properties are not allowed but firstly because the current cache directory 'absolute/path/to/your/cache/directory' is not writable (since it doesn't exist).

        var_dump('Example 4:', (!$validator->isValid() ? 'false' : 'true'));
        //var_dump($validator->getErrors());
    }
} catch (\Exception $e) {

    var_dump('Example 4: ', $validator->isValid(), $e->getMessage()); // false, The cache directory 'absolute/path/to/your/cache/directory' is not writable
}
```

### Other examples

#### Example; test input when input is an array

```
try {

    $data   = ["length" => 7.0, "superfluousProperty" => 12.0];
    $schema = '{"type": "array","items": {"type": "number"}}';

    $validator = new SchemaValidator((($data)), json_decode($schema));

    var_dump('Example 5:', $validator->isValid()); // true
    //var_dump($validator->getErrors());
} catch (\Exception $e) {

    var_dump('Example 5', $e->getMessage());
}
```

#### Example; test input when input is a boolean

```
try {

    $data   = true;
    $schema = '{"type": "boolean"}';

    $validator = new SchemaValidator((($data)), json_decode($schema));

    var_dump('Example 6:', $validator->isValid());  // true
//    var_dump($validator->getErrors());
} catch (\Exception $e) {

    var_dump('Example 6:', $e->getMessage());
}
```

#### Example; test input when input is a number (float)

```
try {

    $data   = 1.4;
    $schema = '{"type": "boolean"}';

    $validator = new SchemaValidator((($data)), json_decode($schema));

    var_dump('Example 7:', $validator->isValid());  // false
    var_dump($validator->getErrors()); // message reads: The Data property '/' needs to be a 'boolean' but got a 'number' (with value '1.4')
} catch (\Exception $e) {

    var_dump('Example 7:', $e->getMessage());
}
```

#### Example; test input when input is a number (integer)

```
try {

    $data   = 22;
    $schema = '{"type": "number"}';

    $validator = new SchemaValidator((($data)), json_decode($schema));

    var_dump('Example 8:', $validator->isValid());  // true
    //var_dump($validator->getErrors());
} catch (\Exception $e) {

    var_dump('Example 8:', $e->getMessage());
}
```

#### Example; test input when input is a string

```
try {

    $data   = 'test12345';
    $schema = '{"type": "string"}';

    $validator = new SchemaValidator((($data)), json_decode($schema));

    var_dump('Example 9:', $validator->isValid());  // true
//    var_dump($validator->getErrors());
} catch (\Exception $e) {

    var_dump('Example 9:', $e->getMessage());
}

// Example; simple one-line example
$isValid = (new Json('true'))->validate(null, $errors);
(var_dump('Example 10: ', $isValid, $errors));
```

Requirements
------------

- PHP 5.4+
- [optional] PHPUnit 3.5+ to execute the test suite (phpunit --version)

Submitting bugs and feature requests
------------------------------------

Bugs and feature request are tracked on [GitHub](https://github.com/cmpayments/schemavalidator/issues)

Todo
----

- [ ] [Pattern Validation](http://json-schema.org/latest/json-schema-validation.html#anchor33)
- [ ] [allOf (Validation keywords for any instance type)](http://json-schema.org/latest/json-schema-validation.html#anchor82)
- [ ] [anyOf (Validation keywords for any instance type)](http://json-schema.org/latest/json-schema-validation.html#anchor85)
- [ ] [allOf (Validation keywords for any instance type)](http://json-schema.org/latest/json-schema-validation.html#anchor88)
- [ ] [not (Validation keywords for any instance type)](http://json-schema.org/latest/json-schema-validation.html#anchor91)
- [ ] [title & description Metadata](http://json-schema.org/latest/json-schema-validation.html#anchor98)
- [ ] [hostname (Defined Format Attributes)](http://json-schema.org/latest/json-schema-validation.html#anchor114)
- [ ] [ipv4 (Defined Format Attributes)](http://json-schema.org/latest/json-schema-validation.html#anchor117)
- [ ] [ipv6 (Defined Format Attributes)](http://json-schema.org/latest/json-schema-validation.html#anchor120)
- [ ] [uri (Defined Format Attributes)](http://json-schema.org/latest/json-schema-validation.html#anchor123)

Author
------

Boy Wijnmaalen - <boy.wijnmaalen@cmtelecom.com> - <https://twitter.com/boywijnmaalen>

License
-------

Schema Validator is licensed under the MIT License - see the LICENSE file for details
