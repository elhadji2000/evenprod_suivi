<!-- Footer simple avec texte défilant -->
<div class="footer-banner">
    <div class="scrolling-text">
        &copy; <?php echo date("Y"); ?> EvenProd - Maison de production & événements |
        Adresse : Sipres 2 Cité Villa N*36*BP : 15949 Dakar Fann. |
        Tél : +221 33 827 60 61 |
        Email : commercialetyprod19@gmail.com
    </div>
</div>

<style>
.footer-banner {
    background: #302e2eda; /* fond sombre */
    color: #f1f1f1;
    padding: 10px 0;
    overflow: hidden;
    width: 100%;
    font-family: Arial, sans-serif;
    font-size: 14px;
    margin-top: 40px; /* espace avant le footer */
}

.scrolling-text {
    display: inline-block;
    white-space: nowrap;
    padding-left: 100%;  /* commence hors écran */
    animation: scroll-left 30s linear infinite;
}

@keyframes scroll-left {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%);
    }
}
</style>
