<header class="dashboardHead">
    <style>
        .notify-badge {
            color: #fff;
            background: #dc3545;
            border-radius: 10px;
            font-size: 11px;
            padding: 2px 6px;
            font-weight: 600;
        }

        /* === Dropdown === */
        .notif-dd-item {
            padding: 8px 10px;
            border-left: 3px solid transparent;
            font-size: 13px;
        }

        .notif-dd-item.unread {
            background: #f8f9fa;
            border-left-color: #0d6efd;
        }

        .notif-dd-item.read {
            opacity: .45;
        }

        .notif-title {
            font-weight: 600;
            font-size: 13px;
            line-height: 1.2;
        }

        .notif-msg {
            font-size: 12px;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 230px;
        }

        .notif-time {
            font-size: 11px;
            color: #adb5bd;
        }

        .notif-mark {
            font-size: 11px;
            color: #0d6efd;
            cursor: pointer;
            padding: 0;
            border: 0;
            background: none;
        }

        /* === Modal === */
        .notif-row {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
        }

        .notif-row.unread {
            background: #f9fafb;
            border-left: 4px solid #0d6efd;
        }

        .notif-row.read {
            opacity: .55;
        }

        .notif-expiry {
            font-size: 11px;
            background: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 4px;
        }
    </style>

    <div class="leftItem">
        <a href="javascript:void(0)"><img src="{{ asset('frontend/images/mittlearn-logo.svg') }}" alt=""
                width="130"></a>

    </div>
    <div class="rightItem">
        <button type="button" class="toggleBtn">
            <img src="{{ asset('frontend/images/toggletop-icon.svg') }}" alt="" width="16" class="me-md-3">
        </button>

        <div class="alertsSec d-lg-block d-none @if (Session::has('admin_id')) alertWhenAdmin @endif">
            <div class="alertList">
                <a href="javascript:void(0);">This is the useful tool</a>
                <a href="javascript:void(0);">This is the useful tool</a>
            </div>
        </div>
        <button type="button" class="searchBtn d-md-none ms-auto me-3" data-bs-toggle="dropdown"><img
                src="{{ asset('frontend/images/topsearch-icon.svg') }}" alt="img" width="20"></button>

        @if (Session::has('admin_id'))
            <a href="{{ route('superadmin.backToAdmin') }}" class="btn btn-sm btn-warning ms-md-auto me-3 me-md-4">Back
                to Admin</a>
        @endif
        @if (Session::has('parent_school_id'))
            <a href="{{ route('sp.back.to.parent') }}" class="btn btn-sm btn-warning ms-md-auto me-3 me-md-4">Back
                to Parent School</a>
        @endif

        <a href="#" class="ms-md-auto me-3 me-md-4" data-bs-toggle="tooltip" data-bs-placement="top"
            title="User Manual / Guide">
        </a>

        </a>

        <!-- 🔔 Notifications -->
        <div class="dropdown me-3">
            <button class="btn position-relative p-0 bg-transparent border-0" type="button" id="notificationDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">

                <i class="fa fa-bell fs-5"></i>

                @php
                    $unreadCount = DB::table('notifications')
                        ->where('notifiable_type', App\Models\User::class)
                        ->where('notifiable_id', Auth::id())
                        ->whereNull('read_at')
                        ->count();
                @endphp

                @if ($unreadCount > 0)
                    <span
                        class="position-absolute top-0 start-100 translate-middle notify-badge rounded-pill bg-danger">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <!-- Dropdown -->
            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px">

                <li class="dropdown-header fw-semibold small">
                    Notifications
                </li>

                @php
                    $notifications = DB::table('notifications')
                        ->where('notifiable_type', App\Models\User::class)
                        ->where('notifiable_id', Auth::id())
                        ->where(function ($q) {
                            $q->whereDate('created_at', today())->orWhereNull('read_at');
                        })
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();
                @endphp

                @forelse ($notifications as $notification)
                    @php
                        $data = json_decode($notification->data, true);
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <li class="notif-dd-item {{ $isUnread ? 'unread' : 'read' }}" data-id="{{ $notification->id }}"
                        data-created="{{ \Carbon\Carbon::parse($notification->created_at)->toDateString() }}">

                        <div class="notif-title">
                            {{ $data['title'] ?? 'Notification' }}
                        </div>

                        <div class="notif-msg">
                            {{ $data['message'] ?? '' }}
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="notif-time">
                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                            </span>

                            @if ($isUnread)
                                <button class="notif-mark mark-read-btn" data-id="{{ $notification->id }}">
                                    Mark as read
                                </button>
                            @endif
                        </div>
                    </li>

                @empty
                    <li class="dropdown-item text-muted small text-center">
                        No notifications
                    </li>
                @endforelse

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item text-center small" data-bs-toggle="modal"
                        data-bs-target="#allNotificationsModal">
                        View all
                    </a>
                </li>
            </ul>

        </div>
        <button class="dropdownPrf me-3" type="button" data-bs-target="#profile" data-bs-toggle="modal">
            <img src="{{ Auth::user()->image ? Storage::url('uploads/user/profile_image/' . Auth::user()->image) : asset('frontend/images/default-image.jpg') }}"
                alt="profile-image">{{ ucwords(Auth::user()->name) }}</button>
    </div>
</header>
<div class="modal fade" id="allNotificationsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title fw-semibold">All Notifications</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">

                @php
                    $allNotifications = DB::table('notifications')
                        ->where('notifiable_type', App\Models\User::class)
                        ->where('notifiable_id', Auth::id())
                        ->where(function ($q) {
                            $q->whereDate('created_at', today())->orWhereNull('read_at');
                        })
                        ->orderByDesc('created_at')
                        ->get();
                @endphp

                <ul class="list-group list-group-flush">

                    @forelse ($allNotifications as $notification)
                        @php
                            $data = json_decode($notification->data, true);
                            $isUnread = is_null($notification->read_at);
                        @endphp

                        <li class="notif-row {{ $isUnread ? 'unread' : 'read' }}" data-id="{{ $notification->id }}"
                            data-created="{{ \Carbon\Carbon::parse($notification->created_at)->toDateString() }}">

                            <div class="d-flex justify-content-between align-items-start">

                                <div class="pe-2">
                                    <div class="fw-semibold small">
                                        {{ $data['title'] ?? 'Notification' }}
                                    </div>

                                    <div class="text-muted" style="font-size:12px; line-height:1.2">
                                        {{ $data['message'] ?? '' }}
                                    </div>

                                    <div class="notif-time">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </div>

                                    @if (!empty($data['end_date']))
                                        <div class="notif-expiry">
                                            Expiry: {{ \Carbon\Carbon::parse($data['end_date'])->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>

                                @if ($isUnread)
                                    <button class="notif-mark mark-read-btn" data-id="{{ $notification->id }}">
                                        Mark as Read
                                    </button>
                                @endif
                            </div>
                        </li>

                    @empty
                        <li class="list-group-item text-center text-muted">
                            No notifications
                        </li>
                    @endforelse

                </ul>


            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('mark-read-btn')) return;

        const btn = e.target;
        const id = btn.dataset.id;
        const item = btn.closest('[data-created]');
        const createdDate = item.dataset.created;
        const today = new Date().toISOString().split('T')[0];

        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            // badge update
            const badge = document.querySelector('.notify-badge');
            if (badge) {
                let count = parseInt(badge.innerText) - 1;
                count <= 0 ? badge.remove() : badge.innerText = count;
            }

            if (createdDate !== today) {
                item.remove();
            } else {
                item.classList.remove('unread');
                item.classList.add('read');
                btn.remove();
            }
        });
    });
</script>
