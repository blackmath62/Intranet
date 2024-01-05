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
            eanInput.focus();
            }

            const emplacement = $("#emplacement");

            $(document).ready(function () {
            if (emplacement.length) {
            emplacement.on("input change", function () {
            processEmplacement();
            });
            }
            });

            function processEmplacement() {
            const emplacementValue = emplacement.val();

            if (emplacementValue) {
            $.ajax({
            url: "/emplacement/scan/ajax/1/" + emplacementValue,
            type: "GET"
            }).done(function (data) {

            if (data.empl === false) {
            emplacement.removeClass("is-valid").addClass("is-invalid");
            } else {
            emplacement.removeClass("is-invalid").addClass("is-valid");
            }

            // Fermer la modal ici après avoir traité le scan de l\'emplacement
            closeScannerModal();
            });
            }
            }

            const oldLocationField = $(\'#alimentation_emplacement_ean_oldLocation\');
            const locationField = $(\'#retrait_marchandise_ean_location\');

            function processEan() {
                const ean = $("#ean");
                if (ean.val().length === 13) {
                $.ajax({
                url: "/scan/ean/ajax/1/" + ean.val(),
                type: "GET"
                }).done(function (data) {
                update_imprimer_etiquette_button();
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

                if (data.stock && Array.isArray(data.stock) && data.stock.length > 0) {
                    const resultStock = document.querySelector(".resultStock");

                    // Supprimez le contenu existant avant d\'ajouter la nouvelle table
                    resultStock.innerHTML = "";

                    // Créez une table pour afficher les informations détaillées du stock
                    const table = document.createElement("table");
                    table.classList.add("table"); // Ajoutez des classes Bootstrap si nécessaire

                    // Créez l\'en-tête de la table
                    const thead = document.createElement("thead");
                    const headerRow = document.createElement("tr");
                    const headers = ["Emplacement", "Nature du stock", "Quantité"];

                    headers.forEach(headerText => {
                        const th = document.createElement("th");
                        th.textContent = headerText;
                        headerRow.appendChild(th);
                    });

                    thead.appendChild(headerRow);
                    table.appendChild(thead);

                    // Créez le corps de la table
                    const tbody = document.createElement("tbody");

                    data.stock.forEach(stockRow => {
                        const tr = document.createElement("tr");

                        // Ajoutez chaque cellule de la ligne
                        ["empl", "natureStock", "qteStock"].forEach(column => {
                            const td = document.createElement("td");
                            td.textContent = stockRow[column];
                            tr.appendChild(td);
                        });

                        tbody.appendChild(tr);
                    });

                    table.appendChild(tbody);

                    // Ajoutez la table à votre élément HTML
                    resultStock.appendChild(table);
                } else {
                    // Affichez 0 ou un message approprié
                    const resultStock = document.querySelector(".resultStock");
                    resultStock.textContent = "0";
                }


                if (oldLocationField.length) {
                    alimSource(data.stock);
                }
                if (locationField.length) {
                    alimSource(data.stock);
                }

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
                resultFiles.innerHTML += \'<div class="btn-group m-2"><a id="\' + fileId + \'" href="\' + file +  \'" target="_blank" class="btn btn-outline-info">\' + fileName + \'</a><a type="button" data-target-id="\' + fileId + \'" class="btn btn-outline-secondary deleteButton"><i class="fas fa-xmark"></i></a></div>\';
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
                if (oldLocationField.length) {
                    alimSource(data.stock);
                }
                if (locationField.length) {
                    alimSource(data.stock);
                }
                $("#produit").attr("class", "d-none");
                } else { // Le produit a été trouvé, marquez le champ EAN comme valide
                $("#ean").attr("class", "form-control col-12 is-valid");
                if (oldLocationField.length) {
                    alimSource(data.stock);
                }
                if (locationField.length) {
                    alimSource(data.stock);
                }
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

                $(document).ready(function () { // pour détecter les modifications en temps réel du champ EAN
                $("#ean").on("input", function () {
                processEan();
                });
                });
                $("#ean").on("input change", function () {
                processEan();
                });

                function handleFileDeletion(targetId) { // Vérifier le type d\'élément (image ou fichier)
                    var element = document.getElementById(targetId);
                    var filePath,
                    fileName;

                    if (element.tagName.toLowerCase() === "img") { // Pour les images, utilisez l\'attribut src
                    filePath = element.src;
                    } else if (element.tagName.toLowerCase() === "a") { // Pour les fichiers, utilisez l\'attribut href
                    filePath = element.href;
                    }

                    if (! filePath) {
                    console.error("Impossible de déterminer le chemin du fichier.");
                    return;
                    }

                    fileName = filePath.substring(filePath.lastIndexOf("/") + 1);

                    var isConfirmed = confirm("Êtes-vous sûr de vouloir supprimer le fichier " + fileName + " ?");

                    if (isConfirmed) {
                    $.ajax({
                    url: "/ajax/product/delete/file/1/" + document.getElementById("add_pictures_or_docs_reference").value + "/" + fileName,
                    type: "POST",
                    success: function (response) {
                    processEan();
                    },
                    error: function (error) {
                    console.error("Erreur lors de la suppression du fichier :", error);
                    }
                    });
                    }
                    }
                    document.addEventListener("DOMContentLoaded", function () {
                        $(document).on("click", ".deleteButton", function () {
                            var targetId = $(this).attr("data-target-id");
                            handleFileDeletion(targetId);
                        });
                    });
                    // pour ajouter un fichier par un pour la photo ou fichier selon la page utilisée pour déposer le fichier
                    document.addEventListener("DOMContentLoaded", function () { // Sélectionnez tous les éléments avec la classe addFile
                        const fileInputs = document.querySelectorAll(".addFile");
                        // Supprimez tous les gestionnaires d\'événements change précédemment attachés
                        //fileInputs.off("change");
                        // Ajoutez un gestionnaire d\'événements pour chaque champ de fichier
                        fileInputs.forEach(function (fileInput) {
                        fileInput.addEventListener("change", function () { // Vérifiez si un fichier est sélectionné
                        if (fileInput.files.length > 0) { // Récupérez l\'ID du <i>
                        const iconId = fileInput.id;

                        // Créez un objet FormData pour envoyer le fichier
                        const formData = new FormData();
                        formData.append("addFile", fileInput.files[0]);

                        // Effectuez l\'envoi AJAX
                        $.ajax({
                        url: "/ajax/product/add/file/1/" + iconId + "/" + document.getElementById("add_pictures_or_docs_reference").value,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) { // Traitez la réponse si nécessaire
                            processEan();
                        },
                        error: function (error) { // Traitez les erreurs si nécessaire
                        alert("Le nom de fichier existe déjà..., veuillez renommer le fichier ou mettre un autre fichier");
                        }
                        });
                        }
                        });
                        });
                        });

                        const imprimerEtiquetteLink = document.getElementById("imprimer-etiquette-button");
                        const champNombreInput = document.getElementById("champ_nombre");
                        let linkPath = "";  // Déclarer linkPath en dehors de la fonction

                        function update_imprimer_etiquette_button() {
                            // Récupérez les valeurs des champs ean et champ_nombre
                            const eanValue = eanInput.value;
                            const champNombreValue = champNombreInput.value;
                            // Construisez le chemin du lien avec les valeurs d\'ean et champ_nombre
                            linkPath = "/pdf/etiquette/" + eanValue + "/" + champNombreValue;
                        }

                        champNombreInput.addEventListener("input", function () {
                            update_imprimer_etiquette_button();
                        });

                        imprimerEtiquetteLink.addEventListener("click", function (e) {
                            e.preventDefault();  // Empêche la navigation par défaut du lien
                            // Effectuez l\'envoi AJAX ici
                            $.ajax({
                                url: linkPath,
                                type: "GET",
                                success: function (response) {
                                    // Traitez la réponse si nécessaire
                                    alert(response);
                                },
                                error: function (error) {
                                    // Traitez les erreurs si nécessaire
                                    alert(error);
                                }
                            });
                        });

                        // SYSTEME DE RECHERCHE DE PRODUIT PAR DESIGNATION OU CODE PRODUIT
		document.addEventListener(\'DOMContentLoaded\', function () {
var openButton = document.getElementById(\'openProductsModal\');
var modal;

openButton.addEventListener(\'click\', function () {
var eanInput = document.getElementById(\'ean\');
var modalTemplate = document.getElementById(\'productsModal\');
var eanValue = eanInput.value.trim();

if (eanValue.length >= 5) {
var dosValue = 1;
var searchValue = encodeURIComponent(eanValue.toUpperCase());
var openProductCheck = document.getElementById(\'openProductsCheckbox\');
var checkProd = 0;
// Tester si la case à cocher est cochée
if (openProductCheck.checked) {
checkProd = 1; // Si cochée, assigner la valeur correspondante
}
var searchProducts = \'/products/search/ajax/\' + dosValue + \'/\' + searchValue + \'/\' + checkProd;

// Effectuer une requête AJAX
axios.get(searchProducts).then(function (response) { // Traitez la réponse de la requête AJAX ici
var numberOfResults = response.data.length;
// Clonez le modèle à chaque ouverture pour éviter les problèmes après la fermeture
modal = new bootstrap.Modal(modalTemplate.cloneNode(true));
if (numberOfResults > 0) {
modal._element.querySelector(\'.resultQte\').textContent = numberOfResults + " produit(s) trouvé(s)";
updateProductCards(response.data);
} else {
modal._element.querySelector(\'.modal-body\').innerHTML = "<div class=\'info-box mb-3 bg-light\'><span class=\'info-box-icon\'><i class=\'fa-solid text-info fa-face-dizzy fa-2x\'></i></span><div class=\'info-box-content\'><span class=\'info-box-number\'>Veuillez être plus précis dans votre demande</span><span class=\'info-box-text\'>Aucun produit trouvé .....</span ></div></div>";
}

// Ouvrir la modal
modal.show();
}).catch(function (error) { // Gérez les erreurs de la requête AJAX ici
alert("Erreur lors de la requête AJAX.");
console.error("Erreur lors de la requête AJAX :", error);
});
} else { // Si la condition n\'est pas remplie, affichez une alerte
alert("Le champ EAN doit contenir au moins 5 caractères.");
}
});

function updateProductCards(products) { // Supprimer le contenu existant dans la modal
var modalContent = modal._element.querySelector(\'.modal-body\');
modalContent.innerHTML = "";

// Itérer sur chaque produit et créer une carte pour chacun
products.forEach(function (product) {
var productCard = createProductCard(product);
modalContent.appendChild(productCard);
});
}

function createProductCard(product) { // Créer une nouvelle carte de produit en utilisant le modèle HTML que vous avez fourni
var productCardTemplate = document.getElementById(\'produit_light\');
var productCard = productCardTemplate.cloneNode(true);
productCard.classList.remove(\'d-none\');
// Afficher la carte

// Mettre à jour le contenu de la carte avec les données du produit
productCard.querySelector(\'.resultDes\').textContent = product.designation;
productCard.querySelector(\'.resultRef\').textContent = product.ref;
productCard.querySelector(\'.resultSref1\').textContent = product.sref1;
productCard.querySelector(\'.resultSref2\').textContent = product.sref2;
productCard.querySelector(\'.resultStock\').textContent = product.stock
productCard.querySelector(\'.resultFerme\').textContent = product.ferme;

// Mettre à jour l\'URL de l\'image
var productImage = productCard.querySelector(\'.product-image\');
if (product.pictures[0]) {
productImage.src = product.pictures[0];
} else {
productImage.src = "/img/autre/noPicture.jpg";
}

// Ajoutez un gestionnaire d\'événements au bouton "C\'est ce que je cherche !"
var addToCartButton = productCard.querySelector(\'.trouve\');
var cardFooter = productCard.querySelector(\'.card-footer\');

if (product.ean) { // Afficher le bouton seulement s\'il y a un EAN
cardFooter.classList.remove(\'d-none\');
addToCartButton.innerHTML = "C\'est ce que je cherche ! <i class=\'fa-solid fa-face-laugh-beam\'></i>";
addToCartButton.addEventListener(\'click\', function () { // Mettez à jour le champ EAN avec le code EAN du produit
eanInput.value = product.ean;
// Fermer la modal après avoir mis à jour le champ EAN
processEan();
modal.hide();
});
} else { // Afficher un texte différent si pas d\'EAN
addToCartButton.innerHTML = "Pas de code EAN sur ce produit <i class=\'fa-solid fa-face-frown-open\'></i>";
// Désactiver le bouton ou masquer, selon votre choix
addToCartButton.classList.add(\'disabled\', \'btn-danger\');
// Vous pouvez également masquer la div cardFooter si vous ne voulez pas afficher le bouton du tout
}

return productCard;
}
});
        ';
        return $javascriptCode;
    }
}
