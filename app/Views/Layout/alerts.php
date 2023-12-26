<div class="max-w-lg mx-auto mt-3">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative alert alert-success"
         role="alert"
         style="display: none;">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline alert-text"></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 close cursor-pointer font-bold text-green-700 text-xl close">&times;</span>
    </div>

    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative alert alert-error"
         role="alert"
         style="display: none;">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline alert-text"></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 close cursor-pointer font-bold text-red-700 text-xl close">&times;</span>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('.alert .close').click(function () {
            $(this).parent().hide();
            $(this).parent().find("span.alert-text").text("");
        });

        window.showSuccess = (text) => {
            $(".alert-error").hide();
            $(".alert-success span.alert-text").text(text ?? "Your operation was successful.");
            $(".alert-success").show();

            setTimeout(function () {
                $(".alert-success").hide();
            }, 3000);
        };

        window.showError = (text) => {
            $(".alert-success").hide();
            $(".alert-error span.alert-text").text(text ?? "There was a problem processing the operation.");
            $(".alert-error").show();

            setTimeout(function () {
                $(".alert-error").hide();
            }, 3000);
        };
    });
</script>
