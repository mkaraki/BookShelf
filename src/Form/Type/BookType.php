<?php
namespace App\Form\Type;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Publisher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('bookRead', TextType::class, [
                'required' => false,
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
            ])
            ->add('disambiguation', TextareaType::class, [
                'required' => false,
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => function (Author $author): string {
                    $name = $author->getName();
                    $d = $author->getDisambiguation();

                    if (empty($d))
                        return $name;
                    else
                        return "$name ($d)";
                },
                'required' => false,
                'multiple' => true,
            ])
            ->add('publisher', EntityType::class, [
                'class' => Publisher::class,
                'choice_label' => function (Publisher $pub): string {
                    $name = $pub->getName();
                    $d = $pub->getDisambiguation() ?? '';

                    if (empty($d))
                        return $name;
                    else
                        return "$name ($d)";
                },
                'required' => false,
            ])
            ->add('save', SubmitType::class)
        ;
    }
}
