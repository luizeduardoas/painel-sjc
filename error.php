<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/inc/global.php");

GSecurity::verificarAutenticacao();

$subDescricao = '';
global $_id;
switch ($_id) {
    case "0":
        $icon = "ban";
        $titulo = "Não Autorizado!";
        $descricao = "Infelizmente você não tem autorização para acessar essa informação.";
        global $_permissao;
        if (isset($_permissao))
            $subDescricao = "Solicite ao administrador do sistema sua permissão para a funcionalidade '" . $_permissao . "'.";
        break;
    case "1":
        $icon = "question";
        $titulo = "Informações Incompletas!";
        $descricao = "Favor completar seu cadastro fornecendo suas informações ao sistema.";
        break;
    case "2":
        $icon = "ban";
        $titulo = "Não Autorizado!";
        $descricao = "Seu perfil de usuário não permite acesso a essa funcionalidade.";
        break;
    case "3":
        $icon = "ban";
        $titulo = "Projeto indisponível!";
        $descricao = "Projeto não encontrado ou indisponível.";
        break;
    default:
        break;
}

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add($titulo, $_SERVER["REQUEST_URI"], 1);

$header = new GHeader($titulo, true);
$header->addMenu("", $titulo, $descricao);
$header->show(false, $breadcrumb);
?>
<div class="col-xs-12">
    <div class="error-container">
        <div class="well">
            <h1 class="grey lighter smaller center"><span class="red bigger-125"><i class="ace-icon fa fa-<?php echo $icon; ?>"></i> 001</span> <?php echo $titulo; ?></h1>
            <hr />
            <h3 class="lighter smaller center"><?php echo $descricao; ?><br/><?php echo $subDescricao; ?></h3>
            <hr />
            <div class="space"></div>
            <div class="center">
                <a href="javascript:history.back()" class="btn btn-grey"><i class="ace-icon fa fa-arrow-left"></i>Voltar a página anterior</a>
                <a href="<?php echo URL_SYS; ?>home/" class="btn btn-primary"><i class="ace-icon fa fa-home"></i>Início</a>
            </div>
        </div>
    </div>
    <!-- PAGE CONTENT ENDS -->
</div><!-- /.col -->
<?php
$footer = new GFooter();
$footer->show();
?>