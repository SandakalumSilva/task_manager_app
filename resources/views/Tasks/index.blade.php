@extends('layouts.app')

@section('content')
    <div class="container mt-4">

        <div class="row mb-3">
            <div class="col-md-4">
                <select id="project_filter" class="form-control">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 offset-md-4 text-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#taskModal">
                    Add Task
                </button>
            </div>
        </div>

        <div class="row mt-5" id="taskContainer">
            {{-- task tables --}}
        </div>
    </div>

    {{-- Task Modal --}}
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" class="task_form">
                        @csrf
                        <input type="hidden" name="task_id" id="task_id">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Task Name</label>
                            <input type="text" autocomplete="off" name="task_name" id="task_name" class="form-control"
                                placeholder="Task Name" aria-describedby="emailHelp">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Select Project</label>
                            <select id="projectSelect" class="form-control" name="project_id">
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary submit_btn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function fetchTask(id = null) {
            let url = id ? `/task/fetch-task/${id}` : `/task/fetch-task`;
            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function(response) {
                    console.log(response.projects);
                    if (response.status === "success") {

                        const container = $('#taskContainer');
                        container.empty();

                        response.projects.forEach(project => {

                            let tableHtml = `
                        <div class="col-12 mb-4">
                            <h5 class="mb-2">${project.project_name}</h5>

                            <table class="table table-bordered task_table"
                                   data-project-id="${project.id}">

                                <thead class="thead-dark">
                                    <tr>
                                        <th width="60">#</th>
                                        <th>Task</th>
                                        <th width="150">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="sortable_tbody">
                    `;

                            if (project.tasks.length > 0) {

                                project.tasks.forEach((task, index) => {

                                    tableHtml += `
                                <tr data-id="${task.id}">
                                    <td class="priority_number">${index + 1}</td>
                                    <td>${task.task_name}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit_task"
                                            data-id="${task.id}">
                                            Edit
                                        </button>

                                        <button class="btn btn-sm btn-danger delete_task"
                                            data-id="${task.id}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            `;
                                });

                            } else {

                                tableHtml += `
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No tasks available
                                </td>
                            </tr>
                        `;
                            }

                            tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;

                            container.append(tableHtml);

                        });
                        initSortable();
                    } else {
                        console.error('Failed to fetch tasks');
                    }
                }
            });
        }


        function initSortable() {
            $(".sortable_tbody").sortable({
                placeholder: "ui-state-highlight",
                cursor: "move",
                update: function(event, ui) {

                    let projectId = $(this).closest("table").data("project-id");

                    let sortedIDs = [];

                    $(this).find("tr").each(function(index) {

                        let taskId = $(this).data("id");

                        if (taskId) {
                            sortedIDs.push({
                                id: taskId,
                                priority: index + 1
                            });

                            // Update priority number in UI
                            $(this).find(".priority_number").text(index + 1);
                        }

                    });

                    updateTaskPriority(projectId, sortedIDs);
                }
            }).disableSelection();
        }

        function updateTaskPriority(projectId, tasks) {

            $.ajax({
                type: "POST",
                url: "/task/update-priority",
                data: {
                    _token: "{{ csrf_token() }}",
                    project_id: projectId,
                    tasks: tasks
                },
                success: function(response) {
                    if (response.status === "success") {
                        notyf.success(response.message);
                    }
                },
                error: function() {
                    notyf.error("Priority update failed");
                }
            });
        }

        $("#project_filter").on("change", function(e) {
            const projectId = $(this).val();
            fetchTask(projectId);

        });

        $(".task_form").on('submit', function(e) {
            e.preventDefault();
            let $form = $(this);
            let formData = $form.serialize();

            let taskId = $('#task_id').val();
            let url = '';
            let method = '';

            let projectId = $('#project_filter').val();

            if (taskId) {
                url = `/task/update-task/${taskId}`;
                method = "PUT";
            } else {
                url = `/task/add-task`;
                method = "POST";
            }

            $.ajax({
                type: method,
                url: url,
                data: formData,
                dataType: "json",
                success: function(response) {
                    $("#taskModal").modal('hide');
                    notyf.success(response.message);
                    $form[0].reset();
                    $('#task_id').val('');
                    $('#exampleModalLabel').text('Add Task');
                    $('.submit_btn').text('Submit');
                    fetchTask(projectId);
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            errors[field].forEach(function(message) {
                                notyf.error(message);
                            });
                        }
                    } else {
                        notyf.error(xhr.responseJSON?.message || 'Something went wrong');
                    }
                }
            });
        });

        //task edit-task
        $('#taskContainer').on('click', ".edit_task", function(e) {
            e.preventDefault();
            const taskId = $(this).data('id');

            $.ajax({
                type: "GET",
                url: `/task/edit-task/${taskId}`,
                dataType: "json",
                success: function(response) {
                    if (response.status == "success") {
                        console.log(response.task);

                        let task = response.task;

                        $('#exampleModalLabel').text('Update Task');

                        $('.submit_btn').text('Update');

                        $('#task_id').val(task.id);
                        $('#task_name').val(task.task_name);
                        $('#projectSelect').val(task.project_id);

                        $('#taskModal').modal('show');
                    }
                }
            });

        });

        // task delete 
        $('#taskContainer').on('click', ".delete_task", function(e) {
            e.preventDefault();
            const taskId = $(this).data('id');
            const projectId = $('#project_filter').val();
            Swal.fire({
                title: 'Are you sure?',
                text: "This task will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: `/task/delete-task/${taskId}`,
                        dataType: "json",
                        success: function(response) {
                            if (response.status == "success") {
                                notyf.success(response.message);
                                fetchTask(projectId);
                            }
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            fetchTask();
        });
    </script>
@endsection
