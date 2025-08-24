<?php include '../../../includes/header.php'; ?>
<head>
    <link rel="stylesheet" href="<?php echo $url_base; ?>pages/acteur/add.css">
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
          integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" 
          crossorigin="anonymous" />
    <style>
    .invoice-table {
        width: 100%;
        margin-top: 15px;
        border-collapse: collapse;
    }
    .invoice-table th, .invoice-table td {
        padding: 8px;
        border: 1px solid #ddd;
    }
    .invoice-table input {
        width: 100%;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .add-line {
        margin-top: 10px;
        cursor: pointer;
        color: #007bff;
    }
    </style>
</head>

<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="section-title">
            <h2>Ajouter un Devis</h2>
            <p>Complétez le formulaire pour enregistrer un devis.</p>
        </div>

        <div class="row flex-row-reverse">
            <div class="contact-form">
                <form action="ajouter_devis.php" method="post" enctype="multipart/form-data"
                      class="contactform contact_form" id="contact_form">

                    <!-- Client -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select id="client" name="client" class="form-control" required>
                                    <option value="">-- Sélectionnez un client --</option>
                                    <option value="coca_cola">Coca Cola</option>
                                    <option value="samsung">Samsung</option>
                                    <option value="maggie">Maggie</option>
                                </select>
                            </div>
                        </div>
                        <!-- Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <input id="date" name="date" type="date" class="form-control" required>
                            </div>
                        </div>
                        <!-- Description -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <textarea id="description" name="description" placeholder="Description"
                                    class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau devis -->
                    <h4>Détails du devis</h4>
                    <table class="invoice-table" id="invoiceTable">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Prix Unitaire</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input name="libelle[]" type="text" placeholder="Libellé" class="form-control" required></td>
                                <td><input name="prix_unitaire[]" type="text" placeholder="Prix unitaire" class="form-control"></td>
                                <td><input name="quantite[]" type="number" placeholder="Quantité" class="form-control" min="1" ></td>
                                <td><input name="montant[]" type="number" placeholder="Montant" class="form-control" min="0"></td>
                                <td><button type="button" class="remove-line btn btn-danger btn-sm">X</button></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="add-line" id="addLine">
                        <i class="fas fa-plus-circle"></i> Ajouter une ligne
                    </div>

                    <!-- Bouton Enregistrer -->
                    <div class="form-group" style="margin-top:15px;">
                        <button type="submit" class="px-btn theme">
                            <span>ENREGISTRER</span> <i class="arrow"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
const addLineBtn = document.getElementById("addLine");
const tableBody = document.querySelector("#invoiceTable tbody");

addLineBtn.addEventListener("click", function() {
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input name="libelle[]" type="text" placeholder="Libellé" class="form-control" required></td>
        <td><input name="prix_unitaire[]" type="text" placeholder="Prix unitaire" class="form-control"></td>
        <td><input name="quantite[]" type="number" placeholder="Quantité" class="form-control" min="1"></td>
        <td><input name="montant[]" type="number" placeholder="Montant" class="form-control" min="0"></td>
        <td><button type="button" class="remove-line btn btn-danger btn-sm">X</button></td>
    `;
    tableBody.appendChild(newRow);
});

// Suppression d'une ligne
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("remove-line")) {
        e.target.closest("tr").remove();
    }
});
</script>
