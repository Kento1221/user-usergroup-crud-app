<button type="button"
        class="go-back bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
    Go back
</button>

<script>
    $(document).ready(function () {
        $("button.go-back").click(function () {
            history.back();
        });
    });
</script>