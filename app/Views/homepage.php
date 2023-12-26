<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Project Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
          rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
<?php require __DIR__ . '/Layout/navbar.php'; ?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold text-center mb-6">Welcome to the user user-group management system</h1>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-4">About the Project</h2>
        <p class="mb-4">This project is a simplified user management system designed to streamline the
            process of managing users and their group affiliations. Our platform provides a simple user-friendly
            interface for
            performing a variety of functions, including creating, editing, and deleting users, as well as
            managing user groups.</p>

        <h2 class="text-2xl font-bold mb-4">The Approach</h2>
        <p class="mb-4">In developing this project, I adopted a methodical and user-centric approach. My primary focus
            was on creating a system that is both intuitive and efficient, ensuring that even users with minimal
            technical expertise can navigate and utilize the platform with ease.</p>
        <p class="mb-4">I utilized the pure PHP 7.4 with MVC approach, JQuery and Select2 library on the front with
            MariaDB database, ensuring
            that our system is not only
            functional but also aligns with provided project requirements.</p>

        <h2 class="text-2xl font-bold mb-4">Key Features</h2>
        <ul class="list-disc list-inside mb-4">
            <li>User Creation and Management</li>
            <li>Group Management and Assignments</li>
            <li>Responsive Design for Multi-Device Accessibility</li>
            <li>Real-Time Data Processing and Updates</li>
        </ul>

        <p class="mb-4">I am committed to continuous improvement and are always open to feedback and suggestions from
            our
            reviewers.</p>
        <a href="https://github.com/Kento1221/user-usergroup-crud-app"
           class="text-blue-500 underline">Kento1221 github project site.</a>
    </div>
</div>
</body>
</html>