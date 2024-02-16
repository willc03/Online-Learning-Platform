<x-structure.wrapper title="Settings">
    <x-components.3d_button class="course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73" onclick="location.href = '{{ route('course.home', ['id' => $course->id]) }}'">Return to course home</x-components.3d_button>
    <h1>COURSE SETTINGS</h1>
    {{-- The user will be able to change basic details about the course here --}}
    <h2>COURSE DETAILS</h2>
    <div id="course-details" class="flex-col">
        <form method="post" action="{{ route('course.settings.set', ['id' => $course->id]) }}">
            @csrf
            <label class="flex-row">
                <span>Course Title:</span>
                <input name="title" type="text" value="{{ $course->title }}" required>
            </label>
            <label class="flex-row">
                <span>Course Description:</span>
                <textarea name="description" style="resize: none">{{ $course->description }}</textarea>
            </label>
            <label class="flex-row">
                <span>Course Privacy:</span>
                <select name="publicity" required>
                    <option selected value="{{ $course->is_public }}">{{ $course->is_public ? "Public" : "Private" }}</option>
                    <option value="{{ !$course->is_public }}">{{ !$course->is_public ? "Public" : "Private" }}</option>
                </select>
            </label>
            <x-components.3d_button id="details-submit" class="course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73" disabled>Set new details</x-components.3d_button>
        </form>
    </div>
    {{-- The file management section for the course will be displayed here, allowing users to upload or remove files as necessary --}}
    <h2>COURSE FILES</h2>
    <div id="course-files" class="flex-col">
        <h3>Upload new files</h3>
        <form method="post" enctype="multipart/form-data" id="basic-file-upload">
            {{-- A basic file upload form will be used to allow for unique styling on different pages --}}
            @csrf
            @method('POST')

            <input type="hidden" name="id" id="course-id" value="{{ explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2] }}">

            <fieldset class="flex-col">
                <div class="message" id="file-upload-message-box" style="display: none">
                    <p id="file-upload-message"></p>
                </div>

                <label for="name">File name:</label>
                <input type="text" name="name" id="file-upload-name" required>

                <label for="file">Upload a file:</label>
                <input type="file" name="file" id="file-upload-slot" required>

                <input type="submit">
            </fieldset>
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
                            <x-components.3d_button class="course-button-mini no-buffer" fg_color="#CA6565" bg_color="#A23636">Delete</x-components.3d_button>
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
            <x-components.3d_button id="new-invite" class="course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73">Create your first invitation</x-components.3d_button>
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
                                <x-components.3d_button id="toggle-invite-activity" data-active="true" class="course-button-mini no-buffer" fg_color="#B1CA65" bg_color="#88A236">Active</x-components.3d_button>
                            @else
                                <x-components.3d_button id="toggle-invite-activity" data-active="false" class="course-button-mini no-buffer" fg_color="#CA6565" bg_color="#A23636">Inactive</x-components.3d_button>
                            @endif
                        </div>
                        <div class="table-col">
                            <p>{{ $invite->uses }} of {{ $invite->max_uses }}</p>
                            <x-components.3d_button id="max-use-toggle" fg-color="#9EC5AB" bg-color="#5e9c73">Modify max uses</x-components.3d_button>
                            <div id="max-use-form" class="flex-col">
                                <label style="margin-bottom: 10px">
                                    New Max Uses:
                                    <input type="number" id="newNumber" data-initial="{{ $invite->max_uses }}" value="{{ $invite->max_uses }}" min="{{ $invite->uses ?? 0 }}">
                                </label>
                                <x-components.3d_button id="submit-invite-max-use" class="course-button-mini no-buffer max-content" fg_color="#B1CA65" bg_color="#88A236" disabled>Update</x-components.3d_button>
                            </div>
                        </div>
                        <div class="table-col"> {{-- Expiry date --}}
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
                            <p>{{ date("d/m/Y H:i", strtotime($invite->expiry_date)) }} UTC</p>
                            <x-components.3d_button id="expiry-date-toggle" fg-color="#9EC5AB" bg-color="#5e9c73">Modify expiry date</x-components.3d_button>
                            <div id="expiry-date-form" class="flex-col">
                                <label style="margin-bottom: 10px">
                                    New Expiry Date:<br>
                                    <input type="text" id="expiryDateTimePicker" data-initial="{{ date("d/m/Y H:i", strtotime($invite->expiry_date)) }}" value="{{ date("d/m/Y H:i", strtotime($invite->expiry_date)) }}">
                                </label>
                                <x-components.3d_button id="submit-expiry-date" class="course-button-mini no-buffer max-content" fg_color="#B1CA65" bg_color="#88A236" disabled>Update</x-components.3d_button>
                            </div>
                        </div>
                        <div class="table-col">
                            <x-components.3d_button class="course-button-mini" fg_color="#CA6565" bg_color="#A23636">Delete</x-components.3d_button><br>
                            <x-components.3d_button class="course-button-mini no-buffer" fg-color="#9EC5AB" bg-color="#5e9c73">Copy link to clipboard</x-components.3d_button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <h2>COURSE USERS</h2>
    <div id="course-users" class="flex-col">
        @if($course->users->count() === 0)
            <p>Your course doesn't have any users! Users will appear here when they join the course.</p>
        @else

        @endif
    </div>

    {{-- Scripts --}}
    <script src="{{ asset("assets/scripts/courses/admin/settings/core_edit.js") }}"></script>
    <script>
        upload_url = "{{ route('course.file.upload', ['id' => $course->id]) }}";
        fileRemoveRoute = "{{ route("course.file.remove", ['id' => $course->id]) }}";
        inviteModifyRoute = "{{ route("course.settings.invite", ['id' => $course->id]) }}";
    </script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/file_upload.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/file_delete.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_active_state.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_max_uses.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/admin/settings/invite_expiry.js") }}"></script>
</x-structure.wrapper>
