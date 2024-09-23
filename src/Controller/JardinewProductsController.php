<?php

namespace App\Controller;

use App\Entity\Main\JardinewProductConditions;
use App\Entity\Main\JardinewProducts;
use App\Repository\Divalto\StocksRepository;
use App\Repository\Main\JardinewProductConditionsRepository;
use App\Repository\Main\JardinewProductsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]

class JardinewProductsController extends AbstractController
{
    #[Route('/jardinew/products', name: 'app_jardinew_products')]
    public function index(JardinewProductsRepository $repo): Response
    {
        return $this->render('jardinew_products/index.html.twig', [
            'products' => $repo->findAll(),
        ]);
    }

    // importer les produits présents sur le site Jardinew pour alimenter le tableau de l'intranet
    #[Route('/jardinew/products/import', name: 'app_jardinew_products_import')]
    public function import(JardinewProductsRepository $repo, EntityManagerInterface $entityManager)
    {
        $localFile = 'C:/wamp64/www/Intranet/public/tmp/export.csv';

        if (!file_exists($localFile)) {
            $this->addFlash('danger', 'Le fichier CSV n\'existe pas.');
            return $this->redirectToRoute('app_jardinew_products');
        }

        $handle = fopen($localFile, 'r');
        if ($handle === false) {
            $this->addFlash('danger', 'Impossible d\'ouvrir le fichier CSV.');
            return $this->redirectToRoute('app_jardinew_products');
        }

        $header = fgetcsv($handle); // Supposons que la première ligne du fichier CSV contient les noms des colonnes
        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            if (count($data) < 9) { // Assurez-vous que chaque ligne a au moins 4 colonnes (name, description, price, sku)
                $this->addFlash('danger', 'Ligne CSV invalide.');
                continue;
            }

            $sku = $data[1];
            $existingProduct = $repo->findOneBy(['sku' => $sku]);

            // supprimer le produit si il est fermé sur le site Jardinew
            if ($data[5] == 'trash' && $existingProduct) {
                $entityManager->remove($existingProduct);
            }
            // créer les produits qui n'existent pas
            if (!$existingProduct) {
                $product = new JardinewProducts();
                $product->setIdWordpress($data[0]);
            } else {
                $product = $existingProduct;
            }
            try {
                if ($data[7] > 0) {
                    $product->setSku($data[1]);
                    $product->setPermalien($data[2]);
                    $product->setLastPurchase((float) str_replace(',', '.', $data[3]));
                    $product->setRef($data[4]);
                    $product->setSref1($data[5]);
                    $product->setSref2($data[6]);
                    $product->setPrice((float) $data[7]);
                    $product->setMarge((((float) $data[7] / (float) $data[3]) - 1) * 100);
                    $entityManager->persist($product);
                }
            } catch (\Throwable $th) {
                continue;
            }
        }

        fclose($handle);
        $entityManager->flush();

