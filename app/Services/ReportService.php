<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService
{
    public function __construct(private readonly ReportRepository $reportRepository)
    {
    }

    public function summary(): array
    {
        return [
            'tma' => $this->reportRepository->tma([]),
            'tme' => $this->reportRepository->tme([]),
            'realtime' => $this->reportRepository->realtime([]),
        ];
    }

    public function tma(array $filters): array
    {
        return $this->reportRepository->tma($filters);
    }

    public function tme(array $filters): array
    {
        return $this->reportRepository->tme($filters);
    }

    public function realtime(array $filters): array
    {
        return $this->reportRepository->realtime($filters);
    }
}
