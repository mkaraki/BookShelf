<?php
namespace App\Form\Type;

use App\Entity\Book;
use App\Entity\Shelf;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OwnedBookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => function (Book $book): string {
                    $name = $book->getName();
                    $d = $book->getDisambiguation() ?? '';

                    if (empty($d))
                        return $name;
                    else
                        return "$name ($d)";
                },
            ])
            ->add('parentShelf', EntityType::class, [
                'class' => Shelf::class,
                // The property to display in the dropdown (e.g., the category's name)
                'choice_label' => function (Shelf $shelf): string {
                    $shelfNo = $shelf->getShelfNumber();
                    $case = $shelf->getParentBookCase();
                    $caseName = $case->getName();
                    $room = $case->getParentRoom();
                    $roomName = $room->getName();
                    $site = $room->getParentSite();
                    $siteName = $site->getName();

                    return "$siteName / $roomName / $caseName / $shelfNo";
                },
            ])
            ->add('save', SubmitType::class)
        ;
    }
}
