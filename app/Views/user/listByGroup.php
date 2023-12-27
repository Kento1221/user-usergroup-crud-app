<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>List of users for group</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
          rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
<?php include __DIR__ . '/../Layout/navbar.php'; ?>
<?php include __DIR__ . '/../Layout/alerts.php'; ?>

<div class="max-w-7xl mx-auto bg-white p-6 rounded shadow mb-12 mt-3">
    <h3 class="text-center font-bold text-2xl">Users for group: <span class="bg-green-300 px-5 py-1 rounded-full"><?php echo $group->name;?></span></h3>

    <table class="table-auto w-full mt-6">
        <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">First name</th>
            <th class="px-4 py-2">Last name</th>
            <th class="px-4 py-2">Date of birth</th>
            <th class="px-4 py-2">Group count</th>
            <th class="px-4 py-2">Created at</th>
            <th class="px-4 py-2">Updated at</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($users ?? [] as $index => $user) {
            $rowClasses = $index % 2 ? 'bg-gray-100' : '';
            $groupCount = count(array_column($user->groups(), 'name'));

            echo <<<EOL
                <tr class="$rowClasses hover:bg-gray-200">
                    <td class="text-center">$user->id</td>
                    <td class="text-center">$user->name</td>
                    <td class="text-center">$user->first_name</td>
                    <td class="text-center">$user->last_name</td>
                    <td class="text-center">$user->date_of_birth</td>
                    <td class="text-center">$groupCount</td>
                    <td class="text-center text-xs">$user->created_at</td>
                    <td class="text-center text-xs">$user->updated_at</td>
                    <td class="text-center">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="/user/edit?userId=$user->id" class="bg-blue-500 text-sm hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Edit</a>
                            <button class="delete-user text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-user-id="$user->id">Delete</button>
                        </div>
                    </td>
                </tr>
            EOL;
        }
        ?>
        </tbody>
    </table>

</div>

<script>
    $(document).ready(function () {

        $("button.delete-user").click(function () {
            const userId = $(this).data("user-id");

            if (confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    url: "/user/delete?userId=" + userId,
                    type: "DELETE",
                    success: function (result) {
                        if (result.success) {
                            showSuccess(result.message ?? "The user has been deleted successfully!");
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showError(result.message ?? "The user could not be deleted.");
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>