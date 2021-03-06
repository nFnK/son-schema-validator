<?php namespace CMPayments\SchemaValidator;

use CMPayments\Cache\Cache;

/**
 * Interface ValidatorInterface
 *
 * @package CMPayments\SchemaValidator
 * @Author  Boy Wijnmaalen <boy.wijnmaalen@cmtelecom.com>
 */
interface ValidatorInterface
{
    public function __construct($data, $schema, Cache $cache);
    public function validateData($data, $schema, $path = null);
    public function validate($schema, $property, $data, $path);
    public function validateSchema($schema, $path = null);
}