<?php


namespace App\Common;


use http\Exception\BadQueryStringException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class QueryParamValidator
{
    /**
     * @var QueryAnnotationReader
     */
    private $queryAnnotationReader;

    public function __construct(QueryAnnotationReader $queryAnnotationReader)
    {

        $this->queryAnnotationReader = $queryAnnotationReader;
    }

    public function validate(Request $request, $classController, $method)
    {
        $queryParams = $request->query->all();
        $queryAnnotations = $this->queryAnnotationReader->readAnnotationMethod($classController, $method);

        foreach ($queryParams as $name => $param) {
            foreach ($queryAnnotations as $annotation) {
                if ($name == $annotation->name) {

                    // CHECK TYPE INTEGER
                    if ($annotation->type == "integer") {
                        if(!is_numeric($param)) {
                            throw new BadRequestHttpException('Invalid Parameters.'.$name.' must be integer');
                        }

                        if(!preg_match($annotation->requirements, intval($param))) {
                            throw new BadRequestHttpException('The parameter '.$name.' don\'t no match with regex ');
                        }
                    }

                    //CHECK TYPE STRING
                    if ($annotation->type == "string") {
                        if (!is_string($param)) {
                            throw new BadRequestHttpException('Invalid Parameters.' . $name . ' must be string');
                        }

                        if (is_string($param) && !(substr($param, 0, 1) === '/' && substr($param, 0, 1) === '(')) {
                            $values = explode("|", $annotation->requirements);
                            $check = in_array($param, $values);
                            if (!$check) {
                                throw new BadRequestHttpException('Invalid Parameters.'.$name.' must need to value: '.implode(" or ", $values));
                            }
                        }
                    }
                }
            }
        }
    }
}