<?php
    include_once ("includes/_database.php");

    $query = $dbCo->prepare("SELECT SUM(amount) AS balance, 
                            (SELECT SUM(amount) FROM transaction WHERE amount > 0 AND date_transaction LIKE :date) AS recette, 
                            (SELECT SUM(amount) FROM transaction WHERE amount < 0 AND date_transaction LIKE :date) AS depense 
                            FROM transaction WHERE date_transaction LIKE :date;");
    $query->execute(["date" => date("Y-m")."%"]);
    $res = $query->fetch();

    $query2 = $dbCo->prepare("SELECT icon_class, category_name, SUM(amount) AS depense,
    SUM(amount)/(SELECT SUM(amount) FROM transaction WHERE date_transaction LIKE :date AND amount < 0 AND id_category IS NOT NULL)*100 AS pourcentage
    FROM transaction JOIN category USING (id_category) WHERE date_transaction LIKE :date AND amount < 0 GROUP BY id_category;");
    $query2->execute(["date" => date("Y-m")."%"]);
    $percent = $query2->fetchAll();

    $query3 = $dbCo->prepare("SELECT COUNT(amount) AS no_cat FROM transaction WHERE date_transaction LIKE :date AND amount < 0 AND id_category IS NULL;");
    $query3->execute(["date" => date("Y-m")."%"]);
    $noCat = $query3->fetchColumn();

    include_once ("includes/_header.php");
?>

    <div class="container">
        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h2 class="my-0 fw-normal fs-4">Balance de Juillet 2023</h2>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-around align-items-center">
                        <span class="rounded-pill text-nowrap bg-warning-subtle fs-2 px-2">
                            <?= $res["balance"] ?> €
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h3 class="fs-4">Recettes</h3>
                        <span class="rounded-pill text-nowrap bg-success-subtle fs-4 px-2">
                            + <?= $res["recette"] ?> €
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <h3 class="fs-4">Dépenses</h3>
                        <span class="rounded-pill text-nowrap bg-warning-subtle fs-4 px-2">
                            - <?= -$res["depense"] ?> €
                        </span>
                    </li>
                </ul>
            </div>
        </section>

        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h2 class="my-0 fw-normal fs-4">Répartition des dépenses de Juillet 2023</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    Attention, <?= $noCat ?> dépenses n'ont pas été catégorisées pour ce mois.
                </div>
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2">Catégorie</th>
                            <th scope="col" class="text-end">Dépense total</th>
                            <th scope="col" class="text-end">% des dépenses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=0; $i<count($percent); $i++){ ?>
                        <tr>
                            <td class="ps-3">
                                <i class="bi bi-<?= $percent[$i]["icon_class"] ?> fs-3"></i>
                            </td>
                            <td>
                            <?= $percent[$i]["category_name"] ?>
                            </td>
                            <td class="text-end">
                                <span class="rounded-pill text-nowrap bg-warning-subtle px-2">
                                    - <?= -$percent[$i]["depense"] ?> €
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                            <?= round($percent[$i]["pourcentage"], 2) ?> %
                            </td>
                        </tr>
                        <?php } ?>
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
                            <a class="page-link" href="#">Juin 2023</a>
                        </li>
                        <li class="page-item">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">
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

<?php
    include_once ("includes/_footer.php");
?>