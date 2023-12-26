<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>Edit user group</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
          rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body class="bg-gray-100">
<?php include __DIR__ . '/../Layout/navbar.php'; ?>
<?php include __DIR__ . '/../Layout/alerts.php'; ?>

<div class="max-w-lg mx-auto bg-white p-6 mt-6 rounded shadow">
    <form id="editGroupForm">
        <input type="hidden"
               id="groupId"
               value="<?php echo $group->id; ?>">

        <div class="mb-4">
            <label for="name"
                   class="block text-gray-700 text-sm font-bold mb-2">Group name:</label>
            <input type="text"
                   id="name"
                   name="name"
                   value="<?php echo $group->name; ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex items-center justify-between">
            <?php include __DIR__ . '/../Layout/back-button.php'; ?>

            <div>
                <button type="button"
                        class="delete-group bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Delete
                </button>
                <button type="submit"
                        disabled
                        class="bg-blue-400 enabled:bg-blue-500 enabled:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {

        let originalGroupName = "<?php echo $group->name; ?>";

        const submitButton = $("#editGroupForm button[type='submit']");

        $("input").on("change", function () {
            const disable = $("#name").val() === originalGroupName;
            submitButton.prop("disabled", disable);
        });

        $('button.delete-group').click(function () {
            const groupId = $("input#groupId").val();

            if (confirm('Are you sure you want to delete this group?')) {
                $.ajax({
                    url: '/group/delete?groupId=' + groupId,
                    type: 'DELETE',
                    success: function (result) {
                        if (result.success) {
                            showSuccess(result.message ?? 'The group has been deleted successfully!');
                            setTimeout(function () {
                                window.location = "/group";
                            }, 1500);
                        } else {
                            showError(result.message ?? 'The group could not be deleted.');
                        }
                    }
                });
            }
        });

        $("#editGroupForm").on("submit", function (e) {
            e.preventDefault();

            const data = {
                id: $("#groupId").val(),
                name: $("#name").val()
            };

            $.ajax({
                type: "POST",
                url: "/group/update",
                data: data,
                success: function (response) {

                    if (response.success) {
                        originalGroupName = $("#name").val()
                        showSuccess(response.message);
                        submitButton.prop("disabled", true)
                    } else {
                        showError(response.message);
                    }

                },
                error: function (response) {
                    showError(response.message ?? null);
                }
            });
        });
    });
</script>
</body>
</html>