<?php
// Assurez-vous d"utiliser la bonne syntaxe pour la déclaration de l"espace de noms
namespace App\Service;

class ProductFormService
{
    public function getProductFormScript(): string
    {
        // Vous générez du code JavaScript ici, donc retournez simplement le script comme une chaîne
        $javascriptCode = '
        $(document).on("click", ".product-image-thumb", function () {
            $(".product-image-thumb").on("click", function () {
            var $image_element = $(this).find("img");
            var newSrc = $image_element.attr("src");
            $(".product-image").attr("src", newSrc);
            $(".product-image-thumb").removeClass("active");
            $(this).addClass("active");
            });
            });

            // Sélectionnez le champ EAN par son ID
            const eanInput = document.querySelector("#ean");
            window.onload = (event) => {
            eanInput.value = "";
            document.getElementById("qte").value = "";
            eanInput.focus();
            }

            function processEAN() {
                const ean = $("#ean");
                if (ean.val().length === 13) {
                $.ajax({
                url: "/scan/ean/ajax/1/" + ean.val(),
                type: "GET"
                }).done(function (data) {
                console.log(data.error);
                const resultRef = document.querySelector(".resultRef");
                const resultDes = document.querySelector(".resultDes");
                // const resultEan = document.querySelector(".resultEan");
                const resultStock = document.querySelector(".resultStock");
                const resultUv = document.querySelector(".resultUv");
                const resultFerme = document.querySelector(".resultFerme");
                const resultSref1 = document.querySelector(".resultSref1");
                const resultSref2 = document.querySelector(".resultSref2");
                const resultFiles = document.querySelector(".resultFiles");
                const resultPictures = document.querySelector(".resultPictures"); // Ajout pour les images

                resultRef.textContent = data.ref;
                resultDes.textContent = data.designation;
                // resultEan.textContent = data.ean;
                resultStock.textContent = data.stock !== null ? data.stock : 0;
                resultUv.textContent = data.uv;
                resultFerme.textContent = data.ferme;
                resultSref1.textContent = data.sref1;
                resultSref2.textContent = data.sref2;
                resultFiles.textContent = data.files;

                // Traiter la liste de fichiers
                if (data.files && data.files.length > 0) {
                resultFiles.innerHTML = \'<div class="btn-group-vertical">\';
                var fileId = 0;
                data.files.forEach(function (file) {
                fileId++;
                var fileName = file.split("/").pop();
                resultFiles.innerHTML += \'<div class="btn-group m-2"><a id="\' + fileId + \'" href="\' + file + \'"target="_blank" class="btn btn-outline-info">\' + fileName + \'</a><a type="button" data-target-id="\' + fileId + \'" class="btn btn-outline-secondary updateButton"><i class="fas fa-xmark"></i></a></div>\';
                });
                resultFiles.innerHTML += \'</div>\';
                } else {
                resultFiles.innerHTML = "Aucun fichier trouvé.";
                }

                // Traiter la liste d\'images
                var mainImage = document.getElementById("product-image");
                var thumbsContainer = document.getElementById("product-image-thumbs");
                // Utilisez un chemin relatif vers le contrôleur Symfony

                if (data.pictures && data.pictures.length > 0) { // Mettre à jour les miniatures
                thumbsContainer.innerHTML = "";
                for (var i = 0; i < data.pictures.length; i++) {
                if (i == 0) { // Mettre à jour l\'image principale
                mainImage.src = data.pictures[0];
                thumbsContainer.innerHTML += \'<div class="product-image-thumb active col-3"><img src="\' + data.pictures[0] + \'" alt="Product Image"></div >\';
                } else { // Mettre à jour les miniatures ici
                thumbsContainer.innerHTML += \'<div class="product-image-thumb col-3"><img src="\' + data.pictures[i] + \'" alt="Product Image"></div > \';
                }

                }
                } else { // Afficher l\'image par défaut si aucune image n\'est trouvée
                mainImage.src = "/img/autre/noPicture.jpg";
                // Afficher un message si aucune image n\'est trouvée
                thumbsContainer.innerHTML = "Aucune image trouvée.";
                }

                if (data.ean == null) { // Le produit n\'a pas été trouvé, donc marquez le champ EAN comme invalide
                $("#ean").attr("class", "form-control col-12 is-invalid");
                $("#produit").attr("class", "d-none");
                } else { // Le produit a été trouvé, marquez le champ EAN comme valide
                $("#ean").attr("class", "form-control col-12 is-valid");
                $("#produit").attr("class", "mt-3");
                document.getElementById("add_pictures_or_docs_reference").value = data.ref;
                }
                if (data.ean == null) { // Le produit n\'a pas été trouvé, donc on affiche pas la section panier
                $("#panier").attr("class", "d-none");
                } else { // Le panier a été trouvé, on donne accés au champs du panier
                $("#panier").attr("class", "");
                }

                if (data.sref1) {
                $("#sref1").attr("class", "nav-link");
                } else {
                $("#sref1").attr("class", "d-none");
                }

                if (data.sref2) {
                $("#sref2").attr("class", "nav-link");
                } else {
                $("#sref2").attr("class", "d-none");
                }

                if (data.ferme) {
                $("#ferme").attr("class", "nav-link");
                } else {
                $("#ferme").attr("class", "d-none");
                }
                });
                } else {
                $("#ean").attr("class", "form-control col-12 is-invalid");
                $("#produit").attr("class", "d-none");
                }
                }

                $(document).ready(function () { // pour détecter les modifications en temps réel
                $("#ean").on("input", function () {
                processEAN();
                });
                });
                $("#ean").on("input change", function () {
                processEAN();
                });

        ';
        return $javascriptCode;
    }
}
