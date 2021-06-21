<?php declare(strict_types=1);

namespace Gunratbe\Gunfire\Service;

use Gunratbe\App\Factory\RepositoryFactory;
use Knops\Gunfire\GunfireService;

abstract class ProductInformationAbstract
{
    private GunfireService $gunfire;
    private RepositoryFactory $repositoryFactory;

    public function __construct(GunfireService $gunfire, RepositoryFactory $repositoryFactory)
    {
        $this->gunfire = $gunfire;
        $this->repositoryFactory = $repositoryFactory;
    }

    protected function getGunfire(): GunfireService
    {
        return $this->gunfire;
    }

    protected function getRepositoryFactory(): RepositoryFactory
    {
        return $this->repositoryFactory;
    }
}