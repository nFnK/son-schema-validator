<?php namespace CMPayments\Exception;

/**
 * Class BaseException
 *
 * @package CMPayments\SchemaValidator\Exceptions
 * @Author  Boy Wijnmaalen <boy.wijnmaalen@cmtelecom.com>
 */
class BaseException extends \ErrorException
{
    private $args = [];

    private $defaultMessage = 'This service is temporarily unavailable';

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     */
    public function setArgs($args)
    {
        // check is $args is an array, if not, cast it into an array
        if (!is_array($args)) {

            $args = [$args];
        }

        $this->args = $args;
    }

    /**
     * BaseException constructor.
     *
     * @param string $code
     * @param array  $args
     * @param null   $message
     */
    public function __construct($code, $args = [], $message = null)
    {
        $this->setArgs($args);

        // parent constructor
        parent::__construct($this->getItemFromVariableArray($code, $message), $code);
    }

    /**
     * Retrieves a specific array key from a class constant
     *
     * @param int    $code
     * @param null   $default
     * @param string $msgArray
     *
     * @return null|string
     */
    public function getItemFromVariableArray($code, $default = null, $msgArray = 'messages')
    {
        $messages = [];
        if (isset($this->$msgArray)) {

            $messages = $this->$msgArray;
        }

        if (empty($default)) {

            if (!isset($messages[$code])) {

                $default = $this->defaultMessage;
            } else {

                $default = vsprintf($messages[$code], $this->getArgs());
            }
        }

        return $default;
    }

    /**
     * PHP 5.4 workaround to something like this
     *
     * @return string
     */
    public static function getClassName(){

        return get_called_class();
    }
}
