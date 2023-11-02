<?php

namespace App\Controller;
use App\Form\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

#[Route('/AfficheBook', name: 'app_AfficheBook')]
public function Affiche(BookRepository $repository)
{
    $publishedBooks = $repository->findBy(['published' => true]);

    $numPublishedBooks = count($publishedBooks);
    $numUnPublishedBooks = $repository->count(['published' => false]);

    if ($numPublishedBooks > 0) {
        return $this->render('book/Affiche.html.twig', [
            'publishedBooks' => $publishedBooks,
            'numPublishedBooks' => $numPublishedBooks,
            'numUnPublishedBooks' => $numUnPublishedBooks,
        ]);
    } else {
        // No published books found, you can render a message directly
        return $this->render('book/no_books_found.html.twig');
    }
}


#[Route('/AddBook', name: 'app_AddBook')]
public function add(Request $request, EntityManagerInterface $entityManager)
{
    $book = new Book();
    $form = $this->createForm(BookType::class, $book);
    $form->add('Ajouter', SubmitType::class);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $author = $book->getAuthor();

        if ($author instanceof Author) {
            $author->setNbBooks($author->getNbBooks() + 1);
        }

        $entityManager->persist($book);
        $entityManager->flush();

        // Récupérez l'ID du livre que vous venez d'ajouter
        $bookId = $book->getId();

        return $this->redirectToRoute('app_AfficheBook');
    }

    return $this->render('book/Add.html.twig', [
        'f' => $form->createView(),
    ]);
}




#[Route('/editbook/{ref}', name: 'app_editBook')]
public function edit($ref, BookRepository $repository, Request $request, EntityManagerInterface $entityManager) 
{
    $book = $repository->find($ref);

    if (!$book) {
        throw $this->createNotFoundException('Livre non trouvé'); // Gestion d'erreur si le livre n'est pas trouvé.
    }

    $form = $this->createForm(BookType::class, $book);
    $form->add('Edit', SubmitType::class);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_AfficheBook');
    }

    return $this->render('book/edit.html.twig', [
        'f' => $form->createView(),
    ]);
}


#[Route('/deletebook/{ref}', name: 'app_deleteBook')]
public function delete($ref, BookRepository $repository, EntityManagerInterface $entityManager)
{
    $book = $repository->find($ref);

    if (!$book) {
        throw $this->createNotFoundException('Livre non trouvé'); // Gestion d'erreur si le livre n'est pas trouvé.
    }

    $entityManager->remove($book);
    $entityManager->flush();

    return $this->redirectToRoute('app_AfficheBook');
}

    #[Route('/ShowBook/{ref}', name: 'app_detailBook')]

    public function showBook($ref, BookRepository $repository)
    {
        $book = $repository->find($ref);
        if (!$book) {
            return $this->redirectToRoute('app_AfficheBook');
        }

        return $this->render('book/show.html.twig', ['b' => $book]);

    }
    #[Route('/books-by-title/{title}', name: 'app_booksByTitle')]
    public function booksByTitle(Request $request, BookRepository $bookRepository, string $title)
    {
        // Rechercher les livres par titre
        $books = $bookRepository->findBy(['title' => $title]);
    
        return $this->render('book/books_by_title.html.twig', [
            'title' => $title,  // Passer le titre à la vue
            'books' => $books,
        ]);
    }
  
    #[Route('/searchBookByRef', name: 'app_searchBookByRef')]
    public function searchBookByRef(Request $request, BookRepository $bookRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $books = [];
    
        if ($searchTerm) {
            // Appelez la méthode de requête personnalisée findByRef
            $books = $bookRepository->findByRef($searchTerm);
        }
    
        return $this->render('book/searchBookByRef.html.twig', [
            'books' => $books,
            'searchTerm' => $searchTerm,
        ]);
    }
#[Route('/books-by-authors/{authorId}', name: 'app_booksByAuthors')]
public function booksByAuthors(BookRepository $bookRepository, EntityManagerInterface $entityManager, $authorId): Response
{
    // Obtenez l'instance de l'entité Author en fonction de l'ID de l'auteur
    $author = $entityManager->getRepository(Author::class)->find($authorId);

    // Vérifiez si l'auteur existe
    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé.');
    }

    // Utilisez la méthode findByAuthors pour obtenir la liste des livres par cet auteur
    $books = $bookRepository->findByAuthors($author);

    return $this->render('book/books_by_authors.html.twig', [
        'books' => $books,
    ]);
}


    #[Route('/books-published-before-2023', name: 'app_booksPublishedBefore2023')]
    public function booksPublishedBefore2023(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBooksPublishedBefore2023WithAuthorMoreThan10Books();

        return $this->render('book/books_published_before_2023.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/update-scifi-to-romance', name: 'app_updateScifiToRomance')]
    public function updateScifiToRomance(BookRepository $bookRepository, EntityManagerInterface $entityManager): Response
    {
        $bookRepository->updateCategoryToRomance();

        $entityManager->flush();

        return $this->redirectToRoute('app_AfficheBook');
    }
    #[Route('/count-romance-books', name: 'app_countRomanceBooks')]
    public function countRomanceBooks(BookRepository $bookRepository): Response
    {
        $count = $bookRepository->countBooksInCategory('Romance');

        return $this->render('book/count_romance_books.html.twig', [
            'count' => $count,
        ]);
    }
    #[Route('/books-published-between-dates', name: 'app_booksPublishedBetweenDates')]
    public function booksPublishedBetweenDates(BookRepository $bookRepository): Response
    {
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2018-12-31');

        $books = $bookRepository->findBooksPublishedBetweenDates($startDate, $endDate);

        return $this->render('book/books_published_between_dates.html.twig', [
            'books' => $books,
        ]);
    }

}