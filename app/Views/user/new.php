<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>Create new user</title>
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
    <form id="addUserForm">

        <div class="mb-4">
            <label for="name"
                   class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
            <input type="text"
                   id="name"
                   name="name"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="firstName"
                   class="block text-gray-700 text-sm font-bold mb-2">First name:</label>
            <input type="text"
                   id="firstName"
                   name="first_name"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="lastName"
                   class="block text-gray-700 text-sm font-bold mb-2">Last name:</label>
            <input type="text"
                   id="lastName"
                   name="last_name"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="dateOfBirth"
                   class="block text-gray-700 text-sm font-bold mb-2">Date of birth:</label>
            <input type="date"
                   id="dateOfBirth"
                   name="date_of_birth"
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
                    echo <<<EOL
                        <option value="$group->id">$group->name</option>
                    EOL;
                }
                ?>
            </select>
        </div>

        <div class="mb-6">
            <label for="password"
                   class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="checkPassword"
                   class="block text-gray-700 text-sm font-bold mb-2">Check password:</label>
            <input type="password"
                   id="checkPassword"
                   name="check_password"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex items-center justify-between">
            <?php include __DIR__ . '/../Layout/back-button.php'; ?>

            <button type="submit"
                    class="bg-blue-400 enabled:bg-blue-500 enabled:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Save
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {

        $("select#groups").select2();

        $("#addUserForm").on("submit", function (e) {
            e.preventDefault();
            const password = $("#password");

            if (password.val() !== $("#checkPassword").val()) {
                showError("Password and check password have to match.");
                return;
            }

            const data = {
                name: $("#name").val(),
                first_name: $("#firstName").val(),
                last_name: $("#lastName").val(),
                date_of_birth: $("#dateOfBirth").val(),
                password: password.val(),
                groups: $("#groups").val()
            };

            $.ajax({
                type: "POST",
                url: "/user/store",
                data: data,
                success: function (response) {

                    if (response.success) {
                        showSuccess(response.message);
                        setTimeout(function () {
                            window.location = "/user/edit?userId=" + response.user.id
                        }, 1500)
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