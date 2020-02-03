<?php


namespace App\Controller\Rest;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface RestInterface
{

    public function getCollectionEntity($className) :JsonResponse;

    public function getOne(Request $request) :JsonResponse;

    public function post(Request $request) :JsonResponse;

    public function patch(Request $request) :JsonResponse;
}