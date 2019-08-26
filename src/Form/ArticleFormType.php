<?php

namespace App\Form;

use App\Entity\Article;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Article $article */
        $article = $options['data'] ?? null;
        $isEdit = $article && $article->getId();

        $builder
            ->add('title', TextType::class, [
                'help' => 'Choose something catchy !'
            ])
            ->add('content', TextareaType::class, [
                'rows' => 15
            ])
            ->add('author', UserSelectTextType::class, [
                'disabled' => $isEdit
            ]);

        /* Keeping for reference
         * ->add('author', EntityType::class, [
            'class' => User::class,
            'choice_label' => function(User $user){
                return sprintf('(%d) %s - %s', $user->getId(), $user->getEmail(), $user);
            },
            'placeholder' => 'Choose an author',
            'choices' => $this->userRepository->findAllEmailAlphabetical(),
            'invalid_message' => 'Symfony is too smart for your hacking !'
        ])*/


        if ($options['include_published_at']) {
            $builder->add('publishedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                //@TODO delete data (=> default value), only to avoid installing a datepicker
                'data' => new \DateTime(),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'include_published_at' => false
        ]);
    }
}
