function redirecionar(url) {
    jQuery.gDisplay.loadStart('HTML');
    window.location = url;
}

function getVisualizacaoCookie(modulo, sevazio) {
    var retorno = jQuery.cookie(modulo);
    if (retorno == '' || retorno == undefined) {
        retorno = sevazio;
    }
    return retorno;
}
function setVisualizacaoCookie(modulo, opcao) {
    return jQuery.cookie(modulo, opcao, paramCookie);
}

function getParametroCookie(parametro, sevazio) {
    var retorno = jQuery.cookie(parametro);
    if (retorno == '' || retorno == undefined) {
        retorno = sevazio;
    }
    return retorno;
}
function setParametroCookie(parametro, valor, options) {
    if (options === undefined) {
        return jQuery.cookie(parametro, valor);
    } else {
        return jQuery.cookie(parametro, valor, options);
    }
}

function jsonConcat(o1, o2) {
    for (var key in o2) {
        o1[key] = o2[key];
    }
    return o1;
}

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

/**
 * Calcula o intervalo em minutos entre duas datas
 * 
 * @param {type} dataInicial
 * @param {type} dataFinal
 * @returns {Number}
 */
function calcularDiferencaEmMinutos(dataInicial, dataFinal) {
    var dataInicio = new Date(dataInicial);
    var dataFim = new Date(dataFinal);
    var timestampInicio = dataInicio.getTime();
    var timestampFim = dataFim.getTime();
    var diferencaMilissegundos = timestampFim - timestampInicio;
    return Math.floor(diferencaMilissegundos / 60000);
}

/**
 * Calcula o intervalo em dias entre duas datas
 * 
 * @param {type} dataInicial
 * @param {type} dataFinal
 * @returns {Number}
 */
function calcularDiferencaEmDias(dataInicial, dataFinal) {
    var dataInicio = new Date(dataInicial);
    var dataFim = new Date(dataFinal);
    var timestampInicio = dataInicio.getTime();
    var timestampFim = dataFim.getTime();
    var diferencaMilissegundos = timestampFim - timestampInicio;
    return Math.floor(diferencaMilissegundos / (1000 * 60 * 60 * 24));
}

/**
 * Converter uma data e hora para formato brasileiro
 * 
 * @param {type} datahorabrasileira
 * @returns {String}
 */
function converterData(datahorabrasileira) {
    var datahora = datahorabrasileira.split(' ');
    var data = datahora[0].split('/');
    var dia = data[0];
    var mes = data[1];
    var ano = data[2];

    if (datahora[1] === undefined)
        return ano + '-' + mes + '-' + dia;
    else
        return ano + '-' + mes + '-' + dia + ' ' + datahora[1];
}

/**
 * Converter um horário em minutos
 * 
 * @param {string} horario
 * @returns {int}
 */
function converterHorarioParaMinutos(horario) {
    var arrHorario = horario.split(':');
    var horasEmMinutos = parseInt(arrHorario[0]) * 60;
    var minutosTotais = horasEmMinutos + parseInt(arrHorario[1]);

    return minutosTotais;
}

/**
 * Converter minutos em horário
 * 
 * @param {int} minutos
 * @returns {string}
 */
function converterMinutosParaHorario(minutos) {
    var horas = Math.floor(minutos / 60);
    var minutosRestantes = minutos % 60;
    return horas.toString().padStart(2, '0') + ':' + minutosRestantes.toString().padStart(2, '0');
}