        $this->addFlash('message', 'Importation effectuée avec succès.');
        return $this->redirectToRoute('app_jardinew_products');
    }

    //
    #[Route('/jardinew/products/maj_stock/{id}', name: 'app_jardinew_products_maj_stock')]
    public function majStockAndPrice(JardinewProductsRepository $repo, EntityManagerInterface $entityManager, StocksRepository $repoStock, JardinewProductConditionsRepository $repoConditions, $id = null)
    {

        if ($id) {
            $produits = $repo->findBy(['idWordpress' => $id]);
        } else {
            $produits = $repo->findAll();
        }
        foreach ($produits as $produit) {

            $produitDivalto = $repoStock->getStockjN($produit->getRef(), $produit->getSref1(), $produit->getSref2());
            if ($produitDivalto) {
                $port = $produitDivalto['ratio_port'];
            }
            try {
            } catch (\Throwable $th) {
                dd($th);
            }
            if (!$produitDivalto) {
                try {
                    $produitDivalto = $repoStock->getNoStockjN($produit->getRef());
                    $port = 0;
                } catch (\Throwable $th) {
                    dd($th);
                }
            }
            $product = $repo->findOneBy(['sku' => $produit->getSku()]);
            if ($produitDivalto) {
                try {
                    $price = $produitDivalto['pu'] + $port;
                    $price = $price * 1.1;
                    if ($product->getCoeffConversion()) {
                        $price = $price * $product->getCoeffConversion();
                    }
                    if ($id) {
                        $lpa = $product->getPreviousPurchase();
                        $lpv = $lpa * (1 + ($product->getMarge() / 100));
                        //dd($product->getMarge());
                    } else {
                        $lpa = $product->getLastPurchase();
                        $lpv = $product->getPrice();
                    }

                } catch (\Throwable $th) {
                    dd($produitDivalto);
                }

                $cond = $repoConditions->findOneBy(['idWordpress' => $product->getIdWordpress(), 'purchase' => $produitDivalto['facture']]);
                if ($cond) {
                    $price = $price * $cond->getCoeffCorrection();
                }

                $product->setStock($produitDivalto['stock'])
                    ->setUv(trim($produitDivalto['uv']))
                    ->setDatePurchase(new DateTime($produitDivalto['datePu']))
                    ->setNumberPurchase($produitDivalto['facture'])
                    ->setPreviousPurchase((float) $lpa)
                    ->setLastPurchase($price)
                    ->setValidationPrice(0)
                    ->setMarge((($lpv / $lpa) - 1) * 100)
                    ->setPrice($price * (1 + (($lpv / $lpa) - 1)))
                    ->setClosed($produitDivalto['conf']);
            } else {
                $product->setMarge('introuvable');
            }
            $entityManager->persist($product);
        }
        $entityManager->flush();

        $this->addFlash('message', 'Stock est prix mis à jours avec succés');
        return $this->redirectToRoute('app_jardinew_products');

    }

    #[Route('/jardinew/process-selected-products', name: 'app_jardinew_process_selected_products', methods: ['POST'])]
    public function processSelectedProducts(Request $request, JardinewProductsRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $selectedProducts = $request->request->all('selected_products');

        if (empty($selectedProducts)) {
            $this->addFlash('warning', 'Aucun produit sélectionné.');
            return $this->redirectToRoute('app_jardinew_products'); // ou la route appropriée
        }

        foreach ($selectedProducts as $productId) {
            $product = $repo->findOneBy(['idWordpress' => $productId]);
            $product->setValidationPrice(1);
            $entityManager->persist($product);
        }
        $entityManager->flush();

        $this->addFlash('success', 'Les produits sélectionnés ont été traités.');
        return $this->redirectToRoute('app_jardinew_products'); // Redirige vers la liste après traitement
    }

    #[Route('/list-condition', name: 'list_condition')]
    public function listCondition(Request $request, JardinewProductConditionsRepository $repoConditions): JsonResponse
    {
        // Récupérer le contenu JSON de la requête
        $data = json_decode($request->getContent(), true);
        $idWordpress = $data['idWordpress'] ?? null;

        if (!$idWordpress) {
            return new JsonResponse(['status' => 'error', 'message' => 'idWordpress is required'], 400);
        }

        // Rechercher les conditions correspondant à cet idWordpress
        $conditions = $repoConditions->findBy(['idWordpress' => $idWordpress]);

        // Préparer un tableau pour stocker les résultats à renvoyer
        $conditionData = [];

        // Parcourir les conditions et récupérer purchase et coeffCorrection
        foreach ($conditions as $condition) {
            $conditionData[] = [
                'id' => $condition->getId(),
                'purchase' => $condition->getPurchase(),
                'coeffCorrection' => $condition->getCoeffCorrection(),
            ];
        }

        // Renvoyer les conditions sous forme de réponse JSON
        return new JsonResponse(['conditions' => $conditionData]);
    }

    #[Route('/create-condition', name: 'create_condition')]
    public function createCondition(Request $request, EntityManagerInterface $entityManager, JardinewProductsRepository $repo, JardinewProductConditionsRepository $repoConditions): JsonResponse
    {
        try {
            // Lire les données JSON envoyées
            $data = json_decode($request->getContent(), true);

            // Assurer que les données sont correctement décodées
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON data'], 400);
            }

            // Extraire les valeurs
            $idWordpress = $data['idWordpress'] ?? null;
            $purchase = $data['purchase'] ?? null;
            $coeff = $data['coeff'] ?? null;

            // Validation simple des données
            if (!$idWordpress || !$purchase || !$coeff) {
                return new JsonResponse(['status' => 'error', 'message' => 'Invalid data'], 400);
            }

            $condition = $repoConditions->findOneBy(['idWordpress' => $idWordpress, 'purchase' => $purchase]);
            if (!$condition) {
                $condition = new JardinewProductConditions();
            }

            // Sauvegarder la condition dans la base de données
            $condition->setPurchase($purchase)
                ->setIdWordpress($idWordpress)
                ->setCoeffCorrection($coeff);

            $entityManager->persist($condition);
            $entityManager->flush();

            return new JsonResponse(['status' => 'success']);
        } catch (\Exception $e) {
            // Loguer l'erreur si nécessaire
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    #[Route('/delete-condition', name: 'delete_condition')]
    public function deleteCondition(Request $request, EntityManagerInterface $entityManager, JardinewProductConditionsRepository $repoConditions): JsonResponse
    {
        // l'id de la condition
        $id = $request->request->get('id');

        // Supprimer la condition dans la base de données
        $condition = $repoConditions->findOneBy(['id' => $id]);
        $entityManager->remove($condition);
        $entityManager->flush();

        return new JsonResponse(['status' => 'deleted']);
    }

}
