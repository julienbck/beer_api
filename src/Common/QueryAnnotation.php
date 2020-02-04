<?php


namespace App\Common;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class QueryAnnotation
 * @Annotation\Target("METHOD")
 * @package App\Common
 */
class QueryAnnotation
{
    /**
     * @Annotation\Required()
     *
     * @var string
     */
    public $name;

    /**
     * @Annotation\Required()
     *
     * @var string
     */
    public $type;

    /**
     * @Annotation\Required()
     *
     * @var string
     */
    public $requirements;

    /**
     * @Annotation\Required()
     * @var boolean
     */
    public $required = false;
}