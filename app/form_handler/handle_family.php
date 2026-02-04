<?php
session_start();
require_once __DIR__ . "/../bd.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $id_familia = intval($_POST["id_familia"]);

    if ($action == "rename_family") {
        $novoNome = mysqli_real_escape_string($bdConexao, $_POST["nome_familia"]);
        mysqli_query($bdConexao, "UPDATE familias SET nome_familia = \"$novoNome\" WHERE id_familia = $id_familia");
        header("Location: /app/pages/manage_family_v2.php?success=renamed");
    }

    if ($action == "add_member") {
        $username = mysqli_real_escape_string($bdConexao, $_POST["username_to_add"]);
        // Busca o ID do usuário pelo login
        $res = mysqli_query($bdConexao, "SELECT ID FROM usuarios WHERE login = \"$username\"");
        if ($row = mysqli_fetch_assoc($res)) {
            $uid = $row["ID"];
            // Evita duplicados
            mysqli_query($bdConexao, "INSERT IGNORE INTO membros_familia (id_familia, id_usuario, papel) VALUES ($id_familia, $uid, \"membro\")");
            header("Location: /app/pages/manage_family_v2.php?success=added");
        } else {
            die("Usuário não encontrado! <a href=\"/app/pages/manage_family_v2.php\">Voltar</a>");
        }
    }

    if ($action == "remove_member") {
        $id_membro = intval($_POST["id_membro"]);
        mysqli_query($bdConexao, "DELETE FROM membros_familia WHERE id_membro = $id_membro");
        header("Location: /app/pages/manage_family_v2.php?success=removed");
    }
}
