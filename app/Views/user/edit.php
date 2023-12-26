<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>Edit user</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
          rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
          rel="stylesheet"/>

</head>
<body class="bg-gray-100">
<?php include __DIR__ . '/../Layout/navbar.php'; ?>
<?php include __DIR__ . '/../Layout/alerts.php'; ?>

<div class="max-w-lg mx-auto bg-white p-6 mt-6 rounded shadow">
    <form id="editUserForm">
        <input type="hidden"
               id="userId"
               value="<?php echo $user->id; ?>">

        <div class="mb-4">
            <label for="email"
                   class="block text-gray-700 text-sm font-bold mb-2">E-mail address:</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="<?php echo $user->email; ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="firstName"
                   class="block text-gray-700 text-sm font-bold mb-2">First name:</label>
            <input type="text"
                   id="firstName"
                   name="first_name"
                   value="<?php echo $user->first_name; ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="lastName"
                   class="block text-gray-700 text-sm font-bold mb-2">Last name:</label>
            <input type="text"
                   id="lastName"
                   name="last_name"
                   value="<?php echo $user->last_name; ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="dateOfBirth"
                   class="block text-gray-700 text-sm font-bold mb-2">Date of birth:</label>
            <input type="date"
                   id="dateOfBirth"
                   name="date_of_birth"
                   value="<?php echo $user->date_of_birth; ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="groups"
                   class="block text-gray-700 text-sm font-bold mb-2">Groups:</label>
            <select id="groups"
                    name="groups[]"
                    multiple="multiple"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <?php
                foreach ($groups ?? [] as $group) {
                    $isSelected = in_array($group->id, array_column($userGroups ?? [], 'id')) ? 'selected' : '';
                    echo <<<EOL
                        <option value="$group->id" $isSelected>$group->name</option>
                    EOL;
                }
                ?>
            </select>
        </div>

        <div class="flex items-center justify-between">
            <?php include __DIR__ . '/../Layout/back-button.php'; ?>

            <div>
                <button type="button"
                        class="delete-user bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {

        let originalUserHash = '<?php echo implode('-', [
            $user->email,
            $user->first_name,
            $user->last_name,
            $user->date_of_birth,
            implode(',', array_column($userGroups, 'id'))
        ]); ?>';

        const groupSelect = $("select#groups");
        const submitButton = $("#editUserForm button[type='submit']");

        groupSelect.select2();
        const hashUserFields = () => {
            const fieldIds = ["email", "firstName", "lastName", 'dateOfBirth', 'groups'];
            let hash = "";

            for (let i = 0; i < fieldIds.length; i++) {
                const data = $("#" + fieldIds[i]).val();

                hash += Array.isArray(data) ? data.toString() : data;

                if (i + 1 !== fieldIds.length) {
                    hash += "-";
                }
            }
            return hash;
        }

        $("input, select").on("change", function (e) {
            const disable = hashUserFields() === originalUserHash;
            submitButton.prop("disabled", disable);
        });

        $("#editUserForm").on("submit", function (e) {
            e.preventDefault();

            const userId = $("#userId").val();
            const data = {
                id: userId,
                email: $("#email").val(),
                first_name: $("#firstName").val(),
                last_name: $("#lastName").val(),
                date_of_birth: $("#dateOfBirth").val(),
                groups: $("#groups").val()
            };

            $.ajax({
                type: "POST",
                url: "/user/update",
                data: data,
                success: function (response) {

                    if (response.success) {
                        originalUserHash = hashUserFields()
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

        $('button.delete-user').click(function () {
            const userId = $("input#userId").val();

            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '/user/delete?userId=' + userId,
                    type: 'DELETE',
                    success: function (result) {
                        if (result.success) {
                            showSuccess(result.message ?? 'The user has been deleted successfully!');
                            setTimeout(function () {
                                window.location = "/user";
                            }, 1500);
                        } else {
                            showError(result.message ?? 'The user could not be deleted.');
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>