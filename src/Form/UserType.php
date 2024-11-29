<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Form;

use Siganushka\Contracts\Doctrine\ResourceInterface;
use Siganushka\UserBundle\Form\Type\RepeatedPasswordType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 4),
                ],
            ])
            ->add('rawPassword', RepeatedPasswordType::class)
        ;
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $data = $form->getData();
        if ($data instanceof ResourceInterface && $data->getId()) {
            // Using dynamic name for RepeatedType
            $rawPassword = $form->get('rawPassword');
            $firstName = $rawPassword->getConfig()->getOption('first_name', 'first');

            $view['rawPassword'][$firstName]->vars['help'] = 'Please do not fill in if you do not want to change the password!';
            $view['rawPassword'][$firstName]->vars['help_attr'] = ['class' => 'text-warning'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => new UniqueEntity(fields: 'identifier'),
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                return $data instanceof ResourceInterface && $data->getId()
                    ? ['Default']
                    : ['Default', 'PasswordRequired'];
            },
        ]);
    }
}
