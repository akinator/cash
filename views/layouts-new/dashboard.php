<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<?php include 'navbar.php'; ?>

<?php if (isset($_SESSION['alert'])): ?>
    <script>
        swal({
            title: '<?= htmlspecialchars($_SESSION['alert']['title']) ?>',
            text: '<?= htmlspecialchars($_SESSION['alert']['text']) ?>',
            icon: '<?= htmlspecialchars($_SESSION['alert']['type']) ?>',
            <?php if ($_SESSION['alert']['redirect']): ?>
                buttons: false,
                timer: 5000, // Optionnel : durée avant redirection
                onClose: () => {
                    window.location.href = '<?= htmlspecialchars($_SESSION['alert']['redirect']) ?>';
                }
            <?php endif; ?>
        });
    </script>
<?php 
    unset($_SESSION['alert']); // Supprimez l'alerte de la session après affichage
endif; ?>


<!-- Content Wrapper. Contains page content -->

    <?php if (isset($_SESSION['notification'])): ?>
        <div class="alert alert-<?php echo $_SESSION['notification']['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['notification']['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>

    <?php echo $content; ?>


<?php include 'footer.php'; ?>
