<?php

namespace App\Controller;

use App\Service\SpotifyService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends BaseController
{

    public function __construct(private readonly SpotifyService $spotifyService)
    {
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/')]
    public function index() : Response
    {

        return $this->jsonResponse($this->spotifyService->searchTrack('Encore un matin'));

    }

}