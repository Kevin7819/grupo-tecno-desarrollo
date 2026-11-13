<?php

namespace App\Http\Controllers\Admin;

use App\Application\Cms\AdminActor;
use App\Application\Cms\ChangeProgramStatus;
use App\Application\Cms\DeleteProgram;
use App\Application\Cms\HostRepository;
use App\Application\Cms\ProgramInput;
use App\Application\Cms\ProgramRepository;
use App\Application\Cms\SaveProgram;
use App\Application\Cms\ScheduleConflict;
use App\Domain\Cms\InvalidSchedule;
use App\Domain\Cms\Program;
use App\Domain\Cms\ProgramStatus;
use App\Domain\Cms\Weekday;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Http\Requests\Admin\ProgramStatusRequest;
use App\Infrastructure\Storage\PublicImageStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ProgramController extends Controller
{
    public function index(ProgramRepository $programs): View
    {
        return view('admin.programs.index', [
            'programs' => $programs->all(),
            'weekdays' => Weekday::cases(),
        ]);
    }

    public function create(HostRepository $hosts): View
    {
        return view('admin.programs.form', [
            'program' => null,
            'hosts' => $hosts->all(),
            'weekdays' => Weekday::cases(),
            'statuses' => ProgramStatus::cases(),
        ]);
    }

    public function store(
        ProgramRequest $request,
        SaveProgram $save,
        PublicImageStorage $images,
    ): RedirectResponse {
        $input = $this->inputFrom($request, null, $images);

        try {
            $save->handle(null, $input, $this->actor($request));
        } catch (InvalidSchedule|ScheduleConflict $exception) {
            $this->discardNewCover($input, null, $images);

            return back()->withInput()->withErrors(['schedule' => $exception->getMessage()]);
        }

        return redirect()->route('admin.programs.index')->with('status', 'Programa creado.');
    }

    public function edit(int $program, ProgramRepository $programs, HostRepository $hosts): View
    {
        return view('admin.programs.form', [
            'program' => $this->find($program, $programs),
            'hosts' => $hosts->all(),
            'weekdays' => Weekday::cases(),
            'statuses' => ProgramStatus::cases(),
        ]);
    }

    public function update(
        ProgramRequest $request,
        int $program,
        ProgramRepository $programs,
        SaveProgram $save,
        PublicImageStorage $images,
    ): RedirectResponse {
        $existing = $this->find($program, $programs);
        $input = $this->inputFrom($request, $existing, $images);

        try {
            $save->handle($existing, $input, $this->actor($request));
        } catch (InvalidSchedule|ScheduleConflict $exception) {
            $this->discardNewCover($input, $existing, $images);

            return redirect()
                ->route('admin.programs.edit', $existing->id)
                ->withInput()
                ->withErrors(['schedule' => $exception->getMessage()]);
        }

        $images->replace($existing->coverPath, $input->coverPath);

        return redirect()->route('admin.programs.index')->with('status', 'Programa actualizado.');
    }

    public function updateStatus(
        ProgramStatusRequest $request,
        int $program,
        ProgramRepository $programs,
        ChangeProgramStatus $change,
    ): RedirectResponse {
        $status = ProgramStatus::fromString($request->validated('status'));

        $change->handle($this->find($program, $programs), $status, $this->actor($request));

        return redirect()
            ->route('admin.programs.index')
            ->with('status', match ($status) {
                ProgramStatus::Published => 'Programa publicado en la página pública.',
                ProgramStatus::Inactive => 'Programa desactivado. Deja de mostrarse en la página pública.',
                ProgramStatus::Draft => 'Programa devuelto a borrador.',
            });
    }

    public function confirmDelete(int $program, ProgramRepository $programs): View
    {
        return view('admin.programs.delete', [
            'program' => $this->find($program, $programs),
        ]);
    }

    public function destroy(
        int $program,
        ProgramRepository $programs,
        DeleteProgram $delete,
        PublicImageStorage $images,
    ): RedirectResponse {
        $existing = $this->find($program, $programs);

        $delete->handle($existing, AdminActor::fromAdmin(request()->user('admin')));
        $images->delete($existing->coverPath);

        return redirect()->route('admin.programs.index')->with('status', 'Programa eliminado.');
    }

    private function find(int $id, ProgramRepository $programs): Program
    {
        return $programs->find($id) ?? abort(404);
    }

    private function actor(ProgramRequest|ProgramStatusRequest $request): AdminActor
    {
        return AdminActor::fromAdmin($request->user('admin'));
    }

    private function inputFrom(
        ProgramRequest $request,
        ?Program $existing,
        PublicImageStorage $images,
    ): ProgramInput {
        $coverPath = $existing?->coverPath;

        if ($request->hasFile('cover')) {
            $coverPath = $images->store($request->file('cover'), 'programs');
        } elseif ($request->boolean('remove_cover')) {
            $coverPath = null;
        }

        return new ProgramInput(
            name: (string) $request->validated('name'),
            description: $request->validated('description'),
            coverPath: $coverPath,
            status: ProgramStatus::fromString($request->validated('status')),
            sortOrder: (int) ($request->validated('sort_order') ?? 0),
            schedule: array_values((array) ($request->validated('schedule') ?? [])),
            hostIds: array_values(array_map('intval', (array) ($request->validated('hosts') ?? []))),
        );
    }

    private function discardNewCover(ProgramInput $input, ?Program $existing, PublicImageStorage $images): void
    {
        if ($input->coverPath !== null && $input->coverPath !== $existing?->coverPath) {
            $images->delete($input->coverPath);
        }
    }
}
