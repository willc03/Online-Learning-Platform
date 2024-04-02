<x-structure.wrapper title="Settings">
    <div class="flex-row">
        <x-ui.interactive-button class="course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A" onclick="location.href = '{{ route('course.home', ['id' => $course->id]) }}'">Return to course home</x-ui.interactive-button>
        <form method="post" action="{{ route('course.settings.delete', [ 'id' => $course->id ]) }}" style="margin-left: 10px;">
            @csrf
            @method('DELETE')
            <x-ui.interactive-button class="course-button-mini max-content" fg_color="#D10023" bg_color="#840016">Delete course</x-ui.interactive-button>
        </form>
    </div>
    <h1>COURSE SETTINGS</h1>
    {{-- The user will be able to change basic details about the course here --}}
    <h2>COURSE DETAILS</h2>
    <div id="course-details" class="flex-col">
        <form method="post" action="{{ route('course.settings.set', ['id' => $course->id]) }}">
            @csrf
            <legend>Edit course details</legend>
            <label class="form-flex">
                <span class="required">Course Title:</span>
                <input name="title" type="text" value="{{ $course->title }}" placeholder="Enter your course's title here." required />
            </label>
            <label class="form-flex">
                <span>Course Description:</span>
                <textarea name="description" placeholder="Enter your course's description here." style="resize: none">{{ $course->description }}</textarea>
            </label>
            <label class="form-flex">
                <span class="required">Course Privacy:</span>
                <select name="publicity" required>
                    <option selected value="{{ $course->is_public }}">{{ $course->is_public ? "Public" : "Private" }}</option>
                    <option value="{{ !$course->is_public }}">{{ !$course->is_public ? "Public" : "Private" }}</option>
                </select>
            </label>
            <x-ui.interactive-button id="details-submit" class="course-button-mini max-content middle" fg-color="#43AA8B" bg-color="#245B4A" disabled>Set new details</x-ui.interactive-button>
        </form>
    </div>
    {{-- The file management section for the course will be displayed here, allowing users to upload or remove files as necessary --}}
    <h2>COURSE FILES</h2>
    <div id="course-files" class="flex-col">
        <h3>Upload new files</h3>
        <form method="post" enctype="multipart/form-data" id="basic-file-upload" class="flex-col">
            {{-- A basic file upload form will be used to allow for unique styling on different pages --}}
            @csrf
            @method('POST')

            <input type="hidden" name="id" id="course-id" value="{{ explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2] }}" />

            <legend>Upload new files</legend>

            <div class="message" id="file-upload-message-box" style="display: none">
                <p id="file-upload-message"></p>
            </div>

            <label class="form-flex">
                <span class="required">File name:</span>
                <input type="text" name="name" id="file-upload-name" placeholder="Enter your file's public name here." required />
            </label>

            <label class="form-flex" style="border: none">
                <span class="required">Upload a file:</span>
                <input type="file" name="file" id="file-upload-slot" class="flex-col" required style="border: none" />
            </label>

            <x-ui.interactive-button class="middle course-button-mini" fg-color="#43AA8B" bg-color="#245B4A" type="submit" id="submit-file">Submit</x-ui.interactive-button>

        </form>
        <h3>Manage files</h3>
        @if ($course->files->count() === 0)
            <p>This course does not have any files! Files will appear here when available.</p>
        @else
            <div class="file-table" id="file-manager">
                <div class="table-row table-header">
                    <div class="table-col">
                        <p>Public file name</p>
                    </div>
                    <div class="table-col">
                        <p>File name</p>
                    </div>
                    <div class="table-col">
                        <p>Date added</p>
                    </div>
                    <div class="table-col">
                        <p>Action(s)</p>
                    </div>
                </div>
                @foreach($course->files as $file)
                    <div class="table-row" id="{{ $file->id }}">
                        <div class="table-col">
                            <p>{{ $file->name }}</p>
                        </div>
                        <div class="table-col">
                            <p>{{ basename($file->path) }}</p>
                        </div>
                        <div class="table-col">
                            <p>{{ date('d/m/Y H:i', $file->created_at->getTimestamp()) }}</p>
                        </div>
                        <div class="table-col">
                            <x-ui.interactive-button class="course-button-mini no-buffer" fg_color="#CA6565" bg_color="#A23636">Delete</x-ui.interactive-button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    {{-- Invitations will be managed here, if the course is private --}}
    <h2>COURSE INVITATIONS</h2>
    <div id="course-invitations" class="flex-col">
        @if($course->is_public)
            <p>Course invitations cannot be configured right now. <span class="italicise">Set the course to private to manage invitations.</span></p>
        @elseif($course->invites->count() == 0)
            <p>This course currently has no active invitations.</p>
            <x-ui.interactive-button id="new-invite" class="course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A">Create your first invitation</x-ui.interactive-button>
            <x-course.add-invite :course="$course" />
        @else
            <div class="invite-table" id="invite-manager">
                <div class="table-row table-header">
                    <div class="table-col">
                        <p>Invite ID</p>
                    </div>
                    <div class="table-col">
                        <p>Active</p>
                    </div>
                    <div class="table-col">
                        <p>Uses</p>
                    </div>
                    <div class="table-col">
                        <p>Expiry Date</p>
                    </div>
                    <div class="table-col">
                        <p>Action(s)</p>
                    </div>
                </div>
                @foreach($course->invites as $invite)
                    <div class="table-row" id="{{ $invite->invite_id }}">
                        <div class="table-col">
                            <p>{{ $invite->invite_id }}</p>
                        </div>
                        <div class="table-col">
                            @if($invite->is_active)
                                <x-ui.interactive-button data-active="true" class="toggle-invite-activity course-button-mini no-buffer" fg_color="#B1CA65" bg_color="#88A236">Active</x-ui.interactive-button>
                            @else
                                <x-ui.interactive-button data-active="false" class="toggle-invite-activity course-button-mini no-buffer" fg_color="#CA6565" bg_color="#A23636">Inactive</x-ui.interactive-button>
                            @endif
                        </div>
                        <div class="table-col">
                            @if ($invite->max_uses != null)
                                <p>{{ $invite->uses }} of {{ $invite->max_uses }}</p>
                            @else
                                <p>{{ $invite->uses }} (unlimited uses)</p>
                            @endif
                            <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini max-use-toggle">Modify max uses</x-ui.interactive-button>
                            <div class="flex-col max-use-form">
                                <label style="margin-bottom: 10px">
                                    New Max Uses:<br>
                                    <input type="number" id="newNumber" data-initial="{{ $invite->max_uses }}" value="{{ $invite->max_uses }}" min="{{ $invite->uses ?? 0 }}">
                                </label>
                                <x-ui.interactive-button id="submit-invite-max-use" class="course-button-mini no-buffer max-content" fg_color="#B1CA65" bg_color="#88A236" disabled>Update</x-ui.interactive-button>
                                @if($invite->max_uses != null)
                                    <br>
                                    <x-ui.interactive-button id="limit-remove" class="course-button-mini no-buffer" fg_color="#B1CA65" bg_color="#88A236">Remove use limit</x-ui.interactive-button>
                                @endif
                            </div>
                        </div>
                        <div class="table-col"> {{-- Expiry date --}}
                            @if ($invite->expiry_date != null)
                                <p>{{ date("d/m/Y H:i", strtotime($invite->expiry_date)) }} UTC</p>
                            @else
                                <p>No expiry date</p>
                            @endif
                            <x-ui.interactive-button fg-color="#43AA8B" bg-color="#245B4A" class="course-button-mini expiry-date-toggle">Modify expiry date</x-ui.interactive-button>
                            <div class="flex-col expiry-date-form">
                                <label style="margin-bottom: 10px">
                                    New Expiry Date:<br>
                                    <input type="text" id="expiryDateTimePicker" data-initial="{{ date("d/m/Y H:i", strtotime($invite->expiry_date)) }}" value="{{ date("d/m/Y H:i", strtotime($invite->expiry_date)) }}">
                                </label>
                                <x-ui.interactive-button id="submit-expiry-date" class="course-button-mini no-buffer max-content" fg_color="#B1CA65" bg_color="#88A236" disabled>Update</x-ui.interactive-button>
                                @if($invite->expiry_date != null)
                                    <br>
                                    <x-ui.interactive-button id="date-remove" class="course-button-mini no-buffer" fg_color="#B1CA65" bg_color="#88A236">Remove expiration date</x-ui.interactive-button>
                                @endif
                            </div>
                        </div>
                        <div class="table-col">
                            <x-ui.interactive-button id="invite-delete" class="course-button-mini" fg_color="#CA6565" bg_color="#A23636">Delete</x-ui.interactive-button>
                            <br>
                            <x-ui.interactive-button class="course-button-mini no-buffer invite-link-copy" fg-color="#43AA8B" bg-color="#245B4A" data-link="{{ route('join.show', ['id' => $invite->invite_id]) }}">Copy link</x-ui.interactive-button>
                        </div>
                    </div>
                @endforeach
            </div>
            <x-ui.interactive-button id="new-invite" class="course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A">Create another invitation</x-ui.interactive-button>
            <x-course.add-invite :course="$course" />
        @endif
    </div>
    <h2>COURSE USERS</h2>
    <div id="course-users" class="flex-col">
        @if($course->users->count() === 0)
            <p>Your course doesn't have any users! Users will appear here when they join the course.</p>
        @else
            <div class="user-table" id="user-manager">
                <div class="table-row table-header">
                    <div class="table-col">
                        <p>Name</p>
                    </div>
                    <div class="table-col">
                        <p>Date Joined</p>
                    </div>
                    <div class="table-col">
                        <p>Action(s)</p>
                    </div>
                </div>
                @foreach($course->users as $courseUser)
                    <div class="table-row" id="{{ $courseUser->id }}">
                        <div class="table-col">
                            <p>{{ $courseUser->user->name }}</p>
                        </div>
                        <div class="table-col">
                            <p>{{ $courseUser->created_at }}</p>
                        </div>
                        <div class="table-col">
                            <x-ui.interactive-button id="user-delete" class="course-button-mini max-content" fg_color="#CA6565" bg_color="#A23636">Remove user</x-ui.interactive-button>
                            <br>
                            <x-ui.interactive-button id="user-block" class="course-button-mini max-content no-buffer" fg_color="#CA6565" bg_color="#A23636" data-active="{{ var_export($courseUser->blocked, true) }}">{{ var_export($courseUser->blocked, true) ? "Unblock from course" : "Block from course" }}</x-ui.interactive-button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Scripts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/core_edit.js") }}"></script>
    <script>
        upload_url = "{{ route('course.file.upload', ['id' => $course->id]) }}";
        fileRemoveRoute = "{{ route("course.file.remove", ['id' => $course->id]) }}";
        inviteModifyRoute = "{{ route("course.settings.invite", ['id' => $course->id]) }}";
        inviteRemoveRoute = "{{ route("course.settings.invite.delete", [ 'id' => $course->id ]) }}";
        userRemoveRoute = "{{ route('course.settings.user.delete', [ 'id' => $course->id ]) }}";
        userBlockRoute = "{{ route('course.settings.user.block', [ 'id' => $course->id ]) }}";
    </script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_add.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/file_upload.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/file_delete.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_active_state.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_max_uses.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_expiry.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_clipboard.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_delete.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/user_manage.js") }}"></script>
</x-structure.wrapper>
