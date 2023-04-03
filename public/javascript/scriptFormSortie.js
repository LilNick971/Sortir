let champNom = $("#sortie_choixLieu_nom");
let champRue = $("#sortie_choixLieu_rue");
let champLat = $("#sortie_choixLieu_latitude");
let champLong = $("#sortie_choixLieu_longitude");
champNom.prop("display", "none");
champRue.prop("display", "none");
champLat.prop("display", "none");
champLong.prop("display", "none");

$('#sortie_ville').change(function (){
    let villeSelector = $(this);
    let lieuSelect = $("#sortie_lieu");
    let codeSelect = $("#sortie_codePostal");

    if(villeSelector.val() === ""){


        lieuSelect.html('');

        lieuSelect.append('<option value> Choisissez une ville en premier </option>');


        codeSelect.val("");
    } else {
        $.ajax({
            url: "/sortie/getLieuxVille",
            type: "GET",
            dataType: "JSON",
            data: {
                villeId: villeSelector.val()
            },
            success: function (lieux) {
                lieuSelect.html('');

                lieuSelect.append('<option value> Choisissez un lieu de ' + villeSelector.find("option:selected").text() + ' Ou Ajoutez un lieu</option>');

                $.each(lieux, function (key, lieu) {
                    lieuSelect.append('<option value="' + lieu.id + '">' + lieu.nom + '</option>');
                });

                champNom.val("");
                champRue.val("");
                champLat.val("");
                champLong.val("");

                champNom.prop("display", "block");
                champRue.prop("display", "block");
                champLat.prop("display", "block");
                champLong.prop("display", "block");
            },
            error: function (err) {
                alert("Erreur chargement des données de la ville");
            }
        });

        $.ajax({
            url: "/sortie/getPostalVille",
            type: "GET",
            dataType: "JSON",
            data: {
                villeId: villeSelector.val()
            },
            success: function (ville) {
                codeSelect.val(ville[0].codePostal);
            },
            error: function (err) {
                alert("Erreur chargement des données");
            }
        });
    }
});

$('#sortie_lieu').change(function (){
    let lieuSelector = $(this);

    if(lieuSelector.val() === ""){
        champNom.val("");
        champRue.val("");
        champLat.val("");
        champLong.val("");

        champNom.prop("display", "block");
        champRue.prop("display", "block");
        champLat.prop("display", "block");
        champLong.prop("display", "block");

    }else{
        $.ajax({
            url: "/sortie/getInfosLieu",
            type: "GET",
            dataType: "JSON",
            data: {
                lieuId : lieuSelector.val()
            },
            success: function (details) {
                let listeDetails = details[0];
                champNom.val(listeDetails.nom);
                champRue.val(listeDetails.rue);
                if(listeDetails.latitude !== null && listeDetails.longitude !== null){
                    champLat.val(listeDetails.latitude);
                    champLong.val(listeDetails.longitude);
                } else {
                    champLat.val("");
                    champLong.val("");
                }
                champNom.prop("display", "none");
                champRue.prop("display", "none");
                champLat.prop("display", "none");
                champLong.prop("display", "none");
            },
            error: function (err) {
                alert("Erreur chargement des données du lieu");
            }
        })
    }

});








