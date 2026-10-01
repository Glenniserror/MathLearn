<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Teacher Approvals | Bubog NHS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6fb; color: #111827; padding: 2rem 1.5rem 4rem; }
        .page-header { max-width: 1100px; margin: 0 auto 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .page-header h1 { font-size: 1.5rem; font-weight: 800; }
        .page-header p { color: #6b7280; font-size: 0.9rem; margin-top: 0.25rem; }
        .back-link { color: #2563eb; font-weight: 700; text-decoration: none; font-size: 0.85rem; padding: 0.5rem 0.9rem; background: #fff; border: 1px solid #bfdbfe; border-radius: 0.5rem; transition: background 0.15s; }
        .back-link:hover { background: #eff6ff; }
        .container { max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem; }
        .alert { padding: 0.85rem 1.1rem; border-radius: 0.6rem; font-size: 0.9rem; font-weight: 600; }
        .alert-success { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .card { background: #fff; border: 1px solid #e8ecf2; border-radius: 0.85rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
        .card-header { padding: 1.1rem 1.4rem; border-bottom: 1px solid #e8ecf2; display: flex; align-items: center; justify-content: space-between; }
        .card-header h2 { font-size: 1.05rem; font-weight: 700; }
        .badge { font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.6rem; border: 1px solid transparent; border-radius: 999px; }
        .badge-pending { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-rejected { background: #fef2f2; color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.85rem 1.4rem; font-size: 0.88rem; border-bottom: 1px solid #f1f5f9; }
        th { color: #64748b; font-weight: 600; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.03em; }
        tr:last-child td { border-bottom: none; }
        .empty-row td { text-align: center; color: #94a3b8; padding: 1.5rem; }
        .actions { display: flex; gap: 0.5rem; }
        .btn { padding: 0.4rem 0.85rem; border: 1px solid transparent; border-radius: 0.5rem; cursor: pointer; font-family: inherit; font-size: 0.82rem; font-weight: 700; transition: background 0.15s, border-color 0.15s, color 0.15s; }
        .btn-approve { background: #2563eb; color: #fff; }
        .btn-approve:hover { background: #1d4ed8; }
        .btn-reject { background: #fff; border-color: #e5e7eb; color: #6b7280; }
        .btn-reject:hover { background: #fef2f2; border-color: #fca5a5; color: #b91c1c; }
        .btn-reset { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
        .btn-reset:hover { background: #dbeafe; }
        .pagination { padding: 1rem 1.4rem; }
    </style>
</head>
<body>
    <div class="page-header">
        <div>
            <h1>Teacher Approvals</h1>
            <p>Review and manage teacher accounts on the platform</p>
        </div>
        <a class="back-link" href="{{ route('admin.dashboard') }}">&larr; Back to dashboard</a>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h2>Pending <span class="badge badge-pending" id="badge-pending">{{ $pendingTeachers->total() }}</span></h2>
            </div>
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Requested</th><th>Actions</th></tr></thead>
                <tbody id="pending-tbody" data-page="{{ $pendingTeachers->currentPage() }}">
                    @forelse ($pendingTeachers as $teacher)
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->created_at->diffForHumans() }}</td>
                            <td class="actions">
                                <form method="POST" action="{{ route('admin.teacher.approve', $teacher->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-approve">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.teacher.reject', $teacher->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="4">No pending teachers.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $pendingTeachers->links() }}</div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Approved <span class="badge badge-approved" id="badge-approved">{{ $approvedTeachers->total() }}</span></h2>
            </div>
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($approvedTeachers as $teacher)
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td class="actions">
                                <form method="POST" action="{{ route('admin.teacher.reset', $teacher->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-reset">Reset to pending</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="3">No approved teachers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $approvedTeachers->links() }}</div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Rejected <span class="badge badge-rejected" id="badge-rejected">{{ $rejectedTeachers->total() }}</span></h2>
            </div>
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($rejectedTeachers as $teacher)
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td class="actions">
                                <form method="POST" action="{{ route('admin.teacher.reset', $teacher->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-reset">Reset to pending</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="3">No rejected teachers.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $rejectedTeachers->links() }}</div>
        </div>
    </div>

    @vite(['resources/js/polling.js', 'resources/js/approval-queue.js', 'resources/js/nav-progress.js'])
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        document.addEventListener('DOMContentLoaded', function () {
            initApprovalQueuePolling({
                dataUrl: @json(route('admin.teacher-approvals.data')),
                pendingBadgeId: 'badge-pending',
                approvedBadgeId: 'badge-approved',
                rejectedBadgeId: 'badge-rejected',
                pendingTbodyId: 'pending-tbody',
                columns: ['name', 'email', 'requestedAgo'],
                emptyColspan: 4,
                emptyMessage: 'No pending teachers.',
            });
        });
    </script>
</body>
</html>
