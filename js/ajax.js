function openAjax() {
    var ajax;

    try {
        ajax = new XMLHttpRequest;

    } catch (erro) {
        try {
            ajax = new ActiveXObject("Msxl2.XMLHTTP");
        } catch (ee) {
            try {
                ajax = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                ajax = false;
            }
        }
    }
    return ajax;
}

function busca(pag) {

    if (document.getElementById) {
        var termo = document.getElementById('filtro').value;
        var exibeResultado = document.getElementById('resultado');

        if (termo.length >= 1) {
            var ajax = openAjax();

            ajax.open("GET", "atualiza.php?filtro=" + termo + "&pagina=" + pag, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 1) {
                    exibeResultado.innerHTML = '<p>Carregando Resultados...</p>';
                }
                if (ajax.readyState == 4) {
                    if (ajax.status == 200) {
                        var resultado = ajax.responseText;
                        resultado = resultado.replace(/\+/g, " ");
                        resultado = unescape(resultado);
                        exibeResultado.innerHTML = resultado;
                    } else {
                        exibeResultado.innerHTML = '<p>Erro na requisição!</p>';
                    }
                }
            }
            ajax.send(null);
        } else if (termo == "") {
            var ajax = openAjax();

            ajax.open("GET", "atualiza.php?filtro=" + termo + "&pagina=" + pag, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 1) {
                    exibeResultado.innerHTML = '<p>Carregando Resultados...</p>';
                }
                if (ajax.readyState == 4) {
                    if (ajax.status == 200) {
                        var resultado = ajax.responseText;
                        resultado = resultado.replace(/\+/g, " ");
                        resultado = unescape(resultado);
                        exibeResultado.innerHTML = resultado;
                    } else {
                        exibeResultado.innerHTML = '<p>Erro na requisição!</p>';
                    }
                }
            }
            ajax.send(null);
        }
    }
}

function paginacao() {

    $('#resultado').on('click', 'a', function () {

        var urlget = this.href;
        var envio = $(this).serialize();

        $.ajax({
            url: urlget,
            dataType: 'html',
            data: envio,
            type: 'GET',
            success: function (data) {

                $('#resultado').html(data);
            }
        });
        return false;
    });
    $('#resultado').on('click', 'a', function () {
        $('html, body').animate({scrollTop: 450}, 'slow');
        return false;
    });

}

function atualizaPagina() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("resultado").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "atualiza.php?", true);
    xmlhttp.send();
    setTimeout(function () {
        atualizaPagina();
    }, 10000);
}

//------------------------------------------\/BUSCA INDEX LUGARES\/-------------------------------------------
function busca_index_lugares() {

    if (document.getElementById) {
        var termo = document.getElementById('filtro_lugares').value;
        var exibeResultado = document.getElementById('lugares');

        if (termo == "") {
            var ajax = openAjax();

            ajax.open("GET", "atualiza_index_lugares.php?filtro_lugares=" + termo, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 1) {
                    exibeResultado.innerHTML = '<p>Carregando Resultados...</p>';
                }
                if (ajax.readyState == 4) {
                    if (ajax.status == 200) {
                        var resultado = ajax.responseText;
                        resultado = resultado.replace(/\+/g, " ");
                        resultado = unescape(resultado);
                        exibeResultado.innerHTML = resultado;
                    } else {
                        exibeResultado.innerHTML = '<p>Erro na requisição!</p>';
                    }
                }
            }
            ajax.send(null);
        }
    }
}

function atualizaPagina_index_lugares() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("lugares").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "atualiza_index_lugares.php?", true);
    xmlhttp.send();
    setTimeout(function () {
        atualizaPagina_index_lugares();
    }, 5000);
}

//------------------------------------------\/BUSCA INDEX HOTEIS\/-------------------------------------------
function busca_index_hoteis() {

    if (document.getElementById) {
        var termo = document.getElementById('filtro_hoteis').value;
        var exibeResultado = document.getElementById('hoteis');

        if (termo == "") {
            var ajax = openAjax();

            ajax.open("GET", "atualiza_index_hoteis.php?filtro_hoteis=" + termo, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 1) {
                    exibeResultado.innerHTML = '<p>Carregando Resultados...</p>';
                }
                if (ajax.readyState == 4) {
                    if (ajax.status == 200) {
                        var resultado = ajax.responseText;
                        resultado = resultado.replace(/\+/g, " ");
                        resultado = unescape(resultado);
                        exibeResultado.innerHTML = resultado;
                    } else {
                        exibeResultado.innerHTML = '<p>Erro na requisição!</p>';
                    }
                }
            }
            ajax.send(null);
        }
    }
}

function atualizaPagina_index_hoteis() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("hoteis").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "atualiza_index_hoteis.php?", true);
    xmlhttp.send();
    setTimeout(function () {
        atualizaPagina_index_hoteis();
    }, 5000);
}