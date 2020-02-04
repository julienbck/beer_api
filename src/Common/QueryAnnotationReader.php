<?php


namespace App\Common;


use Doctrine\Common\Annotations\Reader;

class QueryAnnotationReader
{

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function readAnnotationMethod($class, $method)
    {
        $reflecObjt = new \ReflectionObject($class);
        $reflecMethod = $reflecObjt->getMethod(str_replace("::", "", strstr($method, '::')));

        $methodsAnnotations = $this->reader->getMethodAnnotations($reflecMethod);

        $queryAnnotations = [];
        foreach ($methodsAnnotations as $annotation) {
            if ($annotation instanceof QueryAnnotation) {
                $queryAnnotations[] = $annotation;
            }
        }

        return $queryAnnotations;
    }
}