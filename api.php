<?php

require 'includes/_database.php';

session_start();

header('content-type:application/json');

$data = json_decode(file_get_contents('php://input'), true);

$isOk = false;

if ($data['action'] === 'delete' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $query = $dbCo->prepare("DELETE FROM transaction WHERE id_transaction = :idT;");
    $isOk = $query->execute(["idT" => intval(strip_tags($data['idT']))]);
    $msg = "Opération supprimée correctement";

    echo json_encode([
        'result' => $isOk,
        'msg' => $msg
    ]);
    exit;
}

if (!array_key_exists('token', $_SESSION) || !array_key_exists('token', $data)
    || $_SESSION['token'] !== $data['token']){
    echo json_encode([
        'result' => 'false',
        'error' => 'Accès refusé, jeton invalide.'
    ]);
    exit;
}

if ($data['action'] === 'addOpe' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $dbCo->prepare("INSERT INTO transaction (name, amount, date_transaction, id_category) VALUES
                                (:name, :amount, :date_transaction, :id_category);");
    $isOk = $query->execute([
        "name" => trim(strip_tags($data["name"])),
        "amount" => floatval(strip_tags($data["amount"])),
        "date_transaction" => strip_tags($data["date"]),
        "id_category" => $data["category"] === "" ? NULL : intval(strip_tags($data["category"]))
    ]);
    $msg = "Opération bien ajoutée";

    echo json_encode([
        'result' => $isOk,
        'msg' => $msg
    ]);
    exit;
}

if ($data['action'] === 'modify' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $idT = intval(strip_tags($data['idT']));
    $idC = $data["idC"] === "" ? NULL : intval(strip_tags($data['idC']));
    $name = trim(strip_tags($data['name']));
    $amount = floatval(strip_tags($data['amount']));
    $date = strip_tags($data['date']);
    $query = $dbCo->prepare("UPDATE transaction SET name = :articleName,
                                                    amount = :amount,
                                                    date_transaction = :date,
                                                    id_category = :idCat
                             WHERE id_transaction = :idTrans;");
    $isOk = $query->execute([
        'articleName' => $name,
        'amount' => $amount,
        'date' => $date,
        'idCat' => $idC,
        'idTrans' => $idT
    ]);

    $query = $dbCo->prepare("SELECT icon_class  FROM transaction LEFT JOIN
    category USING (id_category) WHERE id_transaction = :idTrans;");
    $query->execute(['idTrans' => $idT]);
    $classC = $query->fetchColumn();

    $msg = "Opération modifiée correctement";

    $dataR = [
        'result' => $isOk && $query->rowCount() > 0,
        'name' => $name,
        'amount' => $amount,
        'date' => $date,
        'idC' => $idC,
        'idT' => $idT,
        'msg' => $msg,
        'classC' => $classC
    ];

    echo json_encode($dataR);
    exit;
}

if ($data['action'] === 'addCat' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $dbCo->prepare("INSERT INTO category (category_name, icon_class) VALUES
                                (:name, :icon);");
    $isOk = $query->execute([
        "name" => trim(strip_tags($data["name"])),
        "icon" => trim(strip_tags($data["icon"]))
    ]);
    $msg = "Catégorie bien ajoutée";

    echo json_encode([
        'result' => $isOk,
        'msg' => $msg
    ]);
    exit;
}