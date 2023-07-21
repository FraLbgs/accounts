<?php
    include_once ("includes/_header.php");
?>

    <div class="container">
        <section class="card mb-4 rounded-3 shadow-sm">
            <div class="card-header py-3">
                <h1 class="my-0 fw-normal fs-4">Importer des opérations</h1>
            </div>
            <div class="card-body">
                <form>  
                    <div class="mb-3">
                        <label for="file" class="form-label">Fichier</label>
                        <input type="file" accept=".csv" aria-describedby="file-help" class="form-control" name="file" id="file">
                        <div id="file-help" class="form-text">Seul les fichiers CSV avec séparateur "," (virgule) sont supportés.</div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Envoyer</button>
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