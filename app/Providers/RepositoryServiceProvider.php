<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\ProjectPhaseRepositoryInterface;
use App\Repositories\Contracts\ProjectTaskRepositoryInterface;
use App\Repositories\Contracts\ProjectBudgetRepositoryInterface;
use App\Repositories\Contracts\ProjectRiskRepositoryInterface;
use App\Repositories\Contracts\ProjectChangeRepositoryInterface;
use App\Repositories\Contracts\ProjectKurvaSRepositoryInterface;
use App\Repositories\Contracts\ProjectMilestoneRepositoryInterface;
use App\Repositories\Contracts\ProjectOutsourcingRepositoryInterface;
use App\Repositories\Contracts\ProjectMemberRepositoryInterface;
use App\Repositories\Contracts\ProjectResourceRepositoryInterface;
use App\Repositories\Contracts\BaseRepositoryInterface;

use App\Repositories\ProjectRepository;
use App\Repositories\ProjectPhaseRepository;
use App\Repositories\ProjectTaskRepository;
use App\Repositories\ProjectBudgetRepository;
use App\Repositories\ProjectRiskRepository;
use App\Repositories\ProjectChangeRepository;
use App\Repositories\ProjectKurvaSRepository;
use App\Repositories\ProjectMilestoneRepository;
use App\Repositories\ProjectOutsourcingRepository;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectResourceRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(ProjectPhaseRepositoryInterface::class, ProjectPhaseRepository::class);
        $this->app->bind(ProjectTaskRepositoryInterface::class, ProjectTaskRepository::class);
        $this->app->bind(ProjectBudgetRepositoryInterface::class, ProjectBudgetRepository::class);
        $this->app->bind(ProjectRiskRepositoryInterface::class, ProjectRiskRepository::class);
        $this->app->bind(ProjectChangeRepositoryInterface::class, ProjectChangeRepository::class);
        $this->app->bind(ProjectKurvaSRepositoryInterface::class, ProjectKurvaSRepository::class);
        $this->app->bind(ProjectMilestoneRepositoryInterface::class, ProjectMilestoneRepository::class);
        $this->app->bind(ProjectOutsourcingRepositoryInterface::class, ProjectOutsourcingRepository::class);
        $this->app->bind(ProjectMemberRepositoryInterface::class, ProjectMemberRepository::class);
        $this->app->bind(ProjectResourceRepositoryInterface::class, ProjectResourceRepository::class);
    }
}