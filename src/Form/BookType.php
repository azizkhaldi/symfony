<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Ajout de l'import pour ChoiceType
use Symfony\Component\Form\Extension\Core\Type\DateType; // Ajout de l'import pour DateType
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Ajout de l'import pour EntityType
use App\Entity\Author; // Ajout de l'import pour l'entité Author

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title') // Correction de "tilte" à "title"
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Science-Fiction' => 'Science-Fiction', // Correction de la casse pour "Science-Fiction"
                    'Mystery' => 'Mystery',
                    'Autobiography' => 'Autobiography',
                ],
            ])
            ->add('publicationDate', DateType::class) // Utilisation de DateType pour la date
            ->add('published')
            ->add('author', EntityType::class, [ // Utilisation de EntityType pour l'entité Author
                'class' => Author::class, // Spécifiez la classe de l'entité Author
                'choice_label' => 'username', // Choisir un champ pour l'affichage dans le formulaire (par exemple, "username")
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
