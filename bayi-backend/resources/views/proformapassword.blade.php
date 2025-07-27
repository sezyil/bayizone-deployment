<script>
    //get browser language
    var lang = navigator.language || navigator.userLanguage;
    var promptText = "";
    var errorText = "";
    if (lang == "tr-TR") {
        promptText = "Bu sayfayı görüntülemek için size iletilen şifreyi giriniz";
        errorText = "Şifre yanlış/eksik";
    } else {
        promptText = "Please enter password to view this page";
        errorText = "Wrong/missing password";
    }
    //ask for password
    var password = prompt(promptText, "");
    if (password != null) {
        //get query parameters
        var urlParams = new URLSearchParams(window.location.search);
        //add password to query parameters
        urlParams.set('pass', password);
        //redirect to same page with password
        window.location.href = '{{ $url }}' + "?" + urlParams.toString();
    } else {
        alert(errorText);
    }
</script>
