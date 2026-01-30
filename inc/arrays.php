<?php

// <editor-fold defaultstate="collapsed" desc="Global">
$__arrayServer = array(
    '::1' => 'Localhost',
    '172.31.36.217' => 'Produção'
);

$__arrayNaoGravarAcesso = array(
    "/inc/load/acessos.php"
);

$__arrayEstados = array(
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AM' => 'Amazonas',
    'AP' => 'Amapá',
    'BA' => 'Bahia',
    'CE' => 'Ceará',
    'DF' => 'Distrito Federal',
    'ES' => 'Espírito Santo',
    'GO' => 'Goiás',
    'MA' => 'Maranhão',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Pará',
    'PB' => 'Paraíba',
    'PR' => 'Paraná',
    'PE' => 'Pernambuco',
    'PI' => 'Piauí',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RO' => 'Rondônia',
    'RS' => 'Rio Grande do Sul',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SE' => 'Sergipe',
    'SP' => 'São Paulo',
    'TO' => 'Tocantins'
);

$__arraySexo = array(
    "M" => "Masculino",
    "F" => "Feminino",
    "N" => "Não Informado"
);

$__arrayAtivo = array(
    'I' => 'Inativo',
    'A' => 'Ativo'
);

$__arrayTipoTag = array(
    "A" => "Administrativo",
    "S" => "Site"
);

$__arrayTipoEvento = array(
    'E' => 'Erro',
    'S' => 'Sucesso',
    'A' => 'Atenção'
);

$__arrayStatusEnvio = array(
    "S" => "Sucesso",
    "E" => "Erro"
);

$__arrayBloqueado = array(
    'I' => 'Inativo',
    'A' => 'Ativo',
    'B' => 'Bloqueado'
);

$__arrayExcluido = array(
    'I' => 'Inativo',
    'A' => 'Ativo',
    'E' => 'Excluído'
);

$__arrayStatusDestinatario = array(
    "N" => "Não Lida",
    "L" => "Lida",
    "E" => "Excluída"
);

$__arraySimNao = array(
    'S' => 'Sim',
    'N' => 'Não'
);

$__arrayValidado = array(
    'N' => 'Não',
    'S' => 'Sim',
    'L' => 'Liberado'
);

$__arrayCores = array(
    '#8DD3C7', '#D9D9D9', '#BEBADA', '#FB8072', '#80B1D3', '#FDB462', '#B3DE69', '#FCCDE5', '#BC80BD', '#FFFFB3'
);

