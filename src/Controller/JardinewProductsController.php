<?php

namespace App\Controller;

use App\Entity\Main\JardinewProducts;
use App\Repository\Divalto\StocksRepository;
use App\Repository\Main\JardinewProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            //dd($data);
            if (count($data) < 4) { // Assurez-vous que chaque ligne a au moins 4 colonnes (name, description, price, sku)
                $this->addFlash('danger', 'Ligne CSV invalide.');
                continue;
            }

            $sku = $data[3];
            $existingProduct = $repo->findOneBy(['sku' => $sku]);

            if (!$existingProduct) {

                $product = new JardinewProducts();
                $product->setIdWordpress($data[0]);
                $product->setPrice((float) $data[1]);
                $product->setSku($data[2]);
                $product->setStock((float) $data[4]);
                $entityManager->persist($product);

            }
        }

        fclose($handle);
        $entityManager->flush();

        $this->addFlash('message', 'Importation effectuée avec succès.');
        return $this->redirectToRoute('app_jardinew_products');
    }

    #[Route('/jardinew/products/maj_stock', name: 'app_jardinew_products_maj_stock')]
    public function majStockAndPrice(JardinewProductsRepository $repo, EntityManagerInterface $entityManager, StocksRepository $repoStock)
    {

        $produits = $repo->findAll();
        foreach ($produits as $produit) {
            try {
                $produitDivalto = $repoStock->getStockjN($produit->getSku());
            } catch (\Throwable $th) {
                dd($th);
            }
            $product = $repo->findOneBy(['sku' => $produit->getSku()]);
            if ($produitDivalto) {
                $product->setStock($produitDivalto['stock'])
                    ->setLastPurchase($produitDivalto['prix']);
            } else {
                $product->setMarge('introuvable');
            }
            $entityManager->persist($product);
        }
        $entityManager->flush();

        $this->addFlash('message', 'Stock est prix mis à jours avec succés');
        return $this->redirectToRoute('app_jardinew_products');

    }

    #[Route('/jardinew/products/import/fortune', name: 'app_jardinew_products_import_fortune')]
    public function importFortune(JardinewProductsRepository $repo, EntityManagerInterface $entityManager)
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
            if (count($data) < 4) { // Assurez-vous que chaque ligne a au moins 4 colonnes (name, description, price, sku)
                $this->addFlash('danger', 'Ligne CSV invalide.');
                continue;
            }

            $id = $data[0];
            $existingProduct = $repo->findOneBy(['idWordpress' => $id]);

            if ($existingProduct) {

                $product = $existingProduct;
                $product->setRef($data[1]);
                $product->setMarge($data[4]);
                $entityManager->persist($product);

            }
        }

        fclose($handle);
        $entityManager->flush();

        $this->addFlash('message', 'Importation effectuée avec succès.');
        return $this->redirectToRoute('app_jardinew_products');
    }
}
