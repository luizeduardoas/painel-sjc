var URL_SYS = 'https://capte.me/';
var SERVIDOR = 'D';

function redirecionar(url, parente) {
    if (parente == false || parente == undefined) {
        jQuery.gDisplay.loadStart('HTML');
        window.location = url;
    } else {
        parent.jQuery.gDisplay.loadStart('HTML');
        parent.window.location = url;
    }
}

/**
 * Busca a posição de latitude e longitude de um endereço
 * 
 * @param string address
 * @param function callback
 * @returns function
 */
function GetLocation(address, callback) {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': address}, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var lat = parseFloat(results[0].geometry.location.lat()).toPrecision(10);
            var lon = parseFloat(results[0].geometry.location.lng()).toPrecision(10);
            console.log("lat: " + lat + " lon: " + lon);
            callback({status: "OK", lat: lat, lon: lon});
        }
    });
}

/**
 * Limita a quantidade de caracteres a ser digitado em um campo
 * 
 * @param {type} Campo
 * @param {type} Quantos
 */
function Limitar_Caracteres(Campo, Quantos) {
    jQuery("#" + Campo).keyup(function (event) {
        var Limite = Quantos;
        if (jQuery(this).val().length >= Limite) {
            jQuery(this).val(jQuery(this).val().substring(0, Limite));
        }
    });
}

/**
 * Arredonda valor de acordo com as casas decimais passada
 * 
 * @param {type} valor
 * @param {type} casas
 * @returns {Number}
 */
function Arredonda(valor, casas) {
    var novo = Math.round(valor * Math.pow(10, casas)) / Math.pow(10, casas);
    return(novo);
}

/**
 * Converte moeda em valor float
 * 
 * @param {type} moeda
 * @returns float
 */
function moeda2float(moeda) {
    moeda = moeda.replace(".", "");
    moeda = moeda.replace(",", ".");
    return parseFloat(moeda);
}

/**
 * Converte float em moeda
 * 
 * @param {type} num
 * @returns {ret|String}
 */
function float2moeda(num) {
    x = 0;
    if (num < 0) {
        num = Math.abs(num);
        x = 1;
    }
    if (isNaN(num))
        num = "0";
    cents = Math.floor((num * 100 + 0.5) % 100);
    num = Math.floor((num * 100 + 0.5) / 100).toString();
    if (cents < 10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + '.'
                + num.substring(num.length - (4 * i + 3));
    ret = num + ',' + cents;
    if (x == 1)
        ret = ' - ' + ret;
    return ret;
}

/**
 * Preenche uma string para um certo tamanho com outra string
 * 
 * @param {type} input
 * @param {type} pad_length
 * @param {type} pad_string
 * @param {type} pad_type
 * @returns {str_pad.str_pad_repeater.collect|String|@var;half|@var;input}
 */
function str_pad(input, pad_length, pad_string, pad_type) {
    var half = '', pad_to_go;
    var str_pad_repeater = function (s, len) {
        var collect = '', i;
        while (collect.length < len) {
            collect += s;
        }
        collect = collect.substr(0, len);
        return collect;
    };
    input += '';
    pad_string = pad_string !== undefined ? pad_string : ' ';
    if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') {
        pad_type = 'STR_PAD_RIGHT';
    }
    if ((pad_to_go = pad_length - input.length) > 0) {
        if (pad_type == 'STR_PAD_LEFT') {
            input = str_pad_repeater(pad_string, pad_to_go) + input;
        } else if (pad_type == 'STR_PAD_RIGHT') {
            input = input + str_pad_repeater(pad_string, pad_to_go);
        } else if (pad_type == 'STR_PAD_BOTH') {
            half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
            input = half + input + half;
            input = input.substr(0, pad_length);
        }
    }

    return input;
}

/**
 * Checa se um valor existe em um array
 * 
 * @param {type} needle
 * @param {type} haystack
 * @returns {Boolean}
 */
function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
            return true;
    }
    return false;
}

function verifyCpf(cpf) {
    cpf = retirarMask(cpf);
    if (cpf == "") {
        return true;
    }
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11) {
        return false;
    }
    for (i = 0; i < cpf.length - 1; i++) {
        if (cpf.charAt(i) != cpf.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
        }
    }
    if (!digitos_iguais) {
        numeros = cpf.substring(0, 9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--) {
            soma += numeros.charAt(10 - i) * i
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) {
            return false;
        }
        numeros = cpf.substring(0, 10);
        soma = 0;
        for (i = 11; i > 1; i--) {
            soma += numeros.charAt(11 - i) * i
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) {
            return false;
        }
        return true;
    } else {
        return false;
    }
}

function seVazioOuNullOuMenosUm(valor) {
    if (valor == null || valor == '-1' || valor == '' || valor == undefined || valor == NaN) {
        return true;
    } else {
        return false;
    }
}

function seVazioOuNullRetorneMenosUm(valor) {
    if (valor == null || valor == '' || valor == undefined || valor == NaN) {
        return '-1';
    } else {
        return valor;
    }
}