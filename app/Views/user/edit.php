<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
          rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        <div class="flex items-center justify-end">
            <button type="submit"
                    disabled
                    class="bg-blue-400 enabled:bg-blue-500 enabled:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Save
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        let originalUserHash = '<?php echo implode('-', [$user->email, $user->first_name, $user->last_name, $user->date_of_birth]); ?>';
        const submitButton = $("#editUserForm button[type='submit']");

        const hashUserFields = () => {
            const fieldIds = ["email", "firstName", "lastName", 'dateOfBirth'];
            let hash = "";

            for (let i = 0; i < fieldIds.length; i++) {
                const data = $("#" + fieldIds[i]).val();

                hash += data;

                if (i + 1 !== fieldIds.length) {
                    hash += "-";
                }
            }
            return hash;
        }

        $("input").on("change", function (e) {
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
                date_of_birth: $("#dateOfBirth").val()
            };

            $.ajax({
                type: "POST",
                url: "/user/update",
                data: data,
                success: function (response) {
                    console.log(response);
                    if (response.success) {
                        originalUserHash = hashUserFields()
                        showSuccess(response.message);
                    } else {
                        showError(response.message);
                    }
                    submitButton.prop("disabled", true)
                },
                error: function (response) {
                    console.error(response);
                    showError(response.message ?? null);
                }
            });
        });
    });
</script>
</body>
</html>