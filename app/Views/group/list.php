<?php
$page = $page ?? 1;
$isNextPage = $isNextPage ?? false;

$previousPage = $page > 1 ? $page - 1 : $page;
$nextPage = $isNextPage ? $page + 1 : $page;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>List of user groups</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
          rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
<?php include __DIR__ . '/../Layout/navbar.php'; ?>
<?php include __DIR__ . '/../Layout/alerts.php'; ?>

<div class="max-w-7xl mx-auto bg-white p-6 rounded shadow mb-12 mt-3">
    <div class="flex mb-3 justify-end">
        <a href="/group/create"
           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            + Add new group
        </a>
    </div>

    <table class="table-auto w-full">
        <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">User count</th>
            <th class="px-4 py-2">Created at</th>
            <th class="px-4 py-2">Updated at</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($groups ?? [] as $index => $group) {
            $rowClasses = $index % 2 ? 'bg-gray-100' : '';
            $userCount = count(array_column($group->users(), 'id'));

            echo <<<EOL
                <tr class="$rowClasses hover:bg-gray-200">
                    <td class="text-center">$group->id</td>
                    <td class="text-center">$group->name</td>
                    <td class="text-center">$userCount</td>
                    <td class="text-center text-xs">$group->created_at</td>
                    <td class="text-center text-xs">$group->updated_at</td>
                    <td class="text-center">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="/user/listByGroup?groupId=$group->id" class="bg-yellow-400 text-sm hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Show users</a>
                            <a href="/group/edit?groupId=$group->id" class="bg-blue-500 text-sm hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Edit</a>
                            <button class="delete-group text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-group-id="$group->id">Delete</button>
                        </div>
                    </td>
                </tr>
            EOL;
        }
        ?>
        </tbody>
    </table>
    <div class="flex justify-center mt-4">
        <a href="/group?page=<?php echo isset($page) && $page > 1 ? $page - 1 : $page; ?>"
           class="mx-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Previous</a>
        <a href="/group?page=<?php echo isset($isNextPage) && $isNextPage ? $page + 1 : $page; ?>"
           class="mx-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Next</a>
    </div>

</div>

<script>
    $(document).ready(function () {

        $("button.delete-group").click(function () {
            const groupId = $(this).data("group-id");

            if (confirm("Are you sure you want to delete this group?")) {
                $.ajax({
                    url: "/group/delete?groupId=" + groupId,
                    type: "DELETE",
                    success: function (result) {
                        if (result.success) {
                            showSuccess(result.message ?? "The group has been deleted successfully!");
                            setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showError(result.message ?? "The group could not be deleted.");
                        }
                    }
                });
            }
        });
    });
</script>
</body>
</html>