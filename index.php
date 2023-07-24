<?php
    session_start();

    $_SESSION["token"] = md5(uniqid(mt_rand(), true));


    include_once ("includes/_database.php");

    $query = $dbCo->prepare("SELECT id_transaction, name, amount, date_transaction, id_category, icon_class  FROM transaction LEFT JOIN
                            category USING (id_category) WHERE date_transaction LIKE :date ORDER BY date_transaction DESC;");
    $query->execute(["date" => date("Y-m")."%"]);
    $res = $query->fetchAll();

    // $query = $dbCo->prepare("SELECT SUM(amount) AS total FROM transaction WHERE date_transaction LIKE :date ;");
    // $query->execute(["date" => date("Y-m")."%"]);
    $query = $dbCo->prepare("SELECT SUM(amount) AS total FROM transaction;");
    $query->execute();
    $restAmount = $query->fetchColumn();

    $query = $dbCo->prepare("SELECT id_category AS idC, category_name AS nameC FROM category;");
    $query->execute();
    $cat = $query->fetchAll();

    include_once ("includes/_header.php");
?>

    <div class="container">
        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h2 class="my-0 fw-normal fs-4">Solde aujourd'hui</h2>
            </div>
            <div class="card-body">
                <p class="card-title pricing-card-title text-center fs-1"><?= $restAmount ?> €</p>
            </div>
        </section>

        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h1 class="my-0 fw-normal fs-4">Opérations de Juillet 2023</h1>
            </div>
            <div class="card-body">
                <div id="msg" class="text-center bg-success text-white"></div>
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2">Opération</th>
                            <th scope="col" class="text-end">Montant</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for($i=0; $i<count($res); $i++){
                                $dateTransaction = strtotime($res[$i]["date_transaction"]);
                        ?>
                            <tr id="<?= $res[$i]["id_transaction"] ?>">
                                <td width="50" class="ps-3">
                                    <?php
                                        if($res[$i]["icon_class"] !== NULL){
                                            echo "<i class='bi bi-".$res[$i]["icon_class"]." fs-3'></i>";
                                        }
                                    ?>
                                </td>
                                <td data-name="<?= $res[$i]["name"] ?>">
                                <time datetime="<?= $res[$i]["date_transaction"] ?>" class="d-block fst-italic fw-light"><?= date("d/m/Y",$dateTransaction) ?></time>
                                <?= $res[$i]["name"] ?>
                                </td>
                                <td class="text-end">
                                    <span class="rounded-pill text-nowrap bg-<?php echo $res[$i]["amount"] > 0 ? "success" : "warning"  ?>-subtle px-2">
                                        <?= $res[$i]["amount"] ?>
                                    </span> €
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="#" class="btn btn-outline-primary btn-sm rounded-circle">
                                        <i data-transaction="<?= $res[$i]["id_transaction"] ?>" data-cat="<?= $res[$i]["id_category"] ?>" class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm rounded-circle">
                                        <i data-delete="<?= $res[$i]["id_transaction"] ?>" class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <nav class="text-center">
                    <ul class="pagination d-flex justify-content-center m-2">
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="bi bi-arrow-left"></i>
                            </span>
                        </li>
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">Juillet 2023</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="index.php">Juin 2023</a>
                        </li>
                        <li class="page-item">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="index.php">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
    </div>

    <div class="position-fixed bottom-0 end-0 m-3">
        <a href="add.php" class="btn btn-primary btn-lg rounded-circle">
            <i class="bi bi-plus fs-1"></i>
        </a>
    </div>

    <template id="modify-transaction">
        <tr data-form-id="">
            <td colspan="4">
                <form action="" method="post">
                    <select class="form-select" name="category" id="category">
                        <option value="">Aucune catégorie</option>
                        <?php
                            for($i=0; $i<count($cat); $i++){
                                echo "<option value='".$cat[$i]["idC"]."'>".$cat[$i]["nameC"]."</option>";
                            }
                        ?>
                    </select>
                    <input type="date" name="date" value="">
                    <input type="text" name="name" value="">
                    <input type="number" name="amount" value="">
                    <input type="hidden" name="idTransaction" value="">
                    <input type="hidden" id="token-csrf" name="token" value="<?= $_SESSION["token"] ?>">
                    <input type="submit" value="valider">
                </form>
            </td>
        </tr>
    </template>

<?php
    include_once ("includes/_footer.php");
?>