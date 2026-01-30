<?php

/**
 * Classe mÃ£e da biblioteca
 */
class Genesis {

    function __construct($sistema) {

        // carregar bibliotecas
        require_once(ROOT_GENESIS . "inc/functions.class.php");
        require_once(ROOT_GENESIS . "inc/exceptions/exceptions.php");
        require_once(ROOT_GENESIS . "inc/database.class.php");
        require_once(ROOT_GENESIS . "inc/databaseMoodle.class.php");
        require_once(ROOT_GENESIS . "inc/security.class.php");
        require_once(ROOT_GENESIS . "inc/header.parent.class.php");
        require_once(ROOT_GENESIS . "inc/footer.parent.class.php");
        require_once(ROOT_GENESIS . "inc/form.class.php");
        require_once(ROOT_GENESIS . "inc/breadCrumbs.class.php");
        require_once(ROOT_GENESIS . "inc/email.class.php");
        require_once(ROOT_GENESIS . "inc/gApiRest.class.php");

        return true;
    }

}

?>