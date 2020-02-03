<?php


namespace App\Controller\Rest;


use App\Entity\Style;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StyleController extends AbstractRestController
{
    /**
     * @Route("/styles", name="get_styles", methods={"GET"})
     */
    public function getCollection() :JsonResponse
    {
        $styles = $this->getCollectionEntity(Style::class);
        $json = $this->serializer->serialize($styles, 'json');

        dump($json); die;
    }

    /**
     * @Route("/styles/{id}", name="get_style", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getOne(Request $request) :JsonResponse
    {
        $style =  $this->getOneEntity(Style::class, $request->get('id'));
        $json = $this->serializer->serialize($style, 'json');
        dump($json); die;
    }

    /**
     * @Route("/breweries", name="post_brewery", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function post(Request $request) :JsonResponse
    {

    }

    /**
     * @Route("/breweries/{id}", name="patch_brewery", methods={"PATCH"})
     * @param Request $request
     * @return JsonResponse
     */
    public function patch(Request $request) :JsonResponse
    {

    }
}