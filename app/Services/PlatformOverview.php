<?php

namespace App\Services;

use App\Models\StudentProgress;
use App\Models\TeacherFeedback;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Platform-wide numbers for the homepage's teacher dashboard preview.
 *
 * The homepage is public, so this only ever returns counts and averages,
 * never a name or any one student's data. Each number is computed the way
 * the teacher dashboard computes it for a single class, just across every
 * section: a topic counts as done once the student has a post-test result.
 */
class PlatformOverview
{
    /**
     * Curriculum topics per module, matching MODULE_GROUPS in
     * App\Http\Controllers\StudentController, labelled as on the homepage.
     *
     * @var array<string, list<string>>
     */
    private const MODULES = [
        'Sequences and Series' => ['ari', 'geo', 'har', 'fib', 'fin'],
        'Polynomials and Polynomial Equations' => ['div', 'rem', 'poly'],
        'Advanced Equations and Functions' => ['rat', 'rad', 'exp', 'log'],
    ];

    private const CACHE_KEY = 'homepage.platform-overview';

    /**
     * Long enough that a busy homepage doesn't recount on every visit,
     * short enough to still read as live.
     */
    public const CACHE_MINUTES = 5;

    /**
     * Null until at least one student account is approved, so the homepage
     * keeps its labelled sample data instead of a preview full of zeros.
     *
     * @return array{students: int, avg_progress: int, pending_feedback: int, modules: list<array{name: string, avg: int}>}|null
     */
    public function get(): ?array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_MINUTES), fn (): ?array => $this->compute());
    }

    /**
     * @return array{students: int, avg_progress: int, pending_feedback: int, modules: list<array{name: string, avg: int}>}|null
     */
    private function compute(): ?array
    {
        // student_progress.session_id stores the user id as a string.
        $studentIds = User::query()
            ->where('role', 'student')
            ->where('approval_status', 'approved')
            ->pluck('id')
            ->map(fn (int $id): string => (string) $id);

        if ($studentIds->isEmpty()) {
            return null;
        }

        $allTopics = array_merge(...array_values(self::MODULES));

        // One row per (student, topic) with a post-test result; the table is
        // unique on (session_id, topic_key, phase), so a retake updates it.
        $completedPairs = StudentProgress::query()
            ->where('phase', 'post')
            ->whereIn('topic_key', $allTopics)
            ->whereIn('session_id', $studentIds)
            ->get(['session_id', 'topic_key']);

        $studentCount = $studentIds->count();

        return [
            'students' => $studentCount,
            'avg_progress' => $this->percent($completedPairs->count(), $studentCount * count($allTopics)),
            'pending_feedback' => TeacherFeedback::query()
                ->where('sender', 'teacher')
                ->whereNull('read_at')
                ->count(),
            'modules' => collect(self::MODULES)
                ->map(fn (array $topics, string $name): array => [
                    'name' => $name,
                    'avg' => $this->percent($completedPairs->whereIn('topic_key', $topics)->count(), $studentCount * count($topics)),
                ])
                ->values()
                ->all(),
        ];
    }

    private function percent(int $part, int $whole): int
    {
        return $whole > 0 ? (int) round($part / $whole * 100) : 0;
    }
}
