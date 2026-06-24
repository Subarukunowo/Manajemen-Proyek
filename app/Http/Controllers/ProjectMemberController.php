<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Services\ProjectMemberService;
use Illuminate\Http\JsonResponse;

class ProjectMemberController extends Controller
{
    public function __construct(
        private readonly ProjectMemberService $memberService
    ) {}

    public function index(int $projectId): JsonResponse
    {
        return response()->json(['data' => $this->memberService->getByProject($projectId)]);
    }

    public function store(StoreMemberRequest $request, int $projectId): JsonResponse
    {
        $member = $this->memberService->add($projectId, $request->validated());
        return response()->json(['data' => $member], 201);
    }

    public function update(UpdateMemberRequest $request, int $projectId, int $id): JsonResponse
    {
        return response()->json(['data' => $this->memberService->updatePeran($id, $request->input('peran'))]);
    }

    public function destroy(int $projectId, int $id): JsonResponse
    {
        $this->memberService->remove($id);
        return response()->json(['message' => 'Member berhasil dihapus dari proyek.']);
    }
}