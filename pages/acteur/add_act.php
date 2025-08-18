<head>
    <link rel="stylesheet" href="add1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css"
        integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />
</head>
<?php include '../../includes/header.php'; ?>
<section class="section gray-bg" id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-title">
                    <h2>Ajouter un acteur</h2>
                    <p>Remplissez le formulaire ci-dessous pour enregistrer un nouvel acteur dans une série existante.</p>
                </div>
            </div>
        </div>
        <div class="row flex-row-reverse">
            <div class="col-md-7 col-lg-8 m-15px-tb">
                <div class="contact-form">
                    <form action="/" method="post" class="contactform contact_form" id="contact_form">
                        <div class="returnmessage valid-feedback p-15px-b"
                            data-success="Your message has been received, We will contact you soon."></div>
                        <div class="empty_notice invalid-feedback p-15px-b"><span>Please Fill Required Fields</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="name" type="text" placeholder="Prenom" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="email" type="text" placeholder="Nom" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="budget" type="text" placeholder="Email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="budget" type="text" placeholder="Contact" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="budget" type="text" placeholder="CV" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input id="photo" type="text" placeholder="Profile photo" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea id="message" placeholder="Description" class="form-control"
                                        rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="send">
                                    <a id="send_message" class="px-btn theme" href="#"><span>ENREGISTRER</span> <i
                                            class="arrow"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-5 col-lg-4 m-15px-tb">
                <div class="contact-name">
                    <h5>Dernière série ajoutée</h5>
                    <p>Nom : Breaking Bad</p>
                    <p>Type : Drame</p>
                    <p>Budget : 2M $</p>
                </div>
                <div class="contact-name">
                    <h5>Conseil</h5>
                    <p>Ajoutez d’abord la série avant d’ajouter ses acteurs.</p>
                </div>
            </div>

        </div>
    </div>
</section>