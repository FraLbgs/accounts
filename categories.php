<?php
    session_start();

    include_once ("includes/_database.php");


    $query = $dbCo->prepare("SELECT id_category AS idC, icon_class, category_name, COUNT(id_category) AS totalOpe FROM transaction 
                        LEFT JOIN category USING (id_category) 
                        WHERE icon_class IS NOT NULL GROUP BY id_category ORDER BY totalOpe DESC;");
    $query->execute();
    $cats = $query->fetchAll();


    include_once ("includes/_header.php");
?>

    <div class="container">
        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h1 class="my-0 fw-normal fs-4">Catégories</h1>
            </div>
            <div class="card-body">
                <div id="msg" class="text-center bg-success text-white"></div>
                <ul class="list-group list-group-flush">
                    <?php for($i=0; $i<count($cats); $i++){ ?>
                        <li id="<?= $cats[$i]["idC"] ?>" class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-<?= $cats[$i]["icon_class"] ?> fs-3"></i>
                                &nbsp;
                                <?= $cats[$i]["category_name"] ?>
                                &nbsp;
                                <span class=" badge bg-secondary"><?= $cats[$i]["totalOpe"] ?> opérations</span>
                            </div>
                            <div>
                                <a href="#" class="btn btn-outline-primary btn-sm rounded-circle">
                                    <i class="bi bi-pencil" data-cat-id="<?= $cats[$i]["idC"] ?>" data-name="<?= $cats[$i]["category_name"] ?>" data-icon="<?= $cats[$i]["icon_class"] ?>" ></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger btn-sm rounded-circle">
                                    <i class="bi bi-trash" data-cat-id="<?= $cats[$i]["idC"] ?>"></i>
                                </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </section>

        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h2 class="my-0 fw-normal fs-4">Ajouter une catégorie</h2>
            </div>
            <div class="card-body">
                <form class="row align-items-end">
                    <div class="col col-md-5">
                        <label for="name" class="form-label">Nom *</label>
                        <input type="name" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="col-md-5">
                        <label for="icon" class="form-label">Classe icone bootstrap *</label>
                        <input type="text" class="form-control" name="icon" id="icon" required>
                    </div>
                    <input type="hidden" id="token-csrf" name="token" value="<?= $_SESSION["token"] ?>">
                    <div class="col col-md-2 text-center text-md-end mt-3 mt-md-0">
                        <button id="add-category" type="submit" class="btn btn-secondary">Ajouter</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="position-fixed bottom-0 end-0 m-3">
        <a href="add.php" class="btn btn-primary btn-lg rounded-circle">
            <i class="bi bi-plus fs-1"></i>
        </a>
    </div>

    <template id="modify-category">
        <li class="list-group-item d-flex justify-content-between align-items-center" data-form-id="">
                <form action="" method="post">
                    <input type="text" name="icon" value="">
                    <input type="text" name="name" value="">
                    <input type="hidden" id="token-csrf" name="token" value="<?= $_SESSION["token"] ?>">
                    <input type="submit" value="valider">
                </form>
        </li>
    </template>

<?php
    include_once ("includes/_footer.php");
?>