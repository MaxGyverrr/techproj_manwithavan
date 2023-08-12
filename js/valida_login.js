function validarLogin() {
    if ((document.formLogin.login.value == "") || (document.formLogin.login.value.length < 3)) {
        alert("Login inválido!");
        document.formLogin.login.focus();
        return false;
    }

    if (document.formLogin.senha.value.length < 3) {
        alert("Login inválido!");
        document.formLogin.senha.focus();
        return false;
    }

    return true;
}