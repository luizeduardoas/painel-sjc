<?php

$__arrayStylos = array(
    'font' => 'Verdana, Arial, Helvetica, sans-serif;',
    'botao' => 'padding: 10px 25px; cursor: pointer; -webkit-border-radius: 15px !important; -moz-border-radius: 15px !important; border-radius: 15px !important; text-decoration: none;background-color: #046ca5; color: #ffffff;line-height: 30pt; font-weight: bold',
    'fundo' => array('bg' => '#efefef', 'color' => '#c30000', 'align' => 'center', 'size' => '8pt', 'line-height' => '10pt'),
    'topo' => array('bg' => '#ffffff', 'color' => '#046ca5', 'align' => 'center', 'size' => '18pt', 'line-height' => '22pt'),
    'titulo' => array('bg' => '#ffffff', 'color' => '#046ca5', 'align' => 'center', 'size' => '18pt', 'line-height' => '22pt'),
    'corpo' => array('bg' => '#ffffff', 'color' => '#838383', 'align' => 'justify', 'size' => '12pt', 'line-height' => '18pt'),
    'corpoObs' => array('bg' => '#ffffff', 'color' => '#9c01ff', 'align' => 'center', 'size' => '11pt', 'line-height' => '14pt'),
    'rodape' => array('bg' => '#fec413', 'color' => '#ffffff', 'align' => 'center', 'size' => '11pt', 'line-height' => '14pt'),
    'separador' => array('bg' => '#ffffff', 'color' => '#f3f3f3', 'align' => 'center', 'size' => '10pt', 'line-height' => '12pt')
);

/**
 * Montar código HTML com mensagem de email
 *
 * @param String $corpo mensagem em html a ser enviada
 * @param String $titulo título do email
 * @param String $rodape html a ser inserido no final do email
 * @param bool $automatico exibir mensagem no final do email "Este é um e-mail automático. Não é necessário respondê-lo." Default: true
 * @param String $remetente Endereço de email remetente Default: SYS_CONTATO
 * @param string $visualizador Cria no topo do email a opção de o cliente clicar e ver o email externamente. Default: false
 * @return String 
 */
