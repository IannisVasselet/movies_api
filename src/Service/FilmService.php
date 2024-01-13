<?php
// src/Service/FilmService.php
namespace App\Service;

use App\Repository\FilmRepository;

class FilmService
{
    private FilmRepository $filmRepository;

    public function __construct(FilmRepository $filmRepository)
    {
        $this->filmRepository = $filmRepository;
    }

    public function getAllFilms(int $page = 1, int $pageSize = 10): array
    {
        return $this->filmRepository->findAllFilms($page, $pageSize);
    }
}


