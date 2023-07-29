<?php
    include_once ("includes/_database.php");

    $msg = "";

    if(isset($_POST["submit"])){
        $fileName = $_FILES["file"]["tmp_name"];
        if($_FILES["file"]["size"] > 0){
            $file = fopen($fileName, "r");
            while (($column = fgetcsv($file, 100, ",", "\"")) !== FALSE){
                $query = $dbCo->prepare("INSERT INTO transaction (name,amount,date_transaction)
                    VALUES (:name, :amount, :date);");
                $isOK = $query->execute([
                    "name" => $column[0],
                    "amount" => $column[1],
                    "date" => $column[2]
                ]);
                if($isOK){
                    $msg = "Les données ont bien été importées dans la base de données";
                }
                else{
                    $msg = "Problème lors de l'importation des données du fichier";
                }
            }
        }
    }



    include_once ("includes/_header.php");
?>

    <div class="container">
        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h1 class="my-0 fw-normal fs-4">Importer des opérations</h1>
            </div>
            <div class="card-body">
                <div class="text-center  bg-success text-white"><?= $msg ?></div>
                <form enctype='multipart/form-data' method="post" action="#">  
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier</label>
                        <input type="file" accept=".csv" aria-describedby="file-help" class="form-control" name="file" id="file">
                        <div id="file-help" class="form-text">Seul les fichiers CSV avec séparateur "," (virgule) sont supportés.</div>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg">Envoyer</button>
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

<?php
    include_once ("includes/_footer.php");
?>