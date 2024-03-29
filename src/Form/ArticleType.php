<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextType::class,['required' => false])
            ->add('createdAt', DateType::class, ['required' => false])
            ->add('updatedAt', DateType::class, ['required' => false])
            ->add('author', TextType::class, ['required' => false])
            ->add('nbViews', IntegerType::class)
            ->add('published', BooleanType::class, ['required' => false])
            ->add('categories', EntityType::class,
                    ['class' => Category::class,
                        'multiple' => true,
                        'required' => false,
                        'choice_label' => 'name',
                        'query_builder' => function (EntityRepository $em) {
                            return $em->createQueryBuilder('c')
                                ->orderBy('c.name', 'ASC');
                        }
                    ]
                );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
