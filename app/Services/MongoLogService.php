<?php

namespace App\Services;

use App\Models\Mongo\ActivityLog;
use App\Models\Mongo\RecommendationLog;
use App\Models\Mongo\RevisionHistory;
use App\Models\Mongo\UploadBuktiLog;

class MongoLogService
{
    public function activity(?int $userId, string $role, string $action, string $description, array $metadata = []): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'role' => $role,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);
    }

    public function uploadBukti(array $data): void
    {
        UploadBuktiLog::create($data + ['created_at' => now()]);
    }

    public function recommendation(int $requestedByMabaId, array $inputNrpList, array $recommendedGroups, array $scoringDetail): void
    {
        RecommendationLog::create([
            'requested_by_maba_id' => $requestedByMabaId,
            'input_nrp_list' => $inputNrpList,
            'recommended_groups' => $recommendedGroups,
            'scoring_detail' => $scoringDetail,
            'created_at' => now(),
        ]);
    }

    public function revision(array $data): void
    {
        RevisionHistory::create($data + ['created_at' => now()]);
    }
}
