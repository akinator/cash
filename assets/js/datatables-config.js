// Configuration de la traduction française pour DataTables
const frenchTranslation = {
    "emptyTable": "Aucune donnée disponible dans le tableau",
    "info": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
    "infoEmpty": "Affichage de 0 à 0 sur 0 éléments",
    "infoFiltered": "(filtré à partir de _MAX_ éléments au total)",
    "infoThousands": " ",
    "lengthMenu": "Afficher _MENU_ éléments",
    "loadingRecords": "Chargement...",
    "processing": "Traitement...",
    "search": "Rechercher :",
    "zeroRecords": "Aucun élément correspondant trouvé",
    "paginate": {
        "first": "Premier",
        "last": "Dernier",
        "next": "Suivant",
        "previous": "Précédent"
    },
    "aria": {
        "sortAscending": ": activer pour trier la colonne par ordre croissant",
        "sortDescending": ": activer pour trier la colonne par ordre décroissant"
    },
    "buttons": {
        "copy": "Copier",
        "excel": "Excel",
        "csv": "CSV",
        "pdf": "PDF",
        "print": "Imprimer",
        "colvis": "Visibilité des colonnes"
    }
};

// Configuration DataTables
const dataTableConfig = {
    "language": frenchTranslation,
    "pageLength": 10,
    "ordering": true,
    "responsive": true,
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'excel',
            className: 'btn btn-sm btn-success',
            text: '<i class="fas fa-file-excel mr-1"></i> Excel'
        },
        {
            extend: 'pdf',
            className: 'btn btn-sm btn-danger',
            text: '<i class="fas fa-file-pdf mr-1"></i> PDF'
        },
        {
            extend: 'print',
            className: 'btn btn-sm btn-info',
            text: '<i class="fas fa-print mr-1"></i> Imprimer'
        }
    ]
};

// Initialisation des DataTables
document.addEventListener('DOMContentLoaded', function() {
    if ($.fn.DataTable) {
        // Initialiser uniquement les tables qui n'ont pas encore été initialisées
        $('table.datatable').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable(dataTableConfig);
            }
        });
    }
});