$__arrayAcaoAuditoria = array(
    "I" => "Inserido",
    "A" => "Alterado",
    "E" => "Excluído"
);
// </editor-fold>
// 
// <editor-fold defaultstate="collapsed" desc="Parametros">
$__paramDataNascimento = array(
    "startDate" => "'01-01-1910'",
    "endDate" => "'0d'",
    "startView" => "'decade'"
);
$__paramDataLog = array(
    "startDate" => "'01-01-2025'",
    "endDate" => "'0d'"
);
// </editor-fold>
// 
// <editor-fold defaultstate="collapsed" desc="Específicos">
$__arrayTipoAcesso = array(
    "EA" => "Entrar no AVA",
    "SA" => "Sair do AVA",
    "EC" => "Entrou no Curso"
);
//$__arrayTipoGrafico = array(
//    "qtd_por_dia" => "Quantidade por Dia"   
//);
// </editor-fold>
// <editor-fold desc="Menu">
$__arrMinhaConta = array(
    "ESCOLHERPERFIL" => "Escolher Perfil",
    "ALTERARSENHA" => "Alterar Senha",
    "MEUSDADOS" => "Meus Dados",
    "MINHASMENSAGENS" => array(
        "ENVIARMENSAGEM" => "Enviar Mensagem",
        "RECEBIDAS" => "Recebidas",
        "ENVIADAS" => "Enviadas"
    ),
    "MINHASPENDENCIAS" => "Minhas Pendências"
);
$__arrSeguranca = array(
    "PERFIL" => "Perfis",
    "PERMISSAO" => "Permisssões",
    "PERMISSAOPERFIL" => "Permissões dos Perfis",
    "PERFILTROCA" => "Trocas de Perfis"
);
$__arrConfiguracoes = array(
    "EVENTO" => "Eventos",
    "PARAMETRO" => "Parâmetros",
    "TAG" => "Tags"
);
$__arrAdministracao = array(
    "CRONACESSO" => "Atualização de Acessos",
    "CRONCONCLUSAO" => "Atualização de Conclusões",
    "CRONCURSO" => "Atualização de Curso",
    "CRONESCOLA" => "Atualização de Escolas",
    "CRONNIVEL" => "Atualização de Estrutura Organizacional",
    "CRONMATRICULA" => "Atualização de Matrículas",
    "CRONUSUARIO" => "Atualização de Usuários",
    "CRONARQUIVOS" => "Limpeza de Arquivos",
);
$__arrCadastro = array(
    "USUARIO" => "Usuários"
);
$__arrGerenciamento = array(
    "CURSO" => "Cursos",
    "ESCOLA" => "Escolas",
    "NIVEL" => "Estrutura Hierárquica"
);
$__arrGraficos = array(
    "G_ACESSOS" => "Acessos",
    "G_NAOACESSO" => "Não Acesso",
    "G_PROGRESSO" => "Progresso/Desempenho",
    "G_MATRICULAS" => "Matrículas"
);
$__arrTabelas = array(
    "T_ACESSOS" => "Acessos",
    "T_NAOACESSO" => "Não Acesso",
    "T_PROGRESSO" => "Progresso/Desempenho",
    "T_MATRICULAS" => "Matrículas"
);
$__arrMonitoramento = array(
    "ACESSO" => "Acessos dos Usuários",
    "CRON_ACESSO" => "Logs da CRON de atualização de acessos",
    "CRON_ARQUIVOS" => "Logs da CRON Limpeza de arquivos",
    "CRON_CONCLUSAO" => "Logs da CRON de atualização de conclusões",
    "CRON_CURSO" => "Logs da CRON de atualização de cursos",
    "CRON_ESCOLA" => "Logs da CRON de atualização de escolas",
    "CRON_MATRICULA" => "Logs da CRON de atualização de matrículas",
    "CRON_NIVEL" => "Logs da CRON de atualização de estrutura organizacional",
    "CRON_USUARIO" => "Logs da CRON de atualização de usuários"
);
$__arrRelatorio = array(
    "LISTAGENS" => array(
        "USUARIOS" => "Usuários"
    ),
    "QUANTITATIVOS" => array(
        "USUARIOS" => "Usuários"
    )
);
$__arrMenu = array(
    "MINHACONTA" => "Minha Conta",
    "SEGURANCA" => "Segurança",
    "CONFIGURACOES" => "Configurações",
    "ADMINISTRACAO" => "Administração",
    "GERENCIAMENTO" => "Gerenciamentos",
    "GRAFICOS" => "Gráficos",
    "TABELAS" => "Tabelas",
    "MONITORAMENTO" => "Monitoramento",
    "CADASTROS" => "Cadastros",
    "RELATORIOS" => "Relatórios"
);
$__arrMenuIcons = array(
    "MINHACONTA" => "fa-user",
    "ADMINISTRACAO" => "fa-cogs",
    "SEGURANCA" => "fa-key",
    "CONFIGURACOES" => "fa-wrench",
    "GERENCIAMENTO" => "fa-cubes",
    "GRAFICOS" => "fa-bar-chart",
    "TABELAS" => "fa-table",
    "MONITORAMENTO" => "fa-desktop",
    "CADASTROS" => "fa-pencil-square-o",
    "RELATORIOS" => "fa-file-text-o"
);
$__arrSubMenu = array(
    "LISTAGENS" => "Listagens",
    "QUANTITATIVOS" => "Quantitativos",
    "MINHASMENSAGENS" => "Minhas Mensagens"
);
// </editor-fold>
?>