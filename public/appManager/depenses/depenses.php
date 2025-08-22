<head>
    <link rel="stylesheet" href="depenses.css">
</head>
<?php include '../../../includes/header.php'; ?>

<section id="depenses" class="section-padding">
    <div class="container">					
        <div class="section-title text-center mb-5">
            <h2>Résumé des Dépenses</h2>
            <p>Visualisez le total de vos dépenses par catégorie et accédez aux détails.</p>
        </div>				

        <div class="row text-center">	
            <!-- Dépense : Cachet -->
            <div class="col-lg-4 col-sm-6 col-xs-12 mb-4">
                <div class="pricing_design">
                    <div class="single-pricing shadow rounded p-3">
                        <div class="price-head">		
                            <h2>Cachet</h2>
                            <h4 class="price text-success">1 200 000 F CFA</h4>
                        </div>
                        <a href="details_depenses.php?type=cachet" class="btn btn-outline-success mt-3">Voir détails</a>
                    </div>
                </div>
            </div><!--- END COL -->	

            <!-- Dépense : Transport -->
            <div class="col-lg-4 col-sm-6 col-xs-12 mb-4">
                <div class="pricing_design">
                    <div class="single-pricing shadow rounded p-3">
                        <div class="price-head">		
                            <h2>Transport</h2>
                            <h4 class="price text-primary">800 000 F CFA</h4>
                        </div>
                        <a href="details_depenses.php?type=transport" class="btn btn-outline-primary mt-3">Voir détails</a>
                    </div>
                </div>
            </div><!--- END COL -->	

            <!-- Dépense : Autres -->
            <div class="col-lg-4 col-sm-6 col-xs-12 mb-4">
                <div class="pricing_design">
                    <div class="single-pricing shadow rounded p-3">
                        <div class="price-head">		
                            <h2>Autres</h2>
                            <h4 class="price text-warning">450 000 F CFA</h4>
                        </div>
                        <a href="details_depenses.php?type=autres" class="btn btn-outline-warning mt-3">Voir détails</a>
                    </div>
                </div>
            </div><!--- END COL -->			  
        </div><!--- END ROW -->
    </div><!--- END CONTAINER -->
</section>