function gerarEmail($corpo, $titulo, $rodape = '', $automatico = true, $remetente = SYS_CONTATO, $visualizador = false) {
    global $__arrayStylos;

    $assinatura = '<p style="color: ' . $__arrayStylos["rodape"]["color"] . ';font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["rodape"]["size"] . ';line-height:' . $__arrayStylos["rodape"]["line-height"] . ';"><strong>' . SYS_ASSINATURA . '</strong><br/>';
    $assinatura .= '<p style="color: ' . $__arrayStylos["rodape"]["color"] . ';font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["rodape"]["size"] . ';line-height:' . $__arrayStylos["rodape"]["line-height"] . ';">';
    $assinatura .= '<a href="' . URL_SYS . '" style="color: ' . $__arrayStylos["rodape"]["color"] . '; " target="_blank">' . URL_ENDERECO . '</a><br />';
    $assinatura .= '<a href="mailto:' . SYS_EMAIL . '" style="color: ' . $__arrayStylos["rodape"]["color"] . '; " target="_blank">' . CONTATO . '</a><br/>';
    $assinatura .= '</p>';


    $msg = '';
    if ($visualizador !== false) {
        $msg .= gerarVisualizadorEmail($visualizador);
    }
    $msg .= trim('<div style="font-family:' . $__arrayStylos["font"] . 'text-align:left;margin:0;padding:0;width:100%;background-color:' . $__arrayStylos["fundo"]["bg"] . ';color:' . $__arrayStylos["fundo"]["color"] . ';">');
    $msg .= trim('    <center>');
    $msg .= trim('       <table width="875" border="0" cellspacing="0" cellpadding="0" style="max-width: 875px; width: 100%;font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';text-align:' . $__arrayStylos["corpo"]["align"] . ';background-repeat:no-repeat;color:' . $__arrayStylos["corpo"]["color"] . '" bgcolor="' . $__arrayStylos["corpo"]["bg"] . '">');
    $msg .= trim('            <tbody>');
//<editor-fold desc="Fundo">
    $msg .= trim('                <tr>');
    $msg .= trim('                    <td colspan="3" align="center" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="30" width="100%">');
    $msg .= trim('                        <font style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["fundo"]["size"] . ';line-height:' . $__arrayStylos["fundo"]["line-height"] . ';text-align:' . $__arrayStylos["fundo"]["align"] . ';color:' . $__arrayStylos["fundo"]["color"] . ';background:' . $__arrayStylos["fundo"]["bg"] . ';">Adicione "<a href="mailto:' . $remetente . '" target="_blank" style="color:' . $__arrayStylos["fundo"]["color"] . ';">' . SYS_NOME . '</a>" aos seus endereços de e-mail.</font>');
    $msg .= trim('                    </td>');
    $msg .= trim('                </tr>');
//</editor-fold>
//<editor-fold desc="Topo">
    $msg .= trim('               <tr>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="170" width="10%">&nbsp;</td>');
    $msg .= trim('                   <td>');
    $msg .= trim('                      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="' . $__arrayStylos["topo"]["bg"] . '">');
    $msg .= trim('                          <tr>');
    $msg .= trim('                              <td align="center" valign="middle" bgcolor="' . $__arrayStylos["topo"]["bg"] . '"><img src="' . URL_SYS_TEMA_GLOBAL . 'images/email_topo.jpg" width="100%" height="100%" alt="" style="display: block; height: auto; width: 100%;"/></td>');
    $msg .= trim('                          </tr>');
    $msg .= trim('                      </table>');
    $msg .= trim('                   </td>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="170" width="10%">&nbsp;</td>');
    $msg .= trim('               </tr>');
//</editor-fold>
//<editor-fold desc="Titulo">
    $msg .= trim('               <tr>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="40" width="10%">&nbsp;</td>');
    $msg .= trim('                   <td align="center" valign="middle" bgcolor="' . $__arrayStylos["titulo"]["bg"] . '" style="padding: 20px 40px;">');
    $msg .= trim('                      <span style="color: ' . $__arrayStylos["titulo"]["color"] . '; display: block; font-family: ' . $__arrayStylos["font"] . '; font-size:' . $__arrayStylos["titulo"]["size"] . ';line-height:' . $__arrayStylos["titulo"]["line-height"] . '; font-weight: bold; margin-left: auto; margin-right: auto; max-width: 80%; text-align: center;">' . $titulo . '</span>');
    $msg .= trim('                   </td>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="40" width="10%">&nbsp;</td>');
    $msg .= trim('               </tr>');
//</editor-fold>
//<editor-fold desc="Separador">
    $msg .= trim('                <tr>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="10">&nbsp;</td>');
    $msg .= trim('                   <td align="center" valign="middle" bgcolor="' . $__arrayStylos["separador"]["bg"] . '" height="20" style="display: block;">&nbsp;</td>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="10">&nbsp;</td>');
    $msg .= trim('                </tr>');
//</editor-fold>
//<editor-fold desc="Corpo">
    $msg .= trim('               <tr>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '">&nbsp;</td>');
    $msg .= trim('                   <td align="left" valign="top" bgcolor="' . $__arrayStylos["corpo"]["bg"] . '" style="padding: 20px 40px;">');
    $msg .= trim('                      <span style="font-family:' . $__arrayStylos["font"] . 'color:' . $__arrayStylos["corpo"]["color"] . ';font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . '; margin-left: auto; margin-right: auto; max-width: 80%; text-align: ' . $__arrayStylos["corpo"]["align"] . ';">' . $corpo . '</span>');
    $msg .= trim('                   </td>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '">&nbsp;</td>');
    $msg .= trim('               </tr>');
//</editor-fold>
//<editor-fold desc="Separador">
    $msg .= trim('                <tr>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="10">&nbsp;</td>');
    $msg .= trim('                   <td align="center" valign="middle" bgcolor="' . $__arrayStylos["separador"]["bg"] . '" height="20" style="display: block;">&nbsp;</td>');
    $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="10">&nbsp;</td>');
    $msg .= trim('                </tr>');
//</editor-fold>
    if ($assinatura || $rodape) {
//<editor-fold desc="Rodape">
        $msg .= trim('               <tr>');
        $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '">&nbsp;</td>');
        $msg .= trim('                   <td align="left" valign="top" bgcolor="' . $__arrayStylos["rodape"]["bg"] . '" style="padding: 20px 40px; background: url(\'' . URL_SYS_TEMA_GLOBAL . 'images/email_rodape.jpg\');">');
        if ($assinatura) {
            $msg .= trim('                      <span style="font-family:' . $__arrayStylos["font"] . 'color:' . $__arrayStylos["rodape"]["color"] . ';font-size:' . $__arrayStylos["rodape"]["size"] . ';line-height:' . $__arrayStylos["rodape"]["line-height"] . '; margin-left: auto; margin-right: auto; max-width: 80%; text-align: ' . $__arrayStylos["rodape"]["align"] . ';">' . $assinatura . '</span>');
        }
        if ($rodape) {
            $msg .= trim('                      <span style="font-family:' . $__arrayStylos["font"] . 'color:' . $__arrayStylos["rodape"]["color"] . ';font-size:' . $__arrayStylos["rodape"]["size"] . ';line-height:' . $__arrayStylos["rodape"]["line-height"] . '; margin-left: auto; margin-right: auto; max-width: 80%; text-align: ' . $__arrayStylos["rodape"]["align"] . ';">' . $rodape . '</span>');
        }
        $msg .= trim('                   </td>');
        $msg .= trim('                   <td valign="top" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '">&nbsp;</td>');
        $msg .= trim('               </tr>');
//</editor-fold>
    }
//<editor-fold desc="Email Automático">
    if ($automatico) {
        $msg .= trim('                <tr>');
        $msg .= trim('                    <td colspan="3" align="center" valign="top"  height="40" width="100%" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '">');
        $msg .= trim('                    	<span style="font-family:' . $__arrayStylos["font"] . 'font-size:12px;color:' . $__arrayStylos["fundo"]["color"] . ';background:' . $__arrayStylos["fundo"]["bg"] . ';">Este é um e-mail automático. Não é necessário respondê-lo.</span>');
        $msg .= trim('                    </td>');
        $msg .= trim('                </tr>');
    }
//</editor-fold>
    $msg .= trim('            </tbody>');
    $msg .= trim('        </table>');
    $msg .= trim('    </center>');
    $msg .= trim('</div>');
    return trim($msg);
}

/**
 * Montar código HTML com mensagem para incluir no email caso o mesmo não seja visualizado
 *
 * @param String $codigo
 * @return String 
 */
function gerarVisualizadorEmail($codigo) {
    global $__arrayStylos;
    $msg = '';
    $msg .= trim('<div style="font-family:' . $__arrayStylos["font"] . 'text-align:left;margin:0;padding:0;width: 100%;background-color:' . $__arrayStylos["fundo"]["bg"] . ';color:' . $__arrayStylos["fundo"]["color"] . ';">');
    $msg .= trim('    <center>');
    $msg .= trim('       <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="' . $__arrayStylos["corpo"]["bg"] . '" style="font-size:12px;text-align:left;background-repeat:no-repeat;">');
    $msg .= trim('            <tbody>');
    $msg .= trim('                <tr>');
    $msg .= trim('                    <td align="center" bgcolor="' . $__arrayStylos["fundo"]["bg"] . '" height="20">');
    $msg .= trim('                        <font style="font-family:' . $__arrayStylos["font"] . 'font-size:12px;color:' . $__arrayStylos["fundo"]["color"] . ';background:' . $__arrayStylos["fundo"]["bg"] . ';">Se você não conseguir visualizar esta mensagem, acesse este <a href="' . URL_SYS . 'email/' . $codigo . '" target="_blank" style="color:' . $__arrayStylos["fundo"]["color"] . ';">link</a>.</font>');
    $msg .= trim('                    </td>');
    $msg .= trim('                </tr>');
    $msg .= trim('            </tbody>');
    $msg .= trim('        </table>');
    $msg .= trim('    </center>');
    $msg .= trim('</div>');
    return $msg;
}

/**
 * Montar código HTML com mensagem enviada através de formulário
 * 
 * @param String $formulario
 * @param String $nome
 * @param String $email
 * @param String $telefone
 * @param String $assunto
 * @param String $mensagem
 * @return String
 */
function gerarEmailFormulario($formulario, $nome, $email, $telefone, $assunto, $mensagem) {
    global $__arrayStylos;
    $msg = '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';"><b>' . $nome . '</b> enviou uma mensagem através do formulário ' . $formulario . '.</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Email: <b>' . $email . '</b></p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Telefone: <b>' . $telefone . '</b></p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Assunto: <b>' . $assunto . '</b></p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Mensagem:</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';"><b>' . $mensagem . '</b></p>';
    return gerarEmail($msg, 'Mensagem do Formulário ' . $formulario);
}

/**
 * Montar código HTML com mensagem de email informando do cadastro e com link de ativação
 *
 * @param Usuario $usuario Objeto Usuario que se cadastrou
 * @param String $token String com código do token para ativação
 * @param String $senha Senha de acesso do usuário
 * @return String
 */
function gerarEmailCadastro($usuario, $token, $senha = false) {
    global $__arrayStylos;
    $msg = '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Olá <b>' . $usuario->getUsu_var_nome() . '</b>,</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Seja bem-vindo(a) ao <a href="' . URL_SYS . '" style="color:' . $__arrayStylos["corpo"]["color"] . ';text-decoration:none;font-weight:bold;" target="_blank">' . SYS_NOME . '</a> e para ter acesso ao sistema, é necessário que ative seu cadastro confirmando seu endereço de e-mail. Para isso, clique no botão:</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';text-align:center;"><a href="' . URL_SYS . 'validate/' . $token . '/" style="' . $__arrayStylos["botao"] . ';">CONFIRMAR CONTA</a></p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Segue abaixo as informações necessárias para acessar nosso sistema:</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">E-Mail*: <b>' . $usuario->getUsu_var_email() . '</b></p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Identificador*: <b>' . $usuario->getUsu_var_identificador() . '</b></p>';
    if ($senha) {
        $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Senha: <b>' . $senha . '</b></p>';
    }
    $msg .= '<br/>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpoObs"]["size"] . ';line-height:' . $__arrayStylos["corpoObs"]["line-height"] . ';color:' . $__arrayStylos["corpoObs"]["color"] . ';text-align:' . $__arrayStylos["corpoObs"]["align"] . ';">*Para acessar o nosso sistema, você pode utilizar seu identificador ou seu e-mail.</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpoObs"]["size"] . ';line-height:' . $__arrayStylos["corpoObs"]["line-height"] . ';color:' . $__arrayStylos["corpoObs"]["color"] . ';text-align:' . $__arrayStylos["corpoObs"]["align"] . ';">Obs. Para melhor segurança, solicitamos que acesse nosso sistema e altere sua senha para uma de mais fácil compreensão.</p>';
    return gerarEmail($msg, 'Bem-vindo(a) ao ' . SYS_NOME, '');
}


/**
 * Montar código HTML com mensagem de email para recuperar a senha do usuário.
 *
 * @param Usuario $usuario Objeto Usuário que deseja recuperar a senha
 * @param String $token String com o código do token que deseja enviar
 * @return String
 */
function gerarEmailEsqueciSenha($usuario, $token) {
    global $__arrayStylos;
    $msg = '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Olá <b>' . $usuario->getUsu_var_nome() . '</b>,</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';">Você solicitou a recuperação de sua senha do sistema <a href="' . URL_SYS . '" style="color:' . $__arrayStylos["corpo"]["color"] . ';text-decoration:none;font-weight:bold;" target="_blank">' . SYS_NOME . '</a>. Para realizar a alteração é necessário criar uma nova senha. Confirme essa solicitação e digite uma nova senha clicando no botão:</p>';
    $msg .= '<p style="font-family:' . $__arrayStylos["font"] . 'font-size:' . $__arrayStylos["corpo"]["size"] . ';line-height:' . $__arrayStylos["corpo"]["line-height"] . ';color:' . $__arrayStylos["corpo"]["color"] . ';text-align:center;"><a href="' . URL_SYS . 'recovery/' . $token . '/" style="' . $__arrayStylos["botao"] . ';">CRIAR NOVA SENHA</a></p>';
    return gerarEmail($msg, 'Recuperar sua Senha', '');
}

?>