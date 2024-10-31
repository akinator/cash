<?php ob_start(); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Importer des commandes</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=commandes">Commandes</a></li>
                    <li class="breadcrumb-item active">Import</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Import de fichier Excel</h3>
                    </div>
                    <div class="card-body">
                        <form id="importForm" action="index.php?action=commandes/import" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="importFile">Sélectionnez le fichier Excel</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="importFile" name="importFile" accept=".xlsx, .xls" required>
                                        <label class="custom-file-label" for="importFile">Choisir un fichier</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Formats acceptés : .xlsx, .xls</small>
                            </div>
                            <div class="form-group">
                                <button type="button" id="btnImport" class="btn btn-primary">
    <i class="fas fa-upload mr-2"></i>Importer
</button>
                                <a href="index.php?action=commandes/template" class="btn btn-outline-secondary ml-2">
                                    <i class="fas fa-download mr-2"></i>Télécharger le modèle
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Instructions</h3>
                    </div>
                    <div class="card-body">
                        <p>Le fichier Excel doit contenir les colonnes suivantes :</p>
                        <ul class="text-muted">
                            <li>Produit</li>
                            <li>Nom du client</li>
                            <li>Téléphone</li>
                            <li>Adresse</li>
                            <li>Montant TTC</li>
                            <li>Statut (En attente, En cours, Terminée, Annulée)</li>
                        </ul>
                        <div class="alert alert-info mt-3">
                            <h5><i class="icon fas fa-info"></i> Note</h5>
                            <p class="mb-0">Pour faciliter l'import, téléchargez et utilisez notre modèle Excel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ajout des scripts nécessaires -->
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de bs-custom-file-input
    bsCustomFileInput.init();

    // Mise à jour du label du fichier
    document.getElementById('importFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choisir un fichier';
        e.target.nextElementSibling.textContent = fileName;
    });

    // Gestion du formulaire d'import
    document.getElementById('btnImport').addEventListener('click', function(e) {
        e.preventDefault();

        const fileInput = document.getElementById('importFile');

        // Vérification du fichier
        if(fileInput.files.length === 0) {
            alert('Veuillez sélectionner un fichier à importer');
            return false;
        }

        const fileName = fileInput.files[0].name;
        const fileExt = fileName.split('.').pop().toLowerCase();

        if(!['xlsx', 'xls'].includes(fileExt)) {
            alert('Seuls les fichiers Excel (.xlsx, .xls) sont acceptés');
            return false;
        }

        // Création de la popup style suppression
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.setAttribute('role', 'dialog');
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-circle fa-4x text-warning"></i>
                        </div>
                        <h3 class="modal-title mb-3">Confirmation d'import</h3>
                        <p>Voulez-vous vraiment importer le fichier<br><strong>${fileName}</strong> ?</p>
                        <div class="mt-5">
                            <button type="button" class="btn btn-danger mr-2" id="confirmImport">Oui, importer</button>
                            <button type="button" class="btn btn-primary" id="cancelImport">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Gestion des boutons de la popup
        document.getElementById('confirmImport').addEventListener('click', function() {
            document.getElementById('importForm').submit();
        });

        document.getElementById('cancelImport').addEventListener('click', function() {
            document.body.removeChild(modal);
        });

        // Fermeture au clic en dehors de la modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    });
});
</script>

<style>
.custom-file-input:lang(fr)~.custom-file-label::after {
    content: "Parcourir";
}

.card {
    margin-bottom: 1rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.alert-info {
    color: #004085;
    background-color: #cce5ff;
    border-color: #b8daff;
}

.btn {
    margin-bottom: 5px;
}

.breadcrumb {
    background: transparent;
    margin-top: 0;
    margin-bottom: 0;
    font-size: 12px;
    padding: 0;
}

.modal-content {
    border: none;
    border-radius: 8px;
}

.modal-body {
    padding: 2rem;
}

.modal-title {
    color: #333;
    font-size: 1.5rem;
}

.fa-exclamation-circle {
    color: #ffc107;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-primary {
    background-color: #3085d6;
    border-color: #3085d6;
}

.modal-body p {
    font-size: 1.1rem;
    color: #555;
}

.modal-body strong {
    color: #333;
}
</style>

<?php
$content = ob_get_clean();
require('views/layouts/admin.php');
?>