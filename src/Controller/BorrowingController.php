<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Entity\Product;
use App\Form\BorrowingType;
use App\Repository\BorrowingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class BorrowingController extends AbstractController
{
    /**
     * @Route("/borrowing/{id}", name="borrowing")
     * @IsGranted("CAN_BORROW", subject="product")
     */
    public function index(
        Product $product,
        Request $request,
        ObjectManager $objectManager,
        BorrowingRepository $borrowingRepository
    )
    {
        $user = $this->getUser();
        $borrowing = new Borrowing();

        $borrowing->setUser($user);
        $borrowing->setProduct($product);

        $borrowingForm = $this->createForm(BorrowingType::class, $borrowing);

        $borrowingForm->handleRequest($request);

        if($borrowingForm->isSubmitted() && $borrowingForm->isValid()) {
            $objectManager->persist($borrowing);
            $objectManager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('borrowing/index.html.twig', [
            'borrowing_form' => $borrowingForm->createView(),
        ]);
    }
}